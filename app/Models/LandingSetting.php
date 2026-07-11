<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingSetting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'type',
        'value',
        'site_name',
        'site_subtitle',
        'site_logo',
        'site_favicon',
        'headline',
        'hero_kicker',
        'hero_title',
        'hero_highlight',
        'hero_description',
        'hero_image_url',
        'hero_primary_button',
        'hero_secondary_button',
        'subheadline',
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
        'logo_path',
        'contact_title',
        'contact_description',
        'footer_text',
        'contact_email',
        'contact_whatsapp',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path || ! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public static function normalizePublicAssetPathValue(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = collect($value)
                ->filter(fn (mixed $path): bool => filled($path))
                ->last();
        }

        if (blank($value)) {
            return null;
        }

        $path = str_replace('\\', '/', trim((string) $value));

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (str_contains($path, 'livewire-tmp')) {
            return null;
        }

        foreach ([storage_path('app/public'), public_path('storage'), public_path()] as $prefix) {
            $prefix = str_replace('\\', '/', rtrim($prefix, '\\/')).'/';

            if (Str::startsWith($path, $prefix)) {
                return ltrim(Str::after($path, $prefix), '/');
            }
        }

        foreach (['storage/app/public/', 'public/storage/', '/storage/', 'storage/'] as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                return ltrim(Str::after($path, $prefix), '/');
            }
        }

        if (preg_match('/^[A-Za-z]:\//', $path)) {
            return null;
        }

        return ltrim($path, '/');
    }
}
