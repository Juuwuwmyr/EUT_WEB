# EUT_WEB Staging Server — Full Setup Guide

**Server:** Ubuntu 24.04 LTS  
**Domain:** eut-test.duckdns.org  
**IP:** 3.107.83.87  
**Repo:** https://github.com/Juuwuwmyr/EUT_WEB.git  
**Branch:** staging  

---

## PHASE 1 — System Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Add PHP 8.3 repo (sury.org — works on Ubuntu 24.04)
sudo apt install -y lsb-release ca-certificates curl
sudo curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
sudo apt update

# Install PHP 8.3 + extensions
sudo apt install -y \
  php8.3 php8.3-fpm php8.3-cli \
  php8.3-mbstring php8.3-xml php8.3-bcmath \
  php8.3-curl php8.3-zip php8.3-intl \
  php8.3-mysql php8.3-sqlite3 php8.3-redis \
  unzip curl git

# Install Nginx
sudo apt install -y nginx
sudo systemctl enable --now nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 22
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor
sudo apt install -y supervisor
sudo systemctl enable --now supervisor

# Install MySQL client
sudo apt install -y mysql-client

# Install phpMyAdmin
sudo apt install -y phpmyadmin php-mbstring php-zip php-gd php-json php-curl
# During install: select NO to apache2, Tab to OK
# Select YES to dbconfig-common

# Swap file (prevents crash on t2/t3.micro)
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
echo 'vm.swappiness=10' | sudo tee -a /etc/sysctl.conf
sudo sysctl vm.swappiness=10
```

---

## PHASE 2 — SSL Certificate

```bash
# Install certbot for Nginx
sudo apt install -y certbot python3-certbot-nginx

# Get SSL cert (enter email, agree to terms, choose redirect = 2)
sudo certbot --nginx -d eut-test.duckdns.org
```

---

## PHASE 3 — Clone the Project

```bash
# Clear web root and clone staging branch
sudo rm -rf /var/www/html/* /var/www/html/.[!.]*
sudo git clone -b staging https://github.com/Juuwuwmyr/EUT_WEB.git /var/www/html

# Fix git safe directory
sudo git config --global --add safe.directory /var/www/html
```

---

## PHASE 4 — Nginx Config

```bash
sudo tee /etc/nginx/sites-available/default > /dev/null << 'EOF'
server {
    listen 80;
    server_name eut-test.duckdns.org;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name eut-test.duckdns.org;

    ssl_certificate /etc/letsencrypt/live/eut-test.duckdns.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/eut-test.duckdns.org/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    root /var/www/html/public;
    index index.php;

    location /phpmyadmin {
        root /usr/share;
        index index.php;
        location ~ \.php$ {
            fastcgi_pass unix:/run/php/php8.3-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

sudo nginx -t && sudo systemctl reload nginx
```

---

## PHASE 5 — PHP Upload Limit (for phpMyAdmin imports)

```bash
sudo tee /etc/php/8.3/fpm/conf.d/99-upload.ini > /dev/null << 'EOF'
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 256M
EOF
sudo systemctl restart php8.3-fpm
```

---

## PHASE 6 — Laravel Setup

```bash
cd /var/www/html

# Install PHP dependencies
sudo composer install --no-dev --optimize-autoloader

# Create .env
sudo tee /var/www/html/.env > /dev/null << 'EOF'
APP_NAME="E.U.T Snack House"
APP_ENV=production
APP_KEY=
APP_DEBUG=true
APP_URL=https://eut-test.duckdns.org
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eut_restaurant
DB_USERNAME=root
DB_PASSWORD=eut
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=eut-production
REVERB_APP_KEY=eutsnackhouse2026
REVERB_APP_SECRET=eutsnacksecret2026
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https
VITE_REVERB_APP_KEY=eutsnackhouse2026
VITE_REVERB_HOST=eut-test.duckdns.org
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=https
MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@eut.com"
MAIL_FROM_NAME="E.U.T Snack House"
GOOGLE_CLIENT_ID=300566746053-97b183aok41zimqqdof81r2fipp49mbf.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=https://eut-test.duckdns.org/auth/google/callback
VITE_APP_NAME="E.U.T Snack House"
EOF

# Generate app key
sudo php artisan key:generate

# Run migrations
sudo php artisan migrate --force

# Build frontend assets
sudo npm install
sudo npm run build

# Fix permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache config
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

---

## PHASE 7 — Supervisor (Queue Worker + Reverb WebSocket)

```bash
sudo tee /etc/supervisor/conf.d/eut.conf > /dev/null << 'EOF'
[program:eut-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log

[program:eut-reverb]
process_name=%(program_name)s
command=php /var/www/html/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/reverb.log
EOF

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

---

## DAILY DEPLOY — After Every Git Push

```bash
cd /var/www/html
sudo git fetch origin
sudo git reset --hard origin/staging
sudo npm run build
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo supervisorctl restart all
```

---

## TROUBLESHOOTING

```bash
# Check Nginx errors
sudo tail -50 /var/log/nginx/error.log

# Check Laravel errors
sudo tail -50 /var/www/html/storage/logs/laravel.log

# Restart everything
sudo systemctl restart nginx php8.3-fpm
sudo supervisorctl restart all

# Check disk space
df -h

# Check memory
free -h

# Check Supervisor status
sudo supervisorctl status

# Force sync server to staging branch
cd /var/www/html
sudo git fetch origin
sudo git reset --hard origin/staging
```

---

## AWS SECURITY GROUP — Required Open Ports

| Port | Protocol | Purpose         |
|------|----------|-----------------|
| 22   | TCP      | SSH             |
| 80   | TCP      | HTTP            |
| 443  | TCP      | HTTPS           |
| 8080 | TCP      | Reverb WebSocket|

---

## QUICK REFERENCE

| Item            | Value                                      |
|-----------------|--------------------------------------------|
| Domain          | eut-test.duckdns.org                       |
| Server IP       | 3.107.83.87                                |
| Web root        | /var/www/html/public                       |
| Laravel root    | /var/www/html                              |
| phpMyAdmin URL  | https://eut-test.duckdns.org/phpmyadmin    |
| DB name         | eut_restaurant                             |
| DB user         | root                                       |
| PHP version     | 8.3                                        |
| Node version    | 22                                         |
| Branch          | staging                                    |
