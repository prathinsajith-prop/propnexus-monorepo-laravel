<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * DeleteProductPropertyAction
 *
 * Soft-delete (or force-delete) a product property.
 *
 * @package App\Actions\ProductProperty
 */
class DeleteProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id'    => 'required|string',
            'force' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $id = $this->data['id'];

            if (!is_numeric($id)) {
                $decoded = hashids_decode($id);
                $id      = $decoded ?: $id;
            }

            $property = BixoProductProperties::where('id', $id)->first();
            if (!$property) {
                return ActionResult::failure('Property not found');
            }

            $force = $this->data['force'] ?? false;

            if ($force) {
                $property->forceDelete();
                $message = 'Property permanently deleted';
            } else {
                $property->delete();
                $message = 'Property deleted successfully';
            }

            return ActionResult::success(null, $message);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to delete property: ' . $e->getMessage());
        }
    }
}
