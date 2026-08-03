@echo off
title EUT Kitchen Print Server
echo.
echo ==========================================
echo   EUT Kitchen Auto-Print Server
echo ==========================================
echo.

REM Check if Node.js is installed
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js is not installed or not in PATH.
    echo Please download and install Node.js from https://nodejs.org
    echo.
    pause
    exit /b 1
)

REM Check if .env file exists
if not exist ".env" (
    echo WARNING: .env file not found!
    echo Please copy .env.example to .env and configure it first.
    echo.
    pause
    exit /b 1
)

REM Check if node_modules exists
if not exist "node_modules" (
    echo Installing dependencies...
    call npm install
    if errorlevel 1 (
        echo ERROR: Failed to install dependencies.
        pause
        exit /b 1
    )
)

echo Starting print server...
echo Press Ctrl+C to stop
echo.
node index.js
pause