<?php

namespace App\Forms\Listing;

use Litepie\Layout\Components\FormComponent;

/**
 * ListingOverviewForm
 * 
 * Read-only overview form for displaying listing details in a 3-column grid.
 * Used in cards and overview sections.
 * 
 * @package App\Forms\Listing
 */
class ListingOverviewForm
{
    /**
     * Create listing overview form structure
     *
     * @param string $formId Form identifier
     * @param string|null $dataUrl URL to fetch data from
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make(string $formId = 'listing-overview-form', ?string $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->columns(3)
            ->gap('md')
            ->meta(['readOnly' => true]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl);
        }

        // Row 1: Price, Property Type, Listing Type
        $form->text('price')
            ->label(__('layout.price'))
            ->disabled(true)
            ->width(4);

        $form->text('property_type')
            ->label(__('layout.property_type'))
            ->disabled(true)
            ->width(4);

        $form->text('listing_type')
            ->label(__('layout.listing_type'))
            ->disabled(true)
            ->width(4);

        // Row 2: Bedrooms, Bathrooms, Size
        $form->text('bedrooms')
            ->label(__('layout.bedrooms'))
            ->disabled(true)
            ->width(4);

        $form->text('bathrooms')
            ->label(__('layout.bathrooms'))
            ->disabled(true)
            ->width(4);

        $form->text('size_sqft')
            ->label(__('layout.size'))
            ->disabled(true)
            ->width(4);

        // Row 3: Address (spanning 2 cols), City
        $form->text('address')
            ->label(__('layout.location'))
            ->disabled(true)
            ->width(8);

        $form->text('city')
            ->label(__('layout.city'))
            ->disabled(true)
            ->width(4);

        // Row 4: Status, Available From, MLS Number
        $form->text('status')
            ->label(__('layout.status'))
            ->disabled(true)
            ->width(4);

        $form->text('available_from')
            ->label(__('layout.available_from'))
            ->disabled(true)
            ->width(4);

        $form->text('mls_number')
            ->label(__('layout.mls_number'))
            ->disabled(true)
            ->width(4);

        return $form;
    }

    /**
     * Create a compact version with fewer fields
     *
     * @param string $formId Form identifier
     * @param string|null $dataUrl URL to fetch data from
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function makeCompact(string $formId = 'listing-overview-compact', ?string $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->columns(3)
            ->gap('sm')
            ->meta(['readOnly' => true]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl);
        }

        // Only essential fields
        $form->text('price')
            ->label(__('layout.price'))
            ->disabled(true)
            ->width(12);

        $form->text('bedrooms')
            ->label(__('layout.bedrooms'))
            ->disabled(true)
            ->width(4);

        $form->text('bathrooms')
            ->label(__('layout.bathrooms'))
            ->disabled(true)
            ->width(4);

        $form->text('size_sqft')
            ->label(__('layout.size'))
            ->disabled(true)
            ->width(4);

        $form->text('status')
            ->label(__('layout.status'))
            ->disabled(true)
            ->width(12);

        return $form;
    }
}
