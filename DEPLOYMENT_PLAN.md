# 🚀 Deployment & Canlıya Alma Planı

**Oluşturma Tarihi:** 13 Ekim 2025  
**Durum:** Planlama Aşaması  
**Hedef:** Production'a alma

---

## 🎯 SUNUCU KARARI

### Tavsiye Edilen: DigitalOcean + Laravel Forge

**Maliyet:**
- DigitalOcean Droplet: $12/ay (2GB RAM, 1 vCPU)
- Laravel Forge: $12/ay (Yönetim paneli)
- **Toplam: ~$24/ay (~₺800)**

**Avantajları:**
- ✅ GitHub push = otomatik deploy
- ✅ SSL otomatik (Let's Encrypt)
- ✅ Zero downtime deployment
- ✅ Database backup otomatik
- ✅ Queue worker otomatik
- ✅ Scheduled tasks otomatik

**Alternatif Ucuz:** Vultr VPS ($12/ay, manuel kurulum)

---

## 📋 DEPLOYMENT CHECKLIST

### Hazırlık Aşaması (Önce Yapılacaklar)

#### 1. Kod Hazırlığı
- [ ] `.env.example` dosyasını kontrol et
- [ ] Production ayarlarını ekle
- [ ] Debug mode kapalı olacak şekilde ayarla
- [ ] Database credentials template'i hazırla
- [ ] SMTP ayarlarını hazırla

#### 2. Güvenlik Kontrolleri
- [ ] `APP_DEBUG=false` kontrol
- [ ] Strong secret key oluştur
- [ ] Database şifreleri güçlü olsun
- [ ] CORS ayarları production için
- [ ] Rate limiting aktif
- [ ] CSRF koruması aktif

#### 3. Optimizasyon Hazırlığı
- [ ] Config cache script hazırla
- [ ] Route cache script hazırla
- [ ] View cache script hazırla
- [ ] Autoloader optimize script hazırla
- [ ] Asset minify kontrol

---

## 🔧 DEPLOYMENT ADIMLARI

### Adım 1: Sunucu Kurulumu (1 saat)

**DigitalOcean:**
1. [ ] Hesap aç: https://digitalocean.com
2. [ ] Droplet oluştur:
   - Ubuntu 22.04 LTS
   - 2GB RAM, 1 vCPU
   - Lokasyon: Frankfurt (Avrupa) veya Singapore (Asya)
3. [ ] SSH Key ekle
4. [ ] Firewall ayarları:
   - Port 22 (SSH)
   - Port 80 (HTTP)
   - Port 443 (HTTPS)

**Laravel Forge:**
1. [ ] Hesap aç: https://forge.laravel.com
2. [ ] 5 günlük trial başlat
3. [ ] DigitalOcean API token ekle
4. [ ] Sunucuyu Forge'a ekle

---

### Adım 2: Site Kurulumu (30 dk)

1. [ ] Forge'da yeni site oluştur
   - Domain: `yourdomain.com`
   - PHP version: 8.3
   - Database: MySQL 8.0

2. [ ] GitHub repo bağla
   - Repo: `hbarisyildiz/school-schedule-api`
   - Branch: `master`
   - Deploy key ekle

3. [ ] Environment variables (.env)
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_HOST=localhost
   DB_DATABASE=forge
   DB_USERNAME=forge
   DB_PASSWORD=FORGE_GENERATED
   
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   SESSION_DRIVER=redis
   
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your@email.com
   MAIL_PASSWORD=your_app_password
   ```

4. [ ] Database oluştur
   - Forge panelinden otomatik

---

### Adım 3: İlk Deployment (15 dk)

1. [ ] Deploy script güncelle (Forge'da):
   ```bash
   cd /home/forge/yourdomain.com
   git pull origin $FORGE_SITE_BRANCH
   
   $FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   
   ( flock -w 10 9 || exit 1
       echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock
   
   if [ -f artisan ]; then
       $FORGE_PHP artisan migrate --force
       $FORGE_PHP artisan config:cache
       $FORGE_PHP artisan route:cache
       $FORGE_PHP artisan view:cache
       $FORGE_PHP artisan queue:restart
   fi
   ```

2. [ ] İlk deploy yap
   - "Deploy Now" butonuna tıkla
   - Ya da git push yap

3. [ ] Database seed
   ```bash
   php artisan db:seed --class=RoleSeeder
   php artisan db:seed --class=SubscriptionPlanSeeder
   php artisan db:seed --class=CitySeeder
   php artisan db:seed --class=CompleteDistrictSeeder
   ```

---

### Adım 4: SSL & Domain (10 dk)

1. [ ] Domain ayarları
   - A Record: Droplet IP'si
   - CNAME www: yourdomain.com

2. [ ] SSL Sertifikası
   - Forge'da "SSL" sekmesi
   - Let's Encrypt (Ücretsiz)
   - "Obtain Certificate" tıkla
   - Auto-renew aktif

3. [ ] HTTPS Redirect
   - Nginx config'de force HTTPS

---

### Adım 5: Queue & Scheduler (10 dk)

1. [ ] Queue Worker
   - Forge'da "Queue" sekmesi
   - Worker ekle:
     ```
     Connection: redis
     Queue: default
     Processes: 1
     ```

2. [ ] Scheduler (Cron)
   - Forge otomatik ekler:
     ```
     * * * * * php /home/forge/site.com/artisan schedule:run >> /dev/null 2>&1
     ```

3. [ ] Test queue:
   ```bash
   php artisan queue:work --once
   ```

---

### Adım 6: Monitoring & Backup (20 dk)

1. [ ] Database Backup
   - Forge'da "Backup" sekmesi
   - S3 bucket ekle (opsiyonel)
   - Daily backup aktif

2. [ ] Monitoring
   - Forge'da "Monitoring" aktif
   - Email alert ayarla

3. [ ] Log Management
   - Laravel Log Viewer (opsiyonel)
   - Papertrail (opsiyonel)

---

## 🔍 TEST CHECKLIST

### Production Testleri

- [ ] Ana sayfa yükleniyor
- [ ] Admin panel açılıyor
- [ ] Login çalışıyor
- [ ] API endpoints çalışıyor
- [ ] HTTPS aktif
- [ ] SSL geçerli
- [ ] Database bağlantısı OK
- [ ] Redis çalışıyor
- [ ] Queue çalışıyor
- [ ] Email gidiyor
- [ ] File upload çalışıyor
- [ ] Responsive mobil OK

### Performance Testleri

- [ ] Sayfa yükleme < 2 saniye
- [ ] API response < 500ms
- [ ] Database queries optimize
- [ ] Cache çalışıyor
- [ ] Asset minify edilmiş

---

## ⚡ OPTIMIZASYON KOMUTLARI

### Production'da Çalıştır

```bash
# 1. Composer optimize
composer install --optimize-autoloader --no-dev

# 2. Laravel optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Opcache aktif
# (Forge otomatik aktif eder)

# 4. Redis cache
php artisan cache:clear
```

---

## 🔒 GÜVENLİK KONTROL LİSTESİ

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY`
- [ ] Database şifreleri güçlü
- [ ] HTTPS zorunlu
- [ ] CORS ayarları doğru
- [ ] Rate limiting aktif
- [ ] CSRF koruması aktif
- [ ] SQL injection koruması
- [ ] XSS koruması
- [ ] File upload validation
- [ ] API token rotation
- [ ] Session timeout ayarı
- [ ] Failed login attempts limit

---

## 📊 SUNUCU BOYUTLANDIRMA

### Başlangıç (1-10 Okul) - MEVCUT
```
CPU: 1 vCPU
RAM: 2GB
Disk: 50GB SSD
Bandwidth: 2TB
Fiyat: $12/ay
```

### Scale Up Planı (10-50 Okul)
```
CPU: 2 vCPU
RAM: 4GB
Disk: 80GB SSD
Fiyat: $24/ay
+ Redis ekle
```

### Scale Out Planı (50+ Okul)
```
Load Balancer: $12/ay
App Server x2: $24/ay
Database Server: $24/ay
Redis Server: $15/ay
Toplam: ~$75/ay
```

---

## 📝 SONRADAN YAPILACAKLAR

### Phase 1: İlk Hafta
- [ ] Production'da test et
- [ ] Hataları düzelt
- [ ] Performance monitoring
- [ ] Backup test et

### Phase 2: İlk Ay
- [ ] CDN ekle (Cloudflare - Ücretsiz)
- [ ] Image optimization
- [ ] Database optimize
- [ ] Monitoring alerts ayarla

### Phase 3: Sonrası
- [ ] Auto-scaling
- [ ] Load balancer
- [ ] Multiple regions
- [ ] Advanced monitoring

---

## 💰 MALİYET HESAPLAMA

### İlk Ay (Setup)
| Hizmet | Fiyat |
|--------|-------|
| DigitalOcean Droplet | $12 |
| Laravel Forge | $12 (5 gün trial) |
| Domain (.com.tr) | ~₺100 |
| **Toplam** | **~₺900** |

### Aylık Sürekli
| Hizmet | Fiyat |
|--------|-------|
| DigitalOcean | $12 |
| Laravel Forge | $12 |
| **Toplam** | **$24 (~₺800)** |

### Scale Edildiğinde (50+ Okul)
| Hizmet | Fiyat |
|--------|-------|
| Load Balancer | $12 |
| App Servers (x2) | $24 |
| Database Server | $24 |
| Redis | $15 |
| **Toplam** | **$75 (~₺2,500)** |

---

## 🚨 SORUN GİDERME

### Yaygın Sorunlar

**1. 500 Internal Server Error**
```bash
# Log kontrol
tail -f storage/logs/laravel.log

# Permission fix
chmod -R 775 storage bootstrap/cache
```

**2. Database Connection Error**
```bash
# .env kontrol
cat .env | grep DB_

# MySQL çalışıyor mu?
sudo service mysql status
```

**3. Queue Çalışmıyor**
```bash
# Queue restart
php artisan queue:restart

# Worker kontrol
php artisan queue:work --once
```

---

## 📞 DESTEK KAYNAKLARI

- **Laravel Forge Docs:** https://forge.laravel.com/docs
- **DigitalOcean Tutorials:** https://www.digitalocean.com/community/tutorials
- **Laravel Deployment:** https://laravel.com/docs/deployment

---

## ✅ DEPLOYMENT DURUMU

- [ ] Sunucu seçildi
- [ ] Hesaplar oluşturuldu
- [ ] Kod hazırlandı
- [ ] Deploy edildi
- [ ] SSL aktif
- [ ] Test edildi
- [ ] Backup ayarlandı
- [ ] Monitoring aktif
- [ ] **CANLI! 🎉**

---

**Not:** Bu dosyayı deployment yaparken kontrol listesi olarak kullanın!

**Hazırlayan:** AI Assistant  
**Son Güncelleme:** 13 Ekim 2025

