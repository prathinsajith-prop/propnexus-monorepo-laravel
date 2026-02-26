<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoNdocsNote;
use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * DeleteProductPropertyNoteAction
 *
 * Soft-delete a note for a product property.
 */
class DeleteProductPropertyNoteAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'property_id' => 'required|integer',
            'note_eid' => 'required|string',
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

            $note->delete();

            return ActionResult::success(null, 'Note deleted successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to delete note: '.$e->getMessage());
        }
    }
}
