<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'hero_title',
        'hero_description',
        'subheadline',
        'logo_path',
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
}
