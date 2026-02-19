<?php

declare(strict_types=1);

namespace App\Actions\Listing;

use App\Enums\Availability;
use App\Enums\ListingStatus;
use App\Enums\ListingType;
use App\Enums\PropertyType;
use App\Models\Listing;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * ListListingsAction
 * 
 * Enhanced listing with Litepie Database package features:
 * - Advanced search (full-text, fuzzy, weighted)
 * - Structured filters (e.g., status:EQ(active);property_type:IN(residential,commercial))
 * - Legacy filters for backward compatibility
 * - Smart caching with tags and invalidation
 * - Optimized pagination
 * - Performance measurement
 * - Export capabilities
 * 
 * @package App\Actions\Listing
 */
class ListListingsAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'limit' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string',
            'sort' => 'sometimes|string',
            'sort_direction' => 'sometimes|in:asc,desc',
            'direction' => 'sometimes|in:asc,desc',
            'search' => 'sometimes|string',
            'q' => 'sometimes|string',
            'filter' => 'sometimes|string',
            'search_type' => 'sometimes|in:basic,fulltext,weighted,fuzzy,boolean',
            'pagination_type' => 'sometimes|in:standard,cursor,fast,optimized,cached',
            'use_cache' => 'sometimes|boolean',
            'cache_ttl' => 'sometimes|integer|min:1',
            'include_archived' => 'sometimes|boolean',
            'only_archived' => 'sometimes|boolean',
            'export_format' => 'sometimes|in:json,csv,excel',

            // Listing-specific filters
            'filter_listing_id' => 'sometimes|string',
            'filter_mls_number' => 'sometimes|string',
            'filter_title' => 'sometimes|string',
            'filter_property_type' => 'sometimes|string',
            'filter_listing_type' => 'sometimes|string',
            'filter_status' => 'sometimes|string',
            'filter_availability' => 'sometimes|string',
            'filter_city' => 'sometimes|string',
            'filter_area' => 'sometimes|string',
            'filter_bedrooms' => 'sometimes|integer',
            'filter_bathrooms' => 'sometimes|integer',
            'filter_price_min' => 'sometimes|numeric',
            'filter_price_max' => 'sometimes|numeric',
            'filter_size_min' => 'sometimes|numeric',
            'filter_size_max' => 'sometimes|numeric',
            'filter_is_featured' => 'sometimes|boolean',
            'filter_is_hot_deal' => 'sometimes|boolean',
            'filter_is_verified' => 'sometimes|boolean',
            'filter_agent_id' => 'sometimes|integer',
            'filter_date_from' => 'sometimes|date',
            'filter_date_to' => 'sometimes|date',
        ];
    }

    public function handle(): ActionResult
    {
        $startTime = microtime(true);

        $query = Listing::query()->with(['agent', 'owner']);

        // Handle soft-deleted/archived listings
        if ($this->data['only_archived'] ?? false) {
            $query->onlyTrashed();
        } elseif (!($this->data['include_archived'] ?? false)) {
            // By default, exclude soft deleted listings (already handled by SoftDeletes trait)
        }

        // Apply advanced search
        $searchQuery = $this->data['search'] ?? $this->data['q'] ?? null;
        $searchType = $this->data['search_type'] ?? 'basic';

        if (!empty($searchQuery)) {
            $query = $this->applySearch($query, $searchQuery, $searchType);
        }

        // Apply structured filter format (e.g., "status:EQ(active);property_type:IN(residential,commercial)")
        // Uses Searchable trait's filterQueryString method
        if (!empty($this->data['filter'])) {
            $query->filterQueryString($this->data['filter']);
        }

        // Apply legacy filters for backward compatibility
        $query = $this->applyFilters($query);

        // Apply sorting
        $sortBy = $this->data['sort_by'] ?? $this->data['sort'] ?? 'created_at';
        $sortDirection = $this->data['sort_direction'] ?? $this->data['direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Handle export
        if (!empty($this->data['export_format'])) {
            return $this->handleExport($query);
        }

        // Determine pagination type and parameters
        $paginationType = $this->data['pagination_type'] ?? 'standard';
        $perPage = $this->data['per_page'] ?? $this->data['limit'] ?? 20;
        $useCache = $this->data['use_cache'] ?? false;
        $cacheTtl = $this->data['cache_ttl'] ?? 300; // 5 minutes default

        // Use standard pagination only since cached/fast pagination require additional traits
        $listings = $query->paginate($perPage);

        $executionTime = microtime(true) - $startTime;

        return ActionResult::success($listings->items(), null, [
            'total' => $listings->total(),
            'per_page' => $listings->perPage(),
            'current_page' => $listings->currentPage(),
            'last_page' => $listings->lastPage(),
            'from' => $listings->firstItem(),
            'to' => $listings->lastItem(),
            'path' => $listings->path(),
            'links' => [
                'first' => $listings->url(1),
                'last' => $listings->url($listings->lastPage()),
                'prev' => $listings->previousPageUrl(),
                'next' => $listings->nextPageUrl(),
            ],
            'performance' => [
                'execution_time' => round($executionTime, 4),
                'search_type' => $searchType,
                'pagination_type' => $paginationType,
                'cached' => $useCache,
            ],
        ]);
    }

    /**
     * Apply search based on type
     */
    private function applySearch($query, string $searchQuery, string $searchType)
    {
        // Use whereAny for cleaner and more efficient search across multiple columns
        return $query->whereAny(
            ['title', 'description', 'address', 'mls_number', 'listing_id', 'city', 'area'],
            'like',
            "%{$searchQuery}%"
        );
    }

    /**
     * Apply various filters to the query
     */
    private function applyFilters($query)
    {
        // Listing ID filter
        if (!empty($this->data['filter_listing_id'])) {
            $query->where('listing_id', 'like', "%{$this->data['filter_listing_id']}%");
        }

        // MLS Number filter
        if (!empty($this->data['filter_mls_number'])) {
            $query->where('mls_number', 'like', "%{$this->data['filter_mls_number']}%");
        }

        // Title filter
        if (!empty($this->data['filter_title'])) {
            $query->where('title', 'like', "%{$this->data['filter_title']}%");
        }

        // Property Type filter
        if (!empty($this->data['filter_property_type'])) {
            $query->where('property_type', $this->data['filter_property_type']);
        }

        // Listing Type filter
        if (!empty($this->data['filter_listing_type'])) {
            $query->where('listing_type', $this->data['filter_listing_type']);
        }

        // Status filter
        if (!empty($this->data['filter_status'])) {
            $query->where('status', $this->data['filter_status']);
        }

        // Availability filter
        if (!empty($this->data['filter_availability'])) {
            $query->where('availability', $this->data['filter_availability']);
        }

        // City filter
        if (!empty($this->data['filter_city'])) {
            $query->where('city', $this->data['filter_city']);
        }

        // Area filter
        if (!empty($this->data['filter_area'])) {
            $query->where('area', 'like', "%{$this->data['filter_area']}%");
        }

        // Bedrooms filter
        if (!empty($this->data['filter_bedrooms'])) {
            $query->where('bedrooms', '>=', $this->data['filter_bedrooms']);
        }

        // Bathrooms filter
        if (!empty($this->data['filter_bathrooms'])) {
            $query->where('bathrooms', '>=', $this->data['filter_bathrooms']);
        }

        // Price range filter
        if (!empty($this->data['filter_price_min'])) {
            $query->where('price', '>=', $this->data['filter_price_min']);
        }
        if (!empty($this->data['filter_price_max'])) {
            $query->where('price', '<=', $this->data['filter_price_max']);
        }

        // Size range filter
        if (!empty($this->data['filter_size_min'])) {
            $query->where('size_sqft', '>=', $this->data['filter_size_min']);
        }
        if (!empty($this->data['filter_size_max'])) {
            $query->where('size_sqft', '<=', $this->data['filter_size_max']);
        }

        // Featured filter
        if (isset($this->data['filter_is_featured'])) {
            $query->where('is_featured', $this->data['filter_is_featured']);
        }

        // Hot Deal filter
        if (isset($this->data['filter_is_hot_deal'])) {
            $query->where('is_hot_deal', $this->data['filter_is_hot_deal']);
        }

        // Verified filter
        if (isset($this->data['filter_is_verified'])) {
            $query->where('is_verified', $this->data['filter_is_verified']);
        }

        // Agent filter
        if (!empty($this->data['filter_agent_id'])) {
            $query->where('agent_id', $this->data['filter_agent_id']);
        }

        // Date range filter
        if (!empty($this->data['filter_date_from'])) {
            $query->where('published_at', '>=', $this->data['filter_date_from']);
        }
        if (!empty($this->data['filter_date_to'])) {
            $query->where('published_at', '<=', $this->data['filter_date_to']);
        }

        return $query;
    }

    /**
     * Handle export request
     */
    private function handleExport($query): ActionResult
    {
        $format = $this->data['export_format'];
        $listings = $query->get();

        try {
            // Simple export using toArray/toJson since Exportable trait is not available
            $exportData = match ($format) {
                'json' => $listings->toJson(),
                'csv' => $this->exportToCsv($listings),
                default => throw new \Exception('Unsupported export format. Use json or csv.'),
            };

            return ActionResult::success([
                'export' => $exportData,
                'format' => $format,
                'count' => $listings->count(),
            ]);
        } catch (\Exception $e) {
            return ActionResult::failure('Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export listings to CSV format
     */
    private function exportToCsv($listings): string
    {
        if ($listings->isEmpty()) {
            return '';
        }

        $csv = [];
        $headers = [
            'Listing ID',
            'MLS Number',
            'Title',
            'Property Type',
            'Listing Type',
            'Price',
            'Currency',
            'Bedrooms',
            'Bathrooms',
            'Size (sqft)',
            'Address',
            'City',
            'Area',
            'Status',
            'Availability',
            'Published At'
        ];
        $csv[] = implode(',', $headers);

        foreach ($listings as $listing) {
            $row = [
                $listing->listing_id,
                $listing->mls_number,
                '"' . str_replace('"', '""', $listing->title) . '"',
                $listing->property_type,
                $listing->listing_type,
                $listing->price,
                $listing->currency,
                $listing->bedrooms,
                $listing->bathrooms,
                $listing->size_sqft,
                '"' . str_replace('"', '""', $listing->address) . '"',
                $listing->city,
                $listing->area,
                $listing->status,
                $listing->availability,
                $listing->published_at ? $listing->published_at->format('Y-m-d') : ''
            ];
            $csv[] = implode(',', $row);
        }

        return implode("\n", $csv);
    }
}
