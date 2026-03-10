<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * DuplicateProductPropertyAction
 *
 * Duplicate an existing property, resetting status to Draft and clearing publish timestamps.
 */
class DuplicateProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
            'ref' => 'required|string|max:100',
            'title' => 'required|string|max:255',
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

            $duplicate = $property->replicate([
                'ref',
                'title',
                'published_at',
                'unpublished_at',
                'archived_at',
                'activated_at',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $duplicate->status = 'Draft';
            $duplicate->ref = $this->data['ref'];
            $duplicate->title = $this->data['title'];
            $duplicate->published_at = null;
            $duplicate->unpublished_at = null;
            $duplicate->archived_at = null;
            $duplicate->activated_at = null;
            $duplicate->save();

            return ActionResult::success($duplicate->fresh()->toArray(), 'Property duplicated successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to duplicate property: '.$e->getMessage());
        }
    }
}
