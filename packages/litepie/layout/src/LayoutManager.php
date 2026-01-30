<?php

namespace Litepie\Layout;

use Closure;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class LayoutManager
{
    protected CacheContract $cache;

    protected array $layouts = [];

    protected int $cacheTtl = 3600; // 1 hour default

    protected string $cachePrefix = 'litepie_layout';

    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Create a new layout builder (alias for for())
     */
    public function create(string $name): LayoutBuilder
    {
        return new LayoutBuilder($name, 'default');
    }

    /**
     * Create a new layout builder
     */
    public function for(string $module, string $context): LayoutBuilder
    {
        return new LayoutBuilder($module, $context);
    }

    /**
     * Register a layout
     */
    public function register(string $module, string $context, Closure $callback): void
    {
        $key = $this->makeKey($module, $context);
        $this->layouts[$key] = $callback;
    }

    /**
     * Get a layout (with user-specific caching)
     */
    public function get(string $module, string $context, ?int $userId = null): ?Layout
    {
        $cacheKey = $this->makeCacheKey($module, $context, $userId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($module, $context) {
            $key = $this->makeKey($module, $context);

            if (! isset($this->layouts[$key])) {
                return null;
            }

            $callback = $this->layouts[$key];
            $builder = $this->for($module, $context);
            $callback($builder);

            return $builder->build();
        });
    }

    /**
     * Build and cache a layout
     */
    public function build(string $module, string $context, Closure $callback, ?int $userId = null): Layout
    {
        $builder = $this->for($module, $context);
        $callback($builder);
        $layout = $builder->build();

        // Cache the layout
        $cacheKey = $this->makeCacheKey($module, $context, $userId);
        $this->cache->put($cacheKey, $layout, $this->cacheTtl);

        return $layout;
    }

    /**
     * Check if a layout exists
     */
    public function has(string $module, string $context): bool
    {
        $key = $this->makeKey($module, $context);

        return isset($this->layouts[$key]);
    }

    /**
     * Clear cache for a specific layout and user
     */
    public function clearCache(string $module, string $context, ?int $userId = null): void
    {
        $cacheKey = $this->makeCacheKey($module, $context, $userId);
        $this->cache->forget($cacheKey);
    }

    /**
     * Clear all cached layouts for a user
     */
    public function clearUserCache(int $userId): void
    {
        // This would require cache tagging or a custom implementation
        // For simplicity, we'll use a pattern-based approach if supported
        $pattern = "{$this->cachePrefix}:*:user:{$userId}";

        // Note: This depends on your cache driver supporting pattern deletion
        // For Redis, you could use SCAN and DEL commands
        // For file cache, you'd need to iterate through files
    }

    /**
     * Clear all layout caches
     */
    public function clearAllCache(): void
    {
        $this->cache->flush();
    }

    /**
     * Set cache TTL (in seconds)
     */
    public function setCacheTtl(int $seconds): self
    {
        $this->cacheTtl = $seconds;

        return $this;
    }

    /**
     * Set cache prefix
     */
    public function setCachePrefix(string $prefix): self
    {
        $this->cachePrefix = $prefix;

        return $this;
    }

    /**
     * Get all registered layouts
     */
    public function getRegistered(): array
    {
        return array_keys($this->layouts);
    }

    /**
     * Make a key for layout storage
     */
    protected function makeKey(string $module, string $context): string
    {
        return "{$module}.{$context}";
    }

    /**
     * Make a cache key with optional user ID
     */
    protected function makeCacheKey(string $module, string $context, ?int $userId = null): string
    {
        $key = "{$this->cachePrefix}:{$module}:{$context}";

        if ($userId !== null) {
            $key .= ":user:{$userId}";
        }

        return $key;
    }

    /**
     * Get layout directly from registration (without cache)
     */
    public function fresh(string $module, string $context): ?Layout
    {
        $key = $this->makeKey($module, $context);

        if (! isset($this->layouts[$key])) {
            return null;
        }

        $callback = $this->layouts[$key];
        $builder = $this->for($module, $context);
        $callback($builder);

        return $builder->build();
    }
}
