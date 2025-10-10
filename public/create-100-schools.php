<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Laravel app bootstrap
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "=== 100 Okul Ekleme İşlemi Başlatılıyor ===\n\n";

try {
    // Abonelik planlarını al
    $plans = SubscriptionPlan::all();
    $schoolAdminRole = Role::where('name', 'school_admin')->first();
    $teacherRole = Role::where('name', 'teacher')->first();
    
    if ($plans->isEmpty()) {
        echo "❌ Abonelik planları bulunamadı!\n";
        exit;
    }

    // Türkiye şehirleri
    $cities = [
        'Ankara', 'İstanbul', 'İzmir', 'Bursa', 'Adana', 'Gaziantep', 'Konya', 'Antalya', 
        'Diyarbakır', 'Mersin', 'Kayseri', 'Eskişehir', 'Samsun', 'Denizli', 'Şanlıurfa',
        'Adapazarı', 'Malatya', 'Kahramanmaraş', 'Erzurum', 'Van', 'Batman', 'Elazığ',
        'İzmit', 'Manisa', 'Sivas', 'Gebze', 'Balıkesir', 'Tarsus', 'Kütahya', 'Trabzon',
        'Çorum', 'Adıyaman', 'Osmaniye', 'Kırıkkale', 'Antakya', 'Aydın', 'İskenderun',
        'Uşak', 'Aksaray', 'Afyon', 'Isparta', 'İnegöl', 'Tekirdağ', 'Edirne', 'Darıca',
        'Ordu', 'Karaman', 'Gölcük', 'Siirt', 'Körfez', 'Kızıltepe', 'Düzce', 'Tokat'
    ];

    // Okul türleri
    $schoolTypes = [
        'İlkokulu', 'Ortaokulu', 'Anadolu Lisesi', 'Fen Lisesi', 'Sosyal Bilimler Lisesi',
        'Mesleki ve Teknik Anadolu Lisesi', 'İmam Hatip Lisesi', 'Güzel Sanatlar Lisesi',
        'Spor Lisesi', 'Anadolu İmam Hatip Lisesi'
    ];

    // İsim örnekleri
    $nameExamples = [
        'Atatürk', 'Cumhuriyet', 'İstiklal', 'Fatih', 'Mehmet Akif Ersoy', 'Necip Fazıl',
        'Mimar Sinan', 'Yunus Emre', 'İbn-i Sina', 'Farabi', 'Mevlana', 'Hacı Bektaş Veli',
        'Şehit Ömer Halisdemir', 'Gazi', 'Fevzi Çakmak', 'İsmet İnönü', 'Celal Bayar',
        'Adnan Menderes', 'Turgut Özal', 'Süleyman Demirel', '15 Temmuz Şehitleri',
        'Alparslan', 'Malazgirt', 'Çanakkale', 'Sakarya', 'Dumlupınar', 'İnönü'
    ];

    $createdSchools = 0;
    
    for ($i = 1; $i <= 100; $i++) {
        // Rastgele okul bilgileri oluştur
        $city = $cities[array_rand($cities)];
        $schoolType = $schoolTypes[array_rand($schoolTypes)];
        $nameExample = $nameExamples[array_rand($nameExamples)];
        $plan = $plans->random();
        
        $schoolName = "{$nameExample} {$schoolType}";
        // Türkçe karakterleri İngilizce'ye çevir
        $cityCode = str_replace(['ş', 'ğ', 'ü', 'ö', 'ç', 'ı', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç', 'İ'], 
                              ['s', 'g', 'u', 'o', 'c', 'i', 'S', 'G', 'U', 'O', 'C', 'I'], $city);
        $schoolCode = 'SCH' . str_pad(($i + 100), 3, '0', STR_PAD_LEFT); // SCH101, SCH102, etc.
        $cleanNameForEmail = str_replace([' ', 'ş', 'ğ', 'ü', 'ö', 'ç', 'ı', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç', 'İ'], 
                                        ['', 's', 'g', 'u', 'o', 'c', 'i', 's', 'g', 'u', 'o', 'c', 'i'], $nameExample);
        $schoolEmail = strtolower($cleanNameForEmail) . $i . time() . '@edu.tr';
        
        // Okul oluştur
        $school = School::create([
            'name' => $schoolName,
            'slug' => \Illuminate\Support\Str::slug($schoolName) . '-' . $i,
            'code' => $schoolCode,
            'email' => $schoolEmail,
            'phone' => '0' . rand(212, 538) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
            'address' => "{$city} Merkez, " . rand(1, 200) . ". Sokak No:" . rand(1, 50),
            'website' => "https://" . strtolower(str_replace([' ', 'ş', 'ğ', 'ü', 'ö', 'ç', 'ı', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç', 'İ'], 
                                                         ['', 's', 'g', 'u', 'o', 'c', 'i', 's', 'g', 'u', 'o', 'c', 'i'], $schoolName)) . ".edu.tr",
            'subscription_plan_id' => $plan->id,
            'subscription_starts_at' => now()->subDays(rand(1, 365)),
            'subscription_ends_at' => now()->addDays(rand(30, 730)),
            'subscription_status' => rand(0, 10) > 8 ? 'expired' : 'active', // %20 expired
            'current_teachers' => rand(5, 50),
            'current_students' => rand(50, 1000),
            'current_classes' => rand(5, 40),
            'is_active' => rand(0, 10) > 1 // %90 active
        ]);

        // Her okul için bir müdür oluştur
        $principalNames = [
            'Ahmet Yılmaz', 'Mehmet Kaya', 'Ali Demir', 'Mustafa Çelik', 'Hasan Özkan',
            'İbrahim Aydın', 'Fatma Şahin', 'Ayşe Yıldız', 'Emine Arslan', 'Hatice Doğan',
            'Zeynep Kara', 'Meryem Çetin', 'Özlem Tunç', 'Serpil Acar', 'Gülşen Polat'
        ];
        
        $principalName = $principalNames[array_rand($principalNames)];
        $principalEmail = 'mudur' . $i . time() . '@' . str_replace(['https://', '.edu.tr'], ['', ''], $school->website) . '.edu.tr';
        
        User::create([
            'name' => $principalName,
            'email' => $principalEmail,
            'password' => Hash::make('mudur123'), // Tüm müdürler için aynı şifre
            'school_id' => $school->id,
            'role_id' => $schoolAdminRole->id,
            'phone' => '0' . rand(530, 559) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
            'is_active' => true,
            'status' => 'active'
        ]);

        // Her okul için 2-5 öğretmen oluştur
        $teacherCount = rand(2, 5);
        $teacherNames = [
            'Ahmet Yılmaz', 'Fatma Kaya', 'Mehmet Demir', 'Ayşe Çelik', 'Ali Özkan',
            'Zeynep Aydın', 'Mustafa Şahin', 'Emine Yıldız', 'Hasan Arslan', 'Hatice Doğan',
            'İbrahim Kara', 'Meryem Çetin', 'Özlem Tunç', 'Ahmet Acar', 'Serpil Polat',
            'Gülşen Öztürk', 'Mehmet Güven', 'Ayşe Bulut', 'Ali Koç', 'Fatma Şen'
        ];

        for ($j = 0; $j < $teacherCount; $j++) {
            $teacherName = $teacherNames[array_rand($teacherNames)];
            $teacherEmail = 'ogretmen' . $i . $j . time() . rand(100,999) . '@' . str_replace(['https://', '.edu.tr'], ['', ''], $school->website) . '.edu.tr';
            
            User::create([
                'name' => $teacherName,
                'email' => $teacherEmail,
                'password' => Hash::make('teacher123'), // Tüm öğretmenler için aynı şifre
                'school_id' => $school->id,
                'role_id' => $teacherRole->id,
                'phone' => '0' . rand(530, 559) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'is_active' => rand(0, 10) > 1, // %90 active
                'status' => 'active'
            ]);
        }

        $createdSchools++;
        
        if ($i % 10 == 0) {
            echo "✅ {$i} okul oluşturuldu... ({$schoolName})\n";
        }
    }
    
    echo "\n🎉 Başarıyla {$createdSchools} okul oluşturuldu!\n";
    echo "🏫 Toplam okul sayısı: " . School::count() . "\n";
    echo "👥 Toplam kullanıcı sayısı: " . User::count() . "\n";
    echo "\n📝 Test bilgileri:\n";
    echo "- Tüm müdürler için şifre: mudur123\n";
    echo "- Tüm öğretmenler için şifre: teacher123\n";
    echo "- Okul kodları: ŞEH001-ŞEH100 formatında\n";
    echo "- Email'ler: okuladi[numara]@edu.tr formatında\n";
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
    echo "Detay: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>