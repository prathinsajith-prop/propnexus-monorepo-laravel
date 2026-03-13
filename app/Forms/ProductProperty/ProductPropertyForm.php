<?php

namespace App\Forms\ProductProperty;

use Litepie\Layout\Components\FormComponent;

/**
 * ProductPropertyForm
 *
 * Comprehensive product property form with all sections matching
 * the bixo_product_properties table fields.
 */
class ProductPropertyForm
{
    /**
     * Create the product property form.
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method (POST/PUT)
     * @param  string  $action  Form action URL
     * @param  array  $masterData  Master data for dropdowns
     * @param  string|null  $dataUrl  URL to pre-fill data (edit mode)
     * @param  bool  $isCreate  Whether this is a create form
     * @return FormComponent
     */
    public static function make($formId, $method, $action, $masterData, $dataUrl = null, $isCreate = false)
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(2)
            ->gap('lg')
            ->meta(['width' => '1000px']);

        if ($dataUrl) {
            $form->dataUrl($dataUrl)->dataKey('data');
        }

        // ── BASIC INFORMATION ──────────────────────────────────
        $basicGroup = $form->group('basic-info')
            ->title(__('product_property.basic_info'))
            ->icon('home')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $basicGroup->text('title')
            ->label(__('product_property.property_title'))
            ->placeholder(__('product_property.property_title_placeholder'))
            ->required(true)
            ->width(12);

        $basicGroup->text('ref')
            ->label(__('product_property.ref'))
            ->placeholder(__('product_property.ref_placeholder'))
            ->required(true)
            ->width(4);

        $basicGroup->text('ref_old')
            ->label(__('product_property.ref_old'))
            ->placeholder(__('product_property.ref_old_placeholder'))
            ->width(4);

        $basicGroup->text('ref_pf')
            ->label(__('product_property.ref_pf'))
            ->placeholder(__('product_property.ref_pf_placeholder'))
            ->width(4);

        $basicGroup->select('category_type')
            ->label(__('product_property.category_type'))
            ->options($masterData['category_types'] ?? [])
            ->required(true)
            ->width(4);

        $basicGroup->select('property_for')
            ->label(__('product_property.property_for'))
            ->options($masterData['property_for'] ?? [])
            ->required(true)
            ->width(4);

        $basicGroup->select('property_type')
            ->label(__('product_property.property_type'))
            ->options($masterData['property_types'] ?? [])
            ->width(4);

        $basicGroup->select('status')
            ->label(__('product_property.status'))
            ->options($masterData['statuses'] ?? [])
            ->required(true)
            ->width(4);

        $basicGroup->select('category')
            ->label(__('product_property.category'))
            ->options($masterData['categories'] ?? [])
            ->width(4);

        $basicGroup->text('type')
            ->label(__('product_property.type'))
            ->placeholder(__('product_property.type_placeholder'))
            ->width(4);

        $basicGroup->checkbox('premium')
            ->label(__('product_property.premium'))
            ->width(3);

        $basicGroup->checkbox('exclusive')
            ->label(__('product_property.exclusive'))
            ->width(3);

        $basicGroup->checkbox('price_on_request')
            ->label(__('product_property.price_on_request'))
            ->width(3);

        $basicGroup->checkbox('company_listing')
            ->label(__('product_property.company_listing'))
            ->width(3);

        // ── LOCATION ──────────────────────────────────────────
        $locationGroup = $form->group('location-info')
            ->title(__('product_property.location_info'))
            ->icon('pin')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $locationGroup->select('country_id')
            ->label(__('product_property.country_id'))
            ->options($masterData['countries'] ?? [])
            ->width(4);

        $locationGroup->select('region_id')
            ->label(__('product_property.region_id'))
            ->options($masterData['regions'] ?? [])
            ->width(4);

        $locationGroup->select('location_id')
            ->label(__('product_property.location_id'))
            ->options($masterData['locations'] ?? [])
            ->width(4);

        $locationGroup->select('sublocation_id')
            ->label(__('product_property.sublocation_id'))
            ->options($masterData['sublocations'] ?? [])
            ->width(4);

        $locationGroup->select('building_id')
            ->label(__('product_property.building_id'))
            ->options($masterData['buildings'] ?? [])
            ->width(4);

        $locationGroup->select('tower_id')
            ->label(__('product_property.tower_id'))
            ->options($masterData['towers'] ?? [])
            ->width(4);

        $locationGroup->text('tower_name')
            ->label(__('product_property.tower_name'))
            ->placeholder(__('product_property.tower_name_placeholder'))
            ->width(6);

        $locationGroup->text('unit')
            ->label(__('product_property.unit'))
            ->placeholder(__('product_property.unit_placeholder'))
            ->width(3);

