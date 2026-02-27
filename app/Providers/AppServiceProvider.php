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
        $this->fixLitepieLogsEvents();
    }

    /**
     * Register a prepended autoloader that fixes two bugs in the litepie/logs package:
     *
     * 1. PSR-4 mapping expects three individual class files but the package ships
     *    all three event classes inside a single ActivityLogEvents.php file.
     * 2. ActivityLogCreating::__construct() was typed as `array $attributes` but
     *    Laravel's $dispatchesEvents mechanism passes the model instance.
     *
     * Using a custom autoloader means no vendor files are modified, so the fix
     * survives `composer install` / `composer update`.
     */
    private function fixLitepieLogsEvents(): void
    {
        $affected = [
            'Litepie\\Logs\\Events\\ActivityLogCreating',
            'Litepie\\Logs\\Events\\ActivityLogCreated',
            'Litepie\\Logs\\Events\\ActivityLogged',
        ];

        spl_autoload_register(function (string $class) use ($affected): void {
            if (in_array($class, $affected, true)) {
                require_once app_path('Support/LitepieLogsEventsFix.php');
            }
        }, prepend: true);
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
