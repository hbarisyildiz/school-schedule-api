# Laravel Performance Optimization Script (Windows)

Write-Host "🚀 Laravel Performance Optimization Script" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "1️⃣ Clearing all caches..." -ForegroundColor Yellow
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

Write-Host ""
Write-Host "2️⃣ Caching for production speed..." -ForegroundColor Yellow
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

Write-Host ""
Write-Host "3️⃣ Optimizing composer autoloader..." -ForegroundColor Yellow
docker-compose exec app composer dump-autoload -o

Write-Host ""
Write-Host "✅ Optimization completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Expected improvements:" -ForegroundColor Cyan
Write-Host "  - Config: 10x faster"
Write-Host "  - Routes: 5x faster"
Write-Host "  - Views: 3x faster"
Write-Host "  - Autoload: 2x faster"
Write-Host ""
Write-Host "🎯 Overall: Site should be significantly faster now!" -ForegroundColor Green


