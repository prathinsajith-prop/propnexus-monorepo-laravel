<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Caching\CacheManager;

trait Cacheable
{
    protected ?CacheManager $cacheManager = null;

    protected bool $cachingEnabled = false;

    protected ?int $cacheTtl = null;

    protected ?string $cacheKey = null;

    protected array $cacheInvalidationTags = [];

    /**
     * Enable caching for this layout
     */
    public function cache(bool $enabled = true, ?int $ttl = null): self
    {
        $this->cachingEnabled = $enabled;

        if ($ttl !== null) {
            $this->cacheTtl = $ttl;
        }

        return $this;
    }

    /**
     * Set custom cache key
     */
    public function cacheKey(string $key): self
    {
        $this->cacheKey = $key;

        return $this;
    }

    /**
     * Set cache TTL in seconds
     */
    public function cacheTtl(int $seconds): self
    {
        $this->cacheTtl = $seconds;

        return $this;
    }

    /**
     * Add invalidation tags
     */
    public function cacheInvalidateOn(string|array $tags): self
    {
        $tags = is_array($tags) ? $tags : [$tags];
        $this->cacheInvalidationTags = array_merge($this->cacheInvalidationTags, $tags);

        return $this;
    }

    /**
     * Get cache manager instance
     */
    protected function getCacheManager(): CacheManager
    {
        if ($this->cacheManager === null) {
            $this->cacheManager = new CacheManager(
                $this->config('litepie.layout.cache.driver', 'file'),
                $this->config('litepie.layout.cache.ttl', 3600),
                $this->config('litepie.layout.cache.prefix', 'litepie_layout')
            );
        }

        return $this->cacheManager;
    }

    /**
     * Safe config helper
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return function_exists('config') ? config($key, $default) : $default;
    }

    /**
     * Get the cache key for this layout
     */
    protected function getLayoutCacheKey(): string
    {
        if ($this->cacheKey !== null) {
            return $this->cacheKey;
        }

        return $this->getCacheManager()->generateKey(
            $this->name,
            $this->mode,
            $this->getCacheKeyParams()
        );
    }

    /**
     * Get parameters to include in cache key
     */
    protected function getCacheKeyParams(): ?array
    {
        $params = [];

        // Include shared data URL in cache key if present
        if (! empty($this->sharedDataUrl)) {
            $params['shared_data'] = $this->sharedDataUrl;
        }

        // Include user context if needed (for permission-based caching)
        if ($this->config('litepie.layout.cache.per_user', false)) {
            $userId = function_exists('auth') ? (auth()->id() ?? 'guest') : 'guest';
            $params['user'] = $userId;
        }

        return ! empty($params) ? $params : null;
    }

    /**
     * Get cached layout or generate and cache it
     */
    protected function getCachedLayout(): array
    {
        if (! $this->cachingEnabled) {
            return $this->toArray();
        }

        $cacheManager = $this->getCacheManager();
        $cacheKey = $this->getLayoutCacheKey();

        return $cacheManager->remember(
            $cacheKey,
            fn () => $this->toArray(),
            $this->cacheTtl
        );
    }

    /**
     * Invalidate cache for this layout
     */
    public function invalidateCache(): bool
    {
        if (! $this->cachingEnabled) {
            return false;
        }

        $cacheKey = $this->getLayoutCacheKey();

        return $this->getCacheManager()->forget($cacheKey);
    }

    /**
     * Check if layout is cached
     */
    public function isCached(): bool
    {
        if (! $this->cachingEnabled) {
            return false;
        }

        $cacheKey = $this->getLayoutCacheKey();

        return $this->getCacheManager()->has($cacheKey);
    }
}
