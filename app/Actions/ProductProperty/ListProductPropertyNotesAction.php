<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoNdocsNote;
use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ListProductPropertyNotesAction
 *
 * Retrieve all notes for a given product property.
 */
class ListProductPropertyNotesAction extends BaseAction
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

            // Note-specific filters
            'filter_type' => 'sometimes|nullable|string',
            'filter_user_id' => 'sometimes|nullable|integer',
            'filter_date_from' => 'sometimes|nullable|date',
            'filter_date_to' => 'sometimes|nullable|date',
        ];
    }

    public function handle(): ActionResult
    {
        $startTime = microtime(true);
        $query = BixoNdocsNote::with('user')
            ->where('subject_id', $this->data['property_id'])
            ->where('subject_type', BixoProductProperties::class);

        // Search
        $searchQuery = $this->data['search'] ?? $this->data['q'] ?? null;
        if (! empty($searchQuery)) {
            $query->whereAny(
                ['note', 'type'],
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
        $notes = $query->paginate($perPage);

        // Format items
        $items = collect($notes->items())->map(function ($note) {
            return $this->formatNote($note);
        })->all();

        $executionTime = microtime(true) - $startTime;

        return ActionResult::success($items, 'Notes retrieved successfully', [
            'total' => $notes->total(),
            'per_page' => $notes->perPage(),
            'current_page' => $notes->currentPage(),
            'last_page' => $notes->lastPage(),
            'from' => $notes->firstItem(),
            'to' => $notes->lastItem(),
            'performance' => ['execution_time' => round($executionTime, 4)],
        ]);
    }

    private function applyFilters($query): void
    {
        if (! empty($this->data['filter_type'])) {
            $query->where('type', $this->data['filter_type']);
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
    }

    /**
     * Format a note record for the API response.
     *
     * @return array<string, mixed>
     */
    private function formatNote(BixoNdocsNote $item): array
    {
        return [
            'eid' => $item->eid,
            'uuid' => $item->uuid,
            'note' => $item->note,
            'attachments' => $item->attachments ? json_decode($item->attachments, true) : [],
            'type' => $item->type?->value,
            'type_label' => $item->type?->label(),
            'user_id' => $item->user_id,
            'author' => $item->user?->name,
            'created_at' => $item->created_at?->toISOString(),
            'updated_at' => $item->updated_at?->toISOString(),
        ];
    }
}
