#!/bin/bash

# 🚀 Production Server Update Script
# Bu script mevcut production sunucusunu günceller

echo "🚀 Production server güncelleniyor..."

# Renkli output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonksiyonlar
print_step() {
    echo -e "${BLUE}📋 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

# 1. Mevcut durumu kontrol et
print_step "Mevcut durum kontrol ediliyor..."
if [ -d "/var/www/school-schedule" ]; then
    cd /var/www/school-schedule
    print_success "Proje dizini bulundu: /var/www/school-schedule"
else
    print_error "Proje dizini bulunamadı: /var/www/school-schedule"
    exit 1
fi

# 2. Docker container'ları durdur
print_step "Docker container'ları durduruluyor..."
sudo docker-compose down

# 3. Git pull ile güncelle
print_step "Git ile güncelleme yapılıyor..."
git fetch origin
git reset --hard origin/master
print_success "Kod güncellendi"

# 4. Environment dosyasını kontrol et
print_step "Environment dosyası kontrol ediliyor..."
if [ ! -f ".env" ]; then
    print_warning ".env dosyası bulunamadı, oluşturuluyor..."
    cp env.production.example .env
    print_warning "Lütfen .env dosyasını düzenleyin!"
fi

# 5. Docker container'ları yeniden başlat
print_step "Docker container'ları yeniden başlatılıyor..."
sudo docker-compose -f docker-compose.prod.yml up -d

# 6. Container'ların başlamasını bekle
print_step "Container'ların başlaması bekleniyor..."
sleep 30

# 7. Container durumunu kontrol et
print_step "Container durumu kontrol ediliyor..."
sudo docker-compose -f docker-compose.prod.yml ps

# 8. Database migration
print_step "Database migration çalıştırılıyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# 9. Cache'leri temizle ve yeniden oluştur
print_step "Cache'ler temizleniyor ve yeniden oluşturuluyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan cache:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:clear

sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# 10. Composer autoload güncelle
print_step "Composer autoload güncelleniyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app composer dump-autoload

# 11. Storage permissions
print_step "Storage permissions düzenleniyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app chmod -R 775 storage
sudo docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data storage

# 12. Health check
print_step "Health check yapılıyor..."
sleep 10
if curl -f http://localhost/health > /dev/null 2>&1; then
    print_success "Health check başarılı"
else
    print_warning "Health check başarısız, log'ları kontrol edin"
fi

# 13. Sonuç
print_success "Production server güncellendi!"
echo ""
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
echo "🔧 Troubleshooting:"
echo "   sudo docker-compose -f docker-compose.prod.yml restart"
echo "   sudo docker-compose -f docker-compose.prod.yml logs app"
echo ""
print_success "Güncelleme tamamlandı! Siteyi test edin."
