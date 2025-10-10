<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Test okulu oluştur
        $basicPlan = SubscriptionPlan::where('slug', 'basic')->first();
        
        $school = School::create([
            'name' => 'Atatürk Anadolu Lisesi',
            'slug' => 'ataturk-anadolu-lisesi',
            'code' => 'AAL001',
            'email' => 'info@ataturklisesi.edu.tr',
            'phone' => '0312 123 45 67',
            'address' => 'Çankaya, Ankara',
            'website' => 'https://ataturklisesi.edu.tr',
            'subscription_plan_id' => $basicPlan->id,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => now()->addYear(),
            'subscription_status' => 'active',
            'current_teachers' => 0,
            'current_students' => 0,
            'current_classes' => 0,
            'is_active' => true
        ]);

        // Süper Admin kullanıcısı (tüm okulları yönetir)
        $superAdminRole = Role::where('name', 'super_admin')->first();
        User::create([
            'name' => 'Sistem Yöneticisi',
            'email' => 'admin@schoolschedule.com',
            'password' => Hash::make('admin123'),
            'school_id' => null, // Süper admin okula bağlı değil
            'role_id' => $superAdminRole->id,
            'is_active' => true,
            'status' => 'active'
        ]);

        // Okul yöneticisi
        $schoolAdminRole = Role::where('name', 'school_admin')->first();
        User::create([
            'name' => 'Mehmet Öztürk',
            'email' => 'mudur@ataturklisesi.edu.tr',
            'password' => Hash::make('mudur123'),
            'school_id' => $school->id,
            'role_id' => $schoolAdminRole->id,
            'tc_no' => '12345678901',
            'phone' => '0532 111 22 33',
            'is_active' => true,
            'status' => 'active'
        ]);

        // Test öğretmeni
        $teacherRole = Role::where('name', 'teacher')->first();
        User::create([
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse.yilmaz@ataturklisesi.edu.tr',
            'password' => Hash::make('teacher123'),
            'school_id' => $school->id,
            'role_id' => $teacherRole->id,
            'tc_no' => '98765432109',
            'phone' => '0533 444 55 66',
            'teacher_data' => [
                'branch' => 'Matematik',
                'graduation' => 'Gazi Üniversitesi Matematik Bölümü',
                'experience_years' => 8
            ],
            'is_active' => true,
            'status' => 'active'
        ]);

        // Okul istatistiklerini güncelle
        $school->update([
            'current_teachers' => 1,
            'current_students' => 0,
            'current_classes' => 0
        ]);
    }
}
