<?php

namespace App\Services;

use App\Models\PortalApplication;
use Illuminate\Support\Str;

class DuplicatePortalApplication
{
    public function duplicate(PortalApplication $application): PortalApplication
    {
        $copy = $application->replicate([
            'slug',
            'thumbnail_path',
            'created_at',
            'updated_at',
        ]);

        $copy->name = $application->name.' Salinan';
        $copy->slug = $this->uniqueSlug(Str::slug($copy->name));
        $copy->thumbnail_path = null;
        $copy->save();

        $copy->categories()->sync($application->categories()->pluck('app_categories.id')->all());

        return $copy;
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $baseSlug = $baseSlug ?: 'aplikasi-salinan';
        $slug = $baseSlug;
        $counter = 2;

        while (PortalApplication::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
