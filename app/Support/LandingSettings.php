<?php

namespace App\Support;

use App\Models\LandingSetting;

class LandingSettings
{
    private const DEFAULTS = [
        'site_name' => 'SAFA UBP',
        'site_subtitle' => 'Satu Akses Farmasi UBP',
        'site_logo' => '/images/logo-fakultas-farmasi-ubp.png',
        'site_favicon' => '/favicon.png',
        'headline' => 'Satu Akses Farmasi UBP',
        'hero_kicker' => 'Fakultas Farmasi Universitas Buana Perjuangan Karawang',
        'hero_title' => 'Farmasi UBP',
        'hero_highlight' => 'Karawang',
        'hero_description' => 'Mewujudkan pendidikan farmasi yang berwawasan kebangsaan, unggul, inovatif, dan berdaya saing global melalui pengembangan pharmapreneurship dan Evidence-Based Medicine.',
        'hero_image_url' => null,
        'hero_primary_button' => 'Kenali Kami Lebih Lanjut',
        'hero_secondary_button' => 'Program Studi',
        'subheadline' => 'Portal satu halaman untuk layanan digital Fakultas Farmasi UBP Karawang.',
        'value_1_title' => 'Berwawasan Kebangsaan',
        'value_2_title' => 'Unggul & Inovatif',
        'value_3_title' => 'Pharmapreneurship',
        'value_4_title' => 'Evidence-Based Medicine',
        'services_eyebrow' => 'Layanan Digital',
        'services_title' => 'Pilihan Layanan untuk Kebutuhan Akademik Anda',
        'services_description' => 'Semua kartu layanan dikelola dari menu Aplikasi di admin SAFA.',
        'about_eyebrow' => 'Tentang Kami',
        'about_title' => 'Membentuk ekosistem Farmasi UBP yang kredibel',
        'about_description' => 'SAFA UBP membantu civitas akademika mengakses layanan fakultas secara tertib, cepat, dan terdokumentasi melalui satu portal resmi.',
        'about_button_label' => 'Hubungi Fakultas',
        'credible_1_title' => 'Care Giver',
        'credible_2_title' => 'Researcher',
        'credible_3_title' => 'Entrepreneur',
        'credible_4_title' => 'Decision Maker',
        'credible_5_title' => 'Innovator',
        'credible_6_title' => 'Builder',
        'credible_7_title' => 'Leader',
        'credible_8_title' => 'Educator',
        'news_eyebrow' => 'Berita Terkini',
        'news_title' => 'Informasi & Kegiatan Terbaru',
        'contact_title' => 'Butuh bantuan akses layanan?',
        'contact_description' => 'Hubungi kanal resmi fakultas bila akun, akses aplikasi, atau tautan layanan belum sesuai kebutuhan.',
        'footer_text' => 'Fakultas Farmasi UBP Karawang',
        'contact_email' => null,
        'contact_whatsapp' => null,
    ];

    private ?LandingSetting $settings = null;

    public function all(): LandingSetting
    {
        if (! $this->settings) {
            $attributes = self::DEFAULTS;
            $legacy = LandingSetting::query()->whereNull('key')->first();

            if ($legacy) {
                foreach (array_keys(self::DEFAULTS) as $key) {
                    if (filled($legacy->{$key} ?? null)) {
                        $attributes[$key] = $legacy->{$key};
                    }
                }

                if (filled($legacy->logo_path)) {
                    $attributes['logo_path'] = $legacy->logo_path;
                }
            }

            LandingSetting::query()
                ->whereNotNull('key')
                ->get()
                ->each(function (LandingSetting $setting) use (&$attributes): void {
                    if (filled($setting->value)) {
                        $attributes[$setting->key] = $setting->value;
                    }
                });

            $this->settings = new LandingSetting($attributes);
        }

        foreach (self::DEFAULTS as $key => $value) {
            if (blank($this->settings->{$key} ?? null)) {
                $this->settings->setAttribute($key, $value);
            }
        }

        return $this->settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $setting = LandingSetting::query()
            ->where('key', $key)
            ->first();

        $value = $setting?->value ?? $this->all()->getAttribute($key);

        return filled($value) ? $value : ($default ?? self::DEFAULTS[$key] ?? null);
    }
}
