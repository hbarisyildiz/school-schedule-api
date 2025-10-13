# 🏫 Okul Ders Programı Sistemi

**Docker ile Modern Laravel Okul Yönetim Sistemi**

> **🎯 Tamamen stabil Docker ortamı ile geliştirme ve production ready!**

## 📋 Proje Hakkında

Bu proje, okulların ders programlarını dijital olarak yönetebilmelerini sağlayan kapsamlı bir SaaS (Software as a Service) sistemidir. Laravel 11 ve Docker teknolojileri kullanılarak geliştirilmiştir.

## ✨ Ana Özellikler

### � Docker Stack
- **Laravel 11** - PHP 8.3-FPM
- **MySQL 8.0** - Veritabanı
- **Nginx Alpine** - Web server
- **Redis** - Cache & Session
- **phpMyAdmin** - DB yönetimi

### 🔐 Authentication & Authorization
- **Laravel Sanctum** - API token authentication
- **Multi-tenant** - Her okul kendi verisi
- **Role-based** - Super Admin, Okul Müdürü, Öğretmen

### 🌍 Türkiye Coğrafi Veritabanı
- ✅ **81 İl** verisi
- ✅ **973 İlçe** verisi
- ✅ **Güncel ve doğru** coğrafi bilgiler

## � Hızlı Kurulum

### Ön Gereksinimler
- Docker Desktop (Windows/Mac/Linux)
- Git

### Kurulum Adımları
```bash
# 1. Repository klonla
git clone <repository-url>
cd school-schedule-api

# 2. Docker containers başlat
docker-compose up -d

# 3. İlk kurulum
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## � Erişim URL'leri

| Servis | URL | Açıklama |
|--------|-----|----------|
| **Ana Uygulama** | http://localhost | Web interface |
| **phpMyAdmin** | http://localhost:8080 | DB yönetimi |
| **API** | http://localhost/api | REST API |

## 👤 Test Kullanıcıları

| Rol | Email | Şifre | Durum |
|-----|-------|-------|-------|
| **Super Admin** | admin@schoolschedule.com | admin123 | ✅ Aktif |
| **Okul Müdürü** | mudur@ataturklisesi.edu.tr | mudur123 | ✅ Aktif |
| **Öğretmen** | ayse.yilmaz@ataturklisesi.edu.tr | teacher123 | ✅ Aktif |

**AWS Deployment:** http://18.193.119.170

## � API Endpoints

### Public
- `GET /api/cities` - Türkiye şehirleri
- `GET /api/districts/{city_id}` - İlçeler
- `POST /api/auth/login` - Giriş

### Protected (Token Required)
- `GET /api/user` - Kullanıcı bilgileri
- `GET /api/my-school` - Okul bilgileri
- `GET /api/schools` - Tüm okullar (Super Admin)
- `GET /api/school/settings` - Okul ayarları (YENİ!)
- `PUT /api/school/settings` - Okul ayarlarını güncelle (YENİ!)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
