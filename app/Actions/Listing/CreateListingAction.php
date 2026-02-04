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
 * CreateListingAction
 * 
 * Create a new property listing with validation and automatic ID generation
 * 
 * @package App\Actions\Listing
 */
class CreateListingAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'mls_number' => 'nullable|string|unique:listings,mls_number',
            'property_type' => 'required|in:residential,commercial,land,industrial',
            'listing_type' => 'required|in:sale,rent,lease',
            'price' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'address' => 'required|string',
            'city' => 'required|string',
            'area' => 'required|string',
            'state' => 'nullable|string',
            'country' => 'sometimes|string',
            'postal_code' => 'nullable|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'size_sqft' => 'nullable|numeric',
            'plot_size_sqft' => 'nullable|numeric',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'status' => 'sometimes|in:draft,active,pending,sold,rented,expired,archived',
            'availability' => 'sometimes|in:available,reserved,sold,rented',
            'agent_id' => 'required|exists:users,id',
            'features' => 'nullable|array',
            'amenities' => 'nullable|array',
            'is_featured' => 'sometimes|boolean',
            'is_hot_deal' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $listing = Listing::create($this->data);

            return ActionResult::success([
                'data' => $listing,
                'message' => 'Listing created successfully',
            ]);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to create listing: ' . $e->getMessage());
        }
    }
}
