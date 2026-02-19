<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * GetProductPropertyAction
 *
 * Retrieve a single product property by ID, hashid, or ref.
 *
 * @package App\Actions\ProductProperty
 */
class GetProductPropertyAction extends BaseAction
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
            // Try numeric ID first
            if (is_numeric($id)) {
                $property = BixoProductProperties::find($id);
            } else {
                // Try hashid decode
                $decoded  = hashids_decode($id);
                $property = $decoded
                    ? BixoProductProperties::find($decoded)
                    : BixoProductProperties::where('ref', $id)->first();
            }

            if (!$property) {
                return ActionResult::failure('Property not found');
            }

            return ActionResult::success($property->toArray(), 'Property retrieved successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to retrieve property: ' . $e->getMessage());
        }
    }
}
