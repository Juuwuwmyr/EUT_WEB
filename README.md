# E.U.T Snack House — Restaurant Ordering & Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38BDF8?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Laravel Reverb](https://img.shields.io/badge/Reverb-WebSockets-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

**E.U.T Snack House** — *Eat • Unwind • Tea*

A full-featured restaurant ordering platform with dine-in QR, delivery, pickup,
real-time kitchen display, thermal auto-printing, and rider dispatch.
Built for a real snack house in the Philippines.

</div>

---

## Overview

EUT Web is a self-hosted, single-restaurant ordering system. Customers browse the menu, add items to cart, and place orders for **delivery**, **pickup**, or **dine-in** (via table QR code). Staff manage everything through role-specific dashboards: admin, kitchen (chef), and rider.

Real-time updates across all panels are handled by **Laravel Reverb** (self-hosted WebSockets) — the kitchen sees new orders the moment they're placed, no page refresh needed.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Blade templates, Vanilla JS ES6+, Tailwind CSS 4 |
| Build | Vite 8, `@tailwindcss/vite` |
| WebSockets | Laravel Reverb (self-hosted, no Pusher needed) |
| Auth | Laravel session auth + Google OAuth (Socialite) |
| Database | MySQL (production) / SQLite (local dev) |
| File Storage | AWS S3 (production) / local disk (dev) |
| Print Server | Node.js agent on kitchen PC → USB/network thermal printer |

---

## Features

### 🛒 Customer-facing Shop

- **Menu browsing** — categories, item detail with modifier & add-on groups
- **Persistent cart** — `localStorage`-backed; synced server-side for logged-in users
- **Three order types** — Delivery (with map pin), Pickup, Dine-in
- **Table QR scan** — scanning `/shop?table=N` auto-selects dine-in and locks the table number for the session
- **Guest dine-in** — guests can place dine-in orders without creating an account
- **Order tracking** — live status updates via WebSocket (pending → accepted → preparing → ready → delivered)
- **Google OAuth** — one-tap sign-in
- **Saved addresses** — address book with default address for delivery
- **Progressive Web App** — installable on Android via TWA (Trusted Web Activity, see `twa/`)
- **Upsell carousel** — random featured items shown on the cart page

### 🖥️ Admin Panel (`/admin`)

- **Dashboard** — order summary and live feeds
- **Orders** — real-time order board; accept, assign rider, update status, complete table, archive, delete
- **Menu Items** — create/edit/archive items, upload photos, set category & sort order, toggle availability
- **Categories** — add/edit/archive with sort order control
- **Modifier Groups & Options** — attach flavors, sizes, add-ons with min/max selection rules
- **Users** — view all users, change roles (`admin` / `chef` / `rider` / customer), archive, delete
- **Riders** — manage rider profiles
- **Settings** — restaurant name, contact info, delivery fee, minimum order; per-service open/close toggles (Delivery / Pickup / Dine-In) that broadcast in real time via WebSocket
- **Table QR Codes** — generate, download PNG, print A4 coupon sheet or POS roll for tables 1–30

### 👨‍🍳 Kitchen Display (`/chef`)

- Live order queue: **Queued → Cooking → Ready**
- Accept, start cooking, mark ready, assign rider per order
- Cancel individual items from an order
- Auto-print kitchen tickets (browser popup or headless print server)
- Print formats: kitchen ticket, customer receipt, table receipt, takeout slip

### 🏍️ Rider Dashboard (`/rider`)

- View assigned delivery orders
- Update availability status & GPS location
- Mark order as picked up / delivered
- Earnings summary

### 🖨️ Auto-Print Server (`/print-server`)

A standalone **Node.js** script that runs on the kitchen PC. It polls the server every 3 seconds for unprinted kitchen jobs and sends them directly to a thermal printer (USB COM port or network IP). No browser interaction required. See [`print-server/README.md`](print-server/README.md) for full setup.

---

## User Roles

