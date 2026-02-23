<?php

declare(strict_types=1);

namespace App\Actions\Listing;

use App\Models\Listing;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * DeleteListingAction
 *
 * Soft delete a property listing
 */
class DeleteListingAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required|string',
            'force' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $id = $this->data['id'];

            // Try to decode if it's an encoded ID (eid)
            if (! is_numeric($id)) {
                $decodedId = hashids_decode($id);
                $id = $decodedId ?: $id;
            }

            $listing = Listing::where('id', $id)->first();

            if (! $listing) {
                return ActionResult::failure('Listing not found');
            }

            $force = $this->data['force'] ?? false;

            if ($force) {
                $listing->forceDelete();
                $message = 'Listing permanently deleted';
            } else {
                $listing->delete();
                $message = 'Listing deleted successfully';
            }

            return ActionResult::success(null, $message);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to delete listing: '.$e->getMessage());
        }
    }
}
