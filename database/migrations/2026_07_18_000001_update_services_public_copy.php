<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('landing_settings')
            ->where('key', 'services_description')
            ->where('value', 'Semua kartu layanan dikelola dari menu Aplikasi di admin SAFA.')
            ->update([
                'value' => 'Akses layanan akademik, administrasi, laboratorium, dan dukungan fakultas dalam satu tempat yang mudah digunakan.',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('landing_settings')
            ->where('key', 'services_description')
            ->where('value', 'Akses layanan akademik, administrasi, laboratorium, dan dukungan fakultas dalam satu tempat yang mudah digunakan.')
            ->update([
                'value' => 'Semua kartu layanan dikelola dari menu Aplikasi di admin SAFA.',
                'updated_at' => now(),
            ]);
    }
};
