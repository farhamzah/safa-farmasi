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
        'hero_title' => 'Satu Akses untuk Seluruh Layanan Digital Farmasi UBP',
        'hero_description' => 'Portal layanan digital Fakultas Farmasi Universitas Buana Perjuangan Karawang untuk kerja praktek, tata usaha, formulir, program studi, dan bantuan akses.',
        'subheadline' => 'Portal satu halaman untuk layanan digital Fakultas Farmasi UBP Karawang.',
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
