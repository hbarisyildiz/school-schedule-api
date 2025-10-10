# GitHub Repository Oluşturma Rehberi

## 📋 Adımlar

### 1. GitHub'da Yeni Repository Oluşturun

1. **GitHub.com**'a gidin ve giriş yapın
2. **"New repository"** butonuna tıklayın
3. Repository ayarları:
   - **Repository name:** `school-schedule-api`
   - **Description:** `Laravel tabanlı okul ders programı yönetim sistemi`
   - **Visibility:** `Public` (veya Private)
   - **Initialize:** `Boş bırakın` (zaten kodumuz var)

### 2. Repository'yi Bağlayın

Terminal'de şu komutları çalıştırın:

```bash
cd "C:\MAMP\htdocs\dersProg\school-schedule-api"

# GitHub repository'yi remote olarak ekle
git remote add origin https://github.com/KULLANICI_ADI/school-schedule-api.git

# Ana branch'i main olarak ayarla (GitHub standardı)
git branch -M main

# İlk push
git push -u origin main
```

### 3. Repository URL'ini Değiştirin

GitHub'da repository oluşturduktan sonra URL'yi değiştirin:

**Eski:**
```
https://github.com/username/school-schedule-api
```

**Yeni:**
```
https://github.com/GERÇEK_KULLANICI_ADI/school-schedule-api
```

### 4. README'deki Linkleri Güncelleyin

Repository oluşturduktan sonra README.md'deki linkleri güncelleyin:

```bash
git add README.md
git commit -m "docs: Update repository links"
git push
```

## 🔄 Güncellemeler için

Gelecekte değişiklik yaptığınızda:

```bash
git add .
git commit -m "feat: Yeni özellik açıklaması"
git push
```

## 📦 Release Oluşturma

Stable sürüm için:

1. GitHub'da **"Releases"** sekmesine gidin
2. **"Create a new release"** tıklayın
3. **Tag:** `v1.0.0`
4. **Title:** `İlk Stabil Sürüm`
5. **Description:** Değişiklikleri yazın
6. **"Publish release"** tıklayın

## 🎯 Repository Özellikleri

### Etiketler Ekleyin:
- `laravel`
- `php`
- `vuejs`
- `school-management`
- `schedule-system`
- `saas`
- `multi-tenant`

### Branch Protection:
- `main` branch'ini koruma altına alın
- Pull request gerektirin
- Code review zorunlu yapın

## 📊 GitHub Actions (Opsiyonel)

CI/CD için `.github/workflows/laravel.yml` ekleyin:

```yaml
name: Laravel

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  laravel-tests:
    runs-on: ubuntu-latest

    steps:
    - uses: shivammathur/setup-php@15c43e89cdef867065b0213be354c2841860869e
      with:
        php-version: '8.2'
    - uses: actions/checkout@v3
    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"
    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
    - name: Generate key
      run: php artisan key:generate
    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache
    - name: Create Database
      run: |
        mkdir -p database
        touch database/database.sqlite
    - name: Execute tests (Unit and Feature tests) via PHPUnit
      env:
        DB_CONNECTION: sqlite
        DB_DATABASE: database/database.sqlite
      run: vendor/bin/phpunit
```