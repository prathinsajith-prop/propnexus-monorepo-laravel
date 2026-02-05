<?php

declare(strict_types=1);

namespace App\Actions\Listing;

use App\Models\Listing;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * GetListingAction
 * 
 * Retrieve a single listing by ID with caching
 * 
 * @package App\Actions\Listing
 */
class GetListingAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required|string',
            'use_cache' => 'sometimes|boolean',
            'increment_views' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $id = $this->data['id'];
            $incrementViews = $this->data['increment_views'] ?? false;

            // Try to decode if it's an encoded ID (eid)
            if (!is_numeric($id)) {
                $decodedId = hashids_decode($id);
                $id = $decodedId ?: $id;
            }

            $listing = Listing::with(['agent', 'owner', 'lastEditedBy'])
                ->where('id', $id)
                ->first();

            if (!$listing) {
                return ActionResult::failure('Listing not found');
            }

            // Increment view count if requested
            if ($incrementViews) {
                $listing->increment('views_count');
            }

            return ActionResult::success([
                'data' => $listing,
            ]);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to retrieve listing: ' . $e->getMessage());
        }
    }
}
