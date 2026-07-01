<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SAFA Admin Authentication Mode
    |--------------------------------------------------------------------------
    |
    | local: use the local SAFA users table only.
    | core_bridge: verify the password and app access in Core, then log in a
    | local SAFA admin mirror user for the Filament session.
    | core_bridge_with_local_fallback: try Core first, then local credentials.
    |
    */
    'mode' => env('SAFA_AUTH_MODE', 'local'),

    'core' => [
        'connection' => env('SAFA_CORE_DB_CONNECTION', 'core'),
        'app_code' => env('SAFA_CORE_APP_CODE', 'safa-ubp'),
        'allowed_roles' => array_filter(array_map(
            'trim',
            explode(',', env('SAFA_CORE_ALLOWED_ROLES', 'admin-safa'))
        )),
    ],
];
