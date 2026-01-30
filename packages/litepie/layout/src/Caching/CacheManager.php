<?php

namespace Litepie\Layout\Caching;

use Illuminate\Support\Facades\Cache;
use Litepie\Layout\Layout;

class CacheManager
{
    protected string $driver;

    protected int $defaultTtl;

    protected string $prefix;

    public function __construct(string $driver = 'file', int $defaultTtl = 3600, string $prefix = 'litepie_layout')
    {
        $this->driver = $driver;
        $this->defaultTtl = $defaultTtl;
        $this->prefix = $prefix;
    }

    /**
     * Get cached layout
     */
    public function get(string $key): ?array
    {
        $cacheKey = $this->getCacheKey($key);

        return Cache::store($this->driver)->get($cacheKey);
    }

    /**
     * Store layout in cache
     */
    public function put(string $key, array $layout, ?int $ttl = null): bool
    {
        $cacheKey = $this->getCacheKey($key);
        $ttl = $ttl ?? $this->defaultTtl;

        return Cache::store($this->driver)->put($cacheKey, $layout, $ttl);
    }

    /**
     * Remember layout with callback
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): array
    {
        $cacheKey = $this->getCacheKey($key);
        $ttl = $ttl ?? $this->defaultTtl;

        return Cache::store($this->driver)->remember($cacheKey, $ttl, $callback);
    }

    /**
     * Invalidate specific layout cache
     */
    public function forget(string $key): bool
    {
        $cacheKey = $this->getCacheKey($key);

        return Cache::store($this->driver)->forget($cacheKey);
    }

    /**
     * Invalidate all layout caches
     */
    public function flush(): bool
    {
        // Get all cache keys with our prefix
        $tags = $this->getTags();

        if (! empty($tags)) {
            return Cache::store($this->driver)->tags($tags)->flush();
        }

        // Fallback: flush entire cache store (use with caution)
        return Cache::store($this->driver)->flush();
    }

    /**
     * Check if layout is cached
     */
    public function has(string $key): bool
    {
        $cacheKey = $this->getCacheKey($key);

        return Cache::store($this->driver)->has($cacheKey);
    }

    /**
     * Get cache key with prefix
     */
    protected function getCacheKey(string $key): string
    {
        return $this->prefix.':'.$key;
    }

    /**
     * Get cache tags for layout
     */
    protected function getTags(): array
    {
        return [$this->prefix];
    }

    /**
     * Set cache driver
     */
    public function driver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Set default TTL
     */
    public function ttl(int $seconds): self
    {
        $this->defaultTtl = $seconds;

        return $this;
    }

    /**
     * Set cache prefix
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Generate cache key from layout properties
     */
    public function generateKey(string $name, string $mode, ?array $params = null): string
    {
        $key = "{$name}:{$mode}";

        if ($params) {
            $key .= ':'.md5(json_encode($params));
        }

        return $key;
    }

    /**
     * Invalidate by pattern
     */
    public function forgetByPattern(string $pattern): int
    {
        $count = 0;
        $fullPattern = $this->getCacheKey($pattern);

        // This is driver-specific, works best with Redis
        if ($this->driver === 'redis') {
            $redis = Cache::store('redis')->getRedis();
            $keys = $redis->keys($fullPattern);

            foreach ($keys as $key) {
                Cache::store($this->driver)->forget($key);
                $count++;
            }
        }

        return $count;
    }
}
