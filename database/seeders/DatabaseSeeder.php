<?php

namespace Database\Seeders;

use App\Models\AppCategory;
use App\Models\Announcement;
use App\Models\LandingSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@safa.cloud'],
            [
                'name' => 'Admin SAFA UBP',
                'password' => 'password',
                'is_admin' => true,
            ]
        );

        $settings = [
            ['general', 'text', 'site_name', 'SAFA UBP'],
            ['general', 'text', 'site_subtitle', 'Satu Akses Farmasi UBP'],
            ['general', 'text', 'site_logo', '/images/logo-fakultas-farmasi-ubp.png'],
            ['general', 'text', 'site_favicon', '/favicon.png'],
            ['general', 'text', 'headline', 'Satu Akses Farmasi UBP'],
            ['hero', 'text', 'hero_kicker', 'Fakultas Farmasi Universitas Buana Perjuangan Karawang'],
            ['hero', 'text', 'hero_title', 'Farmasi UBP'],
            ['hero', 'text', 'hero_highlight', 'Karawang'],
            ['hero', 'textarea', 'hero_description', 'Mewujudkan pendidikan farmasi yang berwawasan kebangsaan, unggul, inovatif, dan berdaya saing global melalui pengembangan pharmapreneurship dan Evidence-Based Medicine.'],
            ['hero', 'image', 'hero_image_url', null],
            ['hero', 'text', 'hero_primary_button', 'Kenali Kami Lebih Lanjut'],
            ['hero', 'text', 'hero_secondary_button', 'Program Studi'],
            ['general', 'textarea', 'subheadline', 'Portal layanan digital Fakultas Farmasi UBP Karawang untuk kerja praktek, tata usaha, formulir, program studi, dan helpdesk.'],
            ['values', 'text', 'value_1_title', 'Berwawasan Kebangsaan'],
            ['values', 'text', 'value_2_title', 'Unggul & Inovatif'],
            ['values', 'text', 'value_3_title', 'Pharmapreneurship'],
            ['values', 'text', 'value_4_title', 'Evidence-Based Medicine'],
            ['services', 'text', 'services_eyebrow', 'Layanan Digital'],
            ['services', 'text', 'services_title', 'Pilihan Layanan untuk Kebutuhan Akademik Anda'],
            ['services', 'textarea', 'services_description', 'Akses layanan akademik, administrasi, laboratorium, dan dukungan fakultas dalam satu tempat yang mudah digunakan.'],
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
            ['footer', 'text', 'footer_text', 'Fakultas Farmasi UBP Karawang'],
            ['contact', 'email', 'contact_email', 'farmasi@ubpkarawang.ac.id'],
        ];

        foreach ($settings as [$group, $type, $key, $value]) {
            LandingSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => $group,
                    'type' => $type,
                    'value' => $value,
                ]
            );
        }

        $layanan = AppCategory::query()->firstOrCreate(
            ['slug' => 'layanan-akademik'],
            ['name' => 'Layanan Akademik', 'sort_order' => 1]
        );

        $prodi = AppCategory::query()->firstOrCreate(
            ['slug' => 'program-studi'],
            ['name' => 'Program Studi', 'sort_order' => 2]
        );

        $support = AppCategory::query()->firstOrCreate(
            ['slug' => 'dukungan'],
            ['name' => 'Dukungan', 'sort_order' => 3]
        );

        $applications = [
            [$layanan->id, 'Kerja Praktek', 'KP', 'Pengajuan dan monitoring administrasi kerja praktek mahasiswa.', 'https://safa.cloud/kerja-praktek', 1, true],
            [$layanan->id, 'Tata Usaha', 'TU', 'Akses layanan administrasi fakultas secara terpusat.', 'https://safa.cloud/tata-usaha', 2, true],
            [$layanan->id, 'Download Formulir', 'DF', 'Kumpulan formulir resmi yang dapat diunduh mahasiswa dan dosen.', 'https://safa.cloud/formulir', 3, false],
            [$prodi->id, 'Prodi S1 Farmasi', 'S1', 'Informasi dan layanan digital Program Studi S1 Farmasi.', 'https://safa.cloud/s1-farmasi', 1, false],
            [$prodi->id, 'Prodi Apoteker', 'AP', 'Informasi dan layanan digital Program Studi Profesi Apoteker.', 'https://safa.cloud/apoteker', 2, false],
            [$support->id, 'Helpdesk', 'HD', 'Bantuan teknis dan kanal pelaporan kendala layanan digital.', 'https://safa.cloud/helpdesk', 1, true],
        ];

        foreach ($applications as [$categoryId, $name, $shortName, $description, $url, $sortOrder, $isFeatured]) {
            $application = PortalApplication::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'app_category_id' => $categoryId,
                    'name' => $name,
                    'short_name' => $shortName,
                    'description' => $description,
                    'short_description' => $description,
                    'url' => $url,
                    'button_label' => $shortName === 'LAB' ? 'Buka Lab' : 'Buka Aplikasi',
                    'status' => $shortName === 'LAB' ? 'internal' : 'active',
                    'sort_order' => $sortOrder,
                    'is_featured' => $isFeatured,
                    'is_active' => true,
                    'open_in_new_tab' => true,
                ]
            );

            $application->categories()->syncWithoutDetaching([$categoryId]);
        }

        Announcement::query()->firstOrCreate(
            ['title' => 'Selamat datang di SAFA UBP'],
            [
                'body' => 'Gunakan portal ini untuk mengakses layanan digital Fakultas Farmasi UBP Karawang.',
                'type' => 'info',
                'is_active' => true,
            ]
        );

        $this->call(LabPortalCardSeeder::class);
        $this->call(TaPortalCardSeeder::class);
    }
}
