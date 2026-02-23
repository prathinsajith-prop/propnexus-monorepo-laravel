<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * UpdateProductPropertyAction
 *
 * Update an existing product property.
 */
class UpdateProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required|string',
            'title' => 'sometimes|string|max:100',
            'category_type' => 'sometimes|in:Commercial,Residential',
            'property_for' => 'sometimes|in:Rental,Sales',
            'property_type' => 'sometimes|in:Live,Pocket,Developer,Verified Pocket',
            'status' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'beds' => 'sometimes|string',
            'baths' => 'sometimes|integer|min:0',
            'bua' => 'sometimes|numeric',
            'description' => 'sometimes|string',
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

            $updateData = array_filter($this->data, fn ($key) => $key !== 'id', ARRAY_FILTER_USE_KEY);

            // Encode JSON fields if passed as arrays
            foreach (['photos', 'features', 'documents', 'public_documents', 'portals', 'portals_data', 'feature_tags', 'floor_plans', 'medias'] as $field) {
                if (isset($updateData[$field]) && is_array($updateData[$field])) {
                    $updateData[$field] = json_encode($updateData[$field]);
                }
            }

            $updateData['updated_at'] = now();
            $property->update($updateData);

            return ActionResult::success($property->fresh()->toArray(), 'Property updated successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to update property: '.$e->getMessage());
        }
    }
}
