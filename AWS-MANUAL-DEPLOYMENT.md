# 🚀 AWS Manuel Deployment Rehberi

## 📋 Adım Adım AWS Deployment

### 1. AWS Console'da EC2 Instance Oluştur

```bash
# AWS Console > EC2 > Launch Instance
# 1. Name: school-schedule-api
# 2. AMI: Ubuntu Server 22.04 LTS (ami-0c02fb55956c7d316)
# 3. Instance Type: t3.medium (2 vCPU, 4GB RAM)
# 4. Key Pair: Create new key pair (school-schedule-key)
# 5. Security Group: Create new security group
#    - SSH (22) from Anywhere
#    - HTTP (80) from Anywhere  
#    - HTTPS (443) from Anywhere
# 6. Launch Instance
```

### 2. EC2'ye Bağlan

```bash
# SSH ile bağlan
ssh -i school-schedule-key.pem ubuntu@YOUR-EC2-IP

# Veya AWS Console > EC2 > Connect > SSH Client
```

### 3. Deployment Script'ini Çalıştır

```bash
# Deployment script'ini indir ve çalıştır
curl -o quick-aws-deploy.sh https://raw.githubusercontent.com/hbarisyildiz/school-schedule-api/master/quick-aws-deploy.sh
chmod +x quick-aws-deploy.sh
./quick-aws-deploy.sh
```

### 4. Proje Dosyalarını Kopyala

```bash
# Proje dizinine git
cd /var/www/school-schedule

# GitHub'dan klonla
git clone https://github.com/hbarisyildiz/school-schedule-api.git .

# Veya local'den kopyala (eğer local'de varsa)
# scp -i school-schedule-key.pem -r . ubuntu@YOUR-EC2-IP:/var/www/school-schedule/
```

### 5. Environment Dosyasını Düzenle

```bash
# .env dosyasını düzenle
nano .env

# Önemli ayarlar:
APP_URL=http://YOUR-EC2-IP
DB_PASSWORD=secure_password_123
REDIS_PASSWORD=redis_password_123
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### 6. Docker Compose ile Başlat

```bash
# Production compose ile başlat
sudo docker-compose -f docker-compose.prod.yml up -d

# Container'ların durumunu kontrol et
sudo docker-compose -f docker-compose.prod.yml ps

# Log'ları kontrol et
sudo docker-compose -f docker-compose.prod.yml logs
```

### 7. Database Migration

```bash
# Database migration
sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed

# Cache'leri oluştur
sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### 8. Test Et

```bash
# Site URL'leri
http://YOUR-EC2-IP
http://YOUR-EC2-IP/admin-panel

# Health check
curl http://YOUR-EC2-IP/health
```

## 🔧 Troubleshooting

### Container'lar Çalışmıyor

```bash
# Container durumunu kontrol et
sudo docker-compose -f docker-compose.prod.yml ps

# Log'ları kontrol et
sudo docker-compose -f docker-compose.prod.yml logs app

# Restart
sudo docker-compose -f docker-compose.prod.yml restart
```

### Database Bağlantı Sorunu

```bash
# Database container'ını kontrol et
sudo docker-compose -f docker-compose.prod.yml logs db

# Database'e bağlan
sudo docker-compose -f docker-compose.prod.yml exec db mysql -u root -p
```

### Performance Test

```bash
# Response time test
curl -w "@curl-format.txt" -o /dev/null -s http://YOUR-EC2-IP/

# Load test
ab -n 100 -c 10 http://YOUR-EC2-IP/
```

## 📊 Beklenen Performans

- **Local Docker**: 40 saniye
- **AWS EC2**: 1-2 saniye
- **Database**: AWS'de daha hızlı
- **Network**: Daha iyi bağlantı

## 🎯 Test Edilecekler

1. **Ana sayfa yükleme** - http://YOUR-EC2-IP
2. **Admin panel** - http://YOUR-EC2-IP/admin-panel
3. **Sınıf saatleri** - http://YOUR-EC2-IP/class-schedule.html?id=1
4. **API response süreleri**

## 📈 Monitoring

```bash
# System resources
htop
df -h
free -h

# Docker stats
sudo docker stats

# Application logs
sudo docker-compose -f docker-compose.prod.yml logs -f app
```

## 🔄 Backup

```bash
# Database backup
sudo docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p school_schedule > backup.sql

# File backup
tar -czf storage-backup.tar.gz storage/
```

## 🎉 Sonuç

AWS deployment tamamlandıktan sonra:

1. **Site**: http://YOUR-EC2-IP
2. **Admin**: http://YOUR-EC2-IP/admin-panel
3. **Performance**: 1-2 saniye yükleme süresi
4. **Monitoring**: Container ve system logs

Performans testi yapın ve sonuçları bildirin!
