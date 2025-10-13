# 🛠️ Setup & Demo Scripts

Bu klasördeki setup script'leri sistem kurulumu ve demo verisi oluşturmak için kullanılır.

## 📋 Kullanılabilir Script'ler

### 1. **check-and-create-users.php** ✅ ÖNEMLİ
**Kullanım:** `http://localhost/check-and-create-users.php`

**Açıklama:** Test kullanıcılarını kontrol eder ve eksik olanları oluşturur.

**Ne yapar:**
- Super Admin oluşturur
- Test okulu oluşturur
- Okul müdürü oluşturur
- Test öğretmeni oluşturur

**Ne zaman kullanılır:** İlk kurulum veya test kullanıcıları gerektiğinde

---

### 2. **create-100-schools.php** 🏫 DEMO
**Kullanım:** `http://localhost/create-100-schools.php`

**Açıklama:** Türkiye'nin farklı illerinden 100 rastgele okul oluşturur.

**Ne yapar:**
- 100 okul oluşturur
- Her okul için yönetici hesabı açar
- Rastgele il/ilçe atar
- Rastgele abonelik planı atar

**Not:** Bu script sadece bir kez çalıştırılmalı!

---

### 3. **add-teachers-and-classes.php** 👨‍🏫 DEMO
**Kullanım:** `http://localhost/add-teachers-and-classes.php`

**Açıklama:** Her okula 10 öğretmen ve 12 sınıf ekler.

**Ne yapar:**
- Her okula 10 rastgele öğretmen
- Her okula 12 sınıf (9-A'dan 12-C'ye)
- Her sınıfa rastgele öğretmen atar
- Okul istatistiklerini günceller

**Not:** create-100-schools.php çalıştırıldıktan sonra kullanılır!

---

### 4. **create-test-data.php** 🧪 TEST
**Kullanım:** `http://localhost/create-test-data.php`

**Açıklama:** Basit test verisi oluşturur.

**Ne yapar:**
- 1 okul
- 1 öğretmen
- 1 sınıf

**Ne zaman kullanılır:** Hızlı test için

---

## 🎯 Önerilen Kurulum Sırası

### Seçenek 1: Minimal Test (Hızlı)
```bash
1. http://localhost/check-and-create-users.php
2. http://localhost/create-test-data.php
```

### Seçenek 2: Full Demo (Kapsamlı)
```bash
1. http://localhost/check-and-create-users.php
2. http://localhost/create-100-schools.php
3. http://localhost/add-teachers-and-classes.php
```

---

## 🔐 Test Giriş Bilgileri

### Super Admin
- Email: `admin@schoolschedule.com`
- Şifre: `admin123`

### Okul Müdürü
- Email: `mudur@ataturklisesi.edu.tr`
- Şifre: `mudur123`

### 100 Okul Demo (eğer çalıştırdıysanız)
- Email: `mudur@{okul-adi}1.edu.tr`
- Şifre: `123456`
- Örnek: `mudur@ataturk1.edu.tr`

### Öğretmenler (her okul için)
- Email: Script çıktısında gösterilir
- Şifre: `123456` (hepsi için)

---

## ⚠️ Önemli Notlar

1. **Script'ler sadece geliştirme ortamında kullanılmalı!**
2. **Production'da bu script'ler SİLİNMELİ!**
3. Demo script'leri tekrar çalıştırılırsa duplicate hatası alabilirsiniz
4. Veritabanını sıfırlamak için: `php artisan migrate:fresh --seed`

---

## 🗑️ Production'a Geçerken

Bu dosyaları SİLİN:
- ✅ check-and-create-users.php
- ✅ create-100-schools.php
- ✅ add-teachers-and-classes.php
- ✅ create-test-data.php
- ✅ Bu README dosyası

---

**Son Güncelleme:** 13 Ekim 2025

