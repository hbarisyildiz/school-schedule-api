# 🚀 AWS Deployment Rehberi

## 📋 Gereksinimler

- AWS hesabı
- EC2 instance (t3.medium veya daha büyük önerilir)
- Domain (opsiyonel)

## 🔧 EC2 Instance Kurulumu

### 1. EC2 Instance Oluşturma

```bash
# AWS Console'da:
# 1. EC2 > Launch Instance
# 2. Ubuntu Server 22.04 LTS seç
# 3. t3.medium (2 vCPU, 4GB RAM) önerilir
# 4. Key pair oluştur ve indir
# 5. Security Group: HTTP (80), HTTPS (443), SSH (22) açık
# 6. Launch instance
```

### 2. EC2'ye Bağlanma

```bash
# SSH ile bağlan
ssh -i your-key.pem ubuntu@your-ec2-ip

# Veya AWS Console'dan "Connect" butonunu kullan
```

### 3. Deployment Script Çalıştırma

```bash
# Deployment script'ini indir ve çalıştır
curl -o deploy-aws.sh https://raw.githubusercontent.com/your-repo/deploy-aws.sh
chmod +x deploy-aws.sh
./deploy-aws.sh
```

### 4. Proje Dosyalarını Kopyalama

```bash
# Git ile klonla (eğer repo varsa)
git clone https://github.com/your-username/school-schedule-api.git
cd school-schedule-api

# Veya SCP ile kopyala
scp -i your-key.pem -r /local/path/to/project ubuntu@your-ec2-ip:/var/www/school-schedule/
```

### 5. Environment Ayarları

```bash
# .env dosyasını düzenle
nano .env

# Önemli ayarlar:
APP_URL=https://your-domain.com  # Domain varsa
DB_PASSWORD=your-secure-password
REDIS_PASSWORD=your-redis-password
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### 6. Docker Compose ile Başlatma

```bash
# Production compose ile başlat
sudo docker-compose -f docker-compose.prod.yml up -d

# Database migration
sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed

# Cache'leri oluştur
sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## 🌐 Domain ve SSL Kurulumu

### 1. Domain Ayarları

```bash
# Domain DNS ayarlarında A record ekle:
# your-domain.com -> EC2 Public IP
```

### 2. SSL Sertifikası (Let's Encrypt)

```bash
# Certbot kurulumu
sudo apt install certbot python3-certbot-nginx

# SSL sertifikası al
sudo certbot --nginx -d your-domain.com

# Otomatik yenileme
sudo crontab -e
# Şu satırı ekle:
0 12 * * * /usr/bin/certbot renew --quiet
```

## 📊 Performans Optimizasyonu

### 1. Nginx Konfigürasyonu

```bash
# /etc/nginx/sites-available/school-schedule
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    root /var/www/school-schedule/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
    
    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 2. PHP-FPM Optimizasyonu

```bash
# /etc/php/8.1/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000
```

### 3. MySQL Optimizasyonu

```bash
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_size = 64M
query_cache_type = 1
```

## 🔍 Monitoring ve Logs

### 1. Container Logs

```bash
# Tüm container logs
sudo docker-compose -f docker-compose.prod.yml logs

# Sadece app logs
sudo docker-compose -f docker-compose.prod.yml logs app

# Real-time logs
sudo docker-compose -f docker-compose.prod.yml logs -f app
```

### 2. System Monitoring

```bash
# System resources
htop
df -h
free -h

# Docker stats
sudo docker stats
```

## 🚨 Troubleshooting

### 1. Yaygın Sorunlar

```bash
# Container'lar çalışmıyor
sudo docker-compose -f docker-compose.prod.yml ps
sudo docker-compose -f docker-compose.prod.yml restart

# Database bağlantı sorunu
sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate:status

# Cache sorunları
sudo docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
```

### 2. Log Kontrolü

```bash
# Laravel logs
sudo docker-compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

## 📈 Performans Testi

### 1. Load Testing

```bash
# Apache Bench ile test
ab -n 1000 -c 10 http://your-domain.com/

# Curl ile response time
curl -w "@curl-format.txt" -o /dev/null -s http://your-domain.com/
```

### 2. Database Performance

```bash
# MySQL slow query log
sudo docker-compose -f docker-compose.prod.yml exec db mysql -u root -p
SHOW VARIABLES LIKE 'slow_query_log';
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;
```

## 🔄 Backup ve Restore

### 1. Database Backup

```bash
# Backup oluştur
sudo docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p school_schedule > backup.sql

# Restore
sudo docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p school_schedule < backup.sql
```

### 2. File Backup

```bash
# Storage backup
tar -czf storage-backup.tar.gz storage/

# Restore
tar -xzf storage-backup.tar.gz
```

## 🎯 Sonuç

AWS deployment tamamlandıktan sonra:

1. **Domain**: `https://your-domain.com`
2. **Admin Panel**: `https://your-domain.com/admin-panel`
3. **Performance**: Local Docker'dan çok daha hızlı olmalı
4. **SSL**: Let's Encrypt ile güvenli bağlantı
5. **Monitoring**: Container ve system logs

Performans testi yapın ve sonuçları bildirin!
