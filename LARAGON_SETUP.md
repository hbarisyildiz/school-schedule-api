# MAMP'tan Laragon'a Geçiş Rehberi

## 1. Laragon İndirme ve Kurulum

### İndirme Linki:
- **Laragon Full:** https://laragon.org/download/
- **Önerilen Sürüm:** Laragon Full (PHP, MySQL, Apache, Node.js dahil)

### Kurulum Adımları:
1. Laragon Full'u indirin
2. Yönetici olarak çalıştırın
3. C:\laragon dizinine kurun (önerilen)
4. Kurulum tamamlandıktan sonra Laragon'u başlatın

## 2. Proje Taşıma

### Eski MAMP Dizini:
```
C:\MAMP\htdocs\dersProg\school-schedule-api
```

### Yeni Laragon Dizini:
```
C:\laragon\www\school-schedule-api
```

### Taşıma Komutu (PowerShell):
```powershell
# Laragon www dizinini oluştur
New-Item -Path "C:\laragon\www" -ItemType Directory -Force

# Proje klasörünü kopyala
Copy-Item -Path "C:\MAMP\htdocs\dersProg\school-schedule-api" -Destination "C:\laragon\www\" -Recurse -Force
```

## 3. Veritabanı Taşıma

### MySQL Export (MAMP'tan):
```sql
-- MAMP phpMyAdmin'den dışa aktar
-- Veritabanı: school_schedule
-- Format: SQL
```

### MySQL Import (Laragon'a):
```sql
-- Laragon phpMyAdmin'den içe aktar
-- Yeni veritabanı oluştur: school_schedule
-- SQL dosyasını içe aktar
```

## 4. Environment Ayarları

### .env Dosyası Güncellemesi:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_schedule
DB_USERNAME=root
DB_PASSWORD=
```

## 5. Laragon Avantajları

### Hız:
- ✅ Anında başlatma/durdurma
- ✅ Pretty URLs (otomatik domain)
- ✅ SSL sertifikaları

### Özellikler:
- ✅ Multiple PHP versions
- ✅ Node.js, Python, Java desteği
- ✅ Redis, Memcached dahil
- ✅ Mailhog (email testing)

### Pretty URLs:
- http://school-schedule-api.test (otomatik)
- https://school-schedule-api.test (SSL dahil)

## 6. Geçiş Sonrası Test

### Komutlar:
```bash
cd C:\laragon\www\school-schedule-api
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

### Erişim:
- **Laragon Panel:** http://localhost
- **Proje:** http://school-schedule-api.test
- **phpMyAdmin:** http://localhost/phpmyadmin

## 7. Sorun Giderme

### PHP Path:
```
C:\laragon\bin\php\php-8.3.1-Win32-vs16-x64\php.exe
```

### MySQL:
```
Host: 127.0.0.1
Port: 3306
User: root
Pass: (boş)
```

### Apache:
```
Port: 80 (HTTP)
Port: 443 (HTTPS)
```