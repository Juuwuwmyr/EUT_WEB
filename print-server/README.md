# EUT Kitchen Auto-Print Server

Runs on the **kitchen computer**. Polls the EUT server every 3 seconds and sends kitchen tickets directly to the thermal printer — zero browser interaction needed.

## Setup (Kitchen Computer)

### 1. Install Node.js
Download from https://nodejs.org (LTS version)

### 2. Install dependencies
```bash
cd print-server
npm install
```

### 3. Configure
```bash
copy .env.example .env
```
Edit `.env`:
- `APP_URL` = your server URL e.g. `http://osas-delivery.duckdns.org`
- `PRINT_TOKEN` = `eut-print-secret-2024`
- `PRINTER_INTERFACE` = your printer port or name

### 4. Find your printer name/port (Windows)
Open **Device Manager** → Ports (COM & LPT) to find COM port  
Or go to **Printers & Scanners** and note the printer name (e.g. `POS-80`)

For USB thermal printer via COM port:
```
PRINTER_INTERFACE=\\.\COM3
```

For network printer (IP-based):
```
PRINTER_TYPE=network
PRINTER_INTERFACE=tcp://192.168.1.100
```

### 5. Run
```bash
npm start
```

### 6. Run on startup (optional)
Install as a Windows service with PM2:
```bash
npm install -g pm2
pm2 start index.js --name eut-kitchen-printer
pm2 startup
pm2 save
```

## How it works
1. Admin accepts order in admin panel
2. Laravel creates a `kitchen_print_jobs` record
3. This script polls `/api/print-server/pending-prints` every 3s
4. Finds unprinted jobs → sends to thermal printer
5. Marks job as printed so it doesn't re-print
