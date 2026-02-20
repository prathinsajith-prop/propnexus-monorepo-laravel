<?php

namespace App\Support\Settings;

/**
 * ListingSettings
 *
 * Pure configuration provider for listing form groups and fields.
 * Context-aware visibility logic lives in Listing::getSettings().
 *
 * @package App\Support\Settings
 */
class ListingSettings
{
    // ─── Form IDs ─────────────────────────────────────────────────────────────

    public const FORM_LISTING = 'listing-form';
    public const FORM_VIEW    = 'view-listing-form';

    // ─── Defaults — every group and field ON and editable ─────────────────────

    public static function defaults(): array
    {
        return [
            'groups' => [
                // Listing form groups
                'form.listing-form.property-info'       => ['show' => true, 'edit' => true],
                'form.listing-form.location-info'       => ['show' => true, 'edit' => true],
                'form.listing-form.specifications-info' => ['show' => true, 'edit' => true],
                'form.listing-form.features-info'       => ['show' => true, 'edit' => true],
                'form.listing-form.media-info'          => ['show' => true, 'edit' => true],
                'form.listing-form.status-info'         => ['show' => true, 'edit' => true],
                'form.listing-form.agent-info'          => ['show' => true, 'edit' => true],
                'form.listing-form.financial-info'      => ['show' => true, 'edit' => true],
                'form.listing-form.additional-info'     => ['show' => true, 'edit' => true],
                // View form groups
                'form.view-listing-form.overview'       => ['show' => true, 'edit' => true],
                'form.view-listing-form.location'       => ['show' => true, 'edit' => true],
                'form.view-listing-form.description'    => ['show' => true, 'edit' => true],
                'form.view-listing-form.agent'          => ['show' => true, 'edit' => true],
                'form.view-listing-form.status'         => ['show' => true, 'edit' => true],
            ],
            'fields' => [
                // Property info
                'title'             => ['show' => true, 'edit' => true],
                'slug'              => ['show' => true, 'edit' => true],
                'property_type'     => ['show' => true, 'edit' => true],
                'listing_type'      => ['show' => true, 'edit' => true],
                'price'             => ['show' => true, 'edit' => true],
                'reference_number'  => ['show' => true, 'edit' => true],
                'views_count'       => ['show' => true, 'edit' => false],
                // Location
                'address'           => ['show' => true, 'edit' => true],
                'area'              => ['show' => true, 'edit' => true],
                'city'              => ['show' => true, 'edit' => true],
                'state'             => ['show' => true, 'edit' => true],
                'country'           => ['show' => true, 'edit' => true],
                'zip_code'          => ['show' => true, 'edit' => true],
                'latitude'          => ['show' => true, 'edit' => true],
                'longitude'         => ['show' => true, 'edit' => true],
                // Specifications
                'bedrooms'          => ['show' => true, 'edit' => true],
                'bathrooms'         => ['show' => true, 'edit' => true],
                'area_sqft'         => ['show' => true, 'edit' => true],
                'plot_size'         => ['show' => true, 'edit' => true],
                'year_built'        => ['show' => true, 'edit' => true],
                'parking_spaces'    => ['show' => true, 'edit' => true],
                // Features
                'features'          => ['show' => true, 'edit' => true],
                'amenities'         => ['show' => true, 'edit' => true],
                'description'       => ['show' => true, 'edit' => true],
                'short_description' => ['show' => true, 'edit' => true],
                // Media
                'images'            => ['show' => true, 'edit' => true],
                'floor_plans'       => ['show' => true, 'edit' => true],
                'documents'         => ['show' => true, 'edit' => true],
                'video_url'         => ['show' => true, 'edit' => true],
                'virtual_tour_url'  => ['show' => true, 'edit' => true],
                // Status
                'status'            => ['show' => true, 'edit' => true],
                'availability'      => ['show' => true, 'edit' => true],
                'available_from'    => ['show' => true, 'edit' => true],
                'published_at'      => ['show' => true, 'edit' => true],
                'featured'          => ['show' => true, 'edit' => true],
                // Agent
                'agent_id'          => ['show' => true, 'edit' => true],
                'agent_name'        => ['show' => true, 'edit' => true],
                'agent_email'       => ['show' => true, 'edit' => true],
                'agent_phone'       => ['show' => true, 'edit' => true],
                // Financial
                'commission'        => ['show' => true, 'edit' => true],
                'payment_terms'     => ['show' => true, 'edit' => true],
                'deposit_amount'    => ['show' => true, 'edit' => true],
                'service_charge'    => ['show' => true, 'edit' => true],
                // Additional
                'custom_fields'     => ['show' => true, 'edit' => true],
                'seo_meta'          => ['show' => true, 'edit' => true],
                'schema_markup'     => ['show' => true, 'edit' => true],
                'analytics'         => ['show' => true, 'edit' => false],
                'notes'             => ['show' => true, 'edit' => true],
            ],
        ];
    }

    // ─── Merge ────────────────────────────────────────────────────────────────

    /** Deep-merge $overrides onto defaults. */
    public static function get(array $overrides = []): array
    {
        return array_replace_recursive(self::defaults(), $overrides);
    }

    // ─── Generic helpers ──────────────────────────────────────────────────────

    /** Hide groups (show: false, edit: false). */
    public static function hideGroups(array $keys): array
    {
        $overrides = ['groups' => []];
        foreach ($keys as $key) {
            $overrides['groups'][$key] = ['show' => false, 'edit' => false];
        }
        return self::get($overrides);
    }

    /** Make groups read-only (show: true, edit: false). */
    public static function readOnlyGroups(array $keys): array
    {
        $overrides = ['groups' => []];
        foreach ($keys as $key) {
            $overrides['groups'][$key] = ['show' => true, 'edit' => false];
        }
        return self::get($overrides);
    }

    /** Hide specific fields (show: false, edit: false). */
    public static function hideFields(array $keys): array
    {
        $overrides = ['fields' => []];
        foreach ($keys as $key) {
            $overrides['fields'][$key] = ['show' => false, 'edit' => false];
        }
        return self::get($overrides);
    }

    /** Make specific fields read-only (show: true, edit: false). */
    public static function readOnlyFields(array $keys): array
    {
        $overrides = ['fields' => []];
        foreach ($keys as $key) {
            $overrides['fields'][$key] = ['show' => true, 'edit' => false];
        }
        return self::get($overrides);
    }
}
