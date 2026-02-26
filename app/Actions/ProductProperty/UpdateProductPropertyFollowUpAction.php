<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\FollowUpStatus;
use App\Models\BixoSchedulesFollowUp;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * UpdateProductPropertyFollowUpAction
 *
 * Update an existing follow-up entry for a product property.
 */
class UpdateProductPropertyFollowUpAction extends BaseAction
{
    protected function rules(): array
    {
        $statusValues = implode(',', array_column(FollowUpStatus::cases(), 'value'));

        return [
            'property_id' => 'required|integer',
            'followup_eid' => 'required|string',
            'followup_title' => 'sometimes|string|max:200',
            'followup_date' => 'sometimes|nullable|date',
            'followup_type' => 'sometimes|nullable|in:call,meeting,viewing,offer,other',
            'description' => 'sometimes|nullable|string',
            'send_reminder' => 'sometimes|boolean',
            'status' => 'sometimes|nullable|in:' . $statusValues,
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $followUpId = hashids_decode($this->data['followup_eid']);
            if (! $followUpId) {
                return ActionResult::failure('Follow-up not found');
            }

            $followUp = BixoSchedulesFollowUp::where('id', $followUpId)
                ->where('property_id', $this->data['property_id'])
                ->first();

            if (! $followUp) {
                return ActionResult::failure('Follow-up not found');
            }

            $updates = [];

            if (isset($this->data['followup_title'])) {
                $updates['title'] = $this->data['followup_title'];
            }

            if (isset($this->data['followup_date'])) {
                $updates['start_date'] = $this->data['followup_date'];
            }

            if (isset($this->data['followup_type'])) {
                $updates['type'] = $this->data['followup_type'];
            }

            if (isset($this->data['description'])) {
                $updates['description'] = $this->data['description'];
            }

            if (isset($this->data['status'])) {
                $updates['status'] = $this->data['status'];
            }

            if (array_key_exists('send_reminder', $this->data)) {
                $existing = $followUp->details ? json_decode($followUp->details, true) : [];
                $updates['details'] = json_encode(array_merge($existing, ['send_reminder' => $this->data['send_reminder']]));
            }

            $followUp->update($updates);
            $followUp->refresh();

            $details = $followUp->details ? json_decode($followUp->details, true) : [];

            return ActionResult::success([
                'eid' => $followUp->eid,
                'property_id' => $followUp->property->eid,
                'followup_title' => $followUp->title,
                'followup_date' => $followUp->start_date?->toISOString(),
                'followup_type' => $followUp->type,
                'description' => $followUp->description,
                'send_reminder' => $details['send_reminder'] ?? false,
                'status' => $followUp->status?->value,
                'created_by' => $followUp->created_by,
                'created_at' => $followUp->created_at?->toISOString(),
                'updated_at' => $followUp->updated_at?->toISOString(),
            ], 'Follow-up updated successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to update follow-up: ' . $e->getMessage());
        }
    }
}
