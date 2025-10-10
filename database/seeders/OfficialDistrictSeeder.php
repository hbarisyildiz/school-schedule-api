<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\District;

class OfficialDistrictSeeder extends Seeder
{
    public function run()
    {
        $cities = City::all()->keyBy('name');
        
        // TÜİK 2024 Resmi İl-İlçe Verileri
        $officialDistricts = [
            'İstanbul' => [
                'Adalar', 'Arnavutköy', 'Ataşehir', 'Avcılar', 'Bağcılar', 'Bahçelievler', 
                'Bakırköy', 'Başakşehir', 'Bayrampaşa', 'Beşiktaş', 'Beykoz', 'Beylikdüzü', 
                'Beyoğlu', 'Büyükçekmece', 'Çatalca', 'Çekmeköy', 'Esenler', 'Esenyurt', 
                'Eyüpsultan', 'Fatih', 'Gaziosmanpaşa', 'Güngören', 'Kadıköy', 'Kağıthane', 
                'Kartal', 'Küçükçekmece', 'Maltepe', 'Pendik', 'Sancaktepe', 'Sarıyer', 
                'Silivri', 'Sultanbeyli', 'Sultangazi', 'Şile', 'Şişli', 'Tuzla', 
                'Ümraniye', 'Üsküdar', 'Zeytinburnu'
            ],
            'Ankara' => [
                'Altındağ', 'Ayaş', 'Bala', 'Beypazarı', 'Çamlıdere', 'Çankaya', 'Çubuk', 
                'Elmadağ', 'Etimesgut', 'Evren', 'Gölbaşı', 'Güdül', 'Haymana', 'Kahramankazan', 
                'Kalecik', 'Keçiören', 'Kızılcahamam', 'Mamak', 'Nallıhan', 'Polatlı', 
                'Pursaklar', 'Sincan', 'Şereflikoçhisar', 'Yenimahalle', 'Akyurt'
            ],
            'İzmir' => [
                'Aliağa', 'Balçova', 'Bayındır', 'Bayraklı', 'Bergama', 'Beydağ', 'Bornova', 
                'Buca', 'Çeşme', 'Çiğli', 'Dikili', 'Foça', 'Gaziemir', 'Güzelbahçe', 
                'Karabağlar', 'Karaburun', 'Karşıyaka', 'Kemalpaşa', 'Kınık', 'Kiraz', 
                'Konak', 'Menderes', 'Menemen', 'Narlıdere', 'Ödemiş', 'Seferihisar', 
                'Selçuk', 'Tire', 'Torbalı', 'Urla'
            ],
            'Bursa' => [
                'Büyükorhan', 'Gemlik', 'Gürsu', 'Harmancık', 'İnegöl', 'İznik', 'Karacabey', 
                'Keles', 'Kestel', 'Mudanya', 'Mustafakemalpaşa', 'Nilüfer', 'Orhaneli', 
                'Orhangazi', 'Osmangazi', 'Yenişehir', 'Yıldırım'
            ],
            'Antalya' => [
                'Akseki', 'Aksu', 'Alanya', 'Demre', 'Döşemealtı', 'Elmalı', 'Finike', 
                'Gazipaşa', 'Gündoğmuş', 'İbradı', 'Kaş', 'Kemer', 'Kepez', 'Konyaaltı', 
                'Korkuteli', 'Kumluca', 'Manavgat', 'Muratpaşa', 'Serik'
            ],
            'Adana' => [
                'Aladağ', 'Ceyhan', 'Çukurova', 'Feke', 'İmamoğlu', 'Karaisalı', 'Karataş', 
                'Kozan', 'Pozantı', 'Saimbeyli', 'Sarıçam', 'Seyhan', 'Tufanbeyli', 
                'Yumurtalık', 'Yüreğir'
            ],
            'Konya' => [
                'Ahırlı', 'Akören', 'Akşehir', 'Altınekin', 'Beyşehir', 'Bozkır', 'Cihanbeyli', 
                'Çeltik', 'Çumra', 'Derbent', 'Derebucak', 'Doğanhisar', 'Emirgazi', 'Ereğli', 
                'Güneysınır', 'Hadim', 'Halkapınar', 'Hüyük', 'Ilgın', 'Kadınhanı', 
                'Karapınar', 'Karatay', 'Kulu', 'Meram', 'Sarayönü', 'Selçuklu', 
                'Seydişehir', 'Taşkent', 'Tuzlukçu', 'Yalıhüyük', 'Yunak'
            ],
            'Şanlıurfa' => [
                'Akçakale', 'Birecik', 'Bozova', 'Ceylanpınar', 'Eyyübiye', 'Halfeti', 
                'Haliliye', 'Harran', 'Hilvan', 'Karaköprü', 'Siverek', 'Suruç', 'Viranşehir'
            ],
            'Gaziantep' => [
                'Araban', 'İslahiye', 'Karkamış', 'Nizip', 'Nurdağı', 'Oğuzeli', 
                'Şahinbey', 'Şehitkamil', 'Yavuzeli'
            ],
            'Kocaeli' => [
                'Başiskele', 'Çayırova', 'Darıca', 'Derince', 'Dilovası', 'Gebze', 
                'Gölcük', 'İzmit', 'Kandıra', 'Karamürsel', 'Kartepe', 'Körfez'
            ],
            'Mersin' => [
                'Akdeniz', 'Anamur', 'Aydıncık', 'Bozyazı', 'Çamlıyayla', 'Erdemli', 
                'Gülnar', 'Mezitli', 'Mut', 'Silifke', 'Tarsus', 'Toroslar', 'Yenişehir'
            ],
            'Diyarbakır' => [
                'Bağlar', 'Bismil', 'Çermik', 'Çınar', 'Çüngüş', 'Dicle', 'Eğil', 
                'Ergani', 'Hani', 'Hazro', 'Kocaköy', 'Kulp', 'Lice', 'Silvan', 
                'Sur', 'Yenişehir'
            ],
            'Hatay' => [
                'Altınözü', 'Antakya', 'Arsuz', 'Belen', 'Defne', 'Dörtyol', 'Erzin', 
                'Hassa', 'İskenderun', 'Kırıkhan', 'Kumlu', 'Payas', 'Reyhanlı', 
                'Samandağ', 'Yayladağı'
            ],
            'Manisa' => [
                'Ahmetli', 'Akhisar', 'Alaşehir', 'Demirci', 'Gölmarmara', 'Gördes', 
                'Kırkağaç', 'Köprübaşı', 'Kula', 'Salihli', 'Sarıgöl', 'Saruhanlı', 
                'Selendi', 'Soma', 'Şehzadeler', 'Turgutlu', 'Yunusemre'
            ],
            'Kayseri' => [
                'Akkışla', 'Bünyan', 'Develi', 'Felahiye', 'Hacılar', 'İncesu', 
                'Kocasinan', 'Melikgazi', 'Özvatan', 'Pınarbaşı', 'Sarıoğlan', 
                'Sarız', 'Talas', 'Tomarza', 'Yahyalı', 'Yeşilhisar'
            ],
            'Samsun' => [
                'Alaçam', 'Asarcık', 'Atakum', 'Ayvacık', 'Bafra', 'Canik', 'Çarşamba', 
                'Havza', 'İlkadım', 'Kavak', 'Ladik', 'Ondokuzmayıs', 'Salıpazarı', 
                'Tekkeköy', 'Terme', 'Vezirköprü', 'Yakakent'
            ],
            'Balıkesir' => [
                'Altıeylül', 'Ayvalık', 'Balya', 'Bandırma', 'Bigadiç', 'Burhaniye', 
                'Dursunbey', 'Edremit', 'Erdek', 'Gömeç', 'Gönen', 'Havran', 'İvrindi', 
                'Karesi', 'Kepsut', 'Manyas', 'Marmara', 'Savaştepe', 'Sındırgı', 'Susurluk'
            ],
            'Kahramanmaraş' => [
                'Afşin', 'Andırın', 'Çağlayancerit', 'Dulkadiroğlu', 'Ekinözü', 'Elbistan', 
                'Göksun', 'Nurhak', 'Onikişubat', 'Pazarcık', 'Türkoğlu'
            ],
            'Van' => [
                'Bahçesaray', 'Başkale', 'Çaldıran', 'Çatak', 'Edremit', 'Erciş', 
                'Gevaş', 'Gürpınar', 'İpekyolu', 'Muradiye', 'Özalp', 'Saray', 'Tuşba'
            ],
            'Aydın' => [
                'Bozdoğan', 'Buharkent', 'Çine', 'Didim', 'Efeler', 'Germencik', 
                'İncirliova', 'Karacasu', 'Karpuzlu', 'Koçarlı', 'Köşk', 'Kuşadası', 
                'Kuyucak', 'Nazilli', 'Söke', 'Sultanhisar', 'Yenipazar'
            ],
            'Tekirdağ' => [
                'Çerkezköy', 'Çorlu', 'Ergene', 'Hayrabolu', 'Kapaklı', 'Malkara', 
                'Marmaraereğlisi', 'Muratlı', 'Saray', 'Süleymanpaşa', 'Şarköy'
            ],
            'Adıyaman' => [
                'Adıyaman Merkez', 'Besni', 'Çelikhan', 'Gerger', 'Gölbaşı', 
                'Kahta', 'Samsat', 'Sincik', 'Tut'
            ],
            'Afyonkarahisar' => [
                'Afyonkarahisar Merkez', 'Başmakçı', 'Bayat', 'Bolvadin', 'Çay', 'Çobanlar', 
                'Dazkırı', 'Dinar', 'Emirdağ', 'Evciler', 'Hocalar', 'İhsaniye', 'İscehisar', 
                'Kızılören', 'Sandıklı', 'Sinanpaşa', 'Sultandağı', 'Şuhut'
            ],
            'Ağrı' => [
                'Ağrı Merkez', 'Diyadin', 'Doğubayazıt', 'Eleşkirt', 'Hamur', 
                'Patnos', 'Taşlıçay', 'Tutak'
            ],
            'Amasya' => [
                'Amasya Merkez', 'Göynücek', 'Gümüşhacıköy', 'Hamamözü', 
                'Merzifon', 'Suluova', 'Taşova'
            ],
            'Artvin' => [
                'Ardanuç', 'Arhavi', 'Artvin Merkez', 'Borçka', 'Hopa', 
                'Murgul', 'Şavşat', 'Yusufeli'
            ],
            'Bilecik' => [
                'Bilecik Merkez', 'Bozüyük', 'Gölpazarı', 'İnhisar', 
                'Osmaneli', 'Pazaryeri', 'Söğüt', 'Yenipazar'
            ],
            'Bingöl' => [
                'Adaklı', 'Bingöl Merkez', 'Genç', 'Karlıova', 
                'Kiğı', 'Solhan', 'Yayladere', 'Yedisu'
            ],
            'Bitlis' => [
                'Adilcevaz', 'Ahlat', 'Bitlis Merkez', 'Güroymak', 
                'Hizan', 'Mutki', 'Tatvan'
            ],
            'Bolu' => [
                'Bolu Merkez', 'Dörtdivan', 'Gerede', 'Göynük', 'Kıbrıscık', 
                'Mengen', 'Mudurnu', 'Seben', 'Yeniçağa'
            ],
            'Burdur' => [
                'Ağlasun', 'Altınyayla', 'Bucak', 'Burdur Merkez', 'Çavdır', 
                'Çeltikçi', 'Gölhisar', 'Karamanlı', 'Kemer', 'Tefenni', 'Yeşilova'
            ],
            'Çanakkale' => [
                'Ayvacık', 'Bayramiç', 'Biga', 'Bozcaada', 'Çan', 'Çanakkale Merkez', 
                'Eceabat', 'Ezine', 'Gelibolu', 'Gökçeada', 'Lapseki', 'Yenice'
            ],
            'Çankırı' => [
                'Atkaracalar', 'Bayramören', 'Çankırı Merkez', 'Çerkeş', 'Eldivan', 
                'Ilgaz', 'Kızılırmak', 'Korgun', 'Kurşunlu', 'Orta', 'Şabanözü', 'Yapraklı'
            ],
            'Çorum' => [
                'Alaca', 'Bayat', 'Boğazkale', 'Çorum Merkez', 'Dodurga', 'İskilip', 
                'Kargı', 'Laçin', 'Mecitözü', 'Oğuzlar', 'Ortaköy', 'Osmancık', 
                'Sungurlu', 'Uğurludağ'
            ],
            'Denizli' => [
                'Acıpayam', 'Babadağ', 'Baklan', 'Bekilli', 'Beyağaç', 'Bozkurt', 
                'Buldan', 'Çal', 'Çameli', 'Çardak', 'Çivril', 'Güney', 'Honaz', 
                'Kale', 'Merkezefendi', 'Pamukkale', 'Sarayköy', 'Serinhisar', 'Tavas'
            ],
            'Edirne' => [
                'Edirne Merkez', 'Enez', 'Havsa', 'İpsala', 'Keşan', 
                'Lalapaşa', 'Meriç', 'Süloğlu', 'Uzunköprü'
            ],
            'Elazığ' => [
                'Ağın', 'Alacakaya', 'Arıcak', 'Baskil', 'Elazığ Merkez', 
                'Karakoçan', 'Keban', 'Kovancılar', 'Maden', 'Palu', 'Sivrice'
            ],
            'Erzincan' => [
                'Çayırlı', 'Erzincan Merkez', 'İliç', 'Kemah', 'Kemaliye', 
                'Otlukbeli', 'Refahiye', 'Tercan', 'Üzümlü'
            ],
            'Erzurum' => [
                'Aşkale', 'Aziziye', 'Çat', 'Hınıs', 'Horasan', 'İspir', 'Karaçoban', 
                'Karayazı', 'Köprüköy', 'Narman', 'Oltu', 'Olur', 'Palandöken', 
                'Pasinler', 'Pazaryolu', 'Şenkaya', 'Tekman', 'Tortum', 'Uzundere', 'Yakutiye'
            ],
            'Eskişehir' => [
                'Alpu', 'Beylikova', 'Çifteler', 'Günyüzü', 'Han', 'İnönü', 
                'Mahmudiye', 'Mihalgazi', 'Mihalıççık', 'Odunpazarı', 'Sarıcakaya', 
                'Seyitgazi', 'Sivrihisar', 'Tepebaşı'
            ],
            'Giresun' => [
                'Alucra', 'Bulancak', 'Çamoluk', 'Çanakçı', 'Dereli', 'Doğankent', 
                'Espiye', 'Eynesil', 'Giresun Merkez', 'Görele', 'Güce', 'Keşap', 
                'Piraziz', 'Şebinkarahisar', 'Tirebolu', 'Yağlıdere'
            ],
            'Gümüşhane' => [
                'Gümüşhane Merkez', 'Kelkit', 'Köse', 'Kürtün', 'Şiran', 'Torul'
            ],
            'Hakkâri' => [
                'Çukurca', 'Hakkâri Merkez', 'Şemdinli', 'Yüksekova'
            ],
            'Isparta' => [
                'Aksu', 'Atabey', 'Eğirdir', 'Gelendost', 'Gönen', 'Isparta Merkez', 
                'Keçiborlu', 'Senirkent', 'Sütçüler', 'Şarkikaraağaç', 'Uluborlu', 
                'Yalvaç', 'Yenişarbademli'
            ],
            'Kars' => [
                'Akyaka', 'Arpaçay', 'Digor', 'Kağızman', 'Kars Merkez', 
                'Sarıkamış', 'Selim', 'Susuz'
            ],
            'Kastamonu' => [
                'Abana', 'Ağlı', 'Araç', 'Azdavay', 'Bozkurt', 'Cide', 'Çatalzeytin', 
                'Daday', 'Devrekani', 'Doğanyurt', 'Hanönü', 'İhsangazi', 'İnebolu', 
                'Kastamonu Merkez', 'Küre', 'Pınarbaşı', 'Seydiler', 'Şenpazar', 
                'Taşköprü', 'Tosya'
            ],
            'Kırklareli' => [
                'Babaeski', 'Demirköy', 'Kırklareli Merkez', 'Kofçaz', 
                'Lüleburgaz', 'Pehlivanköy', 'Pınarhisar', 'Vize'
            ],
            'Kırşehir' => [
                'Akçakent', 'Akpınar', 'Boztepe', 'Çiçekdağı', 
                'Kaman', 'Kırşehir Merkez', 'Mucur'
            ],
            'Kütahya' => [
                'Altıntaş', 'Aslanapa', 'Çavdarhisar', 'Domaniç', 'Dumlupınar', 
                'Emet', 'Gediz', 'Hisarcık', 'Kütahya Merkez', 'Pazarlar', 
                'Simav', 'Şaphane', 'Tavşanlı'
            ],
            'Malatya' => [
                'Akçadağ', 'Arapgir', 'Arguvan', 'Battalgazi', 'Darende', 
                'Doğanşehir', 'Doğanyol', 'Hekimhan', 'Kale', 'Kuluncak', 
                'Pütürge', 'Yazıhan', 'Yeşilyurt'
            ],
            'Mardin' => [
                'Artuklu', 'Dargeçit', 'Derik', 'Kızıltepe', 'Mazıdağı', 
                'Midyat', 'Nusaybin', 'Ömerli', 'Savur', 'Yeşilli'
            ],
            'Muğla' => [
                'Bodrum', 'Dalaman', 'Datça', 'Fethiye', 'Kavaklıdere', 'Köyceğiz', 
                'Marmaris', 'Menteşe', 'Milas', 'Ortaca', 'Seydikemer', 'Ula', 'Yatağan'
            ],
            'Muş' => [
                'Bulanık', 'Hasköy', 'Korkut', 'Malazgirt', 'Muş Merkez', 'Varto'
            ],
            'Nevşehir' => [
                'Acıgöl', 'Avanos', 'Derinkuyu', 'Gülşehir', 'Hacıbektaş', 
                'Kozaklı', 'Nevşehir Merkez', 'Ürgüp'
            ],
            'Niğde' => [
                'Altunhisar', 'Bor', 'Çamardı', 'Çiftlik', 'Niğde Merkez', 'Ulukışla'
            ],
            'Ordu' => [
                'Akkuş', 'Altınordu', 'Aybasti', 'Çamaş', 'Çatalpınar', 'Çaybaşı', 
                'Fatsa', 'Gölköy', 'Gülyalı', 'Gürgentepe', 'İkizce', 'Kabadüz', 
                'Kabataş', 'Korgan', 'Kumru', 'Mesudiye', 'Perşembe', 'Ulubey', 'Ünye'
            ],
            'Rize' => [
                'Ardeşen', 'Çamlıhemşin', 'Çayeli', 'Derepazarı', 'Fındıklı', 
                'Güneysu', 'Hemşin', 'İkizdere', 'İyidere', 'Kalkandere', 
                'Pazar', 'Rize Merkez'
            ],
            'Sakarya' => [
                'Adapazarı', 'Akyazı', 'Arifiye', 'Erenler', 'Ferizli', 'Geyve', 
                'Hendek', 'Karapürçek', 'Karasu', 'Kaynarca', 'Kocaali', 'Pamukova', 
                'Sapanca', 'Serdivan', 'Söğütlü', 'Taraklı'
            ],
            'Siirt' => [
                'Baykan', 'Eruh', 'Kurtalan', 'Pervari', 'Siirt Merkez', 'Şirvan', 'Tillo'
            ],
            'Sinop' => [
                'Ayancık', 'Boyabat', 'Dikmen', 'Durağan', 'Erfelek', 
                'Gerze', 'Saraydüzü', 'Sinop Merkez', 'Türkeli'
            ],
            'Sivas' => [
                'Akıncılar', 'Altınyayla', 'Divriği', 'Doğanşar', 'Gemerek', 'Gölova', 
                'Gürün', 'Hafik', 'İmranlı', 'Kangal', 'Koyulhisar', 'Sivas Merkez', 
                'Suşehri', 'Şarkışla', 'Ulaş', 'Yıldızeli', 'Zara'
            ],
            'Tokat' => [
                'Almus', 'Artova', 'Başçiftlik', 'Erbaa', 'Niksar', 'Pazar', 
                'Reşadiye', 'Sulusaray', 'Tokat Merkez', 'Turhal', 'Yeşilyurt', 'Zile'
            ],
            'Trabzon' => [
                'Akçaabat', 'Araklı', 'Arsin', 'Beşikdüzü', 'Çarşıbaşı', 'Çaykara', 
                'Dernekpazarı', 'Düzköy', 'Hayrat', 'Köprübaşı', 'Maçka', 'Of', 
                'Ortahisar', 'Sürmene', 'Şalpazarı', 'Tonya', 'Vakfıkebir', 'Yomra'
            ],
            'Tunceli' => [
                'Çemişgezek', 'Hozat', 'Mazgirt', 'Nazımiye', 'Ovacık', 
                'Pertek', 'Pülümür', 'Tunceli Merkez'
            ],
            'Uşak' => [
                'Banaz', 'Eşme', 'Karahallı', 'Sivaslı', 'Ulubey', 'Uşak Merkez'
            ],
            'Yozgat' => [
                'Akdağmadeni', 'Aydıncık', 'Boğazlıyan', 'Çandır', 'Çayıralan', 
                'Çekerek', 'Kadışehri', 'Saraykent', 'Sarıkaya', 'Sorgun', 
                'Şefaatli', 'Yenifakılı', 'Yerköy', 'Yozgat Merkez'
            ],
            'Zonguldak' => [
                'Alaplı', 'Çaycuma', 'Devrek', 'Gökçebey', 'Kilimli', 
                'Kozlu', 'Zonguldak Merkez', 'Ereğli'
            ],
            'Aksaray' => [
                'Ağaçören', 'Aksaray Merkez', 'Eskil', 'Gülağaç', 
                'Güzelyurt', 'Ortaköy', 'Sarıyahşi'
            ],
            'Bayburt' => [
                'Aydıntepe', 'Bayburt Merkez', 'Demirözü'
            ],
            'Karaman' => [
                'Ayrancı', 'Başyayla', 'Ermenek', 'Karaman Merkez', 
                'Kazımkarabekir', 'Sarıveliler'
            ],
            'Kırıkkale' => [
                'Bahşili', 'Balışeyh', 'Çelebi', 'Delice', 'Karakeçili', 
                'Keskin', 'Kırıkkale Merkez', 'Sulakyurt', 'Yahşihan'
            ],
            'Batman' => [
                'Batman Merkez', 'Beşiri', 'Gercüş', 'Hasankeyf', 'Kozluk', 'Sason'
            ],
            'Şırnak' => [
                'Beytüşşebap', 'Cizre', 'Güçlükonak', 'İdil', 'Silopi', 
                'Şırnak Merkez', 'Uludere'
            ],
            'Bartın' => [
                'Amasra', 'Bartın Merkez', 'Kurucaşile', 'Ulus'
            ],
            'Ardahan' => [
                'Ardahan Merkez', 'Çıldır', 'Damal', 'Göle', 'Hanak', 'Posof'
            ],
            'Iğdır' => [
                'Aralık', 'Iğdır Merkez', 'Karakoyunlu', 'Tuzluca'
            ],
            'Yalova' => [
                'Altınova', 'Armutlu', 'Çınarcık', 'Çiftlikköy', 'Termal', 'Yalova Merkez'
            ],
            'Karabük' => [
                'Eflani', 'Eskipazar', 'Karabük Merkez', 'Ovacık', 'Safranbolu', 'Yenice'
            ],
            'Kilis' => [
                'Elbeyli', 'Kilis Merkez', 'Musabeyli', 'Polateli'
            ],
            'Osmaniye' => [
                'Bahçe', 'Düziçi', 'Hasanbeyli', 'Kadirli', 'Osmaniye Merkez', 
                'Sumbas', 'Toprakkale'
            ],
            'Düzce' => [
                'Akçakoca', 'Cumayeri', 'Çilimli', 'Düzce Merkez', 
                'Gölyaka', 'Gümüşova', 'Kaynaşlı', 'Yığılca'
            ]
        ];

        foreach ($officialDistricts as $cityName => $districts) {
            if (!isset($cities[$cityName])) {
                echo "City not found: $cityName\n";
                continue;
            }

            $cityId = $cities[$cityName]->id;
            
            foreach ($districts as $districtName) {
                District::create([
                    'city_id' => $cityId,
                    'name' => $districtName
                ]);
            }
            
            echo "Completed: $cityName (" . count($districts) . " districts)\n";
        }
        
        echo "\nTotal districts created: " . District::count() . "\n";
    }
}