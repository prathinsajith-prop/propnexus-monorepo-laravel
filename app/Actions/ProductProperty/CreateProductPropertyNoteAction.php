<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\NoteType;
use App\Models\BixoNdocsNote;
use App\Models\BixoProductProperties;
use Illuminate\Support\Str;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * CreateProductPropertyNoteAction
 *
 * Create a note for a product property.
 */
class CreateProductPropertyNoteAction extends BaseAction
{
    protected function rules(): array
    {
        $noteTypes = implode(',', array_column(NoteType::cases(), 'value'));

        return [
            'property_id' => 'required|integer',
            'note' => 'required|string',
            'type' => 'sometimes|nullable|in:'.$noteTypes,
            'attachments' => 'sometimes|nullable',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $property = BixoProductProperties::find($this->data['property_id']);
            if (! $property) {
                return ActionResult::failure('Property not found');
            }

            $attachments = $this->data['attachments'] ?? null;

            $note = BixoNdocsNote::create([
                'uuid' => Str::uuid()->toString(),
                'note' => $this->data['note'],
                'type' => $this->data['type'] ?? null,
                'attachments' => is_array($attachments) ? json_encode($attachments) : $attachments,
                'subject_id' => $this->data['property_id'],
                'subject_type' => BixoProductProperties::class,
                'user_id' => auth()->id() ?? 1,
                'user_type' => 'App\\Models\\User',
            ]);

            return ActionResult::success([
                'eid' => $note->eid,
                'uuid' => $note->uuid,
                'note' => $note->note,
                'attachments' => $note->attachments ? json_decode($note->attachments, true) : [],
                'type' => $note->type?->value,
                'type_label' => $note->type?->label(),
                'user_id' => $note->user_id,
                'created_at' => $note->created_at?->toISOString(),
            ], 'Note created successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to create note: '.$e->getMessage());
        }
    }
}
