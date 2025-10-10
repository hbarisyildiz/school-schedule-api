# 🚀 Hızlı Laragon Kurulum Rehberi

## 1. İndirme
**Laragon Full İndirme Linki:**
👉 https://laragon.org/download/

**Tavsiye Edilen:** Laragon Full (PHP, MySQL, Apache, Node.js dahil)

## 2. Kurulum
1. **Yönetici olarak çalıştır**
2. **C:\laragon** dizinine kur (varsayılan)
3. **Full kurulum** seç

## 3. Proje Taşıma Komutları

### PowerShell'de çalıştır:
```powershell
# Laragon www dizini oluştur
New-Item -Path "C:\laragon\www" -ItemType Directory -Force

# Projeyi kopyala
Copy-Item -Path "C:\MAMP\htdocs\dersProg\school-schedule-api" -Destination "C:\laragon\www\" -Recurse -Force
```

## 4. Veritabanı Taşıma

### MAMP'tan Export:
1. http://localhost/phpMyAdmin/ git
2. **school_schedule** veritabanını seç
3. **Export** → **SQL format** → **Go**
4. SQL dosyasını kaydet

### Laragon'a Import:
1. Laragon'u başlat
2. http://localhost/phpmyadmin git
3. **Yeni veritabanı oluştur:** school_schedule
4. SQL dosyasını **Import** et

## 5. Environment Ayarları

**C:\laragon\www\school-schedule-api\.env** dosyasını güncelle:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_schedule
DB_USERNAME=root
DB_PASSWORD=
```

## 6. Test

```bash
cd C:\laragon\www\school-schedule-api
php artisan migrate:status
```

## 7. Erişim URL'leri

Laragon otomatik pretty URL oluşturur:
- **Ana URL:** http://school-schedule-api.test
- **HTTPS:** https://school-schedule-api.test
- **phpMyAdmin:** http://localhost/phpmyadmin

## 8. Server Kontrolü

Laragon panel'den:
- ✅ **Apache** - Web Server
- ✅ **MySQL** - Database
- ✅ **PHP 8.3** - Aktif versiyon

## ⚡ Avantajları

- **Hız:** Anında start/stop
- **Güvenilirlik:** Stabil çalışma
- **Pretty URLs:** Otomatik domain
- **SSL:** Ücretsiz HTTPS
- **Multiple PHP:** Kolay versiyon değiştirme

---

**🎯 Sonuç:** MAMP yerine Laragon ile çok daha hızlı ve stabil geliştirme ortamı!