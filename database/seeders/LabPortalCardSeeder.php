<?php

namespace Database\Seeders;

use App\Models\AppCategory;
use App\Models\PortalApplication;
use Illuminate\Database\Seeder;

class LabPortalCardSeeder extends Seeder
{
    public function run(): void
    {
        $category = AppCategory::query()->firstOrCreate(
            ['slug' => 'laboratorium'],
            [
                'name' => 'Laboratorium',
                'description' => 'Aplikasi operasional laboratorium dan fasilitas praktikum.',
                'icon' => 'flask-conical',
                'sort_order' => 4,
                'is_active' => true,
            ],
        );

        $application = PortalApplication::query()->updateOrCreate(
            ['slug' => 'lab-farmasi-ubp'],
            [
                'app_category_id' => $category->id,
                'name' => 'Lab Farmasi UBP',
                'short_name' => 'LAB',
                'description' => 'Sistem Operasional Laboratorium untuk absensi QR, logbook alat, stok bahan/reagen, SOP/SDS/K3, maintenance/kalibrasi, dashboard, dan laporan.',
                'short_description' => 'Sistem Operasional Laboratorium',
                'long_description' => 'Absensi lab berbasis QR, logbook alat, stok bahan/reagen, SOP/SDS/K3, maintenance/kalibrasi, dashboard, dan laporan.',
                'url' => env('LAB_FARMASI_DASHBOARD_URL', 'http://127.0.0.1:8006/dashboard'),
                'button_label' => 'Buka Lab',
                'accent_color' => '#0f6fb7',
                'status' => PortalApplication::STATUS_INTERNAL,
                'sort_order' => 1,
                'open_in_new_tab' => true,
                'is_featured' => true,
                'is_active' => true,
            ],
        );

        $application->categories()->syncWithoutDetaching([$category->id]);
    }
}
