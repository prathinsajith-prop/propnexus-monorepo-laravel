<?php

namespace App\Forms\Listing;

use Litepie\Layout\Components\FormComponent;

/**
 * ListingForm
 * 
 * Comprehensive property listing form with sections for:
 * - Property Details (title, type, price)
 * - Location (address, city, area)
 * - Specifications (bedrooms, bathrooms, size)
 * - Features & Amenities
 * - Media (images, floor plans, videos)
 * - Status & Availability
 * - Agent Information
 * - Financial Details
 * - SEO Settings
 * 
 * @package App\Forms\Listing
 */
class ListingForm
{
    /**
     * Create listing form structure
     *
     * @param string $formId Form identifier
     * @param string $method HTTP method (POST/PUT)
     * @param string $action Form action URL
     * @param array $masterData Master data for dropdowns
     * @param string|null $dataUrl URL to fetch existing data
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $method, $action, $masterData, $dataUrl = null, $isCreate = false)
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(2)
            ->gap('lg')
            ->meta([
                'width' => '1000px',
            ]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl)->dataKey('data');
        }

        /** @var \Litepie\Layout\Components\FormComponent $form */
        // === PROPERTY DETAILS SECTION ===
        $propertyGroup = $form->group('property-info')
            ->title(__('layout.property_details'))
            ->icon('home')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $propertyGroup->text('title')
            ->label(__('layout.property_title'))
            ->placeholder(__('layout.property_title_placeholder'))
            ->required(true)
            ->width(12);

        $propertyGroup->select('property_type')
            ->label(__('layout.property_type'))
            ->options($masterData['property_types'] ?? [])
            ->required(true)
            ->width(4);

        $propertyGroup->select('listing_type')
            ->label(__('layout.listing_type'))
            ->options($masterData['listing_types'] ?? [])
            ->required(true)
            ->width(4);

        $propertyGroup->text('mls_number')
            ->label(__('layout.mls_number'))
            ->placeholder(__('layout.mls_placeholder'))
            ->width(4);

        $propertyGroup->number('price')
            ->label(__('layout.price'))
            ->placeholder('0.00')
            ->required(true)
            ->width(4);

        $propertyGroup->select('currency')
            ->label(__('layout.currency'))
            ->options($masterData['currencies'] ?? [])
            ->width(4);

        $propertyGroup->checkbox('is_negotiable')
            ->label(__('layout.price_negotiable'))
            ->width(4);

        $propertyGroup->textarea('short_description')
            ->label(__('layout.short_description'))
            ->placeholder(__('layout.short_description_placeholder'))
            ->rows(2)
            ->width(12);

        $propertyGroup->textarea('description')
            ->label(__('layout.full_description'))
            ->placeholder(__('layout.full_description_placeholder'))
            ->required(true)
            ->rows(6)
            ->width(12);

