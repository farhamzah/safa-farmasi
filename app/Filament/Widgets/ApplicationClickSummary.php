<?php

namespace App\Filament\Widgets;

use App\Models\ApplicationClick;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApplicationClickSummary extends Widget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.application-click-summary';

    protected int | string | array $columnSpan = 'full';

    public function getTopApplications(): Collection
    {
        if (! Schema::hasTable('application_clicks')) {
            return collect();
        }

        return Cache::remember('admin_top_application_clicks', now()->addSeconds(30), function (): Collection {
            return ApplicationClick::query()
                ->select('application_name', DB::raw('COUNT(*) as total_clicks'))
                ->whereNotNull('application_name')
                ->groupBy('application_name')
                ->orderByDesc('total_clicks')
                ->limit(5)
                ->get();
        });
    }
}
