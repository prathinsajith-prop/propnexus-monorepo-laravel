<?php

namespace App\Support\Settings;

/**
 * ProductPropertySettings
 *
 * Pure configuration provider for product property form groups and fields.
 * Context-aware visibility logic lives in BixoProductProperties::getSettings().
 *
 * Form IDs:
 *   FORM_CREATE          — create-property-form
 *   FORM_EDIT            — edit-property-form
 *   FORM_VIEW            — view-property-form
 *   FORM_VIEW_FULLSCREEN — view-property-fullscreen-form
 *   FORM_CREATE_MODAL    — create-property-form-modal
 *
 * @package App\Support\Settings
 */
class ProductPropertySettings
{
    // ─── Form IDs (public — used in Model::getSettings) ──────────────────────

    public const FORM_CREATE          = 'create-property-form';
    public const FORM_EDIT            = 'edit-property-form';
    public const FORM_VIEW            = 'view-property-form';
    public const FORM_VIEW_FULLSCREEN = 'view-property-fullscreen-form';
    public const FORM_CREATE_MODAL    = 'create-property-form-modal';

    // ─── Group IDs ────────────────────────────────────────────────────────────

    public const COMMON_GROUPS = [
        'basic-info',
        'location-info',
        'specs-info',
        'pricing-info',
        'desc-info',
        'media-info',
        'status-info',
        'reg-info',
        'assign-info',
    ];

    // ─── Core builder (public — used in Model::getSettings) ───────────────────

    /**
     * Build group settings for the given form+group combinations.
     */
    public static function buildGroups(array $formIds, array $groupIds, bool $show = true, bool $edit = true): array
    {
        $groups = [];
        foreach ($formIds as $formId) {
            foreach ($groupIds as $groupId) {
                $groups["form.{$formId}.{$groupId}"] = compact('show', 'edit');
            }
        }
        return $groups;
    }

    // ─── Defaults — every group and field ON and editable ─────────────────────

    public static function defaults(): array
    {
        $allForms = [
            self::FORM_CREATE,
            self::FORM_EDIT,
            self::FORM_VIEW,
            self::FORM_VIEW_FULLSCREEN,
            self::FORM_CREATE_MODAL,
        ];

        return [
            'groups' => self::buildGroups($allForms, self::COMMON_GROUPS, true, true),
            'fields' => [
                // Basic info
                'title'            => ['show' => true, 'edit' => true],
                'ref'              => ['show' => true, 'edit' => true],
                'category_type'    => ['show' => true, 'edit' => true],
                'property_for'     => ['show' => true, 'edit' => true],
                'property_type'    => ['show' => true, 'edit' => true],
                'status'           => ['show' => true, 'edit' => true],
                'exclusive'        => ['show' => true, 'edit' => true],
                'company_listing'  => ['show' => true, 'edit' => true],
                // Location
                'country'          => ['show' => true, 'edit' => true],
                'region'           => ['show' => true, 'edit' => true],
                'location'         => ['show' => true, 'edit' => true],
                'building'         => ['show' => true, 'edit' => true],
                'unit'             => ['show' => true, 'edit' => true],
                'floor'            => ['show' => true, 'edit' => true],
                'latitude'         => ['show' => true, 'edit' => true],
                'longitude'        => ['show' => true, 'edit' => true],
                // Specs
                'beds'             => ['show' => true, 'edit' => true],
                'baths'            => ['show' => true, 'edit' => true],
                'parking_no'       => ['show' => true, 'edit' => true],
                'bua'              => ['show' => true, 'edit' => true],
                'plot'             => ['show' => true, 'edit' => true],
                'furnishing'       => ['show' => true, 'edit' => true],
                'completion_on'    => ['show' => true, 'edit' => true],
                // Pricing (admin-only by default in Model::getSettings)
                'price'            => ['show' => true, 'edit' => true],
                'price_on_request' => ['show' => true, 'edit' => true],
                'original_price'   => ['show' => true, 'edit' => true],
                'frequency'        => ['show' => true, 'edit' => true],
                'service_charge'   => ['show' => true, 'edit' => true],
                'commission'       => ['show' => true, 'edit' => true],
                'payment_plan'     => ['show' => true, 'edit' => true],
                'cheques'          => ['show' => true, 'edit' => true],
                // Description
                'description'      => ['show' => true, 'edit' => true],
                'notes'            => ['show' => true, 'edit' => true],
                // Media
                'photos'           => ['show' => true, 'edit' => true],
                'floor_plans'      => ['show' => true, 'edit' => true],
                'documents'        => ['show' => true, 'edit' => true],
                'watermark'        => ['show' => true, 'edit' => true],
                // Status / dates
                'available_from'   => ['show' => true, 'edit' => true],
                'rented'           => ['show' => true, 'edit' => true],
                'rented_price'     => ['show' => true, 'edit' => true],
                'rent_start'       => ['show' => true, 'edit' => true],
                'rent_end'         => ['show' => true, 'edit' => true],
                'handover_date'    => ['show' => true, 'edit' => true],
                'published_at'     => ['show' => true, 'edit' => false],
                // Registration (admin-only, hidden on create)
                'trakheesi'        => ['show' => true, 'edit' => true],
                'trakheesi_expiry' => ['show' => true, 'edit' => true],
                'permit_number'    => ['show' => true, 'edit' => true],
                'dld_status'       => ['show' => true, 'edit' => true],
                'dld_details'      => ['show' => true, 'edit' => true],
                // Assignment
                'user_id'          => ['show' => true, 'edit' => true],
                'assign_to'        => ['show' => true, 'edit' => true],
                'marketed_by'      => ['show' => true, 'edit' => true],
                'referred_by'      => ['show' => true, 'edit' => true],
                'portal_settings'  => ['show' => true, 'edit' => true],
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

    // ─── Key helpers ──────────────────────────────────────────────────────────

    /** Full settings key for a form+group pair. e.g. "form.edit-property-form.pricing-info" */
    public static function groupKey(string $formId, string $groupId): string
    {
        return "form.{$formId}.{$groupId}";
    }

    /** All group keys for a given form ID. */
    public static function groupKeysForForm(string $formId): array
    {
        return array_map(fn(string $g) => self::groupKey($formId, $g), self::COMMON_GROUPS);
    }
}
