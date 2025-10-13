# ☁️ AWS Ücretsiz Deployment Rehberi

**Platform:** Amazon Web Services (AWS)  
**Maliyet:** **ÜCRETSIZ** (12 ay)  
**Hedef:** Laravel School Schedule SaaS

---

## 🎁 AWS FREE TIER (12 Ay Ücretsiz)

### İçerik:
- ✅ **EC2:** 750 saat/ay (t2.micro veya t3.micro)
  - 1 vCPU, 1GB RAM
  - Linux/Ubuntu
- ✅ **RDS (MySQL):** 750 saat/ay (db.t2.micro)
  - 20GB storage
- ✅ **S3:** 5GB storage (dosya depolama)
- ✅ **CloudFront:** 50GB transfer (CDN)
- ✅ **Elastic IP:** 1 adet statik IP
- ✅ **VPC:** Network ücretsiz

**NOT:** 12 ay sonra ücretli olur, ama istersen sonlandır!

**Link:** https://aws.amazon.com/free/

---

## 🚀 ADIM ADIM KURULUM

### ADIM 1: AWS Hesabı Aç (5 dk)

1. [ ] https://aws.amazon.com/ adresine git
2. [ ] "Create Free Account" tıkla
3. [ ] Email, telefon, **kredi kartı** (ücret alınmaz, doğrulama için)
4. [ ] Basic Plan seç (ücretsiz)
5. [ ] Email doğrula

**Önemli:** Kredi kartı gerekli ama 12 ay ücretsiz!

---

### ADIM 2: EC2 Instance Oluştur (15 dk)

#### 2.1. EC2 Konsola Git
- AWS Console → Services → EC2

#### 2.2. Launch Instance
```
Name: school-schedule-app
Application: Ubuntu Server 22.04 LTS (Free tier eligible)
Instance type: t2.micro (Free tier eligible)
  - 1 vCPU
  - 1 GB RAM
  - ✅ Ücretsiz

Key pair: 
  - Create new key pair
  - Name: school-schedule-key
  - Type: RSA
  - Format: .pem (Linux/Mac) veya .ppk (Windows/PuTTY)
  - İndir ve sakla!

Network:
  - Create security group
  - Allow SSH (port 22) - Your IP
  - Allow HTTP (port 80) - Anywhere
  - Allow HTTPS (port 443) - Anywhere

Storage:
  - 30 GB (Free tier: 30GB'a kadar ücretsiz)
  - gp3 (SSD)

Launch instance!
```

#### 2.3. Elastic IP Ata (Statik IP)
```
EC2 → Elastic IPs → Allocate Elastic IP address
→ Instance'ınıza associate et
```

---

### ADIM 3: RDS Database Oluştur (10 dk)

#### 3.1. RDS Konsola Git
- AWS Console → Services → RDS

#### 3.2. Create Database
```
Engine: MySQL
Version: 8.0.35
Templates: Free tier ✅

Settings:
  DB instance identifier: school-schedule-db
  Master username: admin
  Master password: GÜÇLÜ_ŞİFRE_OLUŞTUR

Instance configuration:
  DB instance class: db.t3.micro (Free tier eligible)

Storage:
  Storage type: General Purpose (SSD)
  Allocated storage: 20 GB (Free tier: 20GB)
  ❌ Enable storage autoscaling (kapalı, ücretsiz kalması için)

Connectivity:
  Virtual private cloud (VPC): Default
  Public access: Yes (geliştirme için, sonra No yapılabilir)
  VPC security group: Create new
    - Allow MySQL (3306) from EC2 security group

Database authentication: Password authentication

Create database!
```

**Not:** Database oluşması 5-10 dk sürer.

---

### ADIM 4: SSH ile Sunucuya Bağlan (5 dk)

#### Windows (PowerShell):
```bash
# Key permission ayarla
icacls school-schedule-key.pem /inheritance:r
icacls school-schedule-key.pem /grant:r "%username%:R"

# SSH bağlan
ssh -i school-schedule-key.pem ubuntu@YOUR_ELASTIC_IP
```

#### Linux/Mac:
```bash
chmod 400 school-schedule-key.pem
ssh -i school-schedule-key.pem ubuntu@YOUR_ELASTIC_IP
```

