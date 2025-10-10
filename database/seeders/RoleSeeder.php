<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Süper Admin',
                'description' => 'Sistem yöneticisi - tüm yetkiler',
                'level' => 0,
                'permissions' => [
                    'manage_schools',
                    'manage_subscription_plans',
                    'manage_all_users',
                    'view_all_data',
                    'system_settings'
                ],
                'is_active' => true
            ],
            [
                'name' => 'school_admin',
                'display_name' => 'Okul Yöneticisi',
                'description' => 'Okul müdürü/yöneticisi - okul yönetimi',
                'level' => 1,
                'permissions' => [
                    'manage_school_settings',
                    'manage_teachers',
                    'manage_students',
                    'manage_classes',
                    'manage_subjects',
                    'manage_schedules',
                    'view_reports'
                ],
                'is_active' => true
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Öğretmen',
                'description' => 'Öğretmen - ders programı görüntüleme',
                'level' => 2,
                'permissions' => [
                    'view_own_schedule',
                    'view_class_schedules',
                    'update_profile'
                ],
                'is_active' => true
            ],
            [
                'name' => 'staff',
                'display_name' => 'Personel',
                'description' => 'Okul personeli - sınırlı erişim',
                'level' => 3,
                'permissions' => [
                    'view_schedules',
                    'update_profile'
                ],
                'is_active' => true
            ],
            [
                'name' => 'student',
                'display_name' => 'Öğrenci',
                'description' => 'Öğrenci - ders programı görüntüleme',
                'level' => 4,
                'permissions' => [
                    'view_own_schedule',
                    'update_profile'
                ],
                'is_active' => true
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
