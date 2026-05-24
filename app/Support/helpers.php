<?php

use App\Support\LandingSettings;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(LandingSettings::class)->get($key, $default);
    }
}
