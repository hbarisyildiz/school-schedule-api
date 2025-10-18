#!/bin/bash

# 🚀 Hızlı AWS Deployment Script
# Bu script'i EC2 instance'ında çalıştırın

echo "🚀 Hızlı AWS Deployment başlatılıyor..."

# 1. Sistem güncellemesi
echo "📦 Sistem güncelleniyor..."
sudo apt update -y

# 2. Docker kurulumu
echo "🐳 Docker kuruluyor..."
sudo apt install -y docker.io docker-compose
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker $USER

# 3. Proje dizini
echo "📁 Proje dizini oluşturuluyor..."
sudo mkdir -p /var/www/school-schedule
sudo chown $USER:$USER /var/www/school-schedule
cd /var/www/school-schedule

# 4. Environment dosyası
echo "⚙️ Environment dosyası oluşturuluyor..."
cat > .env << 'EOF'
APP_NAME="Okul Ders Programı"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=http://your-ec2-ip

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

echo "✅ AWS deployment hazır!"
echo ""
echo "📋 Sonraki adımlar:"
echo "1. Proje dosyalarını bu dizine kopyalayın:"
echo "   scp -i your-key.pem -r /local/path ubuntu@your-ec2-ip:/var/www/school-schedule/"
echo ""
echo "2. Docker Compose ile başlatın:"
echo "   sudo docker-compose -f docker-compose.prod.yml up -d"
echo ""
echo "3. Database migration:"
echo "   sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed"
echo ""
echo "4. Cache'leri oluşturun:"
echo "   sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:cache"
echo "   sudo docker-compose -f docker-compose.prod.yml exec app php artisan route:cache"
echo "   sudo docker-compose -f docker-compose.prod.yml exec app php artisan view:cache"
echo ""
echo "5. Siteyi test edin:"
echo "   http://your-ec2-ip"
echo "   http://your-ec2-ip/admin-panel"
