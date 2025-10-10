<?php
// Test data seeder
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\User;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Hash;

echo "Test verileri ekleniyor...\n";

try {
    // Öğretmenler ekle
    $teachers = [
        ['Ahmet Yılmaz', 'ahmet.yilmaz@okul1.com'],
        ['Fatma Demir', 'fatma.demir@okul1.com'],
        ['Mehmet Kaya', 'mehmet.kaya@okul1.com'],
        ['Ayşe Şahin', 'ayse.sahin@okul1.com'],
        ['Ali Özkan', 'ali.ozkan@okul1.com']
    ];
    
    $teacherRoleId = \App\Models\Role::where('name', 'teacher')->first()->id;
    $schoolId = 1;
    
    foreach ($teachers as $teacher) {
        $user = User::firstOrCreate(
            ['email' => $teacher[1]],
            [
                'name' => $teacher[0],
                'password' => Hash::make('password'),
                'role_id' => $teacherRoleId,
                'school_id' => $schoolId,
                'is_active' => true,
                'created_by' => 1
            ]
        );
        echo "Öğretmen eklendi: {$teacher[0]}\n";
    }
    
    // Dersler ekle
    $subjects = [
        ['Matematik', 'MAT101', 6],
        ['Türkçe', 'TUR101', 5],
        ['Fen Bilgileri', 'FEN101', 4],
        ['Sosyal Bilgiler', 'SOS101', 3],
        ['İngilizce', 'ING101', 4],
        ['Beden Eğitimi', 'BED101', 2],
        ['Müzik', 'MUZ101', 2],
        ['Resim', 'RES101', 2]
    ];
    
    foreach ($subjects as $subject) {
        $subjectModel = Subject::firstOrCreate(
            ['code' => $subject[1], 'school_id' => $schoolId],
            [
                'name' => $subject[0],
                'weekly_hours' => $subject[2],
                'school_id' => $schoolId,
                'is_active' => true,
                'created_by' => 1
            ]
        );
        echo "Ders eklendi: {$subject[0]}\n";
    }
    
    // Sınıflar ekle
    $classes = [
        ['1-A', 1, 30],
        ['1-B', 1, 28],
        ['2-A', 2, 32],
        ['2-B', 2, 29],
        ['3-A', 3, 31],
        ['3-B', 3, 27],
        ['4-A', 4, 33],
        ['4-B', 4, 30],
        ['5-A', 5, 29],
        ['5-B', 5, 31]
    ];
    
    foreach ($classes as $class) {
        $classModel = ClassRoom::firstOrCreate(
            ['name' => $class[0], 'school_id' => $schoolId],
            [
                'grade' => $class[1],
                'capacity' => $class[2],
                'school_id' => $schoolId,
                'is_active' => true,
                'created_by' => 1
            ]
        );
        echo "Sınıf eklendi: {$class[0]}\n";
    }
    
    echo "\n✅ Test verileri başarıyla eklendi!\n";
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}