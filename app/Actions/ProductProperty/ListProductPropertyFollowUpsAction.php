<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\FollowUpType;
use App\Models\BixoSchedulesFollowUp;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ListProductPropertyFollowUpsAction
 *
 * Retrieve all follow-ups for a given product property.
 */
class ListProductPropertyFollowUpsAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'property_id' => 'required|integer',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'limit' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|nullable|string',
            'sort' => 'sometimes|nullable|string',
            'sort_direction' => 'sometimes|in:asc,desc',
            'direction' => 'sometimes|in:asc,desc',
            'search' => 'sometimes|nullable|string',
            'q' => 'sometimes|nullable|string',
            'filter' => 'sometimes|nullable|string',

            // Follow-up specific filters
            'filter_type' => 'sometimes|nullable|string',
            'filter_status' => 'sometimes|nullable|string',
            'filter_date_from' => 'sometimes|nullable|date',
            'filter_date_to' => 'sometimes|nullable|date',
        ];
    }

    public function handle(): ActionResult
    {
        $startTime = microtime(true);
        $query = BixoSchedulesFollowUp::with(['property', 'createdBy'])
            ->where('property_id', $this->data['property_id']);

        // Search
        $searchQuery = $this->data['search'] ?? $this->data['q'] ?? null;
        if (! empty($searchQuery)) {
            $query->whereAny(
                ['title', 'description', 'type'],
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
        $sortBy = $this->data['sort_by'] ?? $this->data['sort'] ?? 'start_date';
        $sortDir = $this->data['sort_direction'] ?? $this->data['direction'] ?? 'asc';
        $query->orderBy($sortBy, $sortDir);

        // Pagination
        $perPage = $this->data['per_page'] ?? $this->data['limit'] ?? 20;
        $followUps = $query->paginate($perPage);

        // Format items
        $items = collect($followUps->items())->map(function ($followUp) {
            return $this->formatFollowUp($followUp);
        })->all();

        $executionTime = microtime(true) - $startTime;

        return ActionResult::success($items, 'Follow-ups retrieved successfully', [
            'total' => $followUps->total(),
            'per_page' => $followUps->perPage(),
            'current_page' => $followUps->currentPage(),
            'last_page' => $followUps->lastPage(),
            'from' => $followUps->firstItem(),
            'to' => $followUps->lastItem(),
            'performance' => ['execution_time' => round($executionTime, 4)],
        ]);
    }

    private function applyFilters($query): void
    {
        if (! empty($this->data['filter_type'])) {
            $query->where('type', $this->data['filter_type']);
        }
        if (! empty($this->data['filter_status'])) {
            $query->where('status', $this->data['filter_status']);
        }
        if (! empty($this->data['filter_date_from'])) {
            $query->where('start_date', '>=', $this->data['filter_date_from']);
        }
        if (! empty($this->data['filter_date_to'])) {
            $query->where('start_date', '<=', $this->data['filter_date_to']);
        }
    }

    /**
     * Format a follow-up record for the API response.
     *
     * @return array<string, mixed>
     */
    private function formatFollowUp(BixoSchedulesFollowUp $item): array
    {
        $details = $item->details ? json_decode($item->details, true) : [];

        return [
            'eid' => $item->eid,
            'property_id' => $item->property->eid,
            'followup_title' => $item->title,
            'followup_date' => $item->start_date?->toISOString(),
            'followup_date_formatted' => $item->start_date?->format('d M Y H:i'),
            'followup_date_day' => $item->start_date?->format('j'),
            'followup_date_month' => $item->start_date ? strtoupper($item->start_date->format('M')) : null,
            'followup_type' => $item->type,
            'followup_type_label' => FollowUpType::tryFrom($item->type)?->label(),
            'description' => $item->description,
            'send_reminder' => $details['send_reminder'] ?? false,
            'status' => $item->status?->value,
            'created_by' => $item->created_by,
            'created_by_name' => $item->createdBy?->name,
            'created_at' => $item->created_at?->toISOString(),
            'updated_at' => $item->updated_at?->toISOString(),
        ];
    }
}
