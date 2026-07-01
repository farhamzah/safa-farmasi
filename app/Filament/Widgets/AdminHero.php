<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use App\Models\AppCategory;
use App\Models\ApplicationClick;
use App\Models\PortalApplication;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AdminHero extends Widget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.admin-hero';

    protected int | string | array $columnSpan = 'full';

    public function getSummary(): array
    {
        return Cache::remember('admin_hero_summary', now()->addSeconds(30), function (): array {
            return [
                'visible_applications' => PortalApplication::query()->visible()->count(),
                'featured_applications' => PortalApplication::query()->visible()->where('is_featured', true)->count(),
                'active_categories' => AppCategory::query()->where('is_active', true)->count(),
                'active_announcements' => Announcement::query()->currentlyVisible()->count(),
                'clicks_today' => Schema::hasTable('application_clicks')
                    ? ApplicationClick::query()->whereDate('clicked_at', today())->count()
                    : 0,
            ];
        });
    }
}
