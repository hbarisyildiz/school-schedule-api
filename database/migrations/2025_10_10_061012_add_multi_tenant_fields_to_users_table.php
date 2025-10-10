<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Multi-tenant alanlar
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles');
            
            // Kullanıcı profil bilgileri
            $table->string('tc_no', 11)->unique()->nullable(); // T.C. Kimlik No
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable(); // Profil fotoğrafı
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // Öğretmen için ek bilgiler (JSON)
            $table->json('teacher_data')->nullable(); // branş, mezuniyet vs.
            
            // Öğrenci için ek bilgiler (JSON)  
            $table->json('student_data')->nullable(); // sınıf, numara vs.
            
            // Sistem alanları
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('status')->default('active'); // active, inactive, suspended
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'school_id', 'role_id', 'tc_no', 'phone', 'avatar', 
                'birth_date', 'gender', 'teacher_data', 'student_data',
                'is_active', 'last_login_at', 'status'
            ]);
        });
    }
};
