<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ListBlogsAction
 *
 * Enhanced blog listing with Litepie Database package features:
 * - Advanced search (full-text, fuzzy, weighted)
 * - Smart caching with tags and invalidation
 * - Optimized pagination (cursor, fast, cached)
 * - Performance measurement
 * - Export capabilities
 */
class ListBlogsAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'limit' => 'sometimes|integer|min:1|max:100', // alias for per_page
            'sort_by' => 'sometimes|string',
            'sort' => 'sometimes|string', // alias for sort_by
            'sort_direction' => 'sometimes|in:asc,desc',
            'direction' => 'sometimes|in:asc,desc', // alias for sort_direction
            'search' => 'sometimes|string',
            'q' => 'sometimes|string', // alias for search
            'filter' => 'sometimes|string', // New structured filter format
            'search_type' => 'sometimes|in:basic,fulltext,weighted,fuzzy,boolean',
            'pagination_type' => 'sometimes|in:standard,cursor,fast,optimized,cached',
            'use_cache' => 'sometimes|boolean',
            'cache_ttl' => 'sometimes|integer|min:1',
            'include_archived' => 'sometimes|boolean',
            'only_archived' => 'sometimes|boolean',
            'export_format' => 'sometimes|in:json,csv,excel',
            // Legacy filters
            'filter_blog_id' => 'sometimes|string',
            'filter_title' => 'sometimes|string',
            'filter_status' => 'sometimes|string',
            'filter_category' => 'sometimes|string',
            'filter_tag' => 'sometimes|string',
            'filter_author_id' => 'sometimes|integer',
            'filter_language' => 'sometimes|string',
            'filter_is_featured' => 'sometimes|boolean',
            'filter_is_sticky' => 'sometimes|boolean',
            'filter_visibility' => 'sometimes|string',
            'filter_date_from' => 'sometimes|date',
            'filter_date_to' => 'sometimes|date',
        ];
    }

    public function handle(): ActionResult
    {
        $startTime = microtime(true);

        $query = Blog::query();

        // Handle archived posts
        if ($this->data['only_archived'] ?? false) {
            $query->onlyArchived();
        } elseif (! ($this->data['include_archived'] ?? false)) {
            // By default, exclude archived posts
            $query->whereNull('archived_at');
        }

        // Apply advanced search based on type
        $searchQuery = $this->data['search'] ?? $this->data['q'] ?? null;
        $searchType = $this->data['search_type'] ?? 'basic';

        if (! empty($searchQuery)) {
            $query = $this->applySearch($query, $searchQuery, $searchType);
        }

        // Apply structured filter format (e.g., "status:EQ(published);category:IN(tech,blog)")
        // Uses Searchable trait's filterQueryString method
        if (! empty($this->data['filter'])) {
            $query->filterQueryString($this->data['filter']);
        }

        // Apply legacy filters for backward compatibility
        $query = $this->applyLegacyFilters($query);

        // Apply sorting
        $sortBy = $this->data['sort_by'] ?? $this->data['sort'] ?? 'id';
        $sortDirection = $this->data['sort_direction'] ?? $this->data['direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Determine pagination type and parameters
        $perPage = $this->data['per_page'] ?? $this->data['limit'] ?? 10;
        $paginationType = $this->data['pagination_type'] ?? 'cached';
        $useCache = $this->data['use_cache'] ?? true;
        $cacheTtl = $this->data['cache_ttl'] ?? 300; // 5 minutes default

        // Apply pagination with caching
        $result = $this->applyPagination($query, $paginationType, $perPage, $cacheTtl, $useCache, $searchType);

        // Handle export if requested
        if (! empty($this->data['export_format'])) {
            return $this->handleExport($query, $this->data['export_format']);
        }

        return $result;
    }

    /**
     * Apply search with different strategies
     */
    protected function applySearch($query, string $searchQuery, string $searchType)
    {
        switch ($searchType) {
            case 'fulltext':
                // MySQL FULLTEXT search
                return $query->fullTextSearch($searchQuery);

            case 'weighted':
                // Weighted search with relevance scoring
                return $query->weightedSearch($searchQuery);

            case 'fuzzy':
                // Fuzzy search (finds typos)
                return $query->fuzzySearch($searchQuery, threshold: 2);

            case 'boolean':
                // Boolean search with +/- operators
                return $query->booleanSearch($searchQuery);

            case 'basic':
            default:
                // Basic Searchable trait search
                return $query->search($searchQuery);
        }
    }

    /**
     * Apply legacy filters for backward compatibility
     */
    protected function applyLegacyFilters($query)
    {
        if (! empty($this->data['filter_blog_id'])) {
            $query->where('blog_id', 'like', '%'.$this->data['filter_blog_id'].'%');
        }

        if (! empty($this->data['filter_title'])) {
            $query->where('title', 'like', '%'.$this->data['filter_title'].'%');
        }

        if (! empty($this->data['filter_status'])) {
            $query->where('status', $this->data['filter_status']);
        }

        if (! empty($this->data['filter_category'])) {
            $query->where(function ($categoryQuery) {
                $category = $this->data['filter_category'];
                $categoryQuery->where('category', $category)
                    ->orWhereJsonContains('categories', $category);
            });
        }

        if (! empty($this->data['filter_tag'])) {
            $query->whereJsonContains('tags', $this->data['filter_tag']);
        }

        if (! empty($this->data['filter_author_id'])) {
            $query->where('author_id', $this->data['filter_author_id']);
        }

        if (! empty($this->data['filter_language'])) {
            $query->where('language', $this->data['filter_language']);
        }

        if (isset($this->data['filter_is_featured'])) {
            $query->where('is_featured', (bool) $this->data['filter_is_featured']);
        }

        if (isset($this->data['filter_is_sticky'])) {
            $query->where('is_sticky', (bool) $this->data['filter_is_sticky']);
        }

        if (! empty($this->data['filter_visibility'])) {
            $query->where('visibility', $this->data['filter_visibility']);
        }

        if (! empty($this->data['filter_date_from'])) {
            $query->where('created_at', '>=', $this->data['filter_date_from']);
        }

        if (! empty($this->data['filter_date_to'])) {
            $query->where('created_at', '<=', $this->data['filter_date_to']);
        }

        return $query;
    }

    /**
     * Apply pagination based on type
     */
    protected function applyPagination($query, string $type, int $perPage, int $cacheTtl, bool $useCache, string $searchType)
    {
        switch ($type) {
            case 'cursor':
                // Cursor pagination (best for large datasets, infinite scroll)
                $paginator = $query->cursorPaginate($perPage);

                return $this->formatCursorPagination($paginator, $searchType, $type, $useCache);

            case 'fast':
                // Fast pagination (no total count, uses LIMIT + 1)
                $paginator = $query->fastPaginate($perPage);

                return $this->formatStandardPagination($paginator, $searchType, $type, $useCache);

            case 'optimized':
                // Optimized pagination (uses approximate count for large tables)
                $paginator = $query->optimizedPaginate($perPage);

                return $this->formatStandardPagination($paginator, $searchType, $type, $useCache);

            case 'cached':
                // Cached pagination (caches total count)
                if ($useCache) {
                    $paginator = $query->cachedPaginate(perPage: $perPage, cacheTtl: $cacheTtl);
                } else {
                    $paginator = $query->paginate($perPage);
                }

                return $this->formatStandardPagination($paginator, $searchType, $type, $useCache);

            case 'standard':
            default:
                // Standard Laravel pagination
                $paginator = $query->paginate($perPage);

                return $this->formatStandardPagination($paginator, $searchType, $type, $useCache);
        }
    }

    /**
     * Format standard pagination response
     */
    protected function formatStandardPagination($paginator, $searchType, $paginationType, $useCache): ActionResult
    {
        return ActionResult::success($paginator->items(), null, [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'path' => $paginator->path(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'performance' => [
                'search_type' => $searchType,
                'pagination_type' => $paginationType,
                'cached' => $useCache,
            ],
        ]);
    }

    /**
     * Format cursor pagination response
     */
    protected function formatCursorPagination($paginator, $searchType, $paginationType, $useCache): ActionResult
    {
        return ActionResult::success($paginator->items(), null, [
            'per_page' => $paginator->perPage(),
            'path' => $paginator->path(),
            'cursor' => [
                'current' => $paginator->cursor()?->encode(),
                'prev' => $paginator->previousCursor()?->encode(),
                'next' => $paginator->nextCursor()?->encode(),
            ],
            'has_more' => $paginator->hasMorePages(),
            'performance' => [
                'search_type' => $searchType,
                'pagination_type' => $paginationType,
                'cached' => $useCache,
            ],
        ]);
    }

    /**
     * Handle export requests
     */
    protected function handleExport($query, string $format): ActionResult
    {
        $blogs = $query->get();

        $export = match ($format) {
            'csv' => $blogs->exportToCsv(),
            'excel' => $blogs->exportToExcel(),
            default => $blogs->exportToJson(),
        };

        return ActionResult::success($export, null, [
            'format' => $format,
            'count' => $blogs->count(),
        ]);
    }
}
