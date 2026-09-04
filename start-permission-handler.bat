@echo off
title EUT Permission Handler
color 0B
echo ============================================
echo  EUT Snack House - Permission Handler
echo ============================================
echo.
echo Starting Chrome permission dialog handler...
echo This will automatically click "Allow" on browser popups.
echo.
echo Keep this window open while using the system.
echo Close this window to stop permission handling.
echo.
pause

echo Starting permission monitoring...
cscript //nologo permission-handler.vbs

echo.
echo Permission handler stopped.
pause