<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin kullanıcısı oluştur
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if ($superAdminRole) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@schoolschedule.com',
                'password' => Hash::make('admin123'),
                'role_id' => $superAdminRole->id,
                'school_id' => null, // Super admin'in okulu olmaz
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            echo "✅ Super Admin oluşturuldu: {$superAdmin->email}\n";
        }
        
        // Test okul admin'i oluştur
        $schoolAdminRole = Role::where('name', 'school_admin')->first();
        $testSchool = School::first();
        
        if ($schoolAdminRole && $testSchool) {
            $schoolAdmin = User::create([
                'name' => 'Test Okul Admin',
                'email' => 'school@test.com',
                'password' => Hash::make('123456'),
                'role_id' => $schoolAdminRole->id,
                'school_id' => $testSchool->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            echo "✅ School Admin oluşturuldu: {$schoolAdmin->email}\n";
        }
        
        // Test öğretmen oluştur
        $teacherRole = Role::where('name', 'teacher')->first();
        
        if ($teacherRole && $testSchool) {
            $teacher = User::create([
                'name' => 'Test Öğretmen',
                'email' => 'teacher@test.com',
                'password' => Hash::make('123456'),
                'role_id' => $teacherRole->id,
                'school_id' => $testSchool->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            echo "✅ Teacher oluşturuldu: {$teacher->email}\n";
        }
    }
}