---

### ADIM 5: Sunucu Kurulumu (30 dk)

```bash
# Sistem güncelle
sudo apt update && sudo apt upgrade -y

# PHP 8.3 ve gerekli extension'lar
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-redis \
  php8.3-bcmath php8.3-intl unzip git

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Nginx
sudo apt install -y nginx

# Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server

# MySQL Client (RDS'e bağlanmak için)
sudo apt install -y mysql-client

# Node.js (opsiyonel, eğer asset build gerekirse)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

### ADIM 6: Laravel Projesini Kur (20 dk)

```bash
# Web root'a git
cd /var/www

# GitHub'dan clone
sudo git clone https://github.com/hbarisyildiz/school-schedule-api.git html
cd html

# Permission ayarla
sudo chown -R ubuntu:www-data /var/www/html
sudo chmod -R 775 storage bootstrap/cache

# Composer install
composer install --optimize-autoloader --no-dev

# .env dosyası oluştur
cp .env.example .env
nano .env
```

**`.env` Ayarları:**
```env
APP_NAME="School Schedule"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://YOUR_ELASTIC_IP

DB_CONNECTION=mysql
DB_HOST=YOUR_RDS_ENDPOINT.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=school_schedule
DB_USERNAME=admin
DB_PASSWORD=YOUR_RDS_PASSWORD

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

```bash
# Key generate
php artisan key:generate

# Database migrate
php artisan migrate --force

# Database seed
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SubscriptionPlanSeeder
php artisan db:seed --class=CitySeeder
php artisan db:seed --class=CompleteDistrictSeeder

# Cache optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link
```

---

### ADIM 7: Nginx Yapılandırması (10 dk)

```bash
# Nginx config oluştur
sudo nano /etc/nginx/sites-available/school-schedule
```

**Config içeriği:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name YOUR_ELASTIC_IP;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Config'i aktif et
sudo ln -s /etc/nginx/sites-available/school-schedule /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# Test ve restart
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

---

### ADIM 8: Queue Worker (10 dk)

```bash
# Supervisor yükle
sudo apt install -y supervisor

# Worker config
sudo nano /etc/supervisor/conf.d/school-schedule-worker.conf
```

**Config içeriği:**
```ini
[program:school-schedule-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=ubuntu
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Supervisor başlat
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start school-schedule-worker:*
```

---

### ADIM 9: Cron (Scheduler) (5 dk)

```bash
# Crontab aç
sudo crontab -e

# Bu satırı ekle
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

---

### ADIM 10: SSL Sertifikası (Opsiyonel) (15 dk)

**Domain varsa:**
```bash
# Certbot yükle
sudo apt install -y certbot python3-certbot-nginx

# Domain için SSL al
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Otomatik yenileme
sudo certbot renew --dry-run
```

**Domain yoksa:**
- Elastic IP ile HTTP kullan
- Sonra domain alınca SSL ekle

---

## 🔄 GITHUB OTOMATIK DEPLOYMENT

### Webhook Setup (Ücretsiz!)

```bash
# Deploy script oluştur
nano /var/www/html/deploy.sh
```

**Script içeriği:**
```bash
#!/bin/bash
cd /var/www/html

# Git pull
git pull origin master

# Composer update
composer install --no-dev --optimize-autoloader

# Laravel optimize
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

# Permissions
sudo chown -R ubuntu:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "✅ Deploy tamamlandı: $(date)"
```

```bash
# Script'i çalıştırılabilir yap
chmod +x /var/www/html/deploy.sh
```

**GitHub Webhook:**
1. GitHub repo → Settings → Webhooks
2. Add webhook
3. Payload URL: `http://YOUR_IP/webhook.php`
4. Content type: `application/json`
5. Secret: Rastgele güçlü string
6. Events: `push`

**webhook.php oluştur:**
```php
<?php
// public/webhook.php
$secret = 'YOUR_SECRET_KEY';
$signature = 'sha1=' . hash_hmac('sha1', file_get_contents('php://input'), $secret);

if (hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    shell_exec('/var/www/html/deploy.sh > /dev/null 2>&1 &');
    http_response_code(200);
    echo "Deploy started!";
} else {
    http_response_code(403);
    echo "Invalid signature";
}
```

