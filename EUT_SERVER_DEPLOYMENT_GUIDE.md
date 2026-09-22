# EUT Snack House — New Server Deployment Guide

**Version:** 1.0  
**Date:** September 9, 2026  
**Author:** EUT Dev Team  

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Step 1 — Fix Apache Document Root](#step-1--fix-apache-document-root)
3. [Step 2 — Install Composer and PHP Dependencies](#step-2--install-composer-and-php-dependencies)
4. [Step 3 — Install Node.js and Build Assets](#step-3--install-nodejs-and-build-assets)
5. [Step 4 — Create the .env File](#step-4--create-the-env-file)
6. [Step 5 — Set Google OAuth Credentials](#step-5--set-google-oauth-credentials)
7. [Step 6 — Generate App Key and Run Migrations](#step-6--generate-app-key-and-run-migrations)
8. [Step 7 — Fix Permissions and Clear Caches](#step-7--fix-permissions-and-clear-caches)
9. [Step 8 — Google Cloud Console Setup](#step-8--google-cloud-console-setup)
10. [Step 9 — Verify Everything Works](#step-9--verify-everything-works)
11. [Common Issues and Fixes](#common-issues-and-fixes)

---

## Prerequisites

Before starting, make sure you have:

- AWS EC2 instance running Ubuntu (t3.micro or higher)
- Domain pointed to the server via DuckDNS (e.g. `beta-eut.duckdns.org`)
- SSL certificate already set up via Let's Encrypt
- MySQL database already imported from the previous server
- SSH access to the instance via AWS EC2 Instance Connect or a `.pem` key

> **Note:** Every time the EC2 instance stops and restarts without an Elastic IP, the public IP changes. Always update DuckDNS at https://www.duckdns.org with the new IP before proceeding.

---

## Step 1 — Fix Apache Document Root

The Laravel `public` folder must be the document root. If this is wrong, you will see "Index of /" instead of the website.

```bash
sudo tee /etc/apache2/sites-available/000-default.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName your-domain.duckdns.org
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF
```

```bash
sudo tee /etc/apache2/sites-available/000-default-le-ssl.conf > /dev/null << 'EOF'
<IfModule mod_ssl.c>
<VirtualHost *:443>
    ServerName your-domain.duckdns.org
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/your-domain.duckdns.org/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/your-domain.duckdns.org/privkey.pem
</VirtualHost>
</IfModule>
EOF
```

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

> **Verify:** Go to `https://your-domain.duckdns.org` — it should load the restaurant page, not a file listing.

---

## Step 2 — Install Composer and PHP Dependencies

```bash
# Take ownership so composer can write files
sudo chown -R ubuntu:ubuntu /var/www/html

# Download and install Composer globally
cd /tmp
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
rm composer-setup.php

# Install PHP dependencies
cd /var/www/html && composer install --no-dev --optimize-autoloader
```

> **Verify:** Should end with "Generating optimized autoload files" and list discovered packages.

---

## Step 3 — Install Node.js and Build Assets

```bash
# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install npm packages and build Vite assets
cd /var/www/html && npm install && npm run build
```

> **Verify:** Should end with "built in X.XXs" with a list of compiled files.

---

## Step 4 — Create the .env File

> **Important:** Use `python3` to write the `.env` file. Never use `sed` or `echo` for values containing special characters like dashes or equals signs — they get corrupted.

```bash
sudo python3 << 'EOF'
env_content = """APP_NAME="E.U.T Snack House"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.duckdns.org

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
VITE_REVERB_HOST=your-domain.duckdns.org
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=https

MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=ventiletos@gmail.com
MAIL_PASSWORD=hsflemqlimibuzyn
MAIL_FROM_ADDRESS="ventiletos@gmail.com"
MAIL_FROM_NAME="E.U.T Snack House"

GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID_HERE
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET_HERE
GOOGLE_REDIRECT_URI=https://your-domain.duckdns.org/auth/google/callback

VITE_APP_NAME="E.U.T Snack House"
"""
with open('/var/www/html/.env', 'w') as f:
    f.write(env_content)
print('Done')
EOF
```

---

## Step 5 — Set Google OAuth Credentials

> **Important:** Always use `python3` to update credentials. Never use `sed` — it corrupts values with dashes.

After creating a new OAuth client in Google Cloud Console (see Step 8), run:

```bash
sudo python3 << 'EOF'
with open('/var/www/html/.env', 'r') as f:
    lines = f.readlines()

with open('/var/www/html/.env', 'w') as f:
    for line in lines:
        if line.startswith('GOOGLE_CLIENT_ID='):
            f.write('GOOGLE_CLIENT_ID=PASTE_YOUR_CLIENT_ID_HERE\n')
        elif line.startswith('GOOGLE_CLIENT_SECRET='):
            f.write('GOOGLE_CLIENT_SECRET=PASTE_YOUR_CLIENT_SECRET_HERE\n')
        elif line.startswith('APP_URL='):
            f.write('APP_URL=https://your-domain.duckdns.org\n')
        elif line.startswith('VITE_REVERB_HOST='):
            f.write('VITE_REVERB_HOST=your-domain.duckdns.org\n')
        elif line.startswith('GOOGLE_REDIRECT_URI='):
            f.write('GOOGLE_REDIRECT_URI=https://your-domain.duckdns.org/auth/google/callback\n')
        else:
            f.write(line)
print('Done')
EOF
```

Verify the values were saved correctly:

```bash
grep GOOGLE_ /var/www/html/.env
```

Then clear the config cache:

```bash
cd /var/www/html && php artisan config:clear
```

---

## Step 6 — Generate App Key and Run Migrations

```bash
# Allow ubuntu user to write to .env
sudo chown ubuntu:ubuntu /var/www/html/.env
sudo chmod 664 /var/www/html/.env

# Generate a new APP_KEY
cd /var/www/html && php artisan key:generate --force

# Run all database migrations
php artisan migrate --force
```

> **Note:** If migrate says "Nothing to migrate", the DB connection was wrong when it ran. Fix the `.env` DB credentials first, run `php artisan config:clear`, then re-run migrate.

---

## Step 7 — Fix Permissions and Clear Caches

```bash
# Fix storage and cache permissions for Apache (www-data)
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/bootstrap/cache
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache
sudo chmod 777 /var/www/html/storage/logs

# Clear all Laravel caches
cd /var/www/html
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Restart Apache
sudo systemctl restart apache2
```

---

## Step 8 — Google Cloud Console Setup

1. Go to https://console.cloud.google.com
2. Select your project (top dropdown)
3. Left menu → **APIs & Services** → **Clients**
4. Click **Create Client**
5. Application type: **Web application**
6. Name: e.g. `EUT Beta Server`
7. Under **Authorized JavaScript origins**, click **Add URI**:
   ```
   https://your-domain.duckdns.org
   ```
8. Under **Authorized redirect URIs**, click **Add URI**:
   ```
   https://your-domain.duckdns.org/auth/google/callback
   ```
9. Click **Create**
10. **Copy both the Client ID and Client Secret immediately** — the secret cannot be viewed again after closing the dialog
11. Go back to Step 5 and update the `.env` with these values

> **Warning:** Never use `sed` to update the Client Secret. It contains dashes and special characters that `sed` will corrupt. Always use `python3` as shown in Step 5.

---

## Step 9 — Verify Everything Works

```bash
# Check DB connection returns correct database name
cd /var/www/html && php artisan tinker --execute="echo DB::connection()->getDatabaseName();"
# Expected: eut_restaurant

# Check Google redirect URI config
php artisan tinker --execute="echo config('services.google.redirect');"
# Expected: https://your-domain.duckdns.org/auth/google/callback

# Check sessions table exists
php artisan tinker --execute="echo DB::select('SHOW TABLES LIKE \"sessions\"') ? 'EXISTS' : 'MISSING';"
# Expected: EXISTS

# Check Apache is running
sudo systemctl status apache2
# Expected: active (running)
```

---

## Common Issues and Fixes

| Problem | Cause | Fix |
|---|---|---|
| "Index of /" showing | Document root not pointing to `/public` | Redo Step 1 |
| Google loops to restaurant page | OAuth error — check debug console | Check `storage/logs/google_auth_debug.txt` |
| `invalid_client` error | Client secret corrupted by `sed` | Use `python3` to rewrite the value (Step 5) |
| "Nothing to migrate" | DB connection was wrong during migrate | Fix `.env` DB settings, run `config:clear`, retry migrate |
| Permission denied on logs | Wrong file ownership | `sudo chmod 777 /var/www/html/storage/logs` |
| Config not updating after `.env` change | Laravel config cache is stale | `php artisan config:clear` |
| Site unreachable after EC2 restart | IP changed, DuckDNS not updated | Update IP at https://www.duckdns.org |
| Composer: command not found | Composer not installed globally | Redo Step 2 |
| SSH connection failed | Instance stopped or initializing | Check EC2 console, start instance if stopped |

---

## Quick Reference — Useful Commands

```bash
# Check what's in .env (never share this output publicly)
cat /var/www/html/.env

# Check Apache error logs
sudo tail -n 50 /var/log/apache2/error.log

# Check Laravel Google auth debug log
cat /var/www/html/storage/logs/google_auth_debug.txt

# Check Laravel application log
tail -n 50 /var/www/html/storage/logs/laravel.log

# Restart everything
sudo systemctl restart apache2

# Full cache clear
cd /var/www/html && php artisan config:clear && php artisan route:clear && php artisan cache:clear && php artisan view:clear

# Check disk usage
df -h /var/www/html

# Check memory usage
free -h
```

---

*Document generated from the actual deployment session on September 9, 2026.*
