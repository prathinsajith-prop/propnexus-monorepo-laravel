<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP Cache Headers Middleware
 *
 * Adds appropriate cache-control headers to responses for better performance.
 * Supports different cache strategies for layouts, API endpoints, and static data.
 */
class AddCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $type = 'default'): Response
    {
        $response = $next($request);

        // Only add cache headers to successful responses
        if (! $response->isSuccessful()) {
            return $response;
        }

        // Get cache configuration based on type
        $config = match ($type) {
            'layout' => config('performance.http_cache.layouts'),
            'api' => config('performance.http_cache.api'),
            'static' => config('performance.http_cache.static'),
            default => null,
        };

        if (! $config) {
            return $response;
        }

        // Build Cache-Control header
        $cacheControl = $this->buildCacheControlHeader($config);

        // Add headers
        $response->headers->set('Cache-Control', $cacheControl);

        // Add ETag for better cache validation
        if ($response->getContent()) {
            $etag = md5($response->getContent());
            $response->headers->set('ETag', '"'.$etag.'"');

            // Check if client has cached version
            $clientEtag = str_replace('"', '', $request->header('If-None-Match'));
            if ($clientEtag === $etag) {
                return response('', 304)->withHeaders($response->headers->all());
            }
        }

        // Add Vary header for content negotiation
        $response->headers->set('Vary', 'Accept, Accept-Encoding');

        return $response;
    }

    /**
     * Build Cache-Control header value from configuration
     */
    private function buildCacheControlHeader(array $config): string
    {
        $parts = [];

        // Add public/private directive
        $parts[] = 'public';

        // Add max-age
        if (isset($config['max_age'])) {
            $parts[] = 'max-age='.$config['max_age'];
        }

        // Add s-maxage (for CDN/proxy)
        if (isset($config['shared_max_age'])) {
            $parts[] = 's-maxage='.$config['shared_max_age'];
        }

        // Add stale-while-revalidate
        if (isset($config['stale_while_revalidate'])) {
            $parts[] = 'stale-while-revalidate='.$config['stale_while_revalidate'];
        }

        // Add stale-if-error
        if (isset($config['stale_if_error'])) {
            $parts[] = 'stale-if-error='.$config['stale_if_error'];
        }

        return implode(', ', $parts);
    }
}
