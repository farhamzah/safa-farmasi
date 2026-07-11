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
            ->where('key', 'hero_title')
            ->where('value', 'Farmasi UBP Karawang')
            ->update([
                'value' => 'Farmasi UBP',
                'updated_at' => now(),
            ]);

        DB::table('landing_settings')
            ->where('key', 'hero_highlight')
            ->where('value', 'Satu Akses Digital')
            ->update([
                'value' => 'Karawang',
                'updated_at' => now(),
            ]);

        DB::table('landing_settings')
            ->where('key', 'hero_description')
            ->where('value', 'Portal resmi Fakultas Farmasi UBP Karawang untuk membuka layanan akademik, administrasi, program studi, laboratorium, dan bantuan akses dalam satu halaman.')
            ->update([
                'value' => 'Mewujudkan pendidikan farmasi yang berwawasan kebangsaan, unggul, inovatif, dan berdaya saing global melalui pengembangan pharmapreneurship dan Evidence-Based Medicine.',
                'updated_at' => now(),
            ]);

        DB::table('landing_settings')
            ->where('key', 'hero_primary_button')
            ->where('value', 'Kenali Layanan')
            ->update([
                'value' => 'Kenali Kami Lebih Lanjut',
                'updated_at' => now(),
            ]);

        DB::table('landing_settings')
            ->where('key', 'hero_secondary_button')
            ->where('value', 'Lihat Aplikasi')
            ->update([
                'value' => 'Program Studi',
                'updated_at' => now(),
            ]);

        $exists = DB::table('landing_settings')
            ->where('key', 'hero_image_url')
            ->exists();

        if ($exists) {
            DB::table('landing_settings')
                ->where('key', 'hero_image_url')
                ->update([
                    'group' => 'hero',
                    'type' => 'image',
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('landing_settings')->insert([
            'group' => 'hero',
            'type' => 'image',
            'key' => 'hero_image_url',
            'value' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('landing_settings')
            ->where('key', 'hero_image_url')
            ->update([
                'type' => 'url',
                'updated_at' => now(),
            ]);
    }
};
