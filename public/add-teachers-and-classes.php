<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Adding 10 Teachers and 12 Classes to Each School ===\n\n";

// İsimler
$maleNames = ['Ahmet', 'Mehmet', 'Ali', 'Mustafa', 'Hasan', 'Hüseyin', 'İbrahim', 'Ömer', 'Yusuf', 'Emre', 'Burak', 'Cem', 'Deniz', 'Eren', 'Fatih', 'Kemal', 'Murat', 'Onur', 'Serkan', 'Tolga', 'Barış', 'Can', 'Volkan', 'Kaan', 'Umut'];
$femaleNames = ['Ayşe', 'Fatma', 'Elif', 'Zeynep', 'Merve', 'Emine', 'Hatice', 'Selin', 'Esra', 'Beyza', 'Büşra', 'Gamze', 'İrem', 'Kübra', 'Melike', 'Pınar', 'Rabia', 'Sibel', 'Tuğba', 'Yasemin', 'Dilara', 'Gizem', 'Naz', 'Özge', 'Ceren'];
$lastNames = ['Yılmaz', 'Kaya', 'Demir', 'Şahin', 'Çelik', 'Öztürk', 'Aydın', 'Özdemir', 'Arslan', 'Doğan', 'Kılıç', 'Aslan', 'Çetin', 'Koç', 'Kurt', 'Özkan', 'Şimşek', 'Yıldız', 'Yıldırım', 'Özer', 'Aksoy', 'Erdoğan', 'Güler', 'Karabulut', 'Polat'];

// Branşlar
$branches = [
    'Matematik', 'Türkçe', 'İngilizce', 'Fizik', 'Kimya', 'Biyoloji',
    'Tarih', 'Coğrafya', 'Felsefe', 'Din Kültürü', 'Beden Eğitimi',
    'Müzik', 'Resim', 'Bilgisayar', 'Teknoloji Tasarım', 'Edebiyat',
    'Geometri', 'Almanca', 'Fransızca', 'Rehberlik'
];

// Şubeler
$branches_letters = ['A', 'B', 'C'];

// Roller
$teacherRole = \App\Models\Role::where('name', 'teacher')->first();
if (!$teacherRole) {
    echo "❌ Teacher role not found!\n";
    exit(1);
}

// Tüm okulları çek
$schools = \App\Models\School::where('is_active', true)->get();
if ($schools->isEmpty()) {
    echo "❌ No schools found!\n";
    exit(1);
}

echo "📊 Found {$schools->count()} schools\n";
echo "🎯 Will create 10 teachers + 12 classes for each school\n";
echo "📝 Total teachers: " . ($schools->count() * 10) . "\n";
echo "📝 Total classes: " . ($schools->count() * 12) . "\n\n";

$totalTeachers = 0;
$totalClasses = 0;
$schoolsProcessed = 0;

