#!/bin/bash
# =================================================================
#  EUT Snack House - AWS EC2 Deployment Script
#  Amazon Linux 2023 | Repo cloned into /var/www/html (no subfolder)
#  Usage: chmod +x deploy.sh && ./deploy.sh
# =================================================================

set -e
APP_DIR="/var/www/html"

echo "================================================"
echo " EUT Snack House - EC2 Setup"
echo " App directory: $APP_DIR"
echo "================================================"

# 1. System packages
echo "[1/10] Installing system packages..."
sudo dnf update -y
sudo dnf install -y git curl unzip nginx

# 2. PHP 8.3
echo "[2/10] Installing PHP 8.3..."
sudo dnf install -y php8.3 php8.3-fpm php8.3-mysqlnd php8.3-mbstring \
    php8.3-xml php8.3-zip php8.3-gd php8.3-curl php8.3-intl \
    php8.3-bcmath php8.3-opcache

# 3. Composer
echo "[3/10] Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# 4. Node.js 20
echo "[4/10] Installing Node.js 20..."
curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
sudo dnf install -y nodejs

# 5. Code already cloned into /var/www/html
echo "[5/10] Using code at $APP_DIR..."
cd $APP_DIR

# 6. PHP dependencies (production only - no dev packages)
echo "[6/10] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 7. Build frontend assets
echo "[7/10] Building frontend assets..."
npm ci --production=false
npm run build
rm -rf node_modules

# 8. Laravel setup
echo "[8/10] Running Laravel setup..."

if [ ! -f .env ]; then
    cp .env.production.example .env
    echo ""
    echo "  WARNING: Edit .env with your credentials!"
    echo "  Run: nano /var/www/html/.env"
    echo ""
    read -p "  Press ENTER after editing .env..."
fi

php artisan key:generate --force

# Create tables for database session/queue/cache drivers
php artisan queue:table 2>/dev/null || true
php artisan session:table 2>/dev/null || true
php artisan cache:table 2>/dev/null || true

php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 9. File permissions
echo "[9/10] Setting permissions..."
sudo chown -R nginx:nginx $APP_DIR
sudo chmod -R 755 $APP_DIR
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# 10. Nginx
echo "[10/10] Configuring Nginx..."

sudo tee /etc/nginx/conf.d/eut.conf > /dev/null <<'NGINXCONF'
server {
    listen 80;
    server_name _;

    root /var/www/html/public;
    index index.php;

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

    # Reverb WebSocket proxy
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
NGINXCONF

sudo rm -f /etc/nginx/conf.d/default.conf
sudo nginx -t && sudo systemctl restart nginx
sudo systemctl enable nginx php-fpm

# Systemd: Queue worker
sudo tee /etc/systemd/system/eut-queue.service > /dev/null <<'QUEUESERVICE'
[Unit]
Description=EUT Queue Worker
After=network.target

[Service]
User=nginx
WorkingDirectory=/var/www/html
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
Restart=always
RestartSec=5
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
QUEUESERVICE

# Systemd: Reverb WebSocket
sudo tee /etc/systemd/system/eut-reverb.service > /dev/null <<'REVERBSERVICE'
[Unit]
Description=EUT Reverb WebSocket Server
After=network.target

[Service]
User=nginx
WorkingDirectory=/var/www/html
ExecStart=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=8080 --no-interaction
Restart=always
RestartSec=5
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
REVERBSERVICE

sudo systemctl daemon-reload
sudo systemctl enable eut-queue eut-reverb
sudo systemctl start eut-queue eut-reverb

echo ""
echo "================================================"
echo " Deployment complete!"
echo "================================================"
echo ""
echo " App:     /var/www/html"
echo " Public:  /var/www/html/public"
echo ""
echo " Check services:"
echo "   systemctl status nginx"
echo "   systemctl status eut-queue"
echo "   systemctl status eut-reverb"
echo ""
echo " Logs:"
echo "   tail -f /var/www/html/storage/logs/laravel.log"
echo "   tail -f /var/log/nginx/error.log"
echo "   journalctl -u eut-queue -f"
echo "   journalctl -u eut-reverb -f"
echo ""
echo " Verify health check:"
echo "   curl http://localhost/health"
echo ""
echo " EC2 Security Group - open ports:"
echo "   80   HTTP"
echo "   443  HTTPS"
echo "   8080 Reverb WebSocket"
echo "   22   SSH (your IP only)"
echo ""
echo " Next steps:"
echo "   1. sudo certbot --nginx -d yourdomain.com"
echo "   2. Update VITE_REVERB_HOST=yourdomain.com in .env"
echo "      then: npm ci && npm run build && php artisan config:cache"
echo "   3. Update Google OAuth redirect URI in Google Console"
echo "================================================"
