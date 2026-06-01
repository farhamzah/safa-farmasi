<?php

namespace Database\Seeders;

use App\Models\AppCategory;
use App\Models\PortalApplication;
use Illuminate\Database\Seeder;

class TaPortalCardSeeder extends Seeder
{
    public function run(): void
    {
        $category = AppCategory::query()->firstOrCreate(
            ['slug' => 'layanan-akademik'],
            [
                'name' => 'Layanan Akademik',
                'description' => 'Aplikasi akademik dan layanan proses pembelajaran Fakultas Farmasi UBP.',
                'icon' => 'graduation-cap',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $application = PortalApplication::query()->updateOrCreate(
            ['slug' => 'ta-farmasi-ubp'],
            [
                'app_category_id' => $category->id,
                'name' => 'TA Farmasi UBP',
                'short_name' => 'TA',
                'description' => 'Pengelolaan Tugas Akhir Farmasi UBP.',
                'short_description' => 'Pengelolaan Tugas Akhir',
                'long_description' => 'Portal Tugas Akhir Farmasi untuk pendaftaran, pembimbing, bimbingan, seminar/sidang, revisi, finalisasi, evidence, dan pelaporan.',
                'url' => env('TA_APP_URL', 'http://127.0.0.1:8007'),
                'button_label' => 'Buka TA',
                'accent_color' => '#2563eb',
                'status' => PortalApplication::STATUS_INTERNAL,
                'sort_order' => 2,
                'open_in_new_tab' => true,
                'is_featured' => true,
                'is_active' => true,
            ],
        );

        $application->categories()->syncWithoutDetaching([$category->id]);
    }
}
