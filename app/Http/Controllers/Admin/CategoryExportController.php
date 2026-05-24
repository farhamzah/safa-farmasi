<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppCategory;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CategoryExportController extends Controller
{
    public function __invoke(): RedirectResponse|StreamedResponse
    {
        if (! auth()->check()) {
            return redirect('/admin/login');
        }

        abort_unless(auth()->user()?->is_admin, 403);

        $filename = 'safa-categories-export-'.now()->toDateString().'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'name',
                'slug',
                'description',
                'icon',
                'sort_order',
                'is_active',
                'applications_count',
                'updated_at',
            ]);

            AppCategory::query()
                ->withCount('applications')
                ->ordered()
                ->chunk(100, function ($categories) use ($handle): void {
                    foreach ($categories as $category) {
                        fputcsv($handle, [
                            $category->name,
                            $category->slug,
                            $category->description,
                            $category->icon,
                            $category->sort_order,
                            (int) $category->is_active,
                            $category->applications_count,
                            optional($category->updated_at)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
