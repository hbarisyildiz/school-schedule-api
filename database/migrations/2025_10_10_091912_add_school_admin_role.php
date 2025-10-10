<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // School Admin rolü ekle
        Role::updateOrCreate(
            ['name' => 'school_admin'],
            [
                'display_name' => 'Okul Yöneticisi',
                'description' => 'Okul içi tüm işlemleri yönetir',
                'permissions' => [
                    'manage_teachers',
                    'manage_students', 
                    'manage_classes',
                    'manage_subjects',
                    'manage_schedules',
                    'view_school_reports',
                    'manage_school_settings'
                ]
            ]
        );

        // Teacher rolüne öğrenci görme izni ekle
        $teacherRole = Role::where('name', 'teacher')->first();
        if ($teacherRole) {
            $permissions = $teacherRole->permissions;
            if (!in_array('view_students', $permissions)) {
                $permissions[] = 'view_students';
                $teacherRole->update(['permissions' => $permissions]);
            }
        }

        // Student rolü ekle
        Role::updateOrCreate(
            ['name' => 'student'],
            [
                'display_name' => 'Öğrenci',
                'description' => 'Öğrenci hesabı',
                'permissions' => [
                    'view_own_schedule',
                    'view_class_schedule',
                    'view_classmates'
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::where('name', 'school_admin')->delete();
        Role::where('name', 'student')->delete();
    }
};
