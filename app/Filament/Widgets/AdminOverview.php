<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use App\Models\AppCategory;
use App\Models\ApplicationClick;
use App\Models\PortalApplication;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AdminOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan SAFA UBP';

    protected function getStats(): array
    {
        $stats = Cache::remember('admin_overview_stats', now()->addSeconds(30), function (): array {
            return [
                'active_applications' => PortalApplication::query()->where('status', 'active')->where('is_active', true)->count(),
                'maintenance_applications' => PortalApplication::query()->where('status', 'maintenance')->where('is_active', true)->count(),
                'coming_soon_applications' => PortalApplication::query()->where('status', 'coming_soon')->where('is_active', true)->count(),
                'active_categories' => AppCategory::query()->where('is_active', true)->count(),
                'active_announcements' => Announcement::query()->currentlyVisible()->count(),
                'clicks_last_seven_days' => Schema::hasTable('application_clicks')
                    ? ApplicationClick::query()->where('clicked_at', '>=', now()->subDays(7))->count()
                    : 0,
            ];
        });

        return [
            Stat::make('Aplikasi Aktif', $stats['active_applications']),
            Stat::make('Maintenance', $stats['maintenance_applications']),
            Stat::make('Segera Hadir', $stats['coming_soon_applications']),
            Stat::make('Kategori Aktif', $stats['active_categories']),
            Stat::make('Pengumuman Aktif', $stats['active_announcements']),
            Stat::make('Klik 7 Hari', $stats['clicks_last_seven_days']),
        ];
    }
}
