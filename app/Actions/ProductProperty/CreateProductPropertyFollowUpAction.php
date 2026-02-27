<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\FollowUpStatus;
use App\Models\BixoProductProperties;
use App\Models\BixoSchedulesFollowUp;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * CreateProductPropertyFollowUpAction
 *
 * Create a follow-up scheduled entry for a product property.
 */
class CreateProductPropertyFollowUpAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'property_id' => 'required|integer',
            'followup_title' => 'required|string|max:200',
            'followup_date' => 'required|date',
            'followup_type' => 'sometimes|nullable|in:call,meeting,viewing,offer,other',
            'description' => 'sometimes|nullable|string',
            'send_reminder' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $property = BixoProductProperties::find($this->data['property_id']);
            if (! $property) {
                return ActionResult::failure('Property not found');
            }

            $followUp = BixoSchedulesFollowUp::create([
                'title' => $this->data['followup_title'],
                'start_date' => $this->data['followup_date'],
                'type' => $this->data['followup_type'] ?? 'other',
                'description' => $this->data['description'] ?? null,
                'details' => json_encode(['send_reminder' => $this->data['send_reminder'] ?? false]),
                'property_id' => $this->data['property_id'],
                'subject_id' => $this->data['property_id'],
                'subject_type' => BixoProductProperties::class,
                'status' => FollowUpStatus::Pending->value,
                'created_by' => auth()->id() ?? 1,
            ]);

            return ActionResult::success([
                'eid' => $followUp->eid,
                'property_id' => $property->eid,
                'followup_title' => $followUp->title,
                'followup_date' => $followUp->start_date?->toISOString(),
                'followup_type' => $followUp->type,
                'description' => $followUp->description,
                'send_reminder' => $this->data['send_reminder'] ?? false,
                'status' => $followUp->status?->value,
                'created_by' => $followUp->created_by,
                'created_at' => $followUp->created_at?->toISOString(),
            ], 'Follow-up created successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to create follow-up: ' . $e->getMessage());
        }
    }
}
