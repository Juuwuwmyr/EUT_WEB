#!/bin/bash
# =================================================================
#  EUT Snack House - Update Script
#  Usage: cd /var/www/html && sudo ./deploy-update.sh
# =================================================================

set -e
APP_DIR="/var/www/html"
cd $APP_DIR

echo "================================================"
echo " EUT Snack House - Deploying latest changes"
echo "================================================"

echo "[1/6] Pulling latest code..."
sudo chown -R ubuntu:ubuntu $APP_DIR
git fetch origin
git reset --hard origin/main

echo "[2/6] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "[3/6] Building frontend assets..."
npm ci --production=false
npm run build
rm -rf node_modules

echo "[4/6] Running migrations..."
sudo chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache
sudo -u www-data php artisan migrate --force

echo "[5/6] Clearing and rebuilding cache..."
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache

echo "[6/6] Restarting web server..."
sudo systemctl restart apache2 2>/dev/null || sudo systemctl restart nginx 2>/dev/null || true

echo ""
echo "================================================"
echo " Done! Site is live."
echo "================================================"
