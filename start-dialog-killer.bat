@echo off
title EUT Dialog Killer
color 0C
echo ============================================
echo  EUT Snack House - Universal Dialog Killer
echo ============================================
echo.
echo WARNING: This will close ALL popup dialogs!
echo Use this as a last resort when other scripts fail.
echo.
echo Starting aggressive dialog killer...
echo This will close ANY popup that appears in Chrome.
echo.
echo Keep this window open while using the system.
echo Close this window to stop dialog killing.
echo.
pause

echo Starting dialog monitoring...
cscript //nologo dialog-killer.vbs

echo.
echo Dialog killer stopped.
pause