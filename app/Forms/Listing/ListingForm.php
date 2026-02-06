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
    public static function make($formId, $method, $action, $masterData, $dataUrl = null)
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
            $form->meta([
                'dataUrl' => $dataUrl,
                'dataKey' => 'data',
            ]);
        }

        // === PROPERTY DETAILS SECTION ===
        $propertyGroup = $form->group('property-info')
            ->title('Property Details')
            ->icon('home')
            ->variant('bordered')
            ->columns(12);

        $propertyGroup->text('title')
            ->label('Property Title')
            ->placeholder('e.g., Luxury 3BR Apartment in Dubai Marina')
            ->required(true)
            ->width(12);

        $propertyGroup->select('property_type')
            ->label('Property Type')
            ->options($masterData['property_types'] ?? [])
            ->required(true)
            ->width(4);

        $propertyGroup->select('listing_type')
            ->label('Listing Type')
            ->options($masterData['listing_types'] ?? [])
            ->required(true)
            ->width(4);

        $propertyGroup->text('mls_number')
            ->label('MLS Number')
            ->placeholder('MLS-12345')
            ->width(4);

        $propertyGroup->number('price')
            ->label('Price')
            ->placeholder('0.00')
            ->required(true)
            ->width(4);

        $propertyGroup->select('currency')
            ->label('Currency')
            ->options($masterData['currencies'] ?? [])
            ->width(4);

        $propertyGroup->checkbox('is_negotiable')
            ->label('Price Negotiable')
            ->width(4);

        $propertyGroup->textarea('short_description')
            ->label('Short Description')
            ->placeholder('Brief property summary...')
            ->rows(2)
            ->width(12);

        $propertyGroup->textarea('description')
            ->label('Full Description')
            ->placeholder('Detailed property description...')
            ->required(true)
            ->rows(6)
            ->width(12);

        // === LOCATION SECTION ===
        $locationGroup = $form->group('location-info')
            ->title('Location')
            ->icon('location')
            ->variant('bordered')
            ->columns(12);

        $locationGroup->text('building_name')
            ->label('Building Name')
            ->placeholder('Building name')
            ->width(8);

        $locationGroup->text('unit_number')
            ->label('Unit Number')
            ->placeholder('Unit #')
            ->width(4);

        $locationGroup->text('address')
            ->label('Street Address')
            ->placeholder('Street address')
            ->required(true)
            ->width(12);

        $locationGroup->text('area')
            ->label('Area')
            ->placeholder('Dubai Marina')
            ->required(true)
            ->width(6);

        $locationGroup->text('sub_area')
            ->label('Sub Area')
            ->placeholder('Marina Gate')
            ->width(6);

        $locationGroup->text('city')
            ->label('City')
            ->placeholder('Dubai')
            ->required(true)
            ->width(4);

        $locationGroup->text('state')
            ->label('State/Emirate')
            ->placeholder('Dubai')
            ->width(4);

        $locationGroup->text('country')
            ->label('Country')
            ->placeholder('UAE')
            ->width(4);

        $locationGroup->text('postal_code')
            ->label('Postal Code')
            ->placeholder('00000')
            ->width(4);

        // === SPECIFICATIONS SECTION ===
        $specsGroup = $form->group('specifications-info')
            ->title('Property Specifications')
            ->icon('grid')
            ->variant('bordered')
            ->columns(12);

        $specsGroup->number('bedrooms')
            ->label('Bedrooms')
            ->placeholder('0')
            ->required(true)
            ->width(3);

        $specsGroup->number('bathrooms')
            ->label('Bathrooms')
            ->placeholder('0')
            ->required(true)
            ->width(3);

        $specsGroup->number('parking_spaces')
            ->label('Parking')
            ->placeholder('0')
            ->width(3);

        $specsGroup->number('year_built')
            ->label('Year Built')
            ->placeholder('2024')
            ->width(3);

        $specsGroup->number('size_sqft')
            ->label('Built-up Area (sqft)')
            ->placeholder('0')
            ->width(6);

        $specsGroup->number('plot_size_sqft')
            ->label('Plot Area (sqft)')
            ->placeholder('0')
            ->width(6);

        $specsGroup->number('floor_number')
            ->label('Floor')
            ->placeholder('0')
            ->width(6);

        $specsGroup->number('total_floors')
            ->label('Total Floors')
            ->placeholder('0')
            ->width(6);

        // === FEATURES & AMENITIES SECTION ===
        $featuresGroup = $form->group('features-info')
            ->title('Features & Amenities')
            ->icon('star')
            ->variant('bordered')
            ->columns(12);

        $featuresGroup->select('furnishing_status')
            ->label('Furnishing Status')
            ->options($masterData['furnishing_statuses'] ?? [])
            ->width(12);

        $featuresGroup->checkbox('has_parking')
            ->label('Parking')
            ->width(3);

        $featuresGroup->checkbox('has_balcony')
            ->label('Balcony')
            ->width(3);

        $featuresGroup->checkbox('has_garden')
            ->label('Garden')
            ->width(3);

        $featuresGroup->checkbox('has_pool')
            ->label('Pool')
            ->width(3);

        $featuresGroup->checkbox('pet_friendly')
            ->label('Pet Friendly')
            ->width(6);

        $featuresGroup->textarea('features')
            ->label('Additional Features')
            ->placeholder('Central AC, Built-in wardrobes, Maid\'s room, etc.')
            ->rows(3)
            ->width(12);

        $featuresGroup->textarea('amenities')
            ->label('Building Amenities')
            ->placeholder('Gym, Sauna, Children\'s play area, Concierge, etc.')
            ->rows(3)
            ->width(12);

        // === MEDIA SECTION ===
        $mediaGroup = $form->group('media-info')
            ->title('Media & Photos')
            ->icon('image')
            ->variant('bordered')
            ->columns(2);

        $mediaGroup->file('featured_image')
            ->label('Featured Image')
            ->accept('image/*')
            ->uploadUrl('/api/listing-upload/image')
            ->width(6);

        $mediaGroup->file('images')
            ->label('Property Images')
            ->accept('image/*')
            ->multiple(true)
            ->uploadUrl('/api/listing-upload/image')
            ->width(6);

        $mediaGroup->file('floor_plans')
            ->label('Floor Plans')
            ->accept('image/*,application/pdf')
            ->multiple(true)
            ->uploadUrl('/api/listing-upload/document')
            ->width(6);

        $mediaGroup->text('video_url')
            ->label('Video URL')
            ->placeholder('https://youtube.com/...')
            ->width(6);

        $mediaGroup->text('virtual_tour_url')
            ->label('Virtual Tour URL')
            ->placeholder('https://...')
            ->width(12);

        // === STATUS & AVAILABILITY SECTION ===
        $statusGroup = $form->group('status-info')
            ->title('Status & Availability')
            ->icon('checkmark')
            ->variant('bordered')
            ->columns(12);

        $statusGroup->select('status')
            ->label('Listing Status')
            ->options($masterData['statuses'] ?? [])
            ->required(true)
            ->width(6);

        $statusGroup->select('availability')
            ->label('Availability')
            ->options($masterData['availabilities'] ?? [])
            ->required(true)
            ->width(6);

        $statusGroup->date('published_at')
            ->label('Publish Date')
            ->width(4);

        $statusGroup->date('available_from')
            ->label('Available From')
            ->width(4);

        $statusGroup->date('available_until')
            ->label('Available Until')
            ->width(4);

        $statusGroup->date('expires_at')
            ->label('Listing Expires')
            ->width(12);

        $statusGroup->checkbox('is_featured')
            ->label('Featured')
            ->width(4);

        $statusGroup->checkbox('is_hot_deal')
            ->label('Hot Deal')
            ->width(4);

        $statusGroup->checkbox('is_verified')
            ->label('Verified')
            ->width(4);

        // === AGENT INFORMATION SECTION ===
        $agentGroup = $form->group('agent-info')
            ->title('Agent Information')
            ->icon('person')
            ->variant('bordered')
            ->columns(2);

        $agentGroup->select('agent_id')
            ->label('Assigned Agent')
            ->options($masterData['agents'] ?? [])
            ->placeholder('Select an agent')
            ->required(true)
            ->width(6);

        $agentGroup->text('agent_name')
            ->label('Agent Name')
            ->placeholder('Agent name')
            ->width(6);

        $agentGroup->text('agent_phone')
            ->label('Agent Phone')
            ->placeholder('+971 XX XXX XXXX')
            ->width(6);

        $agentGroup->text('agent_email')
            ->label('Agent Email')
            ->placeholder('agent@email.com')
            ->width(6);

        // === FINANCIAL DETAILS SECTION ===
        $financialGroup = $form->group('financial-info')
            ->title('Financial Details')
            ->icon('cash')
            ->variant('bordered')
            ->columns(3);

        $financialGroup->number('original_price')
            ->label('Original Price')
            ->placeholder('0.00')
            ->width(4);

        $financialGroup->number('discount_percentage')
            ->label('Discount %')
            ->placeholder('0')
            ->width(4);

        $financialGroup->number('service_charge')
            ->label('Service Charge')
            ->placeholder('0.00')
            ->width(4);

        $financialGroup->select('service_charge_period')
            ->label('Period')
            ->options([
                ['value' => 'yearly', 'label' => 'Yearly'],
                ['value' => 'monthly', 'label' => 'Monthly'],
            ])
            ->width(4);

        $financialGroup->number('security_deposit')
            ->label('Security Deposit')
            ->placeholder('0.00')
            ->width(8);

        $financialGroup->textarea('payment_terms')
            ->label('Payment Terms')
            ->placeholder('Payment terms and conditions...')
            ->rows(3)
            ->width(12);

        // === ADDITIONAL INFO SECTION ===
        $additionalGroup = $form->group('additional-info')
            ->title('Additional Information')
            ->icon('information')
            ->variant('bordered')
            ->columns(2);

        $additionalGroup->text('reference_number')
            ->label('Reference Number')
            ->placeholder('REF-12345')
            ->width(6);

        $additionalGroup->number('priority_score')
            ->label('Priority Score')
            ->placeholder('50')
            ->width(6);

        $additionalGroup->textarea('internal_notes')
            ->label('Internal Notes')
            ->placeholder('Private notes (not visible to public)...')
            ->rows(4)
            ->width(12);

        return $form;
    }
}
