# Öğretmen Toplu Yükleme Şablonları

Bu klasörde öğretmen toplu yükleme için şablon dosyaları bulunmaktadır.

## Kullanım

1. Aşağıdaki şablon dosyalardan birini indirin:
   - `ogretmen-sablonu.xlsx` - Excel formatı
   - `ogretmen-sablonu.csv` - CSV formatı

2. Dosyayı açın ve örnek verileri silin.

3. Kendi öğretmen bilgilerinizi doldurun:
   - **ad_soyad** (zorunlu): Öğretmenin adı soyadı
   - **email** (zorunlu): Benzersiz email adresi
   - **brans** (zorunlu): Öğretmenin branşı (örn: Matematik, Türkçe, İngilizce)
   - **kisa_ad** (opsiyonel): 6 karakterlik kısa ad. Boş bırakılırsa otomatik oluşturulur.
   - **telefon** (opsiyonel): Telefon numarası

4. Admin panelinde "📤 Excel Yükle" butonuna tıklayın.

5. Doldurduğunuz dosyayı seçin ve yükleyin.

## Önemli Notlar

- ✅ İlk satır (başlıklar) mutlaka korunmalıdır
- ✅ Email adresleri benzersiz olmalıdır
- ✅ Varsayılan şifre: `12345678` (öğretmenler ilk girişte değiştirmelidir)
- ⚠️ Hatalı satırlar atlanır, başarılı olanlar eklenir
- ⚠️ Maksimum dosya boyutu: 2MB

## Örnek Satır

```
Ahmet Yılmaz,ahmet.yilmaz@okul.com,Matematik,AHMYIL,5551234567
```

## Branş Örnekleri

- Matematik
- Türkçe
- İngilizce
- Fen Bilimleri
- Sosyal Bilgiler
- Beden Eğitimi
- Müzik
- Görsel Sanatlar
- Din Kültürü ve Ahlak Bilgisi
- Teknoloji ve Tasarım

