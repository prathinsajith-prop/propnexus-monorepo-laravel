<?php

namespace App\Support;

use Illuminate\Http\Request;

class CacheKeyGenerator
{
    /**
     * Generate a cache key for a layout response.
     */
    public static function forLayout(string $prefix, ?Request $request = null): string
    {
        $request = $request ?? request();

        $parts = [
            'layout',
            $prefix,
            $request->path(),
        ];

        // Include query parameters in cache key
        if ($request->query()) {
            $queryString = http_build_query(self::normalizeQueryParams($request->query()));
            $parts[] = md5($queryString);
        }

        return implode(':', $parts);
    }

    /**
     * Generate a cache key for API data.
     */
    public static function forApi(string $prefix, array $params = []): string
    {
        $parts = ['api', $prefix];

        if (! empty($params)) {
            $parts[] = md5(json_encode(self::normalizeQueryParams($params)));
        }

        return implode(':', $parts);
    }

    /**
     * Normalize query parameters for consistent cache keys.
     */
    private static function normalizeQueryParams(array $params): array
    {
        // Remove empty values
        $params = array_filter($params, fn ($value) => $value !== null && $value !== '');

        // Sort by key for consistency
        ksort($params);

        return $params;
    }

    /**
     * Get cache tags for a layout.
     */
    public static function tagsForLayout(string $controller): array
    {
        return [
            'layouts',
            'layout:'.$controller,
        ];
    }
}
