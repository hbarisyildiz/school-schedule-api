# 🏫 School Schedule API

**Modern Laravel Okul Ders Programı Yönetim Sistemi**

## 📋 Proje Hakkında

Bu proje, okulların ders programlarını dijital olarak yönetebilmelerini sağlayan kapsamlı bir SaaS (Software as a Service) sistemidir. Laravel 11 ve Vue.js teknolojileri kullanılarak geliştirilmiştir.

## ✨ Özellikler

### 🔐 Kimlik Doğrulama & Yetkilendirme
- **Laravel Sanctum** ile API token bazlı authentication
- **Çok seviyeli rol sistemi** (Super Admin, School Admin, Teacher)
- **Multi-tenant** yapı (her okul kendi verileri)
- **Middleware** tabanlı erişim kontrolü

### 🏢 Okul Yönetimi
- **Basitleştirilmiş kayıt sistemi** (sadece temel bilgiler)
- **Otomatik okul kodu** üretimi
- **Anında aktivasyon** (email doğrulama yok)
- **Hoş geldiniz email sistemi**
- **İl/İlçe** bazlı lokasyon yönetimi

### 📚 Ders Programı
- **Haftalık ders programı** görünümü
- **Öğretmen bazlı** program görüntüleme
- **Sınıf bazlı** program görüntüleme
- **Otomatik program** oluşturma algoritması

### 🌍 Türkiye Coğrafi Veritabanı
- **81 İl** verisi
- **973 İlçe** verisi
- **Güncel ve doğru** coğrafi bilgiler

## 🛠️ Teknologi Stack

### Backend
- **Laravel 11** - PHP Framework
- **MySQL** - Veritabanı
- **Laravel Sanctum** - API Authentication
- **Laravel Mail** - Email sistemi

### Frontend
- **Vue.js 3** - JavaScript Framework
- **Axios** - HTTP Client
- **Bootstrap** benzeri custom CSS

### Development
- **MAMP/Laragon** - Development Environment
- **Composer** - PHP Dependency Manager
- **Git** - Version Control

## 🗄️ Veritabanı Yapısı

### Ana Tablolar
- `users` - Kullanıcılar
- `schools` - Okullar
- `roles` - Roller
- `subjects` - Dersler
- `schedules` - Ders Programları
- `cities` - İller
- `districts` - İlçeler
- `school_registration_requests` - Kayıt Talepleri

## 🚀 Kurulum

### Gereksinimler
- PHP 8.2+
- MySQL 5.7+
- Composer
- Node.js (frontend için)

### Adımlar
```bash
# Repository klonla
git clone https://github.com/username/school-schedule-api.git
cd school-schedule-api

# Dependencies yükle
composer install

# Environment dosyası oluştur
cp .env.example .env

# Uygulama anahtarı oluştur
php artisan key:generate

# Veritabanı konfigürasyonu (.env dosyasında)
DB_DATABASE=school_schedule
DB_USERNAME=root
DB_PASSWORD=

# Veritabanı migration'ları çalıştır
php artisan migrate

# Seed verilerini yükle
php artisan db:seed

# Development server başlat
php artisan serve
```

## 📡 API Endpoints

### Public Endpoints
```
POST /api/register-school     # Okul kaydı
GET  /api/cities             # İl listesi
GET  /api/cities/{id}/districts  # İlçe listesi
POST /api/login              # Giriş
```

### Protected Endpoints
```
GET  /api/user               # Kullanıcı bilgileri
GET  /api/my-school          # Okul bilgileri
GET  /api/schedules          # Ders programları
POST /api/schedules          # Yeni program
GET  /api/subjects           # Dersler
POST /api/users              # Kullanıcı ekleme
```

## 🎨 Frontend Sayfaları

- `school-registration.html` - Okul kayıt formu
- `login.html` - Giriş sayfası
- `admin-panel.html` - Yönetim paneli
- `dashboard.html` - Ana dashboard

## 🧪 Test

```bash
# API test sayfası
http://localhost:8000/api-test.html

# Basit kayıt testi
http://localhost:8000/test-simple-registration.html
```

## 📂 Proje Yapısı

```
school-schedule-api/
├── app/
│   ├── Http/Controllers/Api/    # API Controllers
│   ├── Models/                  # Eloquent Models
│   ├── Mail/                    # Email sınıfları
│   └── Middleware/              # Custom middleware
├── database/
│   ├── migrations/              # Veritabanı migration'ları
│   └── seeders/                 # Seed verileri
├── public/                      # Frontend dosyaları
├── resources/
│   └── views/emails/            # Email template'leri
└── routes/
    └── api.php                  # API route'ları
```

## 🔧 Konfigürasyon

### Email Ayarları (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
```

### Veritabanı Ayarları (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_schedule
DB_USERNAME=root
DB_PASSWORD=
```

## 🚨 Bilinen Sorunlar

- MAMP PHP zip modülü uyarısı (çalışmayı etkilemiyor)
- Server port değişikliği gerekebilir (8000, 8002, 8004 gibi)

## 🤝 Katkıda Bulunma

1. Repository'yi fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Commit yapın (`git commit -m 'Add some AmazingFeature'`)
4. Branch'i push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 👥 Geliştiriciler

- **School Schedule Developer** - *İlk geliştirme* - dev@schoolschedule.com

## 📞 İletişim

- Email: dev@schoolschedule.com
- Project Link: [https://github.com/username/school-schedule-api](https://github.com/username/school-schedule-api)

---

**⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!**

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
