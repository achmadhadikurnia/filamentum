<?php

use App\Support\Csp\FilamentumCspPreset;
use Spatie\Csp\Nonce\RandomString;

return [

    /*
    |--------------------------------------------------------------------------
    | CSP Presets
    |--------------------------------------------------------------------------
    |
    | Presets define which CSP directives will be set.
    | See: app/Support/Csp/FilamentumCspPreset.php
    |
    */

    'presets' => [
        FilamentumCspPreset::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Directives
    |--------------------------------------------------------------------------
    |
    | Register additional global CSP directives here without modifying
    | the preset class. Useful for quick additions via config.
    |
    | Example:
    |   [Directive::SCRIPT, ['https://cdn.example.com']],
    |
    */

    'directives' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Report-Only Presets
    |--------------------------------------------------------------------------
    |
    | These presets will be put in a report-only policy. Great for testing
    | changes to CSP without breaking anything in production.
    |
    */

    'report_only_presets' => [
        //
    ],

    'report_only_directives' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Report URI
    |--------------------------------------------------------------------------
    |
    | All CSP violations will be reported to this URL.
    | A great service for this is https://report-uri.com/
    |
    */

    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable CSP
    |--------------------------------------------------------------------------
    |
    | Toggle CSP headers via .env: CSP_ENABLED=true/false
    | Disable during development if CSP causes issues.
    |
    */

    'enabled' => env('CSP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | CSP During Vite Hot Reload
    |--------------------------------------------------------------------------
    |
    | Whether to add CSP headers during Vite HMR (npm run dev).
    | Set to false to avoid CSP issues during development.
    |
    */

    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    /*
    |--------------------------------------------------------------------------
    | Nonce Generator
    |--------------------------------------------------------------------------
    |
    | The class responsible for generating nonces used in inline tags.
    |
    */

    'nonce_generator' => RandomString::class,

    /*
    |--------------------------------------------------------------------------
    | Nonce Enabled
    |--------------------------------------------------------------------------
    |
    | Set false to disable automatic nonce generation.
    | This is useful when using 'unsafe-inline' (as we do for Filament).
    |
    */

    'nonce_enabled' => env('CSP_NONCE_ENABLED', false),

];
