# 🏫 Okul Ders Programı Sistemi - Docker Kurulum

## 🚀 Hızlı Başlangıç

### 1. Sistemi Başlatma
```bash
docker-compose up -d
```

### 2. İlk Kurulum (Sadece bir kez)
```bash
# Laravel bağımlılıkları
docker-compose exec app composer install

# Uygulama anahtarı
docker-compose exec app php artisan key:generate

# Veritabanı migration
docker-compose exec app php artisan migrate

# Test verilerini yükle
docker-compose exec app php artisan db:seed
```

## 🔗 Erişim URL'leri

| Servis | URL | Açıklama |
|--------|-----|----------|
| **Web App** | http://localhost | Ana uygulama |
| **phpMyAdmin** | http://localhost:8080 | Veritabanı yönetimi |
| **API Base** | http://localhost/api | REST API |

## 👤 Test Kullanıcıları

| Rol | Email | Şifre | Açıklama |
|-----|-------|-------|----------|
| **Super Admin** | admin@schoolschedule.com | admin123 | Sistem yöneticisi |
| **Okul Müdürü** | mudur@ataturklisesi.edu.tr | mudur123 | Okul yöneticisi |
| **Öğretmen** | ayse.yilmaz@ataturklisesi.edu.tr | teacher123 | Öğretmen |

## 🛠️ Geliştirme Komutları

### Docker Yönetimi
```bash
# Servisleri başlat
docker-compose up -d

# Servisleri durdur
docker-compose down

# Logları görüntüle
docker-compose logs -f

# Container'a erişim
docker-compose exec app bash
```

### Laravel Komutları
```bash
# Cache temizle
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Migration
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:rollback

# Seeder
docker-compose exec app php artisan db:seed
```

## 📊 API Endpoints

### Authentication
- `POST /api/auth/login` - Kullanıcı girişi
- `POST /api/auth/logout` - Kullanıcı çıkışı
- `GET /api/user` - Kullanıcı bilgileri

### Geographic Data
- `GET /api/cities` - Türkiye şehirleri
- `GET /api/districts/{city_id}` - Şehre ait ilçeler

### School Management
- `GET /api/my-school` - Okul bilgileri
- `GET /api/schools` - Tüm okullar (Super Admin)

## 🗃️ Veritabanı

### MySQL Bilgileri
- **Host:** db (container içinde) / localhost:3307 (dışarıdan)
- **Database:** school_schedule
- **Username:** laravel_user
- **Password:** laravel_password

### phpMyAdmin
- **URL:** http://localhost:8080
- **Username:** laravel_user
- **Password:** laravel_password

## 🔧 Sorun Giderme

### Container Problemi
```bash
# Container'ları yeniden başlat
docker-compose down
docker-compose up -d

# Cache temizle
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

### Permission Problemi
```bash
# Storage izinleri
docker-compose exec app chmod -R 777 storage
docker-compose exec app chmod -R 777 bootstrap/cache
```

## 📁 Proje Yapısı

```
school-schedule-api/
├── docker-compose.yml      # Docker servisleri
├── Dockerfile             # Laravel container
├── .env                   # Environment ayarları
├── app/                   # Laravel uygulama
├── database/              # Migration & Seeders
├── public/                # Web dosyaları
└── routes/api.php         # API rotaları
```

## ✅ Sistem Özellikleri

- ✅ **Multi-tenant** okul yönetimi
- ✅ **Role-based** yetkilendirme
- ✅ **Sanctum** authentication
- ✅ **81 şehir, 973 ilçe** coğrafi veri
- ✅ **Docker** containerization
- ✅ **MySQL 8.0** veritabanı
- ✅ **Nginx** web server
- ✅ **Redis** cache desteği
- ✅ **phpMyAdmin** arayüzü

---

**🎯 Sistem artık tamamen stabil ve production-ready!**