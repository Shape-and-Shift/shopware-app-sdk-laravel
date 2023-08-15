<?php

declare(strict_types=1);

return [
    'app_name' => env('SW_APP_NAME', 'MyApp'),
    'app_secret' => env('SW_APP_SECRET', 'MyAppSecret'),
    'registration_url' => env('SW_APP_REGISTER_URL', '/app/register'),
    'confirmation_url' => env('SW_APP_CONFIRM_URL', '/app/register/confirm'),
    'activate_url' => env('SW_APP_ACTIVATE_URL', '/app/activate'),
    'deactivate_url' => env('SW_APP_DEACTIVATE_URL', '/app/deactivate'),
    'delete_url' => env('SW_APP_DELETE_URL', '/app/delete'),
];
