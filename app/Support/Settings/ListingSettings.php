<?php

namespace App\Support\Settings;

/**
 * ListingSettings
 * 
 * Manages form group visibility and edit permissions for listing forms
 * Provides default settings and allows contextual overrides
 * 
 * @package App\Support\Settings
 */
class ListingSettings
{
    /**
     * Get default settings for all listing form groups
     *
     * @return array
     */
    public static function defaults(): array
    {
        return [
            'groups' => [
                'form.listing-form.property-info' => ['show' => true, 'edit' => true],
                'form.listing-form.location-info' => ['show' => true, 'edit' => true],
                'form.listing-form.specifications-info' => ['show' => true, 'edit' => true],
                'form.listing-form.features-info' => ['show' => true, 'edit' => true],
                'form.listing-form.media-info' => ['show' => true, 'edit' => true],
                'form.listing-form.status-info' => ['show' => true, 'edit' => true],
                'form.listing-form.agent-info' => ['show' => true, 'edit' => true],
                'form.listing-form.financial-info' => ['show' => true, 'edit' => true],
                'form.listing-form.additional-info' => ['show' => true, 'edit' => true],
            ],
        ];
    }

    /**
     * Get settings with overrides applied
     * 
     * Can be customized based on user permissions, listing status, etc.
     *
     * @param array $overrides Custom settings to merge
     * @return array
     */
    public static function get(array $overrides = []): array
    {
        return array_replace_recursive(self::defaults(), $overrides);
    }

    /**
     * Get settings for viewing a listing
     * Example: Hide financial info for non-admin users
     *
     * @param bool $isAdmin Whether user is admin
     * @return array
     */
    public static function forView(bool $isAdmin = false): array
    {
        $overrides = [];

        if (!$isAdmin) {
            $overrides['groups']['form.listing-form.financial-info'] = [
                'show' => false,
                'edit' => false
            ];
        }

        return self::get($overrides);
    }

    /**
     * Get settings for editing a listing
     *
     * @param bool $canEditAgent Whether user can edit agent fields
     * @return array
     */
    public static function forEdit(bool $canEditAgent = true): array
    {
        $overrides = [];

        if (!$canEditAgent) {
            $overrides['groups']['form.listing-form.agent-info'] = [
                'show' => true,
                'edit' => false
            ];
        }

        return self::get($overrides);
    }

    /**
     * Hide specific groups
     *
     * @param array $groupKeys Group keys to hide
     * @return array
     */
    public static function hideGroups(array $groupKeys): array
    {
        $overrides = ['groups' => []];

        foreach ($groupKeys as $key) {
            $overrides['groups'][$key] = ['show' => false, 'edit' => false];
        }

        return self::get($overrides);
    }

    /**
     * Make specific groups read-only
     *
     * @param array $groupKeys Group keys to make read-only
     * @return array
     */
    public static function readOnlyGroups(array $groupKeys): array
    {
        $overrides = ['groups' => []];

        foreach ($groupKeys as $key) {
            $overrides['groups'][$key] = ['show' => true, 'edit' => false];
        }

        return self::get($overrides);
    }
}
