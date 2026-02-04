<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache TTL (Time To Live) settings for various application components.
    | All values are in seconds.
    |
    */

    'cache' => [
        /*
         * Layout response cache TTL
         * Layout structures rarely change, so we can cache for longer
         */
        'layout_ttl' => env('CACHE_LAYOUT_TTL', 3600), // 1 hour

        /*
         * Master data cache TTL
         * Dropdowns, enums, and static data
         */
        'master_data_ttl' => env('CACHE_MASTER_DATA_TTL', 1800), // 30 minutes

        /*
         * Statistics/metrics cache TTL
         * Count queries and aggregations
         */
        'stats_ttl' => env('CACHE_STATS_TTL', 300), // 5 minutes

        /*
         * Query result cache TTL
         * General database query results
         */
        'query_ttl' => env('CACHE_QUERY_TTL', 600), // 10 minutes

        /*
         * API response cache TTL
         * External API calls or heavy computations
         */
        'api_ttl' => env('CACHE_API_TTL', 900), // 15 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Cache Headers
    |--------------------------------------------------------------------------
    |
    | Cache-Control headers for different endpoint types
    |
    */

    'http_cache' => [
        /*
         * Layout endpoints cache headers
         */
        'layouts' => [
            'max_age' => 3600, // 1 hour
            'shared_max_age' => 7200, // 2 hours for CDN/proxy
            'stale_while_revalidate' => 86400, // 24 hours
            'stale_if_error' => 86400, // 24 hours
        ],

        /*
         * API endpoints cache headers
         */
        'api' => [
            'max_age' => 300, // 5 minutes
            'shared_max_age' => 600, // 10 minutes for CDN/proxy
            'stale_while_revalidate' => 3600, // 1 hour
            'stale_if_error' => 3600, // 1 hour
        ],

        /*
         * Static data endpoints (master data, etc.)
         */
        'static' => [
            'max_age' => 1800, // 30 minutes
            'shared_max_age' => 3600, // 1 hour for CDN/proxy
            'stale_while_revalidate' => 86400, // 24 hours
            'stale_if_error' => 86400, // 24 hours
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Tags
    |--------------------------------------------------------------------------
    |
    | Define cache tags for easy invalidation when data changes
    |
    */

    'tags' => [
        'blogs' => 'cache:blogs',
        'listings' => 'cache:listings',
        'users' => 'cache:users',
        'master_data' => 'cache:master-data',
        'layouts' => 'cache:layouts',
        'stats' => 'cache:stats',
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for query performance optimization
    |
    */

    'query' => [
        /*
         * Enable query result caching
         */
        'cache_enabled' => env('QUERY_CACHE_ENABLED', true),

        /*
         * Log slow queries (in milliseconds)
         */
        'log_slow_queries' => env('LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000), // 1 second

        /*
         * Enable query count tracking
         */
        'track_query_count' => env('TRACK_QUERY_COUNT', false),
    ],

];
