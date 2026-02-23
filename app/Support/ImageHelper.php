<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * ImageHelper
 *
 * High-performance helper class for generating image URLs and handling image paths
 * Uses caching and optimized operations to minimize overhead
 */
class ImageHelper
{
    /**
     * Custom base URL for image serving (overrides APP_URL)
     */
    protected static ?string $baseUrl = null;

    /**
     * Cached app URL to avoid repeated config lookups
     */
    protected static ?string $cachedAppUrl = null;

    /**
     * Cached storage URL base to avoid repeated lookups
     */
    protected static ?string $cachedStorageUrl = null;

    /**
     * Cached public disk instance
     */
    protected static $publicDisk = null;

    /**
     * Cache for file existence checks (LRU cache with max 100 entries)
     */
    protected static array $existsCache = [];

    protected static int $maxCacheSize = 100;

    /**
     * Cache for generated URLs (LRU cache)
     */
    protected static array $urlCache = [];

    /**
     * Set custom base URL for all image URLs
     *
     * @param  string|null  $url  Base URL (e.g., 'http://192.168.1.100:8000')
     */
    public static function setBaseUrl(?string $url): void
    {
        self::$baseUrl = $url ? rtrim($url, '/') : null;
        self::clearCache(); // Clear cache when base URL changes
    }

    /**
     * Get the base URL (cached)
     */
    protected static function getBaseUrl(): string
    {
        if (self::$baseUrl !== null) {
            return self::$baseUrl;
        }

        if (self::$cachedAppUrl === null) {
            self::$cachedAppUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
        }

        return self::$cachedAppUrl;
    }

    /**
     * Get public disk instance (cached)
     */
    protected static function getPublicDisk()
    {
        if (self::$publicDisk === null) {
            self::$publicDisk = Storage::disk('public');
        }

        return self::$publicDisk;
    }

    /**
     * Get cached storage path (without base URL)
     */
    protected static function getStoragePath(): string
    {
        if (self::$cachedStorageUrl === null) {
            // Get only the path portion, not the full URL
            $fullUrl = config('filesystems.disks.public.url', '/storage');

            // Extract path from full URL if needed
            if (str_starts_with($fullUrl, 'http://') || str_starts_with($fullUrl, 'https://')) {
                $parsed = parse_url($fullUrl);
                self::$cachedStorageUrl = $parsed['path'] ?? '/storage';
            } else {
                self::$cachedStorageUrl = $fullUrl;
            }

            self::$cachedStorageUrl = '/'.trim(self::$cachedStorageUrl, '/');
        }

        return self::$cachedStorageUrl;
    }

    /**
     * Check if file exists with caching
     */
    protected static function fileExists(string $path): bool
    {
        if (isset(self::$existsCache[$path])) {
            return self::$existsCache[$path];
        }

        $exists = self::getPublicDisk()->exists($path);

        // Implement simple LRU cache
        if (count(self::$existsCache) >= self::$maxCacheSize) {
            array_shift(self::$existsCache);
        }

        self::$existsCache[$path] = $exists;

        return $exists;
    }

    /**
     * Clear all caches
     */
    public static function clearCache(): void
    {
        self::$existsCache = [];
        self::$urlCache = [];
        self::$cachedAppUrl = null;
        self::$cachedStorageUrl = null;
        self::$publicDisk = null;
    }

