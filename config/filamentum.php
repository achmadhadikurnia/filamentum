<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Panel Path
    |--------------------------------------------------------------------------
    |
    | This configuration sets the URL path for the Filament admin panel.
    |
    */

    'path' => env('FILAMENTUM_PATH', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Panel Domain
    |--------------------------------------------------------------------------
    |
    | Set a specific domain for the panel. Leave null for all domains.
    |
    */

    'domain' => env('FILAMENTUM_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | SPA Mode
    |--------------------------------------------------------------------------
    |
    | Enable SPA-like navigation using Livewire's wire:navigate feature.
    |
    */

    'spa_mode' => env('FILAMENTUM_SPA_MODE', true),

    'spa_prefetching' => env('FILAMENTUM_SPA_PREFETCHING', false),

    /*
    |--------------------------------------------------------------------------
    | Content Layout
    |--------------------------------------------------------------------------
    |
    | Configure panel content layout settings.
    |
    */

    'max_content_width' => env('FILAMENTUM_MAX_CONTENT_WIDTH', '7xl'),

    'sub_navigation_position' => env('FILAMENTUM_SUB_NAV_POSITION', 'start'),

    /*
    |--------------------------------------------------------------------------
    | Panel Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable various panel features.
    |
    */

    'unsaved_changes_alerts' => env('FILAMENTUM_UNSAVED_ALERTS', false),

    'database_transactions' => env('FILAMENTUM_DB_TRANSACTIONS', false),

    'broadcasting' => env('FILAMENTUM_BROADCASTING', false),

    'strict_authorization' => env('FILAMENTUM_STRICT_AUTH', false),

    /*
    |--------------------------------------------------------------------------
    | Authentication Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable authentication features.
    |
    */

    'registration' => env('FILAMENTUM_REGISTRATION', false),

    'password_reset' => env('FILAMENTUM_PASSWORD_RESET', false),

    'email_verification' => env('FILAMENTUM_EMAIL_VERIFICATION', false),

    'email_change_verification' => env('FILAMENTUM_EMAIL_CHANGE_VERIFICATION', false),

    'profile' => env('FILAMENTUM_PROFILE', true),

    /*
    |--------------------------------------------------------------------------
    | Theme Settings
    |--------------------------------------------------------------------------
    |
    | Configure panel theme and appearance.
    |
    */

    'dark_mode' => env('FILAMENTUM_DARK_MODE', true),

    'default_theme_mode' => env('FILAMENTUM_DEFAULT_THEME', 'system'),

    'primary_color' => env('FILAMENTUM_PRIMARY_COLOR', 'amber'),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configure notification settings.
    |
    */

    'database_notifications' => env('FILAMENTUM_DB_NOTIFICATIONS', false),

];
