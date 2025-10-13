<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Test Data ===\n";

// Okul oluştur
$school = \App\Models\School::first();
if (!$school) {
    $school = \App\Models\School::create([
        'name' => 'Test Okulu',
        'email' => 'test@okul.com',
        'slug' => 'test-okulu',
        'subscription_plan_id' => 1,
        'subscription_starts_at' => now(),
        'subscription_ends_at' => now()->addYear(),
        'is_active' => true
    ]);
    echo "School created: {$school->name}\n";
} else {
    echo "School exists: {$school->name}\n";
}

// Öğretmen oluştur
$role = \App\Models\Role::where('name', 'teacher')->first();
$teacher = \App\Models\User::where('email', 'ogretmen@test.com')->first();
if (!$teacher) {
    $teacher = \App\Models\User::create([
        'name' => 'Test Öğretmen',
        'email' => 'ogretmen@test.com',
        'password' => bcrypt('123456'),
        'school_id' => $school->id,
        'role_id' => $role->id,
        'short_name' => 'TESTOG'
    ]);
    echo "Teacher created: {$teacher->name}\n";
} else {
    echo "Teacher exists: {$teacher->name}\n";
}

// Sınıf oluştur
$class = \App\Models\ClassRoom::where('name', '9-A')->first();
if (!$class) {
    $class = \App\Models\ClassRoom::create([
        'school_id' => $school->id,
        'name' => '9-A',
        'grade' => '9',
        'branch' => 'A',
        'class_teacher_id' => $teacher->id,
        'capacity' => 30,
        'is_active' => true
    ]);
    echo "Class created: {$class->name} with teacher: {$teacher->name}\n";
} else {
    echo "Class exists: {$class->name}\n";
}

echo "\n=== Test Data Ready ===\n";
?>
