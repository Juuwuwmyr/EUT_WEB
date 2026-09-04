@echo off
title EUT Complete Automation Suite
color 0E
echo ============================================
echo  EUT Snack House - Complete Automation
echo ============================================
echo.
echo This will start ALL automation scripts:
echo  1. Print Dialog Auto-Clicker
echo  2. Permission Handler  
echo  3. Universal Dialog Killer
echo.
echo All popups and dialogs will be handled automatically.
echo Close this window to stop all automation.
echo.
pause

echo Starting automation suite...
echo.

REM Start all scripts in the background
start "Print Clicker" /min cscript //nologo auto-print-clicker.vbs
timeout /t 2 /nobreak > nul

start "Permission Handler" /min cscript //nologo permission-handler.vbs  
timeout /t 2 /nobreak > nul

start "Dialog Killer" /min cscript //nologo dialog-killer.vbs
timeout /t 2 /nobreak > nul

echo ============================================
echo  ALL AUTOMATION SCRIPTS ARE NOW RUNNING!
echo ============================================
echo.
echo The following scripts are active:
echo  - Print Dialog Auto-Clicker (minimized)
echo  - Chrome Permission Handler (minimized)  
echo  - Universal Dialog Killer (minimized)
echo.
echo All browser popups will be handled automatically.
echo Press any key to stop ALL automation scripts...
pause > nul

echo.
echo Stopping all automation scripts...

REM Kill all VBS processes related to our automation
taskkill /f /im wscript.exe > nul 2>&1
taskkill /f /im cscript.exe > nul 2>&1

echo All automation stopped.
pause