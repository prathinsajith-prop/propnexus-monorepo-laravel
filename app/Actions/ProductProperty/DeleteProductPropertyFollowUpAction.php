<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoSchedulesFollowUp;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * DeleteProductPropertyFollowUpAction
 *
 * Soft-delete a follow-up entry for a product property.
 */
class DeleteProductPropertyFollowUpAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'property_id' => 'required|integer',
            'followup_eid' => 'required|string',
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

            $followUp->delete();

            return ActionResult::success(null, 'Follow-up deleted successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to delete follow-up: ' . $e->getMessage());
        }
    }
}
