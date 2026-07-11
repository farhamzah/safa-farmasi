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
        $settings = [
            ['hero', 'text', 'hero_kicker', 'Fakultas Farmasi Universitas Buana Perjuangan Karawang'],
            ['hero', 'text', 'hero_title', 'Farmasi UBP'],
            ['hero', 'text', 'hero_highlight', 'Karawang'],
            ['hero', 'textarea', 'hero_description', 'Mewujudkan pendidikan farmasi yang berwawasan kebangsaan, unggul, inovatif, dan berdaya saing global melalui pengembangan pharmapreneurship dan Evidence-Based Medicine.'],
            ['hero', 'image', 'hero_image_url', null],
            ['hero', 'text', 'hero_primary_button', 'Kenali Kami Lebih Lanjut'],
            ['hero', 'text', 'hero_secondary_button', 'Program Studi'],
            ['values', 'text', 'value_1_title', 'Berwawasan Kebangsaan'],
            ['values', 'text', 'value_2_title', 'Unggul & Inovatif'],
            ['values', 'text', 'value_3_title', 'Pharmapreneurship'],
            ['values', 'text', 'value_4_title', 'Evidence-Based Medicine'],
            ['services', 'text', 'services_eyebrow', 'Layanan Digital'],
            ['services', 'text', 'services_title', 'Pilihan Layanan untuk Kebutuhan Akademik Anda'],
            ['services', 'textarea', 'services_description', 'Semua kartu layanan dikelola dari menu Aplikasi di admin SAFA.'],
            ['about', 'text', 'about_eyebrow', 'Tentang Kami'],
            ['about', 'text', 'about_title', 'Membentuk ekosistem Farmasi UBP yang kredibel'],
            ['about', 'textarea', 'about_description', 'SAFA UBP membantu civitas akademika mengakses layanan fakultas secara tertib, cepat, dan terdokumentasi melalui satu portal resmi.'],
            ['about', 'text', 'about_button_label', 'Hubungi Fakultas'],
            ['about', 'text', 'credible_1_title', 'Care Giver'],
            ['about', 'text', 'credible_2_title', 'Researcher'],
            ['about', 'text', 'credible_3_title', 'Entrepreneur'],
            ['about', 'text', 'credible_4_title', 'Decision Maker'],
            ['about', 'text', 'credible_5_title', 'Innovator'],
            ['about', 'text', 'credible_6_title', 'Builder'],
            ['about', 'text', 'credible_7_title', 'Leader'],
            ['about', 'text', 'credible_8_title', 'Educator'],
            ['news', 'text', 'news_eyebrow', 'Berita Terkini'],
            ['news', 'text', 'news_title', 'Informasi & Kegiatan Terbaru'],
            ['contact', 'text', 'contact_title', 'Butuh bantuan akses layanan?'],
            ['contact', 'textarea', 'contact_description', 'Hubungi kanal resmi fakultas bila akun, akses aplikasi, atau tautan layanan belum sesuai kebutuhan.'],
        ];

        foreach ($settings as [$group, $type, $key, $value]) {
            DB::table('landing_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'group' => $group,
                    'type' => $type,
                    'value' => $value,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('landing_settings')
            ->whereIn('key', [
                'hero_kicker',
                'hero_highlight',
                'hero_image_url',
                'hero_primary_button',
                'hero_secondary_button',
                'value_1_title',
                'value_2_title',
                'value_3_title',
                'value_4_title',
                'services_eyebrow',
                'services_title',
                'services_description',
                'about_eyebrow',
                'about_title',
                'about_description',
                'about_button_label',
                'credible_1_title',
                'credible_2_title',
                'credible_3_title',
                'credible_4_title',
                'credible_5_title',
                'credible_6_title',
                'credible_7_title',
                'credible_8_title',
                'news_eyebrow',
                'news_title',
                'contact_title',
                'contact_description',
            ])
            ->delete();
    }
};
