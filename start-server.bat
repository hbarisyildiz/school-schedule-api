@echo off
title Laravel School Schedule API Server
echo.
echo ========================================
echo   Laravel School Schedule API Server
echo ========================================
echo.

cd /d "C:\MAMP\htdocs\dersProg\school-schedule-api\public"

echo [INFO] Server başlatılıyor...
echo [INFO] URL: http://localhost:8000
echo [INFO] Durdurmak için Ctrl+C basın
echo.

php -d display_errors=0 -d log_errors=0 -S localhost:8000

echo.
echo [INFO] Server durduruldu.
pause