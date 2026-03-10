<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * MarkFeaturedProductPropertyAction
 *
 * Toggle the premium (featured) flag on a property.
 */
class MarkFeaturedProductPropertyAction extends BaseAction
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
                'premium' => ! $property->premium,
                'updated_at' => now(),
            ]);

            $message = $property->fresh()->premium
                ? 'Property marked as featured'
                : 'Property removed from featured';

            return ActionResult::success($property->fresh()->toArray(), $message);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to update featured status: '.$e->getMessage());
        }
    }
}
