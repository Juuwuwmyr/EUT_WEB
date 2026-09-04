@echo off
title EUT Auto Print Clicker
color 0A
echo ============================================
echo  EUT Snack House - Auto Print Clicker
echo ============================================
echo.
echo Starting auto print dialog handler...
echo This will automatically click print dialogs.
echo.
echo Keep this window open while using the system.
echo Close this window to stop auto-clicking.
echo.
pause

echo Starting monitoring...
cscript //nologo auto-print-clicker.vbs

echo.
echo Auto-clicker stopped.
pause