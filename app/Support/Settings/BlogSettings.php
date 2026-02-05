<?php

namespace App\Support\Settings;

/**
 * BlogSettings
 * 
 * Manages form group visibility and edit permissions for blog forms
 * Provides default settings and allows contextual overrides
 * 
 * @package App\Support\Settings
 */
class BlogSettings
{
    /**
     * Get default settings for all blog form groups
     *
     * @return array
     */
    public static function defaults(): array
    {
        return [
            'groups' => [
                'form.blog-form-activity.content-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.publication-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.media-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.category-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.seo-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.settings-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.analytics-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.custom-info' => ['show' => true, 'edit' => true],
            ],
        ];
    }

    /**
     * Get settings with overrides applied
     * 
     * Can be customized based on user permissions, blog status, etc.
     *
     * @param array $overrides Custom settings to merge
     * @return array
     */
    public static function get(array $overrides = []): array
    {
        return array_replace_recursive(self::defaults(), $overrides);
    }

    /**
     * Get settings for viewing a blog
     * Example: Hide analytics for non-admin users
     *
     * @param bool $isAdmin Whether user is admin
     * @return array
     */
    public static function forView(bool $isAdmin = false): array
    {
        $overrides = [];

        if (!$isAdmin) {
            $overrides['groups']['form.blog-form-activity.analytics-info'] = [
                'show' => false,
                'edit' => false
            ];
        }

        return self::get($overrides);
    }

    /**
     * Get settings for editing a blog
     *
     * @param bool $canEditSeo Whether user can edit SEO fields
     * @return array
     */
    public static function forEdit(bool $canEditSeo = true): array
    {
        $overrides = [];

        if (!$canEditSeo) {
            $overrides['groups']['form.blog-form-activity.seo-info'] = [
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
