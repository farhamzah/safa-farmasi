<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationClick;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClickExportController extends Controller
{
    public function __invoke(): RedirectResponse|StreamedResponse
    {
        if (! auth()->check()) {
            return redirect('/admin/login');
        }

        abort_unless(auth()->user()?->is_admin, 403);

        $filename = 'safa-application-clicks-export-'.now()->toDateString().'.csv';
        $clickTableExists = Schema::hasTable('application_clicks');

        return response()->streamDownload(function () use ($clickTableExists): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'application_name',
                'target_url',
                'clicked_at',
                'user_agent',
            ]);

            if (! $clickTableExists) {
                fclose($handle);

                return;
            }

            ApplicationClick::query()
                ->latest('clicked_at')
                ->chunk(100, function ($clicks) use ($handle): void {
                    foreach ($clicks as $click) {
                        fputcsv($handle, [
                            $click->application_name,
                            $click->target_url,
                            optional($click->clicked_at)->toDateTimeString(),
                            $click->user_agent,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
