# EUT Snack House - Print Automation Setup

This package contains scripts to fix print dialog issues and enable kiosk mode for seamless restaurant operations.

## 🔧 Fixed Issues

### ✅ Print Dialog Auto-Close
- **Problem**: Print dialogs would stay open after printing receipts
- **Solution**: Enhanced JavaScript print handling with `afterprint` event listeners
- **Files Updated**: 
  - `resources/views/admin/partials/kitchen-receipt.blade.php`
  - `resources/views/admin/partials/table-receipt.blade.php` 
  - `resources/views/admin/orders.blade.php` (printReceipt & kitchenAutoPrint functions)

### ✅ Kiosk Mode & Auto-Click
- **Problem**: Need hands-free operation for restaurant environment
- **Solution**: VBS scripts for automatic dialog handling

## 📁 Files Included

### 🖨️ Print & Permission Automation Scripts
1. **`auto-print-clicker.vbs`** - Background script that automatically clicks print dialogs
2. **`start-auto-clicker.bat`** - Easy launcher for the auto-clicker
3. **`permission-handler.vbs`** - Handles browser permission popups (location, notifications)
4. **`start-permission-handler.bat`** - Easy launcher for permission handler
5. **`kiosk-mode.vbs`** - Full kiosk mode with browser launch

### 📋 Instructions

## 🚀 Quick Setup (Recommended)

### For Daily Operations:
1. **Double-click** `start-auto-clicker.bat` - handles print dialogs
2. **Double-click** `start-permission-handler.bat` - handles browser permissions
3. Keep both windows **open** while using the system
4. **Close the windows** to stop auto-clicking

### For Kiosk Mode (Full Setup):
1. **Double-click** `kiosk-mode.vbs`
2. Choose your options:
   - **YES** = Launch kiosk mode with default URL
   - **NO** = Change the URL first
   - **CANCEL** = Exit
3. Browser will open in full kiosk mode with permissions disabled
4. **Press Ctrl+Alt+F4** to exit kiosk mode

## ⚙️ Configuration

### Change Default URL (kiosk-mode.vbs):
```vbs
strURL = "https://eut-delivery.duckdns.org/admin/orders"  ' Change this line
```

### Common URLs:
- Admin Orders: `https://eut-delivery.duckdns.org/admin/orders`
- Chef Dashboard: `https://eut-delivery.duckdns.org/chef/dashboard` 
- Waiter Dashboard: `https://eut-delivery.duckdns.org/waiter/dashboard`
- Shop Home: `https://eut-delivery.duckdns.org/shop`

## 🔧 Advanced Usage

### Manual Script Execution:
```cmd
# Run auto-clicker in command line
cscript auto-print-clicker.vbs

# Run kiosk mode in command line  
cscript kiosk-mode.vbs
```

### Customize Auto-Click Timing:
Edit `auto-print-clicker.vbs`, find:
```vbs
WScript.Sleep 300  ' Change to 500 for slower response
```

## 🛡️ Security & Compatibility

### System Requirements:
- ✅ Windows 7/8/10/11
- ✅ Internet Explorer/Edge (for VBS compatibility)
- ✅ Google Chrome (recommended browser)
- ✅ Administrator privileges (may be required)

### Antivirus Notes:
- Some antivirus software may flag VBS scripts
- Add exception for the script folder if needed
- Scripts only interact with browser windows (no file/network operations)

## 🔍 Troubleshooting

### Print Dialog Still Appears:
1. Make sure `start-auto-clicker.bat` is running
2. Check if antivirus is blocking the script
3. Try running as Administrator
4. Verify Chrome is the default browser

### Browser Permission Popups:
1. **Run permission handler** - `start-permission-handler.bat`
2. **Check Chrome settings** - make sure site permissions are set to "Ask"
3. **Manual click** - if auto-handler fails, manually click "Allow this time"
4. **Use kiosk mode** - permissions are disabled automatically in kiosk mode

### Kiosk Mode Issues:
1. **Update Chrome path** in `kiosk-mode.vbs` if Chrome is installed elsewhere
2. **Check URL accessibility** - make sure the web server is running at `eut-delivery.duckdns.org`
3. **Firewall settings** - ensure internet access is allowed for the domain

## 📞 Support

### Quick Fixes:
- **Restart the auto-clicker** if print dialogs start appearing again
- **Refresh the browser** if kiosk mode becomes unresponsive
- **Check WAMP/XAMPP** is running if localhost URLs don't work (for development)
- **Check internet connection** for production domain access

### For Technical Support:
- Check Windows Event Viewer for VBS script errors
- Test individual functions by running scripts manually
- Verify web application is running on the configured port

## 🎯 Best Practices

### For Restaurant Operations:
1. **Start auto-clicker** at the beginning of each shift
2. **Use kiosk mode** for dedicated POS terminals
3. **Keep backup** of these scripts in case of updates
4. **Train staff** on basic troubleshooting (restart scripts)

### For System Updates:
1. **Backup scripts** before updating the web application
2. **Test print functionality** after any system changes  
3. **Update URLs** in scripts if web structure changes

---

**Created for EUT Snack House Restaurant Management System**
*Seamless printing and kiosk operations for better customer service*