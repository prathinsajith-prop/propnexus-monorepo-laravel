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
            'limit' => 'sometimes|nullable|integer|min:1',
            'page' => 'sometimes|nullable|integer|min:1',
            'order_by' => 'sometimes|nullable|string',
            'order_dir' => 'sometimes|nullable|string',
            'search' => 'sometimes|nullable|string',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $query = BixoNdocsNote::with('user')
                ->where('subject_id', $this->data['property_id'])
                ->where('subject_type', BixoProductProperties::class)
                ->orderBy($this->data['order_by'] ?? 'created_at', $this->data['order_dir'] ?? 'DESC');

            if (! empty($this->data['limit'])) {
                $query->limit((int) $this->data['limit']);
            }

            $notes = $query->get()
                ->map(fn($item) => $this->formatNote($item))
                ->values()
                ->all();

            return ActionResult::success([
                'data' => $notes,
                'meta' => ['total' => count($notes)],
            ], 'Notes retrieved successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to retrieve notes: ' . $e->getMessage());
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
