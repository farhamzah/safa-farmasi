<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalApplication;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationExportController extends Controller
{
    public function __invoke(): RedirectResponse|StreamedResponse
    {
        if (! auth()->check()) {
            return redirect('/admin/login');
        }

        abort_unless(auth()->user()?->is_admin, 403);

        $filename = 'safa-applications-export-'.now()->toDateString().'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'name',
                'slug',
                'short_description',
                'url',
                'status',
                'button_label',
                'categories',
                'sort_order',
                'is_featured',
                'is_active',
                'open_in_new_tab',
                'updated_at',
            ]);

            PortalApplication::query()
                ->with('categories')
                ->ordered()
                ->chunk(100, function ($applications) use ($handle): void {
                    foreach ($applications as $application) {
                        fputcsv($handle, [
                            $application->name,
                            $application->slug,
                            $application->short_description,
                            $application->url,
                            $application->status,
                            $application->button_label,
                            $application->categories->pluck('name')->join('|'),
                            $application->sort_order,
                            (int) $application->is_featured,
                            (int) $application->is_active,
                            (int) $application->open_in_new_tab,
                            optional($application->updated_at)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