---

## 📊 PERFORMANS OPTİMİZASYONU

### 1GB RAM için Özel Ayarlar

**PHP-FPM Optimize:**
```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

```ini
pm = dynamic
pm.max_children = 10
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.max_requests = 500
```

**Nginx Optimize:**
```nginx
# /etc/nginx/nginx.conf
worker_processes auto;
worker_connections 1024;

# Gzip compression
gzip on;
gzip_types text/plain text/css application/json application/javascript;
```

**MySQL (RDS) Optimize:**
```sql
-- Query cache
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL query_cache_type = 1;
```

---

## 💰 MALİYET ANALİZİ

### İlk 12 Ay: ÜCRETSIZ! 🎉

| Hizmet | Normal Fiyat | Free Tier | Tasarruf |
|--------|--------------|-----------|----------|
| EC2 (t2.micro) | $8.50/ay | ÜCRETSİZ | $102/yıl |
| RDS (db.t2.micro) | $15/ay | ÜCRETSİZ | $180/yıl |
| EBS Storage 30GB | $3/ay | ÜCRETSİZ | $36/yıl |
| Data Transfer | $0.09/GB | 15GB ücretsiz | ~$10/yıl |
| **TOPLAM** | **~$26/ay** | **$0** | **$328/yıl** |

### 12 Ay Sonra (Ücretli)
- **Maliyet:** ~$26/ay (~₺850)
- Alternatif: DigitalOcean'a geç ($12/ay)

---

## 🎯 AWS vs DigitalOcean

| Özellik | AWS Free Tier | DigitalOcean + Forge |
|---------|---------------|----------------------|
| **Maliyet (İlk 12 ay)** | ✅ $0 | ❌ $24/ay |
| **Maliyet (Sonrası)** | ❌ ~$26/ay | ✅ $24/ay |
| **Kurulum** | ⚠️ Karmaşık | ✅ Kolay |
| **Yönetim** | ⚠️ Manuel | ✅ Otomatik |
| **Auto Deploy** | ⚠️ Webhook setup | ✅ Built-in |
| **SSL** | ⚠️ Certbot | ✅ Otomatik |
| **Backup** | ⚠️ Manuel | ✅ Otomatik |
| **Monitoring** | ✅ CloudWatch | ✅ Forge |

---

## 🎯 TAVSİYE

### Başlangıç Stratejisi:

**İlk 12 Ay:** AWS Free Tier kullan
- ✅ Tamamen ücretsiz
- ✅ Test et, müşteri topla
- ✅ Gelir elde et

**12 Ay Sonra:** Değerlendirme yap
- Çok müşteri varsa: AWS'de kal, scale et
- Az müşteri varsa: DigitalOcean'a geç (daha ucuz)

---

## 📋 DEPLOYMENT CHECKLIST

### Önce Hazırlık
- [ ] AWS hesabı aç
- [ ] Kredi kartı ekle (doğrulama)
- [ ] Free tier eligible seç (dikkat!)

### EC2 Setup
- [ ] t2.micro instance oluştur
- [ ] Ubuntu 22.04 seç
- [ ] Security group ayarla
- [ ] Key pair indir
- [ ] Elastic IP ata

### RDS Setup
- [ ] db.t2.micro oluştur
- [ ] MySQL 8.0 seç
- [ ] 20GB storage (ücretsiz limit)
- [ ] Public access: Yes
- [ ] Security group ayarla

### Laravel Setup
- [ ] SSH bağlan
- [ ] PHP 8.3 kur
- [ ] Nginx kur
- [ ] Redis kur
- [ ] GitHub clone
- [ ] Composer install
- [ ] .env ayarla
- [ ] Database migrate
- [ ] Nginx config
- [ ] SSL (Certbot)

### Post-Deploy
- [ ] Test et
- [ ] Queue worker başlat
- [ ] Cron ekle
- [ ] Monitoring aktif et
- [ ] Backup ayarla

---

## ⚠️ ÖNEMLİ UYARILAR

### Free Tier Limitlerini Aşmayın!

**EC2:**
- ✅ 750 saat/ay (7/24 çalışsa: 720 saat OK)
- ❌ t2.micro'dan büyük instance: ÜCRET!
- ❌ 2 instance: ÜCRET!

**RDS:**
- ✅ 750 saat/ay
- ✅ 20GB storage limit
- ❌ 20GB'ı aşarsa: ÜCRET!
- ❌ Auto-scaling aktifse: ÜCRET!

**Data Transfer:**
- ✅ İlk 15GB/ay ücretsiz
- ❌ Sonrası: $0.09/GB

**Billing Alarm Kur:**
```
AWS Console → Billing → Billing Preferences
→ Receive Billing Alerts: ENABLE
→ CloudWatch → Alarms → Create alarm
→ $1 threshold (erken uyarı)
```

---

## 🔧 SORUN GİDERME

### Yaygın Sorunlar

**1. EC2'ye SSH bağlanamıyorum**
```bash
# Key permission kontrol
chmod 400 key.pem

