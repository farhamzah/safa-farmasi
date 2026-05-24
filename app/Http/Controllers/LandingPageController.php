<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AppCategory;
use App\Models\PortalApplication;
use App\Support\LandingSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(Request $request, LandingSettings $landingSettings): View
    {
        $search = trim((string) $request->query('q', ''));
        $selectedCategory = trim((string) $request->query('category', ''));

        $categories = AppCategory::query()
            ->active()
            ->ordered()
            ->get();

        $applications = PortalApplication::query()
            ->with('categories')
            ->visible()
            ->when($selectedCategory !== '', function (Builder $query) use ($selectedCategory): void {
                $query->whereHas('categories', function (Builder $query) use ($selectedCategory): void {
                    $query->where('slug', $selectedCategory)->where('is_active', true);
                });
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('short_name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('long_description', 'like', "%{$search}%")
                        ->orWhereHas('categories', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->ordered()
            ->get();

        return view('landing', [
            'settings' => $landingSettings->all(),
            'search' => $search,
            'selectedCategory' => $selectedCategory,
            'categories' => $categories,
            'applications' => $applications,
            'announcements' => Announcement::query()
                ->currentlyVisible()
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }
}
