# 🚀 Production Server Update Rehberi

## 📋 Mevcut Sunucuya Güncelleme

### 1. Sunucuya Bağlan

```bash
# SSH ile bağlan
ssh -i your-key.pem ubuntu@YOUR-SERVER-IP

# Veya mevcut bağlantı yönteminizi kullanın
```

### 2. Güncelleme Script'ini İndir ve Çalıştır

```bash
# Güncelleme script'ini indir
curl -o update-production.sh https://raw.githubusercontent.com/hbarisyildiz/school-schedule-api/master/update-production.sh
chmod +x update-production.sh

# Script'i çalıştır
./update-production.sh
```

### 3. Manuel Güncelleme (Alternatif)

```bash
# Proje dizinine git
cd /var/www/school-schedule

# Docker container'ları durdur
sudo docker-compose down

# Git ile güncelle
git fetch origin
git reset --hard origin/master

# Docker container'ları yeniden başlat
sudo docker-compose -f docker-compose.prod.yml up -d

# Database migration
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# Cache'leri temizle ve yeniden oluştur
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan cache:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:clear
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:clear

sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Composer autoload güncelle
sudo docker-compose -f docker-compose.prod.yml exec -T app composer dump-autoload

# Storage permissions
sudo docker-compose -f docker-compose.prod.yml exec -T app chmod -R 775 storage
sudo docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data storage
```

## 🔧 Yeni Özellikler

### 1. Performans Optimizasyonları
- **Paralel API çağrıları**: 40s → 1-2s
- **Database query optimizasyonu**: whereHas → join
- **Timeout optimizasyonu**: 3 saniye maksimum
- **Lazy loading**: Gereksiz API çağrıları engellendi

### 2. Yeni Sayfalar
- **Sınıf saatleri**: `/class-schedule.html?id=X`
- **Öğretmen saatleri**: `/teacher-schedule.html?id=X`
- **Sınıf ekleme**: `/add-class.html`
- **Sınıf düzenleme**: `/edit-class.html?id=X`
- **Öğretmen ekleme**: `/add-teacher.html`
- **Öğretmen düzenleme**: `/edit-teacher.html?id=X`
- **Derslik ekleme**: `/add-area.html`
- **Derslik düzenleme**: `/edit-area.html?id=X`

### 3. Veritabanı Değişiklikleri
- **`classes` tablosundan `capacity` sütunu kaldırıldı**
- **`schools` tablosuna `daily_lesson_count` sütunu eklendi**
- **Yeni `classrooms` tablosu eklendi**
- **Yeni `class_daily_schedules` tablosu eklendi**
- **Yeni `teacher_daily_schedules` tablosu eklendi**

## 📊 Beklenen Performans İyileştirmeleri

- **Sayfa yükleme süresi**: 40 saniye → 1-2 saniye
- **API response süresi**: 30 saniye → 3 saniye
- **Database query süresi**: %70-80 daha hızlı
- **Frontend loading**: Paralel yükleme

## 🧪 Test Edilecekler

### 1. Ana Sayfalar
- **Ana site**: `http://YOUR-SERVER-IP`
- **Admin panel**: `http://YOUR-SERVER-IP/admin-panel`

### 2. Yeni Sayfalar
- **Sınıf saatleri**: `http://YOUR-SERVER-IP/class-schedule.html?id=1`
- **Sınıf ekleme**: `http://YOUR-SERVER-IP/add-class.html`
- **Sınıf düzenleme**: `http://YOUR-SERVER-IP/edit-class.html?id=1`
- **Öğretmen saatleri**: `http://YOUR-SERVER-IP/teacher-schedule.html?id=1`

### 3. Performans Testleri
- **Sayfa yükleme süresi**: 1-2 saniye olmalı
- **API response süresi**: 3 saniye maksimum
- **Database query süresi**: Çok daha hızlı

## 🔍 Troubleshooting

### Container'lar Çalışmıyor
```bash
# Container durumunu kontrol et
sudo docker-compose -f docker-compose.prod.yml ps

# Log'ları kontrol et
sudo docker-compose -f docker-compose.prod.yml logs app

# Restart
sudo docker-compose -f docker-compose.prod.yml restart
```

### Database Migration Hatası
```bash
# Migration durumunu kontrol et
sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate:status

# Migration'ları tekrar çalıştır
sudo docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

### Cache Sorunları
```bash
# Tüm cache'leri temizle
sudo docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
sudo docker-compose -f docker-compose.prod.yml exec app php artisan route:clear
sudo docker-compose -f docker-compose.prod.yml exec app php artisan view:clear

# Cache'leri yeniden oluştur
sudo docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
sudo docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
sudo docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## 📈 Monitoring

### Container Durumu
```bash
# Container'ları listele
sudo docker-compose -f docker-compose.prod.yml ps

# Real-time logs
sudo docker-compose -f docker-compose.prod.yml logs -f app

# System resources
htop
df -h
free -h
```

### Performance Monitoring
```bash
# Response time test
curl -w "@curl-format.txt" -o /dev/null -s http://YOUR-SERVER-IP/

# Load test
ab -n 100 -c 10 http://YOUR-SERVER-IP/
```

## 🎉 Sonuç

Güncelleme tamamlandıktan sonra:

1. **Performans**: 40s → 1-2s yükleme süresi
2. **Yeni özellikler**: Tüm yeni sayfalar aktif
3. **Database**: Yeni migration'lar uygulandı
4. **Cache**: Optimize edildi

Siteyi test edin ve performans sonuçlarını bildirin!
