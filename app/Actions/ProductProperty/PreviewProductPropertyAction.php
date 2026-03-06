<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Enums\PreviewType;
use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * PreviewProductPropertyAction
 *
 * Generate a preview URL for a property with optional price override.
 */
class PreviewProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
            'preview_type' => 'required|string|in:'.implode(',', array_column(PreviewType::cases(), 'value')),
            'price' => 'sometimes|nullable|numeric|min:0',
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

            $previewType = PreviewType::tryFrom($this->data['preview_type']) ?? PreviewType::WithMyDetails;
            $price = $this->data['price'] ?? null;

            $params = ['type' => $previewType->value];

            if ($price !== null) {
                $params['price'] = $price;
            }

            $previewUrl = url('/property/'.$property->eid.'/preview?'.http_build_query($params));

            return ActionResult::success([
                'preview_url' => $previewUrl,
                'preview_type' => $previewType->value,
                'price' => $price,
                'property_eid' => $property->eid,
            ], 'Preview link generated');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to generate preview: '.$e->getMessage());
        }
    }
}
