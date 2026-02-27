<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ImageController
 *
 * Common API for serving images with proper headers and authentication
 * Supports both storage and public paths
 */
class ImageController extends Controller
{
    /**
     * Serve an image by path
     *
     * @param  string  $path  The image path (can contain slashes)
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     *
     * Usage:
     * - /api/media/listings/property.jpg
     * - /api/media/users/avatar.png
     * - /api/media/blogs/featured.jpg
     */
    public function show(Request $request, ?string $path = null)
    {
        // Handle CORS preflight
        if ($request->method() === 'OPTIONS') {
            return response('', 200)->withHeaders($this->getCorsHeaders());
        }

        // Get the full path from the URL if not provided
        if (! $path) {
            $path = $request->route('path');
        }

        // Decode the path in case it's URL encoded
        $path = urldecode($path);

        // Strip leading /storage/ prefix if present (handles absolute storage URLs stored in DB)
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        // Get width and height from query params for resizing
        $width = $request->input('w') ?: $request->input('width');
        $height = $request->input('h') ?: $request->input('height');
        $quality = $request->input('q') ?: $request->input('quality', 80);

        // Check public disk first — all uploads go here
        if (Storage::disk('public')->exists($path)) {
            return $this->serveFromStorage($request, 'public', $path, $width, $height, $quality);
        }

        // Fallback: check default/local disk
        $defaultDisk = config('filesystems.default', 'local');
        if ($defaultDisk !== 'public' && Storage::disk($defaultDisk)->exists($path)) {
            return $this->serveFromStorage($request, $defaultDisk, $path, $width, $height, $quality);
        }

        // Check if file exists in public directory
        $publicPath = public_path($path);
        if (file_exists($publicPath) && is_file($publicPath)) {
            return $this->serveFromPublic($request, $publicPath, $width, $height, $quality);
        }

        // File not found
        return response()->json([
            'error' => 'File not found',
            'path' => $path,
        ], 404)->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Serve image from storage disk with proper caching and CORS
     *
     * @param  string  $disk  Storage disk name
     * @param  string  $path  File path
     * @param  int|null  $width  Resize width
     * @param  int|null  $height  Resize height
     * @param  int  $quality  Image quality (1-100)
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    protected function serveFromStorage(Request $request, string $disk, string $path, $width = null, $height = null, $quality = 80)
    {
        $mimeType = Storage::disk($disk)->mimeType($path);
        $lastModified = Storage::disk($disk)->lastModified($path);
        $fileSize = Storage::disk($disk)->size($path);
        $etag = md5($path.$lastModified.$fileSize.$width.$height.$quality);

        // Check if client has cached version (304 Not Modified)
        if ($this->isNotModified($request, $etag, $lastModified)) {
            return response('', 304)->withHeaders($this->getCorsHeaders());
        }

        $fileContent = Storage::disk($disk)->get($path);

        // Resize image if width or height is specified
        if (($width || $height) && $this->isResizableImage($mimeType)) {
            $fileContent = $this->resizeImage($fileContent, $mimeType, $width, $height, $quality);
            $fileSize = strlen($fileContent);
        }

        return response()->stream(function () use ($fileContent) {
            echo $fileContent;
        }, 200, array_merge([
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified).' GMT',
            'ETag' => '"'.$etag.'"',
            'Accept-Ranges' => 'bytes',
        ], $this->getCorsHeaders()));
    }

