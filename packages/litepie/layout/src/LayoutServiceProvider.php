<?php

namespace Litepie\Layout;

use Illuminate\Support\ServiceProvider;

class LayoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/layout.php',
            'layout'
        );

        $this->app->singleton('layout', function ($app) {
            return new LayoutManager($app['cache.store']);
        });

        $this->app->alias('layout', LayoutManager::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/layout.php' => config_path('layout.php'),
            ], 'layout-config');
        }

        // Load layouts from configuration if available
        $this->loadLayoutsFromConfig();
    }

    /**
     * Load layouts from configuration
     */
    protected function loadLayoutsFromConfig(): void
    {
        $layouts = config('layout.layouts', []);

        foreach ($layouts as $key => $callback) {
            if (is_callable($callback)) {
                [$module, $context] = explode('.', $key, 2);
                $this->app['layout']->register($module, $context, $callback);
            }
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['layout', LayoutManager::class];
    }
}
