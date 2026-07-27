#!/bin/bash
# =================================================================
#  EUT Snack House - Update Script (run after initial deploy)
#  Usage: cd /var/www/html && ./deploy-update.sh
# =================================================================

set -e
APP_DIR="/var/www/html"
cd $APP_DIR

echo "================================================"
echo " Pulling latest code..."
echo "================================================"
git pull origin main

echo "[1/5] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "[2/5] Building frontend assets..."
npm ci --production=false
npm run build
rm -rf node_modules

echo "[3/5] Running migrations..."
php artisan migrate --force

echo "[4/5] Clearing and warming cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "[5/5] Restarting services..."
sudo systemctl restart eut-queue
sudo systemctl restart eut-reverb
sudo systemctl reload nginx

echo ""
echo " Update complete!"
echo " tail -f /var/www/html/storage/logs/laravel.log"