        // === LOCATION SECTION ===
        $locationGroup = $form->group('location-info')
            ->title(__('layout.location'))
            ->icon('location')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);


        $locationGroup->text('building_name')
            ->label(__('layout.building_name'))
            ->placeholder(__('layout.building_name_placeholder'))
            ->width(8);

        $locationGroup->text('unit_number')
            ->label(__('layout.unit_number'))
            ->placeholder(__('layout.unit_number_placeholder'))
            ->width(4);

        $locationGroup->text('address')
            ->label(__('layout.street_address'))
            ->placeholder(__('layout.street_address_placeholder'))
            ->required(true)
            ->width(12);

        $locationGroup->text('area')
            ->label(__('layout.area'))
            ->placeholder(__('layout.area_placeholder'))
            ->required(true)
            ->width(6);

        $locationGroup->text('sub_area')
            ->label(__('layout.sub_area'))
            ->placeholder(__('layout.sub_area_placeholder'))
            ->width(6);

        $locationGroup->text('city')
            ->label(__('layout.city'))
            ->placeholder(__('layout.city_placeholder'))
            ->required(true)
            ->width(4);

        $locationGroup->text('state')
            ->label(__('layout.state_emirate'))
            ->placeholder(__('layout.state_placeholder'))
            ->width(4);

        $locationGroup->text('country')
            ->label(__('layout.country'))
            ->placeholder(__('layout.country_placeholder'))
            ->width(4);

        $locationGroup->text('postal_code')
            ->label(__('layout.postal_code'))
            ->placeholder('00000')
            ->width(4);

        // === SPECIFICATIONS SECTION ===
        $specsGroup = $form->group('specifications-info')
            ->title(__('layout.property_specifications'))
            ->icon('grid')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);


        $specsGroup->number('bedrooms')
            ->label(__('layout.bedrooms'))
            ->placeholder('0')
            ->required(true)
            ->width(3);

        $specsGroup->number('bathrooms')
            ->label(__('layout.bathrooms'))
            ->placeholder('0')
            ->required(true)
            ->width(3);

        $specsGroup->number('parking_spaces')
            ->label(__('layout.parking'))
            ->placeholder('0')
            ->width(3);

        $specsGroup->number('year_built')
            ->label(__('layout.year_built'))
            ->placeholder('2024')
            ->width(3);

        $specsGroup->number('size_sqft')
            ->label(__('layout.built_up_area_sqft'))
            ->placeholder('0')
            ->width(6);

        $specsGroup->number('plot_size_sqft')
            ->label(__('layout.plot_area_sqft'))
            ->placeholder('0')
            ->width(6);

        $specsGroup->number('floor_number')
            ->label(__('layout.floor'))
            ->placeholder('0')
            ->width(6);

        $specsGroup->number('total_floors')
            ->label(__('layout.total_floors'))
            ->placeholder('0')
            ->width(6);

        // === FEATURES & AMENITIES SECTION ===
        $featuresGroup = $form->group('features-info')
            ->title(__('layout.features_amenities'))
            ->icon('star')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $featuresGroup->select('furnishing_status')
            ->label(__('layout.furnishing_status'))
            ->options($masterData['furnishing_statuses'] ?? [])
            ->width(12);

        $featuresGroup->checkbox('has_parking')
            ->label(__('layout.parking'))
            ->width(3);

        $featuresGroup->checkbox('has_balcony')
            ->label(__('layout.balcony'))
            ->width(3);

        $featuresGroup->checkbox('has_garden')
            ->label(__('layout.garden'))
            ->width(3);

        $featuresGroup->checkbox('has_pool')
            ->label(__('layout.pool'))
            ->width(3);

        $featuresGroup->checkbox('pet_friendly')
            ->label(__('layout.pet_friendly'))
            ->width(6);

        $featuresGroup->textarea('features')
            ->label(__('layout.additional_features'))
            ->placeholder(__('layout.additional_features_placeholder'))
            ->rows(3)
            ->width(12);

        $featuresGroup->textarea('amenities')
            ->label(__('layout.building_amenities'))
            ->placeholder(__('layout.building_amenities_placeholder'))
            ->rows(3)
            ->width(12);

        // === MEDIA SECTION ===
        $mediaGroup = $form->group('media-info')
            ->title(__('layout.media_photos'))
            ->icon('image')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $mediaGroup->file('featured_image')
            ->label(__('layout.featured_image'))
            ->accept('image/*')
            ->maxSize(5120)
            ->uploadUrl('/api/listing-upload/image')
            ->editForm(ListingImageEditForm::make('featured-image-edit'))
            ->preview(false)
            ->useCropper(true)
            ->width(12);

        $mediaGroup->file('images')
            ->label(__('layout.property_images'))
            ->accept('image/*')
            ->multiple(true)
            ->maxSize(10240)
            ->uploadUrl('/api/listing-upload/image')
            ->editForm(ListingImageEditForm::make('featured-image-edit'))
            ->useCropper(true)
            ->width(12);

        $mediaGroup->file('floor_plans')
            ->label(__('layout.floor_plans'))
            ->accept('image/*,application/pdf')
            ->multiple(true)
            ->maxSize(20480)
            ->uploadUrl('/api/listing-upload/document')
            ->width(12);

        $mediaGroup->text('video_url')
            ->label(__('layout.video_url'))
            ->placeholder(__('layout.video_url_placeholder'))
            ->width(12);

        $mediaGroup->text('virtual_tour_url')
            ->label(__('layout.virtual_tour_url'))
            ->placeholder(__('layout.virtual_tour_placeholder'))
            ->width(12);

        // === STATUS & AVAILABILITY SECTION ===
        $statusGroup = $form->group('status-info')
            ->title(__('layout.status_availability'))
            ->icon('checkmark')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $statusGroup->select('status')
            ->label(__('layout.listing_status'))
            ->options($masterData['statuses'] ?? [])
            ->required(true)
            ->width(6);

        $statusGroup->select('availability')
            ->label(__('layout.availability'))
            ->options($masterData['availabilities'] ?? [])
            ->required(true)
            ->width(6);

        $statusGroup->date('published_at')
            ->label(__('layout.publish_date'))
            ->width(4);

        $statusGroup->date('available_from')
            ->label(__('layout.available_from'))
            ->width(4);

        $statusGroup->date('available_until')
            ->label(__('layout.available_until'))
            ->width(4);

        $statusGroup->date('expires_at')
            ->label(__('layout.listing_expires'))
            ->width(12);

        $statusGroup->checkbox('is_featured')
            ->label(__('layout.featured'))
            ->width(4);

        $statusGroup->checkbox('is_hot_deal')
            ->label(__('layout.hot_deal'))
            ->width(4);

        $statusGroup->checkbox('is_verified')
            ->label(__('layout.verified'))
            ->width(4);

        // === AGENT INFORMATION SECTION ===
        $agentGroup = $form->group('agent-info')
            ->title(__('layout.agent_information'))
            ->icon('person')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $agentGroup->select('agent_id')
            ->label(__('layout.assigned_agent'))
            ->options($masterData['agents'] ?? [])
            ->placeholder(__('layout.select_an_agent'))
            ->required(true)
            ->width(6);

        $agentGroup->text('agent_name')
            ->label(__('layout.agent_name'))
            ->placeholder(__('layout.agent_name_placeholder'))
            ->width(6);

        $agentGroup->text('agent_phone')
            ->label(__('layout.agent_phone'))
            ->placeholder(__('layout.phone_placeholder'))
            ->width(6);

        $agentGroup->text('agent_email')
            ->label(__('layout.agent_email'))
            ->placeholder(__('layout.email_placeholder'))
            ->width(6);

        // === FINANCIAL DETAILS SECTION ===
        $financialGroup = $form->group('financial-info')
            ->title(__('layout.financial_details'))
            ->icon('cash')
            ->variant('bordered')
            ->columns(3)
            ->create($isCreate);

        $financialGroup->number('original_price')
            ->label(__('layout.original_price'))
            ->placeholder('0.00')
            ->width(4);

        $financialGroup->number('discount_percentage')
            ->label(__('layout.discount_percentage'))
            ->placeholder('0')
            ->width(4);

        $financialGroup->number('service_charge')
            ->label(__('layout.service_charge'))
            ->placeholder('0.00')
            ->width(4);

        $financialGroup->select('service_charge_period')
            ->label(__('layout.period'))
            ->options([
                ['value' => 'yearly', 'label' => __('layout.yearly')],
                ['value' => 'monthly', 'label' => __('layout.monthly')],
            ])
            ->width(4);

        $financialGroup->number('security_deposit')
            ->label(__('layout.security_deposit'))
            ->placeholder('0.00')
            ->width(8);

        $financialGroup->textarea('payment_terms')
            ->label(__('layout.payment_terms'))
            ->placeholder(__('layout.payment_terms_placeholder'))
            ->rows(3)
            ->width(12);

        // === ADDITIONAL INFO SECTION ===
        $additionalGroup = $form->group('additional-info')
            ->title(__('layout.additional_information'))
            ->icon('information')
            ->variant('bordered')
            ->columns(2)
            ->create($isCreate);

        $additionalGroup->text('reference_number')
            ->label(__('layout.reference_number'))
            ->placeholder('REF-12345')
            ->width(6);

        $additionalGroup->number('priority_score')
            ->label(__('layout.priority_score'))
            ->placeholder('50')
            ->width(6);

        $additionalGroup->textarea('internal_notes')
            ->label(__('layout.internal_notes'))
            ->placeholder(__('layout.internal_notes_placeholder'))
            ->rows(4)
            ->width(12);

        return $form;
    }
}