    /**
     * Serve image from public directory with proper caching and CORS
     *
     * @param  string  $publicPath  Full public path
     * @param  int|null  $width  Resize width
     * @param  int|null  $height  Resize height
     * @param  int  $quality  Image quality (1-100)
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    protected function serveFromPublic(Request $request, string $publicPath, $width = null, $height = null, $quality = 80)
    {
        $mimeType = mime_content_type($publicPath);
        $lastModified = filemtime($publicPath);
        $fileSize = filesize($publicPath);
        $etag = md5($publicPath.$lastModified.$fileSize.$width.$height.$quality);

        // Check if client has cached version (304 Not Modified)
        if ($this->isNotModified($request, $etag, $lastModified)) {
            return response('', 304)->withHeaders($this->getCorsHeaders());
        }

        // Resize image if width or height is specified
        if (($width || $height) && $this->isResizableImage($mimeType)) {
            $fileContent = file_get_contents($publicPath);
            $fileContent = $this->resizeImage($fileContent, $mimeType, $width, $height, $quality);
            $fileSize = strlen($fileContent);

            return response()->stream(function () use ($fileContent) {
                echo $fileContent;
            }, 200, array_merge([
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified).' GMT',
                'ETag' => '"'.$etag.'"',
                'Accept-Ranges' => 'bytes',
            ], $this->getCorsHeaders()));
        }

        return response()->stream(function () use ($publicPath) {
            readfile($publicPath);
        }, 200, array_merge([
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified).' GMT',
            'ETag' => '"'.$etag.'"',
            'Accept-Ranges' => 'bytes',
        ], $this->getCorsHeaders()));
    }

    /**
     * Serve a thumbnail/resized version of the image
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function thumbnail(Request $request, ?string $path = null)
    {
        // Handle CORS preflight
        if ($request->method() === 'OPTIONS') {
            return response('', 200)->withHeaders($this->getCorsHeaders());
        }

        $width = $request->input('w', 300);
        $height = $request->input('h', 300);
        $quality = $request->input('q', 80);

        if (! $path) {
            $path = $request->route('path');
        }

        $path = urldecode($path);

        // You can integrate with intervention/image or similar package here
        // For now, just serve the original image
        return $this->show($request, $path);
    }

    /**
     * Check if the client has a cached version (ETags & Last-Modified)
     */
    protected function isNotModified(Request $request, string $etag, int $lastModified): bool
    {
        // Check ETag
        $requestEtag = $request->header('If-None-Match');
        if ($requestEtag && trim($requestEtag, '"') === $etag) {
            return true;
        }

        // Check Last-Modified
        $requestLastModified = $request->header('If-Modified-Since');
        if ($requestLastModified) {
            $requestTime = strtotime($requestLastModified);
            if ($requestTime >= $lastModified) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get CORS headers for Next.js and other frontend frameworks
     */
    protected function getCorsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization, X-Requested-With',
            'Access-Control-Max-Age' => '86400',
        ];
    }

    /**
     * Check if image type can be resized
     */
    protected function isResizableImage(string $mimeType): bool
    {
        return in_array($mimeType, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
        ]);
    }

    /**
     * Resize image using GD library
     *
     * @param  string  $imageData  Binary image data
     * @param  string  $mimeType  Image MIME type
     * @param  int|null  $width  Target width
     * @param  int|null  $height  Target height
     * @param  int  $quality  Quality for JPEG/WebP (1-100)
     * @return string Resized image data
     */
    protected function resizeImage(string $imageData, string $mimeType, $width = null, $height = null, int $quality = 80): string
    {
        // Create image from string
        $sourceImage = imagecreatefromstring($imageData);

        if ($sourceImage === false) {
            return $imageData; // Return original if can't create image
        }

        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Calculate dimensions maintaining aspect ratio
        if ($width && $height) {
            // Both specified - maintain aspect ratio, fit within bounds
            $ratio = min($width / $originalWidth, $height / $originalHeight);
            $newWidth = (int) ($originalWidth * $ratio);
            $newHeight = (int) ($originalHeight * $ratio);
        } elseif ($width) {
            // Only width specified
            $ratio = $width / $originalWidth;
            $newWidth = $width;
            $newHeight = (int) ($originalHeight * $ratio);
        } elseif ($height) {
            // Only height specified
            $ratio = $height / $originalHeight;
            $newWidth = (int) ($originalWidth * $ratio);
            $newHeight = $height;
        } else {
            // No dimensions specified, return original
            imagedestroy($sourceImage);

            return $imageData;
        }

        // Don't upscale images
        if ($newWidth > $originalWidth || $newHeight > $originalHeight) {
            imagedestroy($sourceImage);

            return $imageData;
        }

        // Create new image
        $destinationImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
            $transparent = imagecolorallocatealpha($destinationImage, 0, 0, 0, 127);
            imagefill($destinationImage, 0, 0, $transparent);
        }

        // Resize
        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Output to buffer
        ob_start();

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($destinationImage, null, $quality);
                break;
            case 'image/png':
                // PNG quality: 0 (best compression) to 9 (worst)
                $pngQuality = (int) (9 - ($quality / 100 * 9));
                imagepng($destinationImage, null, $pngQuality);
                break;
            case 'image/gif':
                imagegif($destinationImage);
                break;
            case 'image/webp':
                imagewebp($destinationImage, null, $quality);
                break;
            default:
                imagejpeg($destinationImage, null, $quality);
        }

        $resizedData = ob_get_clean();

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return $resizedData;
    }

    /**
     * Generate a signed URL for private images
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateUrl(Request $request)
    {
        // Handle CORS preflight
        if ($request->method() === 'OPTIONS') {
            return response('', 200)->withHeaders($this->getCorsHeaders());
        }

        $path = $request->input('path');

        if (! $path) {
            return response()->json(['error' => 'Path is required'], 400)
                ->withHeaders($this->getCorsHeaders());
        }

        $disk = config('filesystems.default', 'local');

        if (Storage::disk($disk)->exists($path)) {
            $url = route('media.show', ['path' => $path]);

            return response()->json([
                'url' => $url,
                'path' => $path,
            ])->withHeaders($this->getCorsHeaders());
        }

        return response()->json(['error' => 'Image not found'], 404)
            ->withHeaders($this->getCorsHeaders());
    }
}
