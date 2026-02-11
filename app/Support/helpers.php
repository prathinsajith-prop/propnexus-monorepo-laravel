<?php

use App\Support\ImageHelper;

if (!function_exists('image_url')) {
    /**
     * Generate an image URL from a storage path
     * 
     * @param string|null $path Image path
     * @param array $params Additional query parameters (w, h, q)
     * @return string|null
     */
    function image_url(?string $path, array $params = []): ?string
    {
        return ImageHelper::url($path, $params);
    }
}

if (!function_exists('image_thumbnail')) {
    /**
     * Generate a thumbnail URL with specific dimensions
     * 
     * @param string|null $path Image path
     * @param int $width Width in pixels
     * @param int $height Height in pixels
     * @param int $quality Quality 1-100
     * @return string|null
     */
    function image_thumbnail(?string $path, int $width = 300, int $height = 300, int $quality = 80): ?string
    {
        return ImageHelper::thumbnail($path, $width, $height, $quality);
    }
}

if (!function_exists('image_placeholder')) {
    /**
     * Get a placeholder image URL
     * 
     * @param int $width Width in pixels
     * @param int $height Height in pixels
     * @param string $text Optional text to display
     * @return string
     */
    function image_placeholder(int $width = 300, int $height = 300, string $text = ''): string
    {
        return ImageHelper::placeholder($width, $height, $text);
    }
}

if (!function_exists('image_or_placeholder')) {
    /**
     * Get image URL or placeholder if path is null
     * 
     * @param string|null $path Image path
     * @param array $params URL parameters
     * @param int $width Placeholder width
     * @param int $height Placeholder height
     * @param string $text Placeholder text
     * @return string
     */
    function image_or_placeholder(
        ?string $path,
        array $params = [],
        int $width = 300,
        int $height = 300,
        string $text = 'No Image'
    ): string {
        return ImageHelper::urlOrPlaceholder($path, $params, [$width, $height, $text]);
    }
}