    /**
     * Generate an image URL from a storage path (optimized with caching)
     *
     * @param  string|null  $path  Image path
     * @param  array  $params  Additional query parameters (w, h, q)
     * @param  bool  $forcePrivate  Force using API route (for private images)
     * @return string|null
     *
     * Usage:
     * ImageHelper::url('listings/property.jpg')
     * ImageHelper::url('users/avatar.png', ['w' => 300, 'h' => 300])
     * ImageHelper::url('private/doc.jpg', [], true)
     */
    public static function url(?string $path, array $params = [], bool $forcePrivate = false): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Check cache for this exact request
        $cacheKey = $path.'|'.serialize($params).'|'.($forcePrivate ? '1' : '0');
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        // Fast check: If already a full URL, return immediately
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return self::cacheUrl($cacheKey, $path);
        }

        $baseUrl = self::getBaseUrl();

        // Use direct storage URL for public files (fastest path)
        if (! $forcePrivate && empty($params) && self::fileExists($path)) {
            $url = $baseUrl.self::getStoragePath().'/'.ltrim($path, '/');

            return self::cacheUrl($cacheKey, $url);
        }

        // Build API URL directly (faster than route())
        $url = $baseUrl.'/api/images/'.$path;

        // Add query parameters if provided
        if (! empty($params)) {
            $url .= '?'.http_build_query($params);
        }

        return self::cacheUrl($cacheKey, $url);
    }

    /**
     * Cache a URL with LRU eviction
     */
    protected static function cacheUrl(string $key, string $url): string
    {
        if (count(self::$urlCache) >= self::$maxCacheSize) {
            array_shift(self::$urlCache);
        }

        self::$urlCache[$key] = $url;

        return $url;
    }

    /**
     * Generate a thumbnail URL with specific dimensions (optimized)
     *
     * @param  string|null  $path  Image path
     * @param  int  $width  Width in pixels
     * @param  int  $height  Height in pixels
     * @param  int  $quality  Quality 1-100
     */
    public static function thumbnail(?string $path, int $width = 300, int $height = 300, int $quality = 80): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Build URL directly (faster than route())
        $baseUrl = self::getBaseUrl();
        $url = "{$baseUrl}/api/images/thumbnail/{$path}?w={$width}&h={$height}&q={$quality}";

        return $url;
    }

    /**
     * Get a placeholder image URL
     *
     * @param  int  $width  Width in pixels
     * @param  int  $height  Height in pixels
     * @param  string  $text  Optional text to display
     * @param  string  $bg  Background color (hex without #)
     * @param  string  $color  Text color (hex without #)
     * @return string
     *
     * Usage:
     * ImageHelper::placeholder(300, 300, 'No Image')
     */
    public static function placeholder(
        int $width = 300,
        int $height = 300,
        string $text = '',
        string $bg = 'cccccc',
        string $color = '666666'
    ): string {
        // Use placeholder.com or similar service
        $text = urlencode($text ?: "{$width}x{$height}");

        return "https://via.placeholder.com/{$width}x{$height}/{$bg}/{$color}?text={$text}";
    }

    /**
     * Get image URL or placeholder if path is null (optimized)
     *
     * @param  string|null  $path  Image path
     * @param  array  $params  URL parameters
     * @param  array  $placeholderParams  Placeholder parameters [width, height, text]
     */
    public static function urlOrPlaceholder(
        ?string $path,
        array $params = [],
        array $placeholderParams = [300, 300, 'No Image']
    ): string {
        return ! empty($path)
            ? self::url($path, $params)
            : self::placeholder(...$placeholderParams);
    }

    /**
     * Get multiple image URLs from an array of paths (optimized batch processing)
     *
     * @param  array|null  $paths  Array of image paths
     * @param  array  $params  URL parameters
     * @param  bool  $forcePrivate  Force private URLs for all
     */
    public static function urls(?array $paths, array $params = [], bool $forcePrivate = false): array
    {
        if (empty($paths)) {
            return [];
        }

        // Pre-compute values that are the same for all URLs
        $baseUrl = self::getBaseUrl();
        $storageBase = self::getStorageUrl();
        $useParams = ! empty($params);
        $queryString = $useParams ? '?'.http_build_query($params) : '';

        $urls = [];

        foreach ($paths as $path) {
            if (empty($path)) {
                continue;
            }

            // Fast path for already full URLs
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $urls[] = $path;

                continue;
            }

            // Optimize: Build URLs directly without individual method calls
            if (! $forcePrivate && ! $useParams && self::fileExists($path)) {
                $urls[] = $baseUrl.$storageBase.'/'.$path;
            } else {
                $urls[] = $baseUrl.'/api/images/'.$path.$queryString;
            }
        }

        return $urls;
    }

    /**
     * Get image dimensions from path (with caching)
     *
     * @param  string  $path  Image path
     * @return array|null [width, height] or null if not found
     */
    public static function dimensions(string $path): ?array
    {
        static $dimensionsCache = [];

        // Check cache first
        if (isset($dimensionsCache[$path])) {
            return $dimensionsCache[$path];
        }

        $fullPath = storage_path('app/public/'.$path);

        if (! file_exists($fullPath)) {
            return $dimensionsCache[$path] = null;
        }

        $size = @getimagesize($fullPath);

        if ($size === false) {
            return $dimensionsCache[$path] = null;
        }

        return $dimensionsCache[$path] = [
            'width' => $size[0],
            'height' => $size[1],
        ];
    }

    /**
     * Check if path is an image (optimized with static lookup)
     *
     * @param  string  $path  File path
     */
    public static function isImage(string $path): bool
    {
        static $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, $imageExtensions, true);
    }

    /**
     * Get image extension from path
     *
     * @param  string  $path  Image path
     */
    public static function extension(string $path): ?string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Generate multiple thumbnails efficiently (batch processing)
     *
     * @param  array  $paths  Array of image paths
     * @param  int  $width  Width in pixels
     * @param  int  $height  Height in pixels
     * @param  int  $quality  Quality 1-100
     */
    public static function thumbnails(array $paths, int $width = 300, int $height = 300, int $quality = 80): array
    {
        if (empty($paths)) {
            return [];
        }

        $baseUrl = self::getBaseUrl();
        $queryString = "?w={$width}&h={$height}&q={$quality}";

        $thumbnails = [];
        foreach ($paths as $path) {
            if (! empty($path)) {
                $thumbnails[] = "{$baseUrl}/api/images/thumbnail/{$path}{$queryString}";
            }
        }

        return $thumbnails;
    }

    /**
     * Warm up the cache for a list of paths (pre-check file existence)
     * Useful for preloading before batch operations
     *
     * @param  array  $paths  Array of image paths to warm cache
     * @return int Number of files that exist
     */
    public static function warmCache(array $paths): int
    {
        $count = 0;
        $disk = self::getPublicDisk();

        foreach ($paths as $path) {
            if (! empty($path) && ! isset(self::$existsCache[$path])) {
                $exists = $disk->exists($path);

                if (count(self::$existsCache) >= self::$maxCacheSize) {
                    array_shift(self::$existsCache);
                }

                self::$existsCache[$path] = $exists;

                if ($exists) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Get cache statistics for monitoring/debugging
     */
    public static function getCacheStats(): array
    {
        return [
            'exists_cache_size' => count(self::$existsCache),
            'url_cache_size' => count(self::$urlCache),
            'max_cache_size' => self::$maxCacheSize,
            'cache_hit_rate' => self::calculateCacheHitRate(),
        ];
    }

    /**
     * Calculate cache hit rate (estimation)
     */
    protected static function calculateCacheHitRate(): float
    {
        $cacheSize = count(self::$urlCache);

        return $cacheSize > 0 ? min(100, ($cacheSize / self::$maxCacheSize) * 100) : 0;
    }

    /**
     * Set maximum cache size
     */
    public static function setMaxCacheSize(int $size): void
    {
        self::$maxCacheSize = max(10, $size); // Minimum 10 entries
    }
}
