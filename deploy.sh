#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  EUT Snack House — AWS EC2 Deployment Script
#  Run this on a fresh Amazon Linux 2023 or Ubuntu 22.04 instance
#  Usage: chmod +x deploy.sh && ./deploy.sh
# ═══════════════════════════════════════════════════════════════

set -e   # Stop on any error

APP_DIR="/var/www/eut"
REPO_URL="https://github.com/YOUR_USERNAME/YOUR_REPO.git"   # <-- change this

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " EUT Snack House — EC2 Setup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# ── 1. System packages ────────────────────────────────────────
echo "[1/10] Installing system packages..."
sudo dnf update -y
sudo dnf install -y git curl unzip nginx

# ── 2. PHP 8.3 ───────────────────────────────────────────────
echo "[2/10] Installing PHP 8.3..."
sudo dnf install -y php8.3 php8.3-fpm php8.3-mysqlnd php8.3-mbstring \
    php8.3-xml php8.3-zip php8.3-gd php8.3-curl php8.3-intl \
    php8.3-bcmath php8.3-opcache

# ── 3. Composer ───────────────────────────────────────────────
echo "[3/10] Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# ── 4. Node.js 20 ────────────────────────────────────────────
echo "[4/10] Installing Node.js 20..."
curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
sudo dnf install -y nodejs

# ── 5. Clone / pull code ──────────────────────────────────────
echo "[5/10] Deploying application code..."
sudo mkdir -p $APP_DIR
sudo chown ec2-user:ec2-user $APP_DIR

if [ -d "$APP_DIR/.git" ]; then
    echo "  → Pulling latest changes..."
    cd $APP_DIR
    git pull origin main
else
    echo "  → Cloning repository..."
    git clone $REPO_URL $APP_DIR
    cd $APP_DIR
fi

# ── 6. PHP dependencies ───────────────────────────────────────
echo "[6/10] Installing PHP dependencies (production only)..."
composer install --no-dev --optimize-autoloader --no-interaction

# ── 7. JS assets build ────────────────────────────────────────
echo "[7/10] Building frontend assets..."
npm ci --production=false
npm run build
rm -rf node_modules   # Remove after build to save disk space

# ── 8. Laravel setup ─────────────────────────────────────────
echo "[8/10] Running Laravel setup..."

# Copy env if not exists
if [ ! -f .env ]; then
    echo "  → .env not found! Creating from example..."
    cp .env.production.example .env
    echo ""
    echo "  ⚠  IMPORTANT: Edit .env with your real credentials before continuing!"
    echo "     Run: nano /var/www/eut/.env"
    echo ""
    read -p "  Press ENTER after editing .env to continue..."
fi

php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link

# Create queue jobs and sessions tables
php artisan queue:table 2>/dev/null || true
php artisan session:table 2>/dev/null || true
php artisan cache:table 2>/dev/null || true
php artisan migrate --force

# ── 9. File permissions ───────────────────────────────────────
echo "[9/10] Setting file permissions..."
sudo chown -R nginx:nginx $APP_DIR
sudo chmod -R 755 $APP_DIR
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# ── 10. Nginx config ──────────────────────────────────────────
echo "[10/10] Configuring Nginx..."
sudo tee /etc/nginx/conf.d/eut.conf > /dev/null <<'NGINX'
server {
    listen 80;
    server_name _;                    # Replace _ with your domain

    root /var/www/eut/public;
    index index.php;

    # Health check
    location /health {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # WebSocket proxy for Reverb (port 8080 → /ws path)
    location /app/ {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 3600s;
        proxy_send_timeout 3600s;
    }

    client_max_body_size 20M;
}
NGINX

sudo nginx -t && sudo systemctl restart nginx
sudo systemctl enable nginx

# ── Systemd services for queue worker + Reverb ───────────────
echo "Setting up systemd services..."

# Queue worker
sudo tee /etc/systemd/system/eut-queue.service > /dev/null <<SERVICE
[Unit]
Description=EUT Queue Worker
After=network.target

[Service]
User=nginx
WorkingDirectory=/var/www/eut
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
Restart=always
RestartSec=5
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
SERVICE

# Reverb WebSocket server
sudo tee /etc/systemd/system/eut-reverb.service > /dev/null <<SERVICE
[Unit]
Description=EUT Reverb WebSocket Server
After=network.target

[Service]
User=nginx
WorkingDirectory=/var/www/eut
ExecStart=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=8080 --no-interaction
Restart=always
RestartSec=5
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
SERVICE

sudo systemctl daemon-reload
sudo systemctl enable eut-queue eut-reverb
sudo systemctl start eut-queue eut-reverb

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " ✓ Deployment complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo " Services running:"
echo "   Nginx:        systemctl status nginx"
echo "   Queue worker: systemctl status eut-queue"
echo "   Reverb WS:    systemctl status eut-reverb"
echo ""
echo " Logs:"
echo "   App:   tail -f /var/www/eut/storage/logs/laravel.log"
echo "   Nginx: tail -f /var/log/nginx/error.log"
echo "   Queue: journalctl -u eut-queue -f"
echo "   Reverb:journalctl -u eut-reverb -f"
echo ""
echo " Open ports needed in EC2 Security Group:"
echo "   80  (HTTP)"
echo "   443 (HTTPS — after SSL setup)"
echo "   8080 (Reverb WebSocket)"
echo ""
echo " Next steps:"
echo "   1. Edit .env with your RDS/S3/domain details"
echo "   2. Add SSL: sudo dnf install certbot python3-certbot-nginx -y"
echo "      sudo certbot --nginx -d yourdomain.com"
echo "   3. Update VITE_REVERB_HOST to your domain & rebuild: npm run build"
echo "   4. Update Google OAuth redirect URI in Google Console"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
