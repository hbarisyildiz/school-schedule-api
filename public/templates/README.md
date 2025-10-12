# Öğretmen Toplu Yükleme Şablonu

Bu klasör, öğretmen toplu yükleme için Excel şablon indirme endpoint'ini barındırır.

## Kullanım

1. Admin panelde "👥 Kullanıcılar" sekmesine gidin
2. **"📥 Excel Şablonu İndir (.xlsx)"** butonuna tıklayın
3. İndirilen Excel dosyasını açın

## Excel Şablonu İçeriği

### Kolonlar:
- **ad_soyad*** (Zorunlu): Öğretmenin adı soyadı
- **email*** (Zorunlu): Benzersiz email adresi
- **brans*** (Zorunlu): Öğretmenin branşı
- **kisa_ad** (Opsiyonel): 6 karakterlik kısa ad (boş bırakılırsa otomatik oluşturulur)
- **telefon** (Opsiyonel): Telefon numarası

### Örnek Veriler:
Şablon içinde örnek 5 öğretmen verisi bulunur. Bu verileri silin ve kendi verilerinizi ekleyin.

### Önemli Notlar:
- ✅ Başlık satırını (1. satır) silmeyin
- ✅ Örnek verileri silin (açık mavi renkli satırlar)
- ✅ Email adresleri benzersiz olmalıdır
- ✅ Varsayılan şifre: `123456` (öğretmenler ilk girişte değiştirmelidir)
- ✅ Maksimum dosya boyutu: 2MB
- ⚠️ Hatalı satırlar atlanır, başarılı olanlar eklenir

## Dinamik Excel Oluşturma

Excel şablonu `/api/templates/teacher-import` endpoint'i üzerinden dinamik olarak oluşturulur:
- Profesyonel başlık formatı (mavi arka plan, beyaz yazı)
- Örnek veriler (açık mavi arka plan)
- Kullanım talimatları (alt kısımda)
- Otomatik kolon genişlikleri
- Tarihli dosya adı: `ogretmen_sablonu_YYYY-MM-DD.xlsx`

## Teknik Detaylar

**Controller**: `App\Http\Controllers\Api\TemplateController@downloadTeacherTemplate`
**Package**: `PhpOffice\PhpSpreadsheet`
**Format**: `.xlsx` (Excel 2007+)

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
- Bilişim Teknolojileri
- Rehberlik

---

**Not**: Excel şablonu her indirmede güncel tarihle oluşturulur.
