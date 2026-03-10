<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ArchiveProductPropertyAction
 *
 * Archive a property by setting archived_at and updating status to Archived.
 */
class ArchiveProductPropertyAction extends BaseAction
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
                'status' => 'Archived',
                'archived_at' => now(),
                'updated_at' => now(),
            ]);

            return ActionResult::success($property->fresh()->toArray(), 'Property archived successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to archive property: '.$e->getMessage());
        }
    }
}
