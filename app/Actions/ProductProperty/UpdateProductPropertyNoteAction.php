<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoNdocsNote;
use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * UpdateProductPropertyNoteAction
 *
 * Update a note for a product property.
 */
class UpdateProductPropertyNoteAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'property_id' => 'required|integer',
            'note_eid' => 'required|string',
            'note' => 'required|string',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $noteId = hashids_decode($this->data['note_eid']);
            if (! $noteId) {
                return ActionResult::failure('Note not found');
            }

            $note = BixoNdocsNote::where('id', $noteId)
                ->where('subject_id', $this->data['property_id'])
                ->where('subject_type', BixoProductProperties::class)
                ->first();

            if (! $note) {
                return ActionResult::failure('Note not found');
            }

            $note->update(['note' => $this->data['note']]);

            return ActionResult::success([
                'eid' => $note->eid,
                'uuid' => $note->uuid,
                'note' => $note->note,
                'type' => $note->type?->value,
                'type_label' => $note->type?->label(),
                'user_id' => $note->user_id,
                'created_at' => $note->created_at?->toISOString(),
                'updated_at' => $note->updated_at?->toISOString(),
            ], 'Note updated successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to update note: ' . $e->getMessage());
        }
    }
}