foreach ($schools as $school) {
    echo "🏫 Processing: {$school->name}\n";
    
    // Bu okulun email domain'ini al
    $emailDomain = explode('@', $school->email)[1] ?? 'okul.edu.tr';
    
    // === 1. ÖĞRETMENLER OLUŞTUR (10 adet) ===
    $schoolTeachers = 0;
    
    for ($i = 1; $i <= 10; $i++) {
        // Rastgele isim oluştur
        $isFemale = rand(0, 1);
        $firstName = $isFemale ? $femaleNames[array_rand($femaleNames)] : $maleNames[array_rand($maleNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $fullName = "{$firstName} {$lastName}";
        
        // Email oluştur
        $emailName = strtolower(\Illuminate\Support\Str::slug($firstName . '.' . $lastName));
        $email = "{$emailName}{$i}@{$emailDomain}";
        
        // Kısa ad oluştur
        $shortName = strtoupper(substr($firstName, 0, 4) . substr($lastName, 0, 2));
        
        // Branş seç
        $branch = $branches[array_rand($branches)];
        
        // Telefon oluştur
        $phone = '0' . rand(530, 559) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99);
        
        // Email zaten var mı kontrol et
        $existingUser = \App\Models\User::where('email', $email)->first();
        if ($existingUser) {
            $email = "{$emailName}" . rand(100, 999) . "@{$emailDomain}";
        }
        
        try {
            \App\Models\User::create([
                'name' => $fullName,
                'email' => $email,
                'password' => bcrypt('123456'),
                'school_id' => $school->id,
                'role_id' => $teacherRole->id,
                'phone' => $phone,
                'branch' => $branch,
                'short_name' => $shortName,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $schoolTeachers++;
            $totalTeachers++;
            
        } catch (\Exception $e) {
            echo "  ❌ Error creating teacher: {$e->getMessage()}\n";
        }
    }
    
    echo "  ✅ Created {$schoolTeachers} teachers\n";
    
    // === 2. SINIFLARI OLUŞTUR (4 kademe x 3 şube = 12 sınıf) ===
    $schoolClasses = 0;
    
    // Bu okuldaki öğretmenleri al (sınıf öğretmeni olarak atayacağız)
    $schoolTeachersList = \App\Models\User::where('school_id', $school->id)
        ->where('role_id', $teacherRole->id)
        ->get();
    
    // Her kademe için (9, 10, 11, 12)
    for ($grade = 9; $grade <= 12; $grade++) {
        // Her şube için (A, B, C)
        foreach ($branches_letters as $branchLetter) {
            $className = "{$grade}-{$branchLetter}";
            
            // Sınıf zaten var mı kontrol et
            $existingClass = \App\Models\ClassRoom::where('school_id', $school->id)
                ->where('name', $className)
                ->first();
            
            if ($existingClass) {
                continue; // Varsa atla
            }
            
            // Rastgele öğretmen seç (sınıf öğretmeni olarak)
            $classTeacher = $schoolTeachersList->random();
            
            try {
                \App\Models\ClassRoom::create([
                    'school_id' => $school->id,
                    'name' => $className,
                    'grade' => $grade,
                    'branch' => $branchLetter,
                    'capacity' => rand(25, 35), // 25-35 arası kapasite
                    'current_students' => rand(20, 30), // Şu anki öğrenci sayısı
                    'class_teacher_id' => $classTeacher->id,
                    'is_active' => true
                ]);
                
                $schoolClasses++;
                $totalClasses++;
                
            } catch (\Exception $e) {
                echo "  ❌ Error creating class {$className}: {$e->getMessage()}\n";
            }
        }
    }
    
    echo "  ✅ Created {$schoolClasses} classes\n";
    
    // Okul istatistiklerini güncelle
    try {
        $school->update([
            'current_teachers' => \App\Models\User::where('school_id', $school->id)
                ->where('role_id', $teacherRole->id)
                ->count(),
            'current_classes' => \App\Models\ClassRoom::where('school_id', $school->id)->count()
        ]);
    } catch (\Exception $e) {
        // İstatistik güncellenemezse sessizce devam et
    }
    
    $schoolsProcessed++;
    
    // Her 10 okulda bir progress göster
    if ($schoolsProcessed % 10 === 0) {
        echo "\n📊 Progress: {$schoolsProcessed} schools processed...\n\n";
    }
    
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
echo "🏫 Schools processed: {$schoolsProcessed}\n";
echo "👨‍🏫 Total teachers created: {$totalTeachers}\n";
echo "🏛️ Total classes created: {$totalClasses}\n\n";

echo "=== STATISTICS ===\n";
echo "📊 Average per school:\n";
echo "   - Teachers: " . round($totalTeachers / $schoolsProcessed, 1) . "\n";
echo "   - Classes: " . round($totalClasses / $schoolsProcessed, 1) . "\n\n";

echo "=== SAMPLE DATA ===\n";
$sampleSchool = \App\Models\School::with(['city', 'district'])->inRandomOrder()->first();
if ($sampleSchool) {
    echo "\n📍 Sample School: {$sampleSchool->name}\n";
    echo "   Location: {$sampleSchool->district->name}, {$sampleSchool->city->name}\n";
    
    $sampleTeachers = \App\Models\User::where('school_id', $sampleSchool->id)
        ->where('role_id', $teacherRole->id)
        ->limit(3)
        ->get();
    
    echo "\n   Teachers (sample):\n";
    foreach ($sampleTeachers as $teacher) {
        echo "   👨‍🏫 {$teacher->name} ({$teacher->branch}) - {$teacher->email}\n";
    }
    
    $sampleClasses = \App\Models\ClassRoom::with('classTeacher')
        ->where('school_id', $sampleSchool->id)
        ->limit(6)
        ->get();
    
    echo "\n   Classes (sample):\n";
    foreach ($sampleClasses as $class) {
        $teacherName = $class->classTeacher ? $class->classTeacher->name : 'No teacher';
        echo "   🏛️ {$class->name} - Teacher: {$teacherName} - Students: {$class->current_students}/{$class->capacity}\n";
    }
}

echo "\n=== ALL DONE! ===\n";
echo "✅ All schools now have:\n";
echo "   - 10 teachers each\n";
echo "   - 12 classes each (9-A to 12-C)\n";
echo "   - Each class has a class teacher assigned\n\n";
echo "Default password for all teachers: 123456\n";
echo "View at: http://localhost/admin-panel-modern.html\n\n";
echo "Login as school admin to see teachers and classes!\n";
?>

