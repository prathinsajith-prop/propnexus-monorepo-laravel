<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * MarkVerifiedProductPropertyAction
 *
 * Mark a property as verified by setting is_verify flag and status to Verified.
 */
class MarkVerifiedProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $id = $this->data['id'];

            if (! is_numeric($id)) {
                $decoded = hashids_decode($id);
                $id = $decoded ?: $id;
            }

            $property = BixoProductProperties::where('id', $id)->first();

            if (! $property) {
                return ActionResult::failure('Property not found');
            }

            $property->update([
                'is_verify' => true,
                'status' => 'Verified',
                'updated_at' => now(),
            ]);

            return ActionResult::success($property->fresh()->toArray(), 'Property marked as verified');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to mark property as verified: '.$e->getMessage());
        }
    }
}
