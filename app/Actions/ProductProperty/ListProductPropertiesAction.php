<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ListProductPropertiesAction
 *
 * List product properties with search, filters, sorting and pagination.
 */
class ListProductPropertiesAction extends BaseAction
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

            // Property-specific filters (filter_ prefix and bare names both accepted)
            'filter_ref' => 'sometimes|string',
            'filter_title' => 'sometimes|string',
            'filter_status' => 'sometimes|string',
            'status' => 'sometimes|string',
            'filter_category_type' => 'sometimes|string',
            'category_type' => 'sometimes|string',
            'filter_property_for' => 'sometimes|string',
            'property_for' => 'sometimes|string',
            'filter_property_type' => 'sometimes|string',
            'property_type' => 'sometimes|string',
            'filter_beds' => 'sometimes|string',
            'beds' => 'sometimes|string',
            'filter_baths' => 'sometimes|integer',
            'baths' => 'sometimes|integer',
            'filter_price_min' => 'sometimes|numeric',
            'filter_price_max' => 'sometimes|numeric',
            'filter_bua_min' => 'sometimes|numeric',
            'filter_bua_max' => 'sometimes|numeric',
            'filter_furnishing' => 'sometimes|string',
            'furnishing' => 'sometimes|string',
            'filter_construction_status' => 'sometimes|string',
            'construction_status' => 'sometimes|string',
            'filter_frequency' => 'sometimes|string',
            'frequency' => 'sometimes|string',
            'filter_parking' => 'sometimes|integer',
            'parking' => 'sometimes|integer',
            'filter_location_id' => 'sometimes|integer',
            'filter_building_id' => 'sometimes|integer',
            'filter_user_id' => 'sometimes|integer',
            'filter_date_from' => 'sometimes|date',
            'filter_date_to' => 'sometimes|date',
            'filter_published_at_from' => 'sometimes|date',
            'filter_published_at_to' => 'sometimes|date',
            'filter_created_at_from' => 'sometimes|date',
            'filter_created_at_to' => 'sometimes|date',
        ];
    }

    public function handle(): ActionResult
    {
        $startTime = microtime(true);
        $query = BixoProductProperties::query();

        // Search
        $searchQuery = $this->data['search'] ?? $this->data['q'] ?? null;
        if (! empty($searchQuery)) {
            $query->whereAny(
                ['title', 'ref', 'unit', 'description', 'tower_name'],
                'like',
                "%{$searchQuery}%"
            );
        }

        // Structured filter (filterQueryString from Searchable trait)
        if (! empty($this->data['filter'])) {
            $query->filterQueryString($this->data['filter']);
        }

        // Individual filters
        $this->applyFilters($query);

        // Sorting
        $sortBy = $this->data['sort_by'] ?? $this->data['sort'] ?? 'created_at';
        $sortDir = $this->data['sort_direction'] ?? $this->data['direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        // Pagination
        $perPage = $this->data['per_page'] ?? $this->data['limit'] ?? 20;
        $properties = $query->paginate($perPage);

        // Format items — add created_at_formatted / updated_at_formatted for display
        $items = collect($properties->items())->map(function ($property) {
            $arr = $property->toArray();
            $arr['created_at_formatted'] = $property->created_at
                ? $property->created_at->format('d M Y H:i A')
                : null;
            $arr['updated_at_formatted'] = $property->updated_at
                ? $property->updated_at->format('d M Y H:i A')
                : null;
            $arr['published_at_formatted'] = $property->published_at
                ? $property->published_at->format('d M Y H:i A')
                : null;

            return $arr;
        })->all();

        $executionTime = microtime(true) - $startTime;

        return ActionResult::success($items, 'Properties retrieved successfully', [
            'total' => $properties->total(),
            'per_page' => $properties->perPage(),
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
            'from' => $properties->firstItem(),
            'to' => $properties->lastItem(),
            'performance' => ['execution_time' => round($executionTime, 4)],
        ]);
    }

    private function applyFilters($query): void
    {
        if (! empty($this->data['filter_ref'])) {
            $query->where('ref', 'like', '%'.$this->data['filter_ref'].'%');
        }
        if (! empty($this->data['filter_title'])) {
            $query->where('title', 'like', '%'.$this->data['filter_title'].'%');
        }
        $status = $this->data['filter_status'] ?? $this->data['status'] ?? null;
        if (! empty($status)) {
            $query->where('status', $status);
        }
        $categoryType = $this->data['filter_category_type'] ?? $this->data['category_type'] ?? null;
        if (! empty($categoryType)) {
            $query->where('category_type', $categoryType);
        }
        $propertyFor = $this->data['filter_property_for'] ?? $this->data['property_for'] ?? null;
        if (! empty($propertyFor)) {
            $query->where('property_for', $propertyFor);
        }
        $propertyType = $this->data['filter_property_type'] ?? $this->data['property_type'] ?? null;
        if (! empty($propertyType)) {
            $query->where('property_type', $propertyType);
        }
        $beds = $this->data['filter_beds'] ?? $this->data['beds'] ?? null;
        if (! empty($beds)) {
            $query->where('beds', $beds);
        }
        $baths = $this->data['filter_baths'] ?? $this->data['baths'] ?? null;
        if (! empty($baths)) {
            $query->where('baths', $baths);
        }
        if (! empty($this->data['filter_price_min'])) {
            $query->where('price', '>=', $this->data['filter_price_min']);
        }
        if (! empty($this->data['filter_price_max'])) {
            $query->where('price', '<=', $this->data['filter_price_max']);
        }
        if (! empty($this->data['filter_bua_min'])) {
            $query->where('bua', '>=', $this->data['filter_bua_min']);
        }
        if (! empty($this->data['filter_bua_max'])) {
            $query->where('bua', '<=', $this->data['filter_bua_max']);
        }
        $furnishing = $this->data['filter_furnishing'] ?? $this->data['furnishing'] ?? null;
        if (! empty($furnishing)) {
            $query->where('furnishing', $furnishing);
        }
        $constructionStatus = $this->data['filter_construction_status'] ?? $this->data['construction_status'] ?? null;
        if (! empty($constructionStatus)) {
            $query->where('construction_status', $constructionStatus);
        }
        $frequency = $this->data['filter_frequency'] ?? $this->data['frequency'] ?? null;
        if (! empty($frequency)) {
            $query->where('frequency', $frequency);
        }
        $parking = $this->data['filter_parking'] ?? $this->data['parking'] ?? null;
        if (isset($parking) && $parking !== '') {
            $query->where('parking', $parking);
        }
        if (! empty($this->data['filter_location_id'])) {
            $query->where('location_id', $this->data['filter_location_id']);
        }
        if (! empty($this->data['filter_building_id'])) {
            $query->where('building_id', $this->data['filter_building_id']);
        }
        if (! empty($this->data['filter_user_id'])) {
            $query->where('user_id', $this->data['filter_user_id']);
        }
        if (! empty($this->data['filter_date_from'])) {
            $query->where('created_at', '>=', $this->data['filter_date_from']);
        }
        if (! empty($this->data['filter_date_to'])) {
            $query->where('created_at', '<=', $this->data['filter_date_to']);
        }
        if (! empty($this->data['filter_published_at_from'])) {
            $query->where('published_at', '>=', $this->data['filter_published_at_from']);
        }
        if (! empty($this->data['filter_published_at_to'])) {
            $query->where('published_at', '<=', $this->data['filter_published_at_to']);
        }
        if (! empty($this->data['filter_created_at_from'])) {
            $query->where('created_at', '>=', $this->data['filter_created_at_from']);
        }
        if (! empty($this->data['filter_created_at_to'])) {
            $query->where('created_at', '<=', $this->data['filter_created_at_to']);
        }
    }
}