# Security group kontrol
# Port 22 açık mı, Your IP doğru mu?
```

**2. Laravel 500 Error**
```bash
# Log kontrol
tail -f /var/www/html/storage/logs/laravel.log

# Permission
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R ubuntu:www-data /var/www/html
```

**3. Database bağlanamıyor**
```bash
# RDS endpoint doğru mu?
# Security group MySQL port 3306 açık mı?
# .env DB_HOST doğru mu?

# Test
mysql -h YOUR_RDS_ENDPOINT -u admin -p
```

**4. Nginx 502 Bad Gateway**
```bash
# PHP-FPM çalışıyor mu?
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm
```

---

## 📱 MOBİL UYGULAMA İÇİN (İleride)

AWS'de mobil için:
- ✅ API zaten var (Laravel)
- ✅ Sanctum token auth çalışır
- ✅ CORS ayarla
- ✅ AWS CloudFront (CDN) ekle

---

## 🎓 AWS ÖĞRENME KAYNAKLARI

- **AWS Free Tier:** https://aws.amazon.com/free/
- **EC2 Tutorial:** https://docs.aws.amazon.com/ec2/
- **RDS Tutorial:** https://docs.aws.amazon.com/rds/
- **Laravel on AWS:** https://laravel.com/docs/deployment

---

## 💡 EK OPTİMİZASYONLAR

### CDN (Ücretsiz!)
```
AWS CloudFront:
- Static dosyalar için CDN
- Free tier: 50GB/ay
- Admin panel CSS/JS cache
```

### S3 Storage (Ücretsiz!)
```
AWS S3:
- Excel şablonu storage
- User uploads
- Free tier: 5GB
- Cost: ~$0.023/GB sonrası
```

### Auto Scaling (İleride)
```
Load Balancer + Auto Scaling
- Traffic arttığında otomatik scale
- Free tier'dan sonra
```

---

## 🚀 DEPLOYMENT SÜRECE GÖRE

### Option 1: Hızlı & Kolay (Önerilen Başlangıç)
**Platform:** DigitalOcean + Laravel Forge  
**Maliyet:** $24/ay  
**Süre:** 1 saat  
**Avantaj:** Otomatik her şey

### Option 2: Ücretsiz Başlangıç
**Platform:** AWS Free Tier  
**Maliyet:** $0 (12 ay)  
**Süre:** 3-4 saat (manuel setup)  
**Avantaj:** Tamamen ücretsiz!

---

## 📝 SONRAKI ADIMLAR

### Şimdi Karar Verin:

**A) AWS Free Tier (Ücretsiz, Manuel)**
- Bu dosyayı takip edin
- 3-4 saat kurulum
- 12 ay ücretsiz

**B) DigitalOcean + Forge (Ücretli, Otomatik)**
- DEPLOYMENT_PLAN.md dosyasını takip edin
- 1 saat kurulum
- $24/ay

**C) Daha sonra deploy (Önce özellikler)**
- Ders Programı UI bitir
- Sonra deploy

---

**Hangisini tercih ediyorsunuz?**

**Son Güncelleme:** 13 Ekim 2025  
**Durum:** Planlama tamamlandı, karar bekleniyor

