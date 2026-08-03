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
- `APP_URL` = your server URL e.g. `https://kalukuhan.duckdns.org`
- `PRINT_TOKEN` = `eut-print-secret-2024`
- `PRINTER_INTERFACE` = your printer port or name

### 4. Test Connection (Optional but Recommended)
```bash
npm test
```
This will verify your server connection and authentication before starting the print service.

### 5. Find your printer name/port (Windows)
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

For USB printer by name (Windows):
```
PRINTER_TYPE=usb
PRINTER_INTERFACE=POS-80
```

### 6. Run
**Option A: Using batch file (easiest for Windows)**
```bash
start-print-server.bat
```

**Option B: Manual start**
```bash
npm start
```

### 7. Test the connection
The server should show:
```
╔══════════════════════════════════════════╗
║   EUT Kitchen Auto-Print Server v1.0     ║
╚══════════════════════════════════════════╝
Server : https://kalukuhan.duckdns.org
Printer: \\.\COM3 (usb)
Poll   : every 3s
─────────────────────────────────────────
Starting...

[POLL] 0 job(s) pending
```

### 8. Run on startup (optional)
Install as a Windows service with PM2:
```bash
npm install -g pm2
pm2 start index.js --name eut-kitchen-printer
pm2 startup
pm2 save
```

## Troubleshooting

### "Printer not connected"
- Check COM port in Device Manager
- Try different PRINTER_INTERFACE values
- Ensure printer is powered on and connected

### "Cannot reach server"
- Check APP_URL is correct and accessible
- Verify internet connection
- Test URL in browser: `https://kalukuhan.duckdns.org/api/print-server/pending-prints`

### "Invalid PRINT_TOKEN"
- Check PRINT_TOKEN matches the Laravel .env file
- Default token: `eut-print-secret-2024`

## How it works
1. Admin accepts order in admin panel
2. Laravel creates a `kitchen_print_jobs` record
3. This script polls `/api/print-server/pending-prints` every 3s
4. Finds unprinted jobs → sends to thermal printer
5. Marks job as printed so it doesn't re-print

## Alternative: Browser Auto-Print
If you can't install Node.js, the kitchen dashboard in the browser also has auto-print functionality:
1. Go to kitchen dashboard: `https://kalukuhan.duckdns.org/chef/dashboard`
2. Allow popups when prompted by browser
3. Click anywhere on the page to enable auto-print
4. Kitchen tickets will print automatically when orders are accepted
