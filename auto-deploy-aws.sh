#!/bin/bash

# 🚀 Otomatik AWS Deployment Script
# Bu script AWS EC2'de otomatik deployment yapar

echo "🚀 Otomatik AWS Deployment başlatılıyor..."

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

# 1. Sistem güncellemesi
print_step "Sistem güncelleniyor..."
sudo apt update -y
sudo apt upgrade -y

# 2. Docker kurulumu
print_step "Docker kuruluyor..."
sudo apt install -y docker.io docker-compose
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker $USER

# 3. Git kurulumu
print_step "Git kuruluyor..."
sudo apt install -y git

# 4. Proje dizini
print_step "Proje dizini oluşturuluyor..."
sudo mkdir -p /var/www/school-schedule
sudo chown $USER:$USER /var/www/school-schedule
cd /var/www/school-schedule

# 5. Proje dosyalarını klonla
print_step "Proje dosyaları klonlanıyor..."
git clone https://github.com/hbarisyildiz/school-schedule-api.git .

# 6. Environment dosyası oluştur
print_step "Environment dosyası oluşturuluyor..."
cat > .env << 'EOF'
APP_NAME="Okul Ders Programı"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=school_schedule
DB_USERNAME=school_user
DB_PASSWORD=secure_password_123

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=redis_password_123
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Okul Ders Programı"
EOF

# 7. Docker Compose ile başlat
print_step "Docker Compose ile uygulama başlatılıyor..."
sudo docker-compose -f docker-compose.prod.yml up -d

# 8. Container'ların başlamasını bekle
print_step "Container'ların başlaması bekleniyor..."
sleep 30

# 9. Container durumunu kontrol et
print_step "Container durumu kontrol ediliyor..."
sudo docker-compose -f docker-compose.prod.yml ps

# 10. Database migration
print_step "Database migration çalıştırılıyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --seed

# 11. Cache'leri oluştur
print_step "Cache'ler oluşturuluyor..."
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# 12. Health check
print_step "Health check yapılıyor..."
sleep 10
curl -f http://localhost/health || print_warning "Health check başarısız"

# 13. Sonuç
print_success "AWS Deployment tamamlandı!"
echo ""
echo "🌐 Site URL'leri:"
echo "   Ana Site: http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)"
echo "   Admin Panel: http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)/admin-panel"
echo ""
echo "📊 Container durumu:"
sudo docker-compose -f docker-compose.prod.yml ps
echo ""
echo "📋 Log'ları kontrol etmek için:"
echo "   sudo docker-compose -f docker-compose.prod.yml logs -f"
echo ""
echo "🔧 Troubleshooting:"
echo "   sudo docker-compose -f docker-compose.prod.yml restart"
echo "   sudo docker-compose -f docker-compose.prod.yml logs app"
echo ""
print_success "Deployment tamamlandı! Siteyi test edin."
