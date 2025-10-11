#!/bin/bash

echo "🐳 Docker ile Okul Ders Programı Sistemi Kurulumu Başlıyor..."
echo "======================================================"

# .env dosyasını kopyala
echo "📁 Environment dosyası hazırlanıyor..."
cp .env.docker .env

echo "🏗️  Docker container'ları build ediliyor..."
docker-compose build --no-cache

echo "🚀 Container'lar başlatılıyor..."
docker-compose up -d

echo "⏳ MySQL'in başlaması bekleniyor..."
sleep 30

echo "📦 Composer paketleri yükleniyor..."
docker-compose exec app composer install --no-dev --optimize-autoloader

echo "🔑 Laravel application key generate ediliyor..."
docker-compose exec app php artisan key:generate

echo "🗃️  Veritabanı migration'ları çalıştırılıyor..."
docker-compose exec app php artisan migrate --force

echo "🌱 Seed veriler ekleniyor..."
docker-compose exec app php artisan db:seed --force

echo "🧹 Cache temizleniyor..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "✅ Kurulum tamamlandı!"
echo ""
echo "🌐 Uygulama: http://localhost"
echo "🗄️  phpMyAdmin: http://localhost:8080"
echo ""
echo "📊 Test için giriş bilgileri:"
echo "Admin: admin@schoolschedule.com / admin123"
echo "Müdür: mudur@ataturklisesi.edu.tr / mudur123"