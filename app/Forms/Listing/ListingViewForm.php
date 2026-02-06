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
            ->title(__('layout.property_overview'))
            ->icon('home')
            ->variant('bordered')
            ->columns(2);

        $overviewGroup->text('title')
            ->label(__('layout.property_title'))
            ->readonly(true)
            ->width(12);

        $overviewGroup->text('property_type')
            ->label(__('layout.property_type'))
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('listing_type')
            ->label(__('layout.listing_type'))
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('formatted_price')
            ->label(__('layout.price'))
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('bedrooms')
            ->label(__('layout.bedrooms'))
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('bathrooms')
            ->label(__('layout.bathrooms'))
            ->readonly(true)
            ->width(4);

        $overviewGroup->text('size_sqft')
            ->label(__('layout.size_sqft'))
            ->readonly(true)
            ->width(4);

        // Location
        $locationGroup = $form->group('location')
            ->title(__('layout.location'))
            ->icon('location')
            ->variant('bordered')
            ->columns(2);

        $locationGroup->text('full_address')
            ->label(__('layout.address'))
            ->readonly(true)
            ->width(12);

        $locationGroup->text('city')
            ->label(__('layout.city'))
            ->readonly(true)
            ->width(6);

        $locationGroup->text('area')
            ->label(__('layout.area'))
            ->readonly(true)
            ->width(6);

        // Description
        $descriptionGroup = $form->group('description')
            ->title(__('layout.description'))
            ->icon('document')
            ->variant('bordered')
            ->columns(1);

        $descriptionGroup->textarea('description')
            ->label(__('layout.description'))
            ->readonly(true)
            ->rows(6)
            ->width(12);

        // Agent Information
        $agentGroup = $form->group('agent')
            ->title(__('layout.agent_information'))
            ->icon('person')
            ->variant('bordered')
            ->columns(2);

        $agentGroup->text('agent_name')
            ->label(__('layout.agent_name'))
            ->readonly(true)
            ->width(6);

        $agentGroup->text('agent_phone')
            ->label(__('layout.phone'))
            ->readonly(true)
            ->width(6);

        $agentGroup->text('agent_email')
            ->label(__('layout.email'))
            ->readonly(true)
            ->width(12);

        // Status & Metrics
        $statusGroup = $form->group('status')
            ->title(__('layout.status_performance'))
            ->icon('analytics')
            ->variant('bordered')
            ->columns(3);

        $statusGroup->text('status')
            ->label(__('layout.status'))
            ->readonly(true)
            ->width(4);

        $statusGroup->text('availability')
            ->label(__('layout.availability'))
            ->readonly(true)
            ->width(4);

        $statusGroup->text('views_count')
            ->label(__('layout.views'))
            ->readonly(true)
            ->width(4);

        $statusGroup->text('inquiries_count')
            ->label(__('layout.inquiries'))
            ->readonly(true)
            ->width(4);

        $statusGroup->text('favorites_count')
            ->label(__('layout.favorites'))
            ->readonly(true)
            ->width(4);

        $statusGroup->text('published_at')
            ->label(__('layout.published_date'))
            ->readonly(true)
            ->width(4);

        return $form;
    }
}
