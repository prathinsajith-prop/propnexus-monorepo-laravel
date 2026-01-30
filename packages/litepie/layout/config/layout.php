<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for layouts. Layouts are cached per user
    | to improve performance.
    |
    */

    'cache' => [
        'ttl' => env('LAYOUT_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('LAYOUT_CACHE_PREFIX', 'litepie_layout'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Registered Layouts
    |--------------------------------------------------------------------------
    |
    | Register your layouts here. Each layout should be keyed by
    | 'module.context' and the value should be a closure that receives
    | a LayoutBuilder instance.
    |
    */

    'layouts' => [
        // Example:
        // 'user.profile' => function ($builder) {
        //     $builder->section('personal_info')
        //         ->label('Personal Information')
        //         ->subsection('basic')
        //             ->field('first_name')->type('text')->label('First Name')->required()->end()
        //             ->field('last_name')->type('text')->label('Last Name')->required()->end()
        //         ->endSubsection()
        //     ->endSection();
        // },
    ],

];
