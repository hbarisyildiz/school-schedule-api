<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Users in Database ===\n\n";

// Tüm kullanıcıları listele
$users = \App\Models\User::with('role')->get();

if ($users->isEmpty()) {
    echo "❌ No users found in database!\n\n";
    echo "Creating test users...\n\n";
} else {
    echo "✅ Found {$users->count()} users:\n\n";
    
    foreach ($users as $user) {
        echo "- {$user->name}\n";
        echo "  Email: {$user->email}\n";
        echo "  Role: " . ($user->role->display_name ?? 'No role') . "\n";
        echo "  Active: " . ($user->is_active ? 'Yes' : 'No') . "\n\n";
    }
}

// Super Admin kontrol
$superAdmin = \App\Models\User::where('email', 'admin@schoolschedule.com')->first();
if (!$superAdmin) {
    echo "❌ Super Admin NOT FOUND - Creating...\n";
    $superAdminRole = \App\Models\Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@schoolschedule.com',
            'password' => bcrypt('admin123'),
            'role_id' => $superAdminRole->id,
            'school_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        echo "✅ Super Admin created!\n";
        echo "   Email: admin@schoolschedule.com\n";
        echo "   Password: admin123\n\n";
    }
} else {
    echo "✅ Super Admin EXISTS\n";
    echo "   Email: admin@schoolschedule.com\n";
    echo "   Password: admin123\n\n";
}

// Okul kontrol
$school = \App\Models\School::first();
if (!$school) {
    echo "❌ No school found - Creating test school...\n";
    $basicPlan = \App\Models\SubscriptionPlan::first();
    $school = \App\Models\School::create([
        'name' => 'Atatürk Anadolu Lisesi',
        'slug' => 'ataturk-anadolu-lisesi',
        'code' => 'AAL001',
        'email' => 'info@ataturklisesi.edu.tr',
        'phone' => '0312 123 45 67',
        'subscription_plan_id' => $basicPlan->id,
        'subscription_starts_at' => now(),
        'subscription_ends_at' => now()->addYear(),
        'subscription_status' => 'active',
        'is_active' => true
    ]);
    echo "✅ School created: {$school->name}\n\n";
}

// Okul Müdürü kontrol
$schoolAdmin = \App\Models\User::where('email', 'mudur@ataturklisesi.edu.tr')->first();
if (!$schoolAdmin) {
    echo "❌ School Admin NOT FOUND - Creating...\n";
    $schoolAdminRole = \App\Models\Role::where('name', 'school_admin')->first();
    if ($schoolAdminRole && $school) {
        \App\Models\User::create([
            'name' => 'Mehmet Öztürk',
            'email' => 'mudur@ataturklisesi.edu.tr',
            'password' => bcrypt('mudur123'),
            'school_id' => $school->id,
            'role_id' => $schoolAdminRole->id,
            'phone' => '0532 111 22 33',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        echo "✅ School Admin created!\n";
        echo "   Email: mudur@ataturklisesi.edu.tr\n";
        echo "   Password: mudur123\n\n";
    }
} else {
    echo "✅ School Admin EXISTS\n";
    echo "   Email: mudur@ataturklisesi.edu.tr\n";
    echo "   Password: mudur123\n";
    echo "   Active: " . ($schoolAdmin->is_active ? 'Yes' : 'No') . "\n\n";
}

// Öğretmen kontrol
$teacher = \App\Models\User::where('email', 'ayse.yilmaz@ataturklisesi.edu.tr')->first();
if (!$teacher) {
    echo "❌ Teacher NOT FOUND - Creating...\n";
    $teacherRole = \App\Models\Role::where('name', 'teacher')->first();
    if ($teacherRole && $school) {
        \App\Models\User::create([
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse.yilmaz@ataturklisesi.edu.tr',
            'password' => bcrypt('teacher123'),
            'school_id' => $school->id,
            'role_id' => $teacherRole->id,
            'phone' => '0533 444 55 66',
            'branch' => 'Matematik',
            'short_name' => 'AYSYLM',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        echo "✅ Teacher created!\n";
        echo "   Email: ayse.yilmaz@ataturklisesi.edu.tr\n";
        echo "   Password: teacher123\n\n";
    }
} else {
    echo "✅ Teacher EXISTS\n";
    echo "   Email: ayse.yilmaz@ataturklisesi.edu.tr\n";
    echo "   Password: teacher123\n\n";
}

echo "\n=== Summary ===\n";
echo "✅ All test users checked/created\n\n";
echo "Login Credentials:\n";
echo "1. Super Admin:\n";
echo "   Email: admin@schoolschedule.com\n";
echo "   Password: admin123\n\n";
echo "2. School Admin (Okul Müdürü):\n";
echo "   Email: mudur@ataturklisesi.edu.tr\n";
echo "   Password: mudur123\n\n";
echo "3. Teacher:\n";
echo "   Email: ayse.yilmaz@ataturklisesi.edu.tr\n";
echo "   Password: teacher123\n\n";
echo "Now you can login at: http://localhost/admin-panel-modern.html\n";
?>


