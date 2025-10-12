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
            $table->string('short_name', 6)->nullable()->after('name')->comment('Kısa ad (6 karakter)');
            $table->string('branch', 100)->nullable()->after('short_name')->comment('Öğretmen branşı (Matematik, Türkçe, vb.)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['short_name', 'branch']);
        });
    }
};
