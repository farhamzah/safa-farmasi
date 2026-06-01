<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortalApplication extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_COMING_SOON = 'coming_soon';
    public const STATUS_INTERNAL = 'internal';
    public const STATUS_INACTIVE = 'inactive';

    public const VISIBLE_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_MAINTENANCE,
        self::STATUS_COMING_SOON,
        self::STATUS_INTERNAL,
    ];

    protected $fillable = [
        'app_category_id',
        'name',
        'slug',
        'short_name',
        'description',
        'thumbnail_path',
        'url',
        'short_description',
        'long_description',
        'button_label',
        'accent_color',
        'status',
        'sort_order',
        'open_in_new_tab',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AppCategory::class, 'app_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(AppCategory::class)->withTimestamps();
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ApplicationClick::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $thumbnailPath = $this->normalizeThumbnailPath($this->thumbnail_path);

        if (! $thumbnailPath || ! Storage::disk('public')->exists($thumbnailPath)) {
            return null;
        }

        return Storage::disk('public')->url($thumbnailPath);
    }

    public function setThumbnailPathAttribute(mixed $value): void
    {
        $this->attributes['thumbnail_path'] = $this->normalizeThumbnailPath($value);
    }

    public static function normalizeThumbnailPathValue(mixed $value): ?string
    {
        return (new self())->normalizeThumbnailPath($value);
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        return $this->short_description ?: $this->description;
    }

    public function getDisplayButtonLabelAttribute(): string
    {
        return $this->button_label ?: 'Buka Aplikasi';
    }

    public function getIsLinkableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_INTERNAL], true) && filled($this->url);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('is_active', true);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('status', self::VISIBLE_STATUSES)->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    private function normalizeThumbnailPath(mixed $value): ?string
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
            $path = parse_url($path, PHP_URL_PATH) ?: '';
        }

        if (str_contains($path, 'livewire-tmp')) {
            return null;
        }

        foreach ([storage_path('app/public'), public_path('storage')] as $prefix) {
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

        if (preg_match('/^[A-Za-z]:\//', $path) || Str::startsWith($path, '/')) {
            return null;
        }

        return ltrim($path, '/');
    }
}
