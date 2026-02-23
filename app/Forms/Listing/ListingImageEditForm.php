<?php

namespace App\Forms\Listing;

use Litepie\Layout\Components\FormComponent;

/**
 * ListingImageEditForm
 *
 * Form for editing listing image metadata including:
 * - Expiry date for temporary images
 * - Image description for accessibility and SEO
 */
class ListingImageEditForm
{
    /**
     * Create image edit form structure
     *
     * @param  string  $formId  Form identifier
     */
    public static function make(string $formId = 'listing-image-edit'): FormComponent
    {
        $form = FormComponent::make($formId)
            ->columns(1)
            ->gap('md');

        $form->date('expiry_date')
            ->label(__('layout.image_expiry_date'))
            ->placeholder(__('layout.select_expiry_date'))
            ->width(12);

        $form->textarea('description')
            ->label(__('layout.image_description'))
            ->placeholder(__('layout.image_description_placeholder'))
            ->rows(3)
            ->width(12);

        return $form;
    }
}
