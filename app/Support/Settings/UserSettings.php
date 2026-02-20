<?php

namespace App\Support\Settings;

/**
 * UserSettings
 * 
 * Manages form group visibility and edit permissions for user forms
 * Provides default settings and allows contextual overrides
 * 
 * @package App\Support\Settings
 */
class UserSettings
{
    /**
     * Base group/field structure — pure config, no logic.
     */
    public static function defaults(): array
    {
        return [
            'groups' => [
                'form.create-user-form.personal-info'    => ['show' => true,  'edit' => true],
                'form.create-user-form.employment-info'  => ['show' => true,  'edit' => true],
                'form.create-user-form.media-info'       => ['show' => true,  'edit' => true],
                'form.create-user-form.security-info'    => ['show' => true,  'edit' => true],
                'form.create-user-form.skills-info'      => ['show' => true,  'edit' => true],
                'form.create-user-form.address-info'     => ['show' => true,  'edit' => true],

                'form.edit-user-form.personal-info'      => ['show' => true,  'edit' => true],
                'form.edit-user-form.employment-info'    => ['show' => true,  'edit' => true],
                'form.edit-user-form.media-info'         => ['show' => true,  'edit' => true],
                'form.edit-user-form.security-info'      => ['show' => true,  'edit' => true],
                'form.edit-user-form.skills-info'        => ['show' => true,  'edit' => true],
                'form.edit-user-form.address-info'       => ['show' => true,  'edit' => true],

                'form.view-user-form.personal-info'      => ['show' => true,  'edit' => false],
                'form.view-user-form.employment-info'    => ['show' => true,  'edit' => false],
                'form.view-user-form.media-info'         => ['show' => true,  'edit' => false],
                'form.view-user-form.security-info'      => ['show' => false, 'edit' => false],
                'form.view-user-form.skills-info'        => ['show' => true,  'edit' => false],
                'form.view-user-form.address-info'       => ['show' => true,  'edit' => false],
            ],
            'fields'       => [],
            'formSettings' => [],
        ];
    }

    /**
     * Compute UI flags for a given context ('create' | 'edit' | 'view').
     * Mirrors the Model::getSettings($context) pattern; lives here because
     * User data is array-based (JSON file), not an Eloquent model instance.
     *
     * @param string $context   'create' | 'edit' | 'view'
     * @param array  $userData  The user data array returned by the action
     * @return array
     */
    public static function getSettings(string $context = 'view', array $userData = []): array
    {
        $settings = self::defaults();

        // Security group: non-admins can see but not edit on edit form;
        // hidden entirely on view form (already off in defaults).
        if ($context === 'edit') {
            $settings['groups']['form.edit-user-form.security-info']['edit'] = false;
        }

        // Create: hide view-form groups (irrelevant on create)
        if ($context === 'create') {
            foreach (array_keys($settings['groups']) as $key) {
                if (str_contains($key, 'view-user-form') || str_contains($key, 'edit-user-form')) {
                    $settings['groups'][$key]['show'] = false;
                }
            }
        }

        return $settings;
    }
}