        $locationGroup->text('floor')
            ->label(__('product_property.floor'))
            ->placeholder(__('product_property.floor_placeholder'))
            ->width(3);

        $locationGroup->text('street_no')
            ->label(__('product_property.street_no'))
            ->placeholder(__('product_property.street_no_placeholder'))
            ->width(6);

        $locationGroup->text('latitude')
            ->label(__('product_property.latitude'))
            ->placeholder(__('product_property.latitude_placeholder'))
            ->width(3);

        $locationGroup->text('longitude')
            ->label(__('product_property.longitude'))
            ->placeholder(__('product_property.longitude_placeholder'))
            ->width(3);

        // ── SPECIFICATIONS ────────────────────────────────────
        $specsGroup = $form->group('specs-info')
            ->title(__('product_property.specs_info'))
            ->icon('listcheck')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $specsGroup->text('beds')
            ->label(__('product_property.beds'))
            ->placeholder(__('product_property.beds_placeholder'))
            ->width(3);

        $specsGroup->number('baths')
            ->label(__('product_property.baths'))
            ->placeholder(__('product_property.baths_placeholder'))
            ->width(3);

        $specsGroup->number('parking')
            ->label(__('product_property.parking'))
            ->placeholder(__('product_property.parking_placeholder'))
            ->width(3);

        $specsGroup->number('bua')
            ->label(__('product_property.bua'))
            ->placeholder(__('product_property.bua_placeholder'))
            ->width(3);

        $specsGroup->text('plot')
            ->label(__('product_property.plot'))
            ->placeholder(__('product_property.plot_placeholder'))
            ->width(3);

        $specsGroup->select('construction_status')
            ->label(__('product_property.construction_status'))
            ->options($masterData['construction_statuses'] ?? [])
            ->width(3);

        $specsGroup->date('completion_on')
            ->label(__('product_property.completion_on'))
            ->width(3);

        $specsGroup->select('furnishing')
            ->label(__('product_property.furnishing'))
            ->options($masterData['furnishing_statuses'] ?? [])
            ->width(3);

        // ── PRICING ───────────────────────────────────────────
        $pricingGroup = $form->group('pricing-info')
            ->title(__('product_property.pricing_info'))
            ->icon('price')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $pricingGroup->number('price')
            ->label(__('product_property.price'))
            ->placeholder(__('product_property.price_placeholder'))
            ->required(true)
            ->width(6);

        $pricingGroup->number('original_price')
            ->label(__('product_property.original_price'))
            ->placeholder(__('product_property.original_price_placeholder'))
            ->width(6);

        $pricingGroup->number('service_charge')
            ->label(__('product_property.service_charge'))
            ->placeholder(__('product_property.service_charge_placeholder'))
            ->width(4);

        $pricingGroup->select('frequency')
            ->label(__('product_property.frequency'))
            ->options($masterData['frequencies'] ?? [])
            ->width(4);

        $pricingGroup->number('cheques')
            ->label(__('product_property.cheques'))
            ->placeholder(__('product_property.cheques_placeholder'))
            ->width(4);

        $pricingGroup->number('deposit')
            ->label(__('product_property.deposit'))
            ->placeholder(__('product_property.deposit_placeholder'))
            ->width(4);

        $pricingGroup->number('deposit_amount')
            ->label(__('product_property.deposit_amount'))
            ->placeholder(__('product_property.deposit_amount_placeholder'))
            ->width(4);

        $pricingGroup->number('conmmission_split')
            ->label(__('product_property.conmmission_split'))
            ->placeholder(__('product_property.conmmission_split_placeholder'))
            ->width(4);

        $pricingGroup->number('referral_pct')
            ->label(__('product_property.referral_pct'))
            ->placeholder(__('product_property.referral_pct_placeholder'))
            ->width(4);

        $pricingGroup->text('payment_plan')
            ->label(__('product_property.payment_plan'))
            ->placeholder(__('product_property.payment_plan_placeholder'))
            ->width(4);

        // ── DESCRIPTION ───────────────────────────────────────
        $descGroup = $form->group('desc-info')
            ->title(__('product_property.desc_info'))
            ->icon('textleft')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $descGroup->textarea('description')
            ->label(__('product_property.description'))
            ->placeholder(__('product_property.description_placeholder'))
            ->rows(5)
            ->width(12);

        $descGroup->textarea('description_more')
            ->label(__('product_property.description_more'))
            ->placeholder(__('product_property.description_more_placeholder'))
            ->rows(4)
            ->width(12);

        $descGroup->textarea('notes')
            ->label(__('product_property.notes'))
            ->placeholder(__('product_property.notes_placeholder'))
            ->rows(3)
            ->width(12);

