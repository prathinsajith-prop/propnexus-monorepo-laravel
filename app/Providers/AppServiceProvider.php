<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('layout', function ($layout, ?int $ttl = null, ?string $cacheKey = null) {
            // Get cache configuration
            $defaultTtl = config('performance.cache.layout_ttl', 3600);
            $ttl = $ttl ?? $defaultTtl;

            // Generate cache key if not provided
            if (! $cacheKey) {
                $cacheKey = \App\Support\CacheKeyGenerator::forLayout(
                    class_basename($layout),
                    request()
                );
            }

            // Always cache layout responses for maximum performance
            $cachedResponse = cache()->remember($cacheKey, $ttl, function () use ($layout) {
                return $layout->render();
            });

            // Add marker header so middleware knows this is a layout response
            return response()
                ->json($cachedResponse)
                ->header('X-Layout-Response', 'true');
        });
    }
}
