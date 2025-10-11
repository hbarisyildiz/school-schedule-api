# Docker ile Okul Ders Programı Sistemi Kurulumu
Write-Host "🐳 Docker ile Okul Ders Programı Sistemi Kurulumu Başlıyor..." -ForegroundColor Green
Write-Host "======================================================" -ForegroundColor Green

# .env dosyasını kopyala  
Write-Host "📁 Environment dosyası hazırlanıyor..." -ForegroundColor Yellow
Copy-Item ".env.docker" ".env" -Force

Write-Host "🏗️  Docker container'ları build ediliyor..." -ForegroundColor Yellow
docker-compose build --no-cache

Write-Host "🚀 Container'lar başlatılıyor..." -ForegroundColor Yellow
docker-compose up -d

Write-Host "⏳ MySQL'in başlaması bekleniyor..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

Write-Host "📦 Composer paketleri yükleniyor..." -ForegroundColor Yellow
docker-compose exec app composer install --no-dev --optimize-autoloader

Write-Host "🔑 Laravel application key generate ediliyor..." -ForegroundColor Yellow
docker-compose exec app php artisan key:generate

Write-Host "🗃️  Veritabanı migration'ları çalıştırılıyor..." -ForegroundColor Yellow
docker-compose exec app php artisan migrate --force

Write-Host "🌱 Seed veriler ekleniyor..." -ForegroundColor Yellow
docker-compose exec app php artisan db:seed --force

Write-Host "🧹 Cache temizleniyor..." -ForegroundColor Yellow
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

Write-Host "✅ Kurulum tamamlandı!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Uygulama: http://localhost" -ForegroundColor Cyan
Write-Host "🗄️  phpMyAdmin: http://localhost:8080" -ForegroundColor Cyan
Write-Host ""
Write-Host "📊 Test için giriş bilgileri:" -ForegroundColor White
Write-Host "Admin: admin@schoolschedule.com / admin123" -ForegroundColor White  
Write-Host "Müdür: mudur@ataturklisesi.edu.tr / mudur123" -ForegroundColor White