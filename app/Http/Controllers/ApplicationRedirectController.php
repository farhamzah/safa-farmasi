<?php

namespace App\Http\Controllers;

use App\Models\ApplicationClick;
use App\Models\PortalApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationRedirectController extends Controller
{
    public function __invoke(Request $request, PortalApplication $portalApplication): RedirectResponse
    {
        abort_unless($portalApplication->is_active && $portalApplication->is_linkable, 404);
        abort_unless($this->isSafeRedirectUrl($portalApplication->url), 404);

        ApplicationClick::query()->create([
            'portal_application_id' => $portalApplication->id,
            'application_name' => $portalApplication->name,
            'target_url' => $portalApplication->url,
            'clicked_at' => now(),
            'ip_hash' => $this->hashIp($request->ip()),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
        ]);

        return redirect()->away($portalApplication->url);
    }

    private function isSafeRedirectUrl(?string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    private function hashIp(?string $ip): ?string
    {
        if (blank($ip)) {
            return null;
        }

        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }
}
