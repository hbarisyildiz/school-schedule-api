#!/bin/bash

# AWS Deployment Script for School Schedule API
# Bu script AWS EC2 instance'ında çalıştırılmalıdır

echo "🚀 AWS Deployment başlatılıyor..."

# Sistem güncellemesi
echo "📦 Sistem güncelleniyor..."
sudo apt update && sudo apt upgrade -y

# Docker kurulumu
echo "🐳 Docker kuruluyor..."
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Docker Compose kurulumu
echo "🔧 Docker Compose kuruluyor..."
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Docker servisini başlat
echo "🔄 Docker servisi başlatılıyor..."
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker $USER

# Proje dizini oluştur
echo "📁 Proje dizini oluşturuluyor..."
sudo mkdir -p /var/www/school-schedule
sudo chown $USER:$USER /var/www/school-schedule
cd /var/www/school-schedule

# Environment dosyası oluştur
echo "⚙️ Environment dosyası oluşturuluyor..."
cat > .env << EOF
APP_NAME="Okul Ders Programı"
APP_ENV=production
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=school_schedule
DB_USERNAME=school_user
DB_PASSWORD=$(openssl rand -base64 32)

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=$(openssl rand -base64 32)
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="\${APP_NAME}"
EOF

echo "✅ AWS deployment hazır!"
echo "📋 Sonraki adımlar:"
echo "1. Proje dosyalarını bu dizine kopyalayın"
echo "2. .env dosyasını düzenleyin"
echo "3. sudo docker-compose -f docker-compose.prod.yml up -d"
echo "4. sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed"
echo "5. sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:cache"
echo "6. sudo docker-compose -f docker-compose.prod.yml exec app php artisan route:cache"
echo "7. sudo docker-compose -f docker-compose.prod.yml exec app php artisan view:cache"
