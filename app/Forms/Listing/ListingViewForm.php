<?php

namespace App\Forms\Listing;

use Litepie\Layout\Components\FormComponent;

/**
 * ListingViewForm
 *
 * Read-only form for viewing listing details in modal/aside
 */
class ListingViewForm
{
    /**
     * Create listing view form structure
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method (GET)
     * @param  string  $action  Form action URL
     * @param  array  $masterData  Master data for dropdowns
     * @param  string|null  $dataUrl  URL to fetch existing data
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $method, $action, $masterData = [], $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(2)
            ->gap('md')
            ->meta([
                'readonly' => true,
                'width' => '900px',
            ]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl)->dataKey('data');
        }

        // Property Overview
        $overviewGroup = $form->group('overview')
            ->title(__('layout.property_overview'))
            ->icon('home')
            ->variant('bordered')
            ->columns(2)
            ->editable(true);

        $overviewGroup->text('title')
            ->label(__('layout.property_title'))
            ->readonly(true)
            ->width(12);

        $overviewGroup->select('property_type')
            ->label(__('layout.property_type'))
            ->options($masterData['property_types'] ?? [])
            ->readonly(true)
            ->width(4);

        $overviewGroup->select('listing_type')
            ->label(__('layout.listing_type'))
            ->options($masterData['listing_types'] ?? [])
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
            ->columns(2)
            ->editable(false);

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
            ->columns(1)
            ->editable(false);

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
            ->columns(2)
            ->editable(false);

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
            ->columns(3)
            ->editable(false);

        $statusGroup->select('status')
            ->label(__('layout.status'))
            ->options($masterData['statuses'] ?? [])
            ->readonly(true)
            ->width(4);

        $statusGroup->select('availability')
            ->label(__('layout.availability'))
            ->options($masterData['availabilities'] ?? [])
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

        $statusGroup->date('published_at')
            ->label(__('layout.published_date'))
            ->readonly(true)
            ->width(4);

        return $form;
    }
}
