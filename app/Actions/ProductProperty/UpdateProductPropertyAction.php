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
            'id' => 'required|integer',
            'title' => 'sometimes|nullable|string|max:100',
            'category_type' => 'sometimes|nullable|in:Commercial,Residential',
            'property_for' => 'sometimes|nullable|in:Rental,Sales',
            'property_type' => 'sometimes|nullable|in:Live,Pocket,Developer,Verified Pocket',
            'status' => 'sometimes|nullable|string',
            'price' => 'sometimes|nullable|numeric|min:0',
            'original_price' => 'sometimes|nullable|numeric|min:0',
            'beds' => 'sometimes|nullable|integer|min:0',
            'baths' => 'sometimes|nullable|integer|min:0',
            'parking' => 'sometimes|nullable|integer|min:0',
            'bua' => 'sometimes|nullable|numeric',
            'plot' => 'sometimes|nullable|string',
            'floor' => 'sometimes|nullable|string',
            'unit' => 'sometimes|nullable|string',
            'ref' => 'sometimes|nullable|string',
            'ref_old' => 'sometimes|nullable|string',
            'ref_pf' => 'sometimes|nullable|string',
            'furnishing' => 'sometimes|nullable|in:Furnished,Unfurnished,Partly Furnished,Fitted,Not Fitted,Shell And Core',
            'construction_status' => 'sometimes|nullable|string',
            'completion_on' => 'sometimes|nullable|date',
            'available_from' => 'sometimes|nullable|date',
            'published_at' => 'sometimes|nullable|date',
            'service_charge' => 'sometimes|nullable|numeric|min:0',
            'lease_term' => 'sometimes|nullable|string',
            'cheques' => 'sometimes|nullable|integer|min:0',
            'frequency' => 'sometimes|nullable|string',
            'views' => 'sometimes|nullable|string',
            'appliances' => 'sometimes|nullable|string',
            'rented' => 'sometimes|nullable|boolean',
            'rented_price' => 'sometimes|nullable|numeric|min:0',
            'exclusive' => 'sometimes|nullable|boolean',
            'premium' => 'sometimes|nullable|boolean',
            'price_on_request' => 'sometimes|nullable|boolean',
            'payment_plan' => 'sometimes|nullable|boolean',
            'latitude' => 'sometimes|nullable|numeric',
            'longitude' => 'sometimes|nullable|numeric',
            'description' => 'sometimes|nullable|string',
            'description_more' => 'sometimes|nullable|string',
            'notes' => 'sometimes|nullable|string',
            'photos' => 'sometimes|nullable',
            'floor_plans' => 'sometimes|nullable',
            'features' => 'sometimes|nullable',
            'documents' => 'sometimes|nullable',
            'public_documents' => 'sometimes|nullable',
            'portals' => 'sometimes|nullable',
            'portals_data' => 'sometimes|nullable',
            'medias' => 'sometimes|nullable',
            'feature_tags' => 'sometimes|nullable',
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

            $updateData = array_filter($this->data, fn($key) => $key !== 'id', ARRAY_FILTER_USE_KEY);

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
            return ActionResult::failure('Failed to update property: ' . $e->getMessage());
        }
    }
}
