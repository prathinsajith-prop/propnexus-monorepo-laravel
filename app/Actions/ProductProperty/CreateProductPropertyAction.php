<?php

declare(strict_types=1);

namespace App\Actions\ProductProperty;

use App\Models\BixoProductProperties;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * CreateProductPropertyAction
 *
 * Create a new product property with validation.
 *
 * @package App\Actions\ProductProperty
 */
class CreateProductPropertyAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'title'         => 'required|string|max:100',
            'ref'           => 'required|string|max:100|unique:bixo_product_properties,ref',
            'category_type' => 'required|in:Commercial,Residential',
            'property_for'  => 'required|in:Rental,Sales',
            'property_type' => 'nullable|in:Live,Pocket,Developer,Verified Pocket',
            'status'        => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'beds'          => 'nullable|string',
            'baths'         => 'nullable|integer|min:0',
            'bua'           => 'nullable|numeric',
            'description'   => 'nullable|string',
            'created_by'    => 'nullable|integer',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $data = $this->data;

            // JSON fields
            foreach (['photos', 'features', 'documents', 'public_documents', 'portals', 'portals_data', 'feature_tags', 'floor_plans', 'medias'] as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $data[$field] = json_encode($data[$field]);
                }
            }

            $data['created_by'] = $data['created_by'] ?? auth()->id() ?? 1;
            $data['status']     = $data['status'] ?? 'Draft';
            $data['created_at'] = now();
            $data['updated_at'] = now();

            $property = BixoProductProperties::create($data);

            return ActionResult::success($property->toArray(), 'Property created successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to create property: ' . $e->getMessage());
        }
    }
}
