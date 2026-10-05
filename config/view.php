<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Vercel's filesystem is read-only except /tmp, so compiled Blade views
    | must live there (AGENTS.md §29).
    */

    'compiled' => env('VIEW_COMPILED_PATH', '/tmp'),

];
