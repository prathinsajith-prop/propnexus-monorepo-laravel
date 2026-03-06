<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\UnpublishReason;
use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * UnpublishProductPropertyAction
 *
 * Unpublish a property with a reason and optional description.
 */
class UnpublishProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
            'reason' => 'required|string|in:'.implode(',', array_column(UnpublishReason::cases(), 'value')),
            'description' => 'sometimes|nullable|string|max:1000',
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
                'status' => 'Unpublished',
                'updated_at' => now(),
            ]);

            return ActionResult::success(
                $property->fresh()->toArray(),
                'Property unpublished successfully'
            );
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to unpublish property: '.$e->getMessage());
        }
    }
}