| Role | Access |
|---|---|
| `admin` | Full admin panel |
| `chef` | Kitchen display dashboard |
| `rider` | Rider dashboard |
| *(no role / customer)* | Shop, cart, checkout, tracking, profile |

Assign a role via **Admin → Users**, or directly with Tinker:

```bash
php artisan tinker
```
```php
User::where('email', 'you@example.com')->update(['role' => 'admin']);
```

---

## Project Structure

```
EUT_WEB/
├── app/
│   ├── Events/               # WebSocket events (ShopStatusUpdated, OrderStatusUpdated, …)
│   ├── Http/
│   │   ├── Controllers/      # AdminController, ShopController, OrderController, ChefController, …
│   │   └── Middleware/       # admin, chef, rider, auth.printserver guards
│   └── Models/               # User, MenuItem, Category, Order, OrderItem, Rider, …
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/            # Admin panel Blade views
│       ├── chef/             # Kitchen dashboard
│       ├── rider/            # Rider dashboard
│       ├── shop/             # Customer pages (index, cart, checkout, tracking, profile)
│       └── partials/         # Shared components (PWA register, print partials)
├── routes/
│   └── web.php               # All routes (shop, admin, chef, rider, auth, print-server API)
├── print-server/             # Node.js kitchen auto-print agent
├── twa/                      # Android TWA (Trusted Web Activity) wrapper
├── deploy.sh                 # Production deployment script
├── vite.config.js
└── .env.production.example   # Reference for production environment variables
```

---

## Local Development Setup

### Prerequisites

- PHP 8.3+ (WAMP / Laragon / Herd on Windows)
- Composer 2
- Node.js 18+ & npm
- MySQL, or use SQLite for zero-config dev

### Steps

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 4. Set up the database
#    SQLite (zero config):
touch database/database.sqlite
#    Or configure DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env for MySQL

php artisan migrate

# 5. Start the dev server + Vite hot reload
composer run dev
```

> `composer run dev` uses `concurrently` to run `php artisan serve` and `npm run dev` in parallel.

For **real-time WebSocket** features (live order updates, shop open/close broadcast), also run:

```bash
php artisan reverb:start
```

Then visit **http://localhost:8000**.

---

## Environment Variables Reference

Key variables beyond standard Laravel defaults:

```env
# App
APP_NAME="E.U.T Snack House"
APP_TIMEZONE=Asia/Manila

# Broadcasting — Reverb WebSockets
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=eut-local
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Print Server shared secret (must match print-server/.env)
PRINT_SERVER_TOKEN=eut-print-secret-2024
```

See [`.env.production.example`](.env.production.example) for a complete production reference covering AWS RDS, S3, SES, and Reverb over HTTPS.

---

## Production Deployment

The app runs on a single server (e.g. AWS EC2) with MySQL on RDS, file uploads to S3, Reverb as a background WebSocket process, and a database queue worker.

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

See [`deploy.sh`](deploy.sh) for the full automated deploy script.

---

## Table QR Code Flow

1. Admin generates QR codes from `/admin/table-qrcodes`
2. Each QR points to `https://yourdomain.com/shop?table=N`
3. Customer scans → shop auto-selects **Dine In**, pre-fills table number, locks order type
4. Session stored in `sessionStorage` + `localStorage` under `eutTableNumber`
5. When admin closes Dine-In (or the whole shop), the QR session clears automatically in real time via the Reverb WebSocket broadcast — customers are not left stuck in dine-in mode

---

## Kitchen Print Server

See [`print-server/README.md`](print-server/README.md) for the full setup guide.

**TL;DR:**

```bash
cd print-server
copy .env.example .env    # set APP_URL and PRINTER_INTERFACE
npm install
npm start
```

Supports USB (COM port), network IP, and Windows printer names. Can be installed as a Windows service using PM2.

---

## License

MIT © E.U.T Snack House

Built with ❤️ in the Philippines 🇵🇭