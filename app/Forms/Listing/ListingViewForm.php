<?php

namespace App\Forms\Listing;

use Litepie\Layout\Components\FormComponent;

/**
 * ListingViewForm
 * 
 * Read-only form for viewing listing details in modal/aside
 * 
 * @package App\Forms\Listing
 */
class ListingViewForm
{
    /**
     * Create listing view form structure
     *
     * @param string $formId Form identifier
     * @param string $dataUrl URL to fetch listing data
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $dataUrl)
    {
        $form = FormComponent::make($formId)
            ->columns(2)
            ->gap('md')
            ->meta([
                'dataUrl' => $dataUrl,
                'dataKey' => 'data',
                'readonly' => true,
                'width' => '900px',
            ]);

        // Property Overview
        $overviewGroup = $form->group('overview')
            ->title('Property Overview')
            ->icon('home')
            ->variant('bordered')
            ->columns(2);

        $overviewGroup->text('title')
            ->label('Title')
            ->readonly(true)
            ->width(12);

        $overviewGroup->text('property_type')
            ->label('Property Type')
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('listing_type')
            ->label('Listing Type')
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('formatted_price')
            ->label('Price')
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('bedrooms')
            ->label('Bedrooms')
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('bathrooms')
            ->label('Bathrooms')
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('size_sqft')
            ->label('Size (sqft)')
            ->readonly(true)
            ->width(4);

        // Location
        $locationGroup = $form->group('location')
            ->title('Location')
            ->icon('location')
            ->variant('bordered')
            ->columns(2);

        $locationGroup->text('full_address')
            ->label('Address')
            ->readonly(true)
            ->width(12);

        $locationGroup->text('city')
            ->label('City')
            ->readonly(true)
            ->width(6);

        $locationGroup->text('area')
            ->label('Area')
            ->readonly(true)
            ->width(6);

        // Description
        $descriptionGroup = $form->group('description')
            ->title('Description')
            ->icon('document')
            ->variant('bordered')
            ->columns(1);

        $descriptionGroup->textarea('description')
            ->label('Description')
            ->readonly(true)
            ->rows(6)
            ->width(12);

        // Agent Information
        $agentGroup = $form->group('agent')
            ->title('Agent Information')
            ->icon('person')
            ->variant('bordered')
            ->columns(2);

        $agentGroup->text('agent_name')
            ->label('Agent Name')
            ->readonly(true)
            ->width(6);

        $agentGroup->text('agent_phone')
            ->label('Phone')
            ->readonly(true)
            ->width(6);

        $agentGroup->text('agent_email')
            ->label('Email')
            ->readonly(true)
            ->width(12);

        // Status & Metrics
        $statusGroup = $form->group('status')
            ->title('Status & Performance')
            ->icon('analytics')
            ->variant('bordered')
            ->columns(3);

        $statusGroup->text('status')
            ->label('Status')
            ->readonly(true)
            ->width(4);

        $statusGroup->text('availability')
            ->label('Availability')
            ->readonly(true)
            ->width(4);

        $statusGroup->text('views_count')
            ->label('Views')
            ->readonly(true)
            ->width(4);

        $statusGroup->text('inquiries_count')
            ->label('Inquiries')
            ->readonly(true)
            ->width(4);

        $statusGroup->text('favorites_count')
            ->label('Favorites')
            ->readonly(true)
            ->width(4);

        $statusGroup->text('published_at')
            ->label('Published')
            ->readonly(true)
            ->width(4);

        return $form;
    }
}
