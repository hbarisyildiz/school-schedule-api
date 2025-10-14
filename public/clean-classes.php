<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;
use App\Models\ClassRoom;

echo "🧹 Hatalı sınıfları temizliyorum...\n\n";

$schools = School::all();

foreach ($schools as $school) {
    echo "📚 {$school->name} ({$school->school_type})...\n";
    
    $gradeLevels = $school->getGradeLevels();
    $validGrades = array_column($gradeLevels, 'value');
    
    // Hatalı sınıfları bul
    $invalidClasses = ClassRoom::where('school_id', $school->id)
        ->whereNotIn('grade', $validGrades)
        ->get();
    
    if ($invalidClasses->count() > 0) {
        echo "   ❌ {$invalidClasses->count()} hatalı sınıf bulundu:\n";
        foreach ($invalidClasses as $class) {
            echo "      - {$class->name} (Seviye: {$class->grade})\n";
        }
        
        // Hatalı sınıfları sil
        ClassRoom::where('school_id', $school->id)
            ->whereNotIn('grade', $validGrades)
            ->delete();
        
        echo "   ✅ Hatalı sınıflar silindi\n";
    } else {
        echo "   ✅ Tüm sınıflar geçerli\n";
    }
    
    echo "\n";
}

echo "🎉 Temizlik tamamlandı!\n";

