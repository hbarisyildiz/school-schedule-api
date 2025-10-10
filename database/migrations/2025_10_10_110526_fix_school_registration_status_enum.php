<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_registration_requests', function (Blueprint $table) {
            // Change status enum to include 'verified' value
            DB::statement("ALTER TABLE school_registration_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'verified') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_registration_requests', function (Blueprint $table) {
            // Revert back to original enum values
            DB::statement("ALTER TABLE school_registration_requests MODIFY status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        });
    }
};
