<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class VerifyRoutes extends Command
{
    protected $signature = 'verify:routes';

    protected $description = 'Verify all routes, controller methods, actions and models';

    public function handle(): int
    {
        // ── Controller methods ────────────────────────────────────────
        $checks = [
            ['BlogController', [
                'list',
                'lists',
                'store',
                'show',
                'update',
                'destroy',
                'getStats',
                'incrementView',
                'masterData',
                'uploadImage',
                'uploadVideo',
                'uploadDocument',
                'uploadAudio',
                'uploadAttachment',
                'upload',
                'deleteFile',
                'getComponentSection',
            ]],
            ['ListingController', [
                'list',
                'create',
                'show',
                'update',
                'delete',
                'getStats',
                'getMasterDataApi',
                'uploadImage',
                'uploadDocument',
                'uploadVideo',
                'uploadAttachment',
                'upload',
                'deleteFile',
                'getComponentSection',
            ]],
            ['ProductPropertyController', [
                'list',
                'create',
                'show',
                'update',
                'delete',
                'getStats',
                'getMasterDataApi',
                'activities',
                'uploadImage',
                'uploadDocument',
                'uploadVideo',
                'uploadAttachment',
                'upload',
                'deleteFile',
                'getComponentSection',
            ]],
            ['GeneralController', [
                'index',
                'documentation',
                'sample',
                'users',
                'store',
                'getUser',
                'masterData',
                'update',
                'destroy',
                'uploadImage',
                'uploadDocument',
                'upload',
                'deleteFile',
                'getComponentSection',
            ]],
        ];

        $missing = [];
        foreach ($checks as [$ctrl, $methods]) {
            $class = "App\\Http\\Controllers\\$ctrl";
            foreach ($methods as $method) {
                if (! method_exists($class, $method)) {
                    $missing[] = "$ctrl@$method";
                }
            }
        }
        $total = array_sum(array_map(fn ($c) => count($c[1]), $checks));
        empty($missing)
            ? $this->info("Controllers ($total methods): ALL OK")
            : $this->error('Controllers MISSING: '.implode(', ', $missing));

        // ── Action classes ────────────────────────────────────────────
        $actions = [
            'App\\Actions\\Blog\\ListBlogsAction',
            'App\\Actions\\Blog\\CreateBlogAction',
            'App\\Actions\\Blog\\UpdateBlogAction',
            'App\\Actions\\Blog\\DeleteBlogAction',
            'App\\Actions\\Blog\\GetBlogAction',
            'App\\Actions\\Listing\\ListListingsAction',
            'App\\Actions\\Listing\\CreateListingAction',
            'App\\Actions\\Listing\\UpdateListingAction',
            'App\\Actions\\Listing\\DeleteListingAction',
            'App\\Actions\\Listing\\GetListingAction',
            'App\\Actions\\ProductProperty\\ListProductPropertiesAction',
            'App\\Actions\\ProductProperty\\CreateProductPropertyAction',
            'App\\Actions\\ProductProperty\\UpdateProductPropertyAction',
            'App\\Actions\\ProductProperty\\DeleteProductPropertyAction',
            'App\\Actions\\ProductProperty\\GetProductPropertyAction',
            'App\\Actions\\User\\ListUsersAction',
            'App\\Actions\\User\\CreateUserAction',
            'App\\Actions\\User\\UpdateUserAction',
            'App\\Actions\\User\\DeleteUserAction',
            'App\\Actions\\User\\GetUserAction',
            'App\\Actions\\File\\FileUploadAction',
        ];
        $missingActions = array_values(array_filter($actions, fn ($a) => ! class_exists($a) || ! method_exists($a, 'handle') || ! method_exists($a, 'run')));
        empty($missingActions)
            ? $this->info('Actions ('.count($actions).' classes): ALL OK')
            : array_map(fn ($a) => $this->error("Action MISSING/invalid: $a"), $missingActions);

        // ── Models ────────────────────────────────────────────────────
        $models = ['App\\Models\\Blog', 'App\\Models\\Listing', 'App\\Models\\BixoProductProperties', 'App\\Models\\User'];
        $missingModels = array_values(array_filter($models, fn ($m) => ! class_exists($m)));
        empty($missingModels)
            ? $this->info('Models ('.count($models).'): ALL OK')
            : array_map(fn ($m) => $this->error("Model MISSING: $m"), $missingModels);

        // ── Live route → controller binding check ─────────────────────
        $routeErrors = [];
        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction();
            if (isset($action['controller']) && str_contains($action['controller'], '@')) {
                [$class, $method] = explode('@', $action['controller']);
                if (! method_exists($class, $method)) {
                    $routeErrors[] = $route->uri()." → $class@$method";
                }
            }
        }
        $this->info('Routes registered: '.count(Route::getRoutes()));
        empty($routeErrors)
            ? $this->info('Route → Controller binding: ALL OK')
            : array_map(fn ($e) => $this->error("Route broken: $e"), $routeErrors);

        $hasErrors = ! empty($missing) || ! empty($missingActions) || ! empty($missingModels) || ! empty($routeErrors);
        if (! $hasErrors) {
            $this->newLine();
            $this->info('All routes, controllers, actions and models are valid.');
        }

        return $hasErrors ? self::FAILURE : self::SUCCESS;
    }
}