        // ── MEDIA ─────────────────────────────────────────────
        $mediaGroup = $form->group('media-info')
            ->title(__('product_property.media_info'))
            ->icon('camera')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $mediaGroup->file('photos')
            ->label(__('product_property.photos'))
            ->accept('image/*')
            ->multiple(true)
            ->maxSize(10240)
            ->uploadUrl('/api/product-property-upload/image')
            ->width(12);

        $mediaGroup->file('floor_plans')
            ->label(__('product_property.floor_plans'))
            ->accept('image/*,application/pdf')
            ->multiple(true)
            ->maxSize(20480)
            ->uploadUrl('/api/product-property-upload/document')
            ->width(12);

        $mediaGroup->file('documents')
            ->label(__('product_property.documents'))
            ->accept('*/*')
            ->multiple(true)
            ->maxSize(20480)
            ->uploadUrl('/api/product-property-upload/document')
            ->width(12);

        // ── STATUS & AVAILABILITY ─────────────────────────────
        $statusGroup = $form->group('status-info')
            ->title(__('product_property.status_info'))
            ->icon('badge')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $statusGroup->date('available_from')
            ->label(__('product_property.available_from'))
            ->width(4);

        $statusGroup->date('form_a_expiry')
            ->label(__('product_property.form_a_expiry'))
            ->width(4);

        $statusGroup->date('expires_at')
            ->label(__('product_property.expires_at'))
            ->width(4);

        $statusGroup->number('rented_price')
            ->label(__('product_property.rented_price'))
            ->placeholder('0')
            ->width(4);

        $statusGroup->date('rent_start')
            ->label(__('product_property.rent_start'))
            ->width(4);

        $statusGroup->date('rent_end')
            ->label(__('product_property.rent_end'))
            ->width(4);

        $statusGroup->select('is_verify')
            ->label(__('product_property.verification'))
            ->options([
                ['value' => 'Yes', 'label' => __('product_property.yes')],
                ['value' => 'No', 'label' => __('product_property.no')],
                ['value' => 'Pending', 'label' => __('product_property.pending')],
            ])
            ->width(6);

        $statusGroup->checkbox('rented')
            ->label(__('product_property.rented'))
            ->width(6);

        // ── TRAKHEESI / DLD ──────────────────────────────────
        $regGroup = $form->group('reg-info')
            ->title(__('product_property.reg_info'))
            ->icon('shield')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $regGroup->text('trakheesi')
            ->label(__('product_property.trakheesi'))
            ->placeholder(__('product_property.trakheesi_placeholder'))
            ->width(6);

        $regGroup->date('trakheesi_expiry')
            ->label(__('product_property.trakheesi_expiry'))
            ->width(3);

        $regGroup->text('trakheesi_branch')
            ->label(__('product_property.trakheesi_branch'))
            ->placeholder(__('product_property.trakheesi_branch_placeholder'))
            ->width(3);

        $regGroup->text('permit_period')
            ->label(__('product_property.permit_period'))
            ->placeholder(__('product_property.permit_period_placeholder'))
            ->width(6);

        $regGroup->text('str')
            ->label(__('product_property.str'))
            ->placeholder(__('product_property.str_placeholder'))
            ->width(6);

        // ── ASSIGNMENT ───────────────────────────────────────
        $assignGroup = $form->group('assign-info')
            ->title(__('product_property.assign_info'))
            ->icon('users')
            ->variant('bordered')
            ->columns(12)
            ->create($isCreate);

        $assignGroup->select('user_id')
            ->label(__('product_property.user_id'))
            ->options($masterData['users'] ?? [])
            ->width(4);

        $assignGroup->select('assign_to')
            ->label(__('product_property.assign_to'))
            ->options($masterData['users'] ?? [])
            ->width(4);

        $assignGroup->select('marketed_by')
            ->label(__('product_property.marketed_by'))
            ->options($masterData['users'] ?? [])
            ->width(4);

        $assignGroup->select('referred_by')
            ->label(__('product_property.referred_by'))
            ->options($masterData['users'] ?? [])
            ->width(4);

        $assignGroup->select('published_by')
            ->label(__('product_property.published_by'))
            ->options($masterData['users'] ?? [])
            ->width(4);

        $assignGroup->select('created_by')
            ->label(__('product_property.created_by'))
            ->options($masterData['users'] ?? [])
            ->width(4);

        $assignGroup->select('listing_source')
            ->label(__('product_property.listing_source'))
            ->options($masterData['listing_sources'] ?? [])
            ->width(4);

        $assignGroup->checkbox('lead_auto_assign')
            ->label(__('product_property.lead_auto_assign'))
            ->width(4);

        $assignGroup->checkbox('watermark')
            ->label(__('product_property.watermark'))
            ->width(4);

        return $form;
    }
}
