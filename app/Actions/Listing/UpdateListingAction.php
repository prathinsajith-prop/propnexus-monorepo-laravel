<?php

declare(strict_types=1);

namespace App\Actions\Listing;

use App\Enums\Availability;
use App\Enums\ListingStatus;
use App\Enums\ListingType;
use App\Enums\PropertyType;
use App\Models\Listing;
use Illuminate\Validation\Rules\Enum;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * UpdateListingAction
 * 
 * Update an existing property listing
 * 
 * @package App\Actions\Listing
 */
class UpdateListingAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required|string',
            'title' => 'sometimes|string|max:255',
            'mls_number' => 'sometimes|string',
            'property_type' => ['sometimes', new Enum(PropertyType::class)],
            'listing_type' => ['sometimes', new Enum(ListingType::class)],
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'area' => 'sometimes|string',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'size_sqft' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'short_description' => 'sometimes|string',
            'status' => ['sometimes', new Enum(ListingStatus::class)],
            'availability' => ['sometimes', new Enum(Availability::class)],
            'agent_id' => 'sometimes|exists:users,id',
            'features' => 'sometimes|array',
            'amenities' => 'sometimes|array',
            'is_featured' => 'sometimes|boolean',
            'is_hot_deal' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $id = $this->data['id'];

            // Try to decode if it's an encoded ID (eid)
            if (!is_numeric($id)) {
                $decodedId = hashids_decode($id);
                $id = $decodedId ?: $id;
            }

            $listing = Listing::where('id', $id)->first();

            if (!$listing) {
                return ActionResult::failure('Listing not found');
            }

            $updateData = array_filter($this->data, fn($key) => $key !== 'id', ARRAY_FILTER_USE_KEY);

            // Convert string fields to arrays for those that need it
            $arrayFields = [
                'features',
                'amenities',
                'images',
                'documents',
                'payment_terms',
                'floor_plans',
                'seo_meta',
                'schema_markup',
                'analytics',
                'custom_fields'
            ];

            foreach ($arrayFields as $field) {
                if (isset($updateData[$field]) && is_string($updateData[$field])) {
                    // Try to decode as JSON first
                    $decoded = json_decode($updateData[$field], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $updateData[$field] = $decoded;
                    } else {
                        // Wrap plain string in array
                        $updateData[$field] = [$updateData[$field]];
                    }
                }
            }

            $updateData['last_edited_at'] = now();
            $updateData['last_edited_by'] = auth()->id();

            $listing->update($updateData);

            return ActionResult::success($listing->fresh()->toArray(), 'Listing updated successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to update listing: ' . $e->getMessage());
        }
    }
}
