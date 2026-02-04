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

        $overviewGroup->display('title')
            ->label('Title')
            ->width(12);

        $overviewGroup->display('property_type')
            ->label('Property Type')
            ->width(4);

        $overviewGroup->display('listing_type')
            ->label('Listing Type')
            ->width(4);

        $overviewGroup->display('formatted_price')
            ->label('Price')
            ->width(4);

        $overviewGroup->display('bedrooms')
            ->label('Bedrooms')
            ->width(4);

        $overviewGroup->display('bathrooms')
            ->label('Bathrooms')
            ->width(4);

        $overviewGroup->display('size_sqft')
            ->label('Size (sqft)')
            ->width(4);

        // Location
        $locationGroup = $form->group('location')
            ->title('Location')
            ->icon('location')
            ->variant('bordered')
            ->columns(2);

        $locationGroup->display('full_address')
            ->label('Address')
            ->width(12);

        $locationGroup->display('city')
            ->label('City')
            ->width(6);

        $locationGroup->display('area')
            ->label('Area')
            ->width(6);

        // Description
        $descriptionGroup = $form->group('description')
            ->title('Description')
            ->icon('document')
            ->variant('bordered')
            ->columns(1);

        $descriptionGroup->display('description')
            ->label('Description')
            ->width(12);

        // Agent Information
        $agentGroup = $form->group('agent')
            ->title('Agent Information')
            ->icon('person')
            ->variant('bordered')
            ->columns(2);

        $agentGroup->display('agent_name')
            ->label('Agent Name')
            ->width(6);

        $agentGroup->display('agent_phone')
            ->label('Phone')
            ->width(6);

        $agentGroup->display('agent_email')
            ->label('Email')
            ->width(12);

        // Status & Metrics
        $statusGroup = $form->group('status')
            ->title('Status & Performance')
            ->icon('analytics')
            ->variant('bordered')
            ->columns(3);

        $statusGroup->display('status')
            ->label('Status')
            ->width(4);

        $statusGroup->display('availability')
            ->label('Availability')
            ->width(4);

        $statusGroup->display('views_count')
            ->label('Views')
            ->width(4);

        $statusGroup->display('inquiries_count')
            ->label('Inquiries')
            ->width(4);

        $statusGroup->display('favorites_count')
            ->label('Favorites')
            ->width(4);

        $statusGroup->display('published_at')
            ->label('Published')
            ->width(4);

        return $form;
    }
}
