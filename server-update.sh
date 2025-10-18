#!/bin/bash

# 🚀 Hızlı Server Update Script
# Bu script'i sunucuda çalıştırın

echo "🚀 Server güncelleniyor..."

# Proje dizinine git
cd /var/www/school-schedule

# Docker container'ları durdur
echo "📦 Docker container'ları durduruluyor..."
sudo docker-compose down

# Git ile güncelle
echo "🔄 Kod güncelleniyor..."
git fetch origin
git reset --hard origin/master

# Docker container'ları yeniden başlat
echo "🚀 Docker container'ları başlatılıyor..."
sudo docker-compose -f docker-compose.prod.yml up -d

# Container'ların başlamasını bekle
echo "⏳ Container'ların başlaması bekleniyor..."
sleep 30

# Database migration
echo "🗄️ Database migration çalıştırılıyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# Cache'leri temizle ve yeniden oluştur
echo "🧹 Cache'ler temizleniyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan cache:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:clear

echo "💾 Cache'ler yeniden oluşturuluyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Composer autoload güncelle
echo "📦 Composer autoload güncelleniyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app composer dump-autoload

# Storage permissions
echo "🔐 Storage permissions düzenleniyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app chmod -R 775 storage
sudo docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data storage

# Health check
echo "🏥 Health check yapılıyor..."
sleep 10
curl -f http://localhost/health && echo "✅ Health check başarılı" || echo "⚠️ Health check başarısız"

# Sonuç
echo "✅ Server güncellendi!"
echo "📊 Container durumu:"
sudo docker-compose -f docker-compose.prod.yml ps
echo ""
echo "🌐 Site URL'leri:"
echo "   Ana Site: http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4 2>/dev/null || hostname -I | awk '{print $1}')"
echo "   Admin Panel: http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4 2>/dev/null || hostname -I | awk '{print $1}')/admin-panel"
echo ""
echo "📋 Log'ları kontrol etmek için:"
echo "   sudo docker-compose -f docker-compose.prod.yml logs -f app"
echo ""
echo "🎉 Güncelleme tamamlandı! Siteyi test edin."
