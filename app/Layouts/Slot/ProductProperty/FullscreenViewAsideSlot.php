<?php

namespace App\Layouts\Slot\ProductProperty;

use App\Enums\FollowUpStatus;
use App\Enums\FollowUpType;
use App\Enums\ProductCategoryType;
use App\Enums\ProductPropertyFor;
use App\Enums\ProductPropertyStatus;
use App\Enums\ProductPropertyType;
use App\Forms\ProductProperty\ProductPropertyForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\CardComponent;
use Litepie\Layout\Components\CommentComponent;
use Litepie\Layout\Components\DividerComponent;
use Litepie\Layout\Components\StatsComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Components\TimelineComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * Fullscreen View Aside Slot — Product Property
 *
 * Two-column immersive layout matching the CRM lead detail reference:
 *   Left  (7/12): Identity header, description highlight, property details, location.
 *   Right (5/12): Price overview card, follow-ups, notes, recent activities.
 */
class FullscreenViewAsideSlot
{
    /**
     * Build fullscreen product property view aside.
     *
     * @param  array  $masterData  Master data for form dropdowns
     */
    public static function make(array $masterData = []): array
    {
        return DetailSection::make('fullscreen-view-property')
            ->setHeader(self::buildHeaderSlot())
            ->setLeft(self::buildLeftSlot($masterData))
            ->setRight(self::buildRightSlot())
            ->setFooter(self::buildFooterSlot())
            ->toArray();
    }

    // =========================================================================
    // LEFT SLOT — Identity + Description + Property Details + Location
    // =========================================================================

    private static function buildLeftSlot(array $masterData): SlotManager
    {
        $leftSlot = SlotManager::make('fullscreen-property-left-slot')
            ->setConfig(['colSpan' => 8]);

        $leftSlot->setComponent(
            ProductPropertyForm::make(
                'view-property-fullscreen-form',
                'GET',
                '/api/product-property/:id',
                $masterData,
                '/api/product-property/:id'
            )
        );

        return $leftSlot;

        // $slot->setComponent(self::buildIdentityCard());
        // $slot->setComponent(self::buildDescriptionCard());
        // $slot->setComponent(self::buildPropertyDetailsCard());
        // $slot->setComponent(self::buildLocationCard());

        // return $slot;
    }

    /**
     * Identity header card: ref badge + category_type badge + property_for badge
     * + views count + prominent price — mirrors the CRM "MC-L-41 / Buyer" header row.
     */
    private static function buildIdentityCard(): CardComponent
    {
        $card = CardComponent::make('identity-card')
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id']);

        $card->addComponent(
            BadgeComponent::make('ref-badge')
                ->content(':{ref}')
                ->variant('standard')
                ->bordered(true)
                ->meta([
                    'key' => 'ref_number',
                    'color' => 'default',
                    'size' => 'sm',
                    'prefix' => '#',
                    'tooltip' => __('product_property.column_ref'),
                ])
                ->gridColumnSpan(4)
        );

        $card->addComponent(
            BadgeComponent::make('category-type-badge')
                ->content(':category_type')
                ->badgeConfig(ProductCategoryType::badgeConfig())
                ->variant('standard')
                ->bordered(false)
                ->meta(['key' => 'category_type', 'size' => 'sm'])
                ->gridColumnSpan(4)
        );

        $card->addComponent(
            BadgeComponent::make('property-for-badge')
                ->content(':property_for')
                ->badgeConfig(ProductPropertyFor::badgeConfig())
                ->variant('standard')
                ->bordered(false)
                ->meta(['key' => 'property_for', 'size' => 'sm'])
                ->gridColumnSpan(4)
        );

        $card->addComponent(
            TextComponent::make('views-count')
                ->content(':views_count')
                ->variant('caption')
                ->meta([
                    'key' => 'views_count',
                    'icon' => 'eyeopen',
                    'iconSize' => 'sm',
                    'color' => 'text-gray-500',
                    'suffix' => ' ' . __('layout.views'),
                ])
                ->gridColumnSpan(6)
        );

        $card->addComponent(
            TextComponent::make('price-stat')
                ->content(':{price}')
                ->variant('h5')
                ->weight('bold')
                ->meta([
                    'key' => 'price',
                    'color' => 'text-primary-600',
                    'prefix' => 'AED ',
                    'format' => 'number',
                ])
                ->gridColumnSpan(6)
        );

        return $card;
    }

    /**
     * Description highlight card — info-coloured banner, mirrors the CRM
     * yellow "The client is interested" note.
     */
    private static function buildDescriptionCard(): CardComponent
    {
        $card = CardComponent::make('description-highlight-card')
            ->variant('outlined')
            ->color('info')
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id'])
            ->meta(['highlight' => true, 'bgColor' => '#FFFBEB']);

        $card->addComponent(
            TextComponent::make('description-label')
                ->content(__('product_property.description'))
                ->variant('caption')
                ->weight('bold')
                ->meta(['color' => 'text-gray-700'])
                ->gridColumnSpan(12)
        );

        $card->addComponent(
            TextComponent::make('description-value')
                ->content(':description')
                ->variant('body2')
                ->meta(['key' => 'description', 'color' => 'text-gray-800'])
                ->gridColumnSpan(12)
        );

        return $card;
    }

    /**
     * Property details card — read-only field grid with Edit button in header,
     * mirrors the CRM "Qualify" section with fields and Edit button.
     */
    private static function buildPropertyDetailsCard(): CardComponent
    {
        $card = CardComponent::make('property-details-card')
            ->title(__('layout.property_details'))
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id'])
            ->addHeaderAction(__('layout.edit'), '/api/product-property/:id', [
                'icon' => 'pen',
                'variant' => 'outlined',
                'size' => 'sm',
                'data' => [
                    'component' => 'edit-property-full',
                    'type' => 'aside',
                    'action' => 'edit',
                    'config' => [
                        'width' => '800px',
                        'height' => '100vh',
                        'anchor' => 'right',
                        'backdrop' => true,
                    ],
                    'params' => ['id' => ':id'],
                    'url' => '/api/product-property/:id',
                ],
                'meta' => ['tooltip' => __('layout.tooltip_edit_property')],
            ]);

        // Text fields rendered as label/value pairs in a 4-col grid
        $textFields = [
            ['beds',          __('product_property.beds'),          []],
            ['baths',         __('product_property.baths'),         []],
            ['bua',           __('product_property.column_bua'),    ['suffix' => ' sqft']],
            ['floor_number',  __('product_property.floor'),         []],
            ['unit_number',   __('product_property.unit'),          []],
            ['price',         __('product_property.price'),         ['color' => 'text-primary-600', 'prefix' => 'AED ', 'format' => 'number']],
            ['property_type', __('product_property.property_type'), []],
            ['furnishing',    __('product_property.furnishing'),    []],
        ];

        foreach ($textFields as [$key, $label, $valueMeta]) {
            $card->addComponent(
                TextComponent::make("{$key}-label")
                    ->content($label)
                    ->variant('caption')
                    ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-500'])
                    ->gridColumnSpan(3)
            );
            $card->addComponent(
                TextComponent::make("{$key}-value")
                    ->content(":{$key}")
                    ->variant('body2')
                    ->meta(array_merge(['key' => $key, 'color' => 'text-gray-900'], $valueMeta))
                    ->gridColumnSpan(3)
            );
        }

        // Status — rendered as a badge
        $card->addComponent(
            TextComponent::make('status-label')
                ->content(__('product_property.status'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-500'])
                ->gridColumnSpan(3)
        );
        $card->addComponent(
            BadgeComponent::make('status-value')
                ->content(':{status}')
                ->badgeConfig(ProductPropertyStatus::badgeConfig())
                ->variant('standard')
                ->bordered(true)
                ->meta(['key' => 'status', 'size' => 'sm'])
                ->gridColumnSpan(3)
        );

        return $card;
    }

    /**
     * Location & Building card — collapsible with "+" icon in header,
     * mirrors the CRM "Preferences" collapsible section.
     */
    private static function buildLocationCard(): CardComponent
    {
        $card = CardComponent::make('location-building-card')
            ->title(__('layout.location_and_building'))
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id'])
            ->addHeaderAction(__('layout.expand'), '#', [
                'icon' => 'plus',
                'variant' => 'outlined',
                'size' => 'sm',
                'isIconButton' => true,
                'meta' => ['action' => 'toggle', 'tooltip' => __('layout.expand')],
            ])
            ->meta(['collapsible' => true, 'collapsed' => false]);

        $locationFields = [
            ['building_name', __('layout.building_name')],
            ['community',     __('layout.community')],
            ['city',          __('layout.city')],
            ['emirate',       __('layout.emirate')],
        ];

        foreach ($locationFields as [$key, $label]) {
            $card->addComponent(
                TextComponent::make("{$key}-label")
                    ->content($label)
                    ->variant('caption')
                    ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-500'])
                    ->gridColumnSpan(4)
            );
            $card->addComponent(
                TextComponent::make("{$key}-value")
                    ->content(":{{$key}}")
                    ->variant('body2')
                    ->meta(['key' => $key, 'color' => 'text-gray-900'])
                    ->gridColumnSpan(8)
            );
        }

        return $card;
    }

    // =========================================================================
    // RIGHT SLOT — Overview + Follow-ups + Notes + Recent Activities
    // =========================================================================

    private static function buildRightSlot(): SlotManager
    {
        $slot = SlotManager::make('fullscreen-property-right-slot')
            ->setConfig(['colSpan' => 4]);

        $slot->setComponent(self::buildOverviewCard());
        $slot->setComponent(self::buildFollowupsCard());
        $slot->setComponent(self::buildLeadCountCard());
        $slot->setComponent(self::buildNotesCard());
        $slot->setComponent(self::buildActivitiesCard());

        return $slot;
    }

    /**
     * Overview card — mirrors the CRM right panel:
     * prominent price, status badge, meta rows (category, for, portal), dates.
     * Like "AED 510.8M – 624.3M" + Status/Source/Expiry rows.
     */
    private static function buildOverviewCard(): CardComponent
    {
        $card = CardComponent::make('property-overview-card')
            ->variant('outlined');
        // ->dataUrl('/api/product-property/:id')
        // ->dataParams(['id' => ':eid']);

        // Prominent price
        $card->addComponent(
            TextComponent::make('overview-price')
                ->content(':{price}')
                ->variant('h4')
                ->weight('bold')
                ->meta([
                    'key' => 'price',
                    'color' => 'text-primary-700',
                    'prefix' => 'AED ',
                    'format' => 'number',
                ])
                ->gridColumnSpan(8)
        );

        // Status badge top-right
        $card->addComponent(
            BadgeComponent::make('overview-status-badge')
                ->content(':{status}')
                ->badgeConfig(ProductPropertyStatus::badgeConfig())
                ->variant('standard')
                ->bordered(true)
                ->meta(['key' => 'status', 'size' => 'sm'])
                ->gridColumnSpan(4)
        );

        $card->addComponent(
            DividerComponent::make('overview-divider-1')->gridColumnSpan(12)
        );

        // Meta rows: label (5 cols) / value (7 cols)
        $metaRows = [
            ['status-row',   __('layout.status'), null, 'status'],
            ['category-row', __('product_property.category_type'), null, 'category_type'],
            ['for-row',      __('product_property.property_for'), null, 'property_for'],
            ['type-row',     __('product_property.property_type'), null, 'property_type'],
        ];

        foreach ($metaRows as [$id, $label, $valueType, $key]) {
            $card->addComponent(
                TextComponent::make("{$id}-label")
                    ->content($label)
                    ->variant('caption')
                    ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-500'])
                    ->gridColumnSpan(5)
            );

            if ($valueType === 'text') {
                $card->addComponent(
                    TextComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->variant('caption')
                        ->meta(['key' => $key, 'color' => 'text-gray-800'])
                        ->gridColumnSpan(7)
                );
            } elseif ($key === 'property_type') {
                $card->addComponent(
                    BadgeComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->badgeConfig(ProductPropertyType::badgeConfig())
                        ->variant('standard')
                        ->bordered(false)
                        ->meta(['key' => $key, 'size' => 'xs'])
                        ->gridColumnSpan(7)
                );
            } elseif ($key === 'status') {
                $card->addComponent(
                    BadgeComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->badgeConfig(ProductPropertyStatus::badgeConfig())
                        ->variant('standard')
                        ->bordered(true)
                        ->meta(['key' => $key, 'size' => 'xs'])
                        ->gridColumnSpan(7)
                );
            } elseif ($key === 'category_type') {
                $card->addComponent(
                    BadgeComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->badgeConfig(ProductCategoryType::badgeConfig())
                        ->variant('standard')
                        ->bordered(false)
                        ->meta(['key' => $key, 'size' => 'xs'])
                        ->gridColumnSpan(7)
                );
            } elseif ($key === 'property_for') {
                $card->addComponent(
                    BadgeComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->badgeConfig(ProductPropertyFor::badgeConfig())
                        ->variant('standard')
                        ->bordered(false)
                        ->meta(['key' => $key, 'size' => 'xs'])
                        ->gridColumnSpan(7)
                );
            } else {
                $card->addComponent(
                    TextComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->variant('caption')
                        ->meta(['key' => $key, 'color' => 'text-gray-800'])
                        ->gridColumnSpan(7)
                );
            }
        }

        $card->addComponent(
            DividerComponent::make('overview-divider-2')->gridColumnSpan(12)
        );

        // Dates
        foreach (['updated_at_formatted' => __('layout.updated_at'), 'created_at_formatted' => __('layout.created_at')] as $key => $label) {
            $card->addComponent(
                TextComponent::make("{$key}-label")
                    ->content($label)
                    ->variant('caption')
                    ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-500'])
                    ->gridColumnSpan(5)
            );
            $card->addComponent(
                TextComponent::make("{$key}-value")
                    ->content(":{{$key}}")
                    ->variant('caption')
                    ->meta(['key' => $key, 'color' => 'text-gray-700'])
                    ->gridColumnSpan(7)
            );
        }

        return $card;
    }

    /**
     * Follow-ups card — mirrors CRM "Create Follow-ups" section with "+" button
     * and a list of tasks (empty state when none exist).
     */
    private static function buildFollowupsCard(): CardComponent
    {
        // Build the per-item template card as an array so it can be embedded in meta().
        // Design: calendar-date avatar on left; type + status badges, title with status dot,
        // and assigned-user row on the right — matching the CRM follow-up card design.
        // $itemTemplate = CardComponent::make('followup-item')
        //     ->title(":{followup_title}")
        //     ->avatar(':{followup_date_day}')
        //     ->meta([
        //         'layout' => 'horizontal',
        //         'avatarType' => 'calendarDate',
        //         'avatarMonthField' => 'followup_date_month',
        //         'avatarDayField' => 'followup_date_day',
        //         'avatarColor' => 'primary',
        //         'componentGap' => 'xs',
        //     ])
        //     ->addComponent(
        //         BadgeComponent::make('followup-type-badge')
        //             ->content(':{followup_type}')
        //             ->badgeConfig(FollowUpType::badgeConfig())
        //             ->variant('standard')
        //             ->bordered(true)
        //             ->meta(['key' => 'followup_type', 'size' => 'sm'])
        //             ->gridColumnSpan(3)
        //     )
        //     ->addComponent(
        //         BadgeComponent::make('followup-status-badge')
        //             ->content(':{status}')
        //             ->badgeConfig(FollowUpStatus::badgeConfig())
        //             ->variant('standard')
        //             ->bordered(true)
        //             ->meta(['key' => 'status', 'size' => 'sm'])
        //             ->gridColumnSpan(3)
        //     )
        //     ->addComponent(
        //         TextComponent::make('followup-description')
        //             ->content(':{description}')
        //             ->variant('caption')
        //             ->meta([
        //                 'key' => 'description',
        //                 'icon' => 'user',
        //                 'iconPosition' => 'left',
        //                 'iconSize' => 'xs',
        //                 'color' => 'text-gray-500',
        //             ])
        //     )
        //     ->addComponent(
        //         TextComponent::make('followup-created-by')
        //             ->content(':{created_by_name}')
        //             ->variant('caption')
        //             ->meta([
        //                 'key' => 'created_by_name',
        //                 'icon' => 'user',
        //                 'iconPosition' => 'left',
        //                 'iconSize' => 'xs',
        //                 'color' => 'text-gray-500',
        //             ])
        //     )
        //     ->addHeaderAction('', '#', [
        //         'icon' => 'pen',
        //         'iconOnly' => true,
        //         'isIconButton' => true,
        //         'variant' => 'text',
        //         'size' => 'sm',
        //         'data' => [
        //             'component' => 'edit-property-followup',
        //             'type' => 'modal',
        //             'action' => 'edit',
        //             'hasParent' => true,
        //             'config' => [
        //                 'width' => '500px',
        //                 'height' => '100vh',
        //                 'anchor' => 'right',
        //                 'backdrop' => true,
        //             ],
        //             'params' => ['id' => ':property_id', 'followup_id' => ':eid'],
        //             'url' => '/api/product-property/:id/followups/:followup_id',
        //         ],
        //         'meta' => ['tooltip' => __('layout.edit_followup')],
        //     ])
        //     ->addHeaderAction('', '#', [
        //         'icon' => 'binempty',
        //         'iconOnly' => true,
        //         'variant' => 'text',
        //         'isIconButton' => true,
        //         'size' => 'sm',
        //         'color' => 'danger',
        //         'data' => [
        //             'component' => 'delete-property-followup',
        //             'type' => 'confirm',
        //             'action' => 'delete',
        //             'hasParent' => true,
        //             'method' => 'DELETE',
        //             'url' => '/api/product-property/:id/followups/:followup_id',
        //             'config' => [
        //                 'width' => '400px',
        //                 'height' => 'auto',
        //                 'anchor' => 'center',
        //                 'backdrop' => true,
        //             ],
        //             'params' => ['id' => ':property_id', 'followup_id' => ':eid'],
        //         ],
        //         'meta' => ['tooltip' => __('layout.delete_followup')],
        //     ])
        //     ->toArray();

        $card = CardComponent::make('followups-card')
            ->title(__('layout.create_followups'))
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id/followups')
            ->dataParams(['id' => ':eid'])
            ->addHeaderButton(
                ButtonComponent::make('add-followup-btn')
                    ->icon('plus')
                    ->variant('text')
                    ->size('sm')
                    ->isIconButton(true)
                    ->data('component', 'create-property-followup')
                    ->data('type', 'modal')
                    ->data('action', 'create')
                    ->data('hasParent', true)
                    ->data('config', [
                        'width' => '500px',
                        'height' => '100vh',
                        'anchor' => 'right',
                        'backdrop' => true,
                    ])
                    ->dataUrl('/api/product-property/:id/followups')
                    ->dataParams(['id' => ':eid'])
                    ->meta(['tooltip' => __('layout.add_followup')])
            );

        $card->meta([
            'emptyIcon' => 'listcheck',
            'emptyText' => __('layout.tasks_empty'),
            'emptySubtext' => __('layout.tasks_empty_hint'),
            // 'template' => $itemTemplate,
            'componentType' => 'followUpCard',
            'limit' => 3,
        ]);

        return $card;
    }

    /**
     * Lead count card — displays the number of leads associated with the property.
     */
    private static function buildLeadCountCard(): CardComponent
    {
        $statTemplate = StatsComponent::make('leads-count-stat')
            ->addMetric('leads_count', __('layout.leads'), [
                'icon' => 'users',
                'color' => 'primary',
                'format' => 'number',
            ])
            ->layout('inline')
            ->size('lg')
            ->showTrend(false)
            ->showChange(false)
            ->toArray();

        return CardComponent::make('lead-count-card')
            ->title(__('layout.leads'))
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id/leads/count')
            ->dataParams(['id' => ':eid'])
            ->meta([
                'template' => $statTemplate,
                'emptyIcon' => 'users',
                'emptyText' => __('layout.no_leads_yet'),
                'emptySubtext' => __('layout.no_leads_yet_hint'),
            ]);
    }

    /**
     * Notes card — mirrors CRM "Notes" section with "+" button
     * and a threaded comment component for note history.
     */
    private static function buildNotesCard(): CardComponent
    {
        $card = CardComponent::make('notes-card')
            ->title(__('layout.notes'))
            ->variant('outlined');

        $card->addComponent(
            CommentComponent::make('notes-comments')
                ->editing(true)
                ->deleting(true)
                ->markdown(false)
                ->sortOrder('newest')
                ->fieldName('note')
                ->dataUrl('/api/product-property/:id/notes')
                ->dataParams(['id' => ':eid'])
                ->meta([
                    'emptyIcon' => 'chat',
                    'emptyText' => __('layout.notes_empty'),
                    'emptySubtext' => __('layout.notes_empty_hint'),
                    'limit' => 4,
                ])
                ->gridColumnSpan(12)
        );

        return $card;
    }

    /**
     * Recent Activities card — mirrors CRM "Recent Activities + Log" section,
     * vertical timeline of audit events loaded from the API.
     */
    private static function buildActivitiesCard(): CardComponent
    {
        $card = CardComponent::make('activities-card')
            ->title(__('layout.recent_activities'))
            ->variant('outlined')
            ->addHeaderButton(
                ButtonComponent::make('log-activity-btn')
                    ->label(__('layout.log'))
                    ->icon('clock')
                    ->variant('outlined')
                    ->size('sm')
                    ->color('info')
                    ->data('component', 'view-property-activity')
                    ->data('type', 'aside')
                    ->data('action', 'view')
                    ->data('params', ['property_id' => ':eid'])
                    ->meta(['tooltip' => __('layout.log_activity')])
            );

        $card->addComponent(
            TimelineComponent::make('activities-timeline')
                ->vertical()
                ->showDates(true)
                ->showIcons(true)
                ->addEvent([])
                ->dateFormat('d M Y, H:i')
                ->dataUrl('/api/product-property/:id/activities')
                ->dataParams(['id' => ':eid'])
                ->meta([
                    'emptyIcon' => 'clock',
                    'emptyText' => __('layout.activities_empty'),
                    'emptySubtext' => __('layout.activities_empty_hint'),
                    'componentType' => 'activityTimeline',
                    'limit' => 5,
                ])
                ->gridColumnSpan(12)
        );

        return $card;
    }

    // =========================================================================
    // HEADER SLOT
    // =========================================================================

    private static function buildHeaderSlot(): SlotManager
    {
        $centerSlot = SlotManager::make('fullscreen-property-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'start',
                'items' => 'start',
                'gridColumnSpan' => 4,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.property_details_fullscreen'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.immersive_property_viewing'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );
        $centerSlot->setComponent(
            BadgeComponent::make('header-center-status-badge')
                ->content(':{status}')
                ->badgeConfig(ProductPropertyStatus::badgeConfig())
                ->bordered(false)
                ->variant('standard')
                ->meta(['key' => 'status', 'size' => 'md'])
        );

        $rightSlot = SlotManager::make('fullscreen-property-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 8,
            ]);

        // Publish — confirm dialog (no extra fields needed)
        $rightSlot->setComponent(
            ButtonComponent::make('publish-btn')
                ->label(__('layout.publish'))
                ->icon('badgecheck')
                ->variant('outlined')
                ->size('sm')
                ->color('success')
                ->confirm([
                    'title' => __('layout.publish_property'),
                    'message' => __('layout.publish_property_confirmation'),
                    'confirmLabel' => __('layout.publish'),
                    'cancelLabel' => __('layout.cancel'),
                    'action' => 'publish',
                    'dataUrl' => '/api/product-property/:id/publish',
                    'method' => 'POST',
                ])
                ->meta(['tooltip' => __('layout.publish_property')])
        );

        // Unpublish — form modal with reason + description
        $rightSlot->setComponent(
            ButtonComponent::make('unpublish-btn')
                ->label(__('layout.unpublish'))
                ->icon('eyeclose')
                ->variant('outlined')
                ->size('sm')
                ->color('warning')
                ->data('component', 'unpublish-property-modal')
                ->data('type', 'modal')
                ->data('action', 'create')
                ->data('config', [
                    'width' => '500px',
                    'height' => 'auto',
                    'anchor' => 'center',
                    'backdrop' => true,
                ])
                ->dataParams(['id' => ':eid'])
                ->meta(['tooltip' => __('layout.unpublish_property')])
        );

        // Preview — form modal with preview type + price
        $rightSlot->setComponent(
            ButtonComponent::make('preview-btn')
                ->label(__('layout.preview'))
                ->icon('eyeopen')
                ->variant('outlined')
                ->size('sm')
                ->data('component', 'preview-property-modal')
                ->data('type', 'modal')
                ->data('action', 'create')
                ->data('config', [
                    'width' => '600px',
                    'height' => 'auto',
                    'anchor' => 'center',
                    'backdrop' => true,
                ])
                ->dataParams(['id' => ':eid'])
                ->meta(['tooltip' => __('layout.preview_property')])
        );

        // Actions — dropdown button
        $rightSlot->setComponent(
            ButtonComponent::make('actions-btn')
                ->label(__('layout.actions'))
                ->icon('chevrondown')
                ->iconPosition('right')
                ->variant('outlined')
                ->size('sm')
                ->dropdown([
                    'id' => 'property-actions-dropdown',
                    'placement' => 'bottom-end',
                    'offset' => [0, 8],
                    'closeOnClick' => true,
                    'items' => self::buildActionsDropdownItems(),
                ])
                ->meta(['tooltip' => __('layout.actions')])
        );

        // Download Photos — dropdown button
        $rightSlot->setComponent(
            ButtonComponent::make('download-photos-btn')
                ->label(__('layout.download_photos'))
                ->icon('downloadcloud')
                ->iconPosition('left')
                ->variant('outlined')
                ->size('sm')
                ->dropdown([
                    'id' => 'property-download-dropdown',
                    'placement' => 'bottom-end',
                    'offset' => [0, 8],
                    'closeOnClick' => true,
                    'items' => self::buildDownloadDropdownItems(),
                ])
                ->meta(['tooltip' => __('layout.download_photos')])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('share-btn')
                ->icon('share')
                ->variant('outlined')
                ->isIconButton(true)
                ->meta(['action' => 'share', 'tooltip' => __('layout.share')])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('print-btn')
                ->icon('printer')
                ->variant('outlined')
                ->isIconButton(true)
                ->meta(['action' => 'print', 'tooltip' => __('layout.print')])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->data('component', 'edit-property-full')
                ->data('type', 'aside')
                ->data('action', 'edit')
                ->data('config', [
                    'width' => '800px',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                ])
                ->data('params', ['id' => ':id'])
                ->data('url', '/api/product-property/:id')
                ->meta([
                    'action' => 'edit',
                    'type' => 'aside',
                    'component' => 'edit-property-full',
                    'tooltip' => __('layout.tooltip_edit_property'),
                ])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->color('error')
                ->confirm([
                    'title' => __('layout.delete_property'),
                    'message' => __('layout.delete_property_confirmation'),
                    'confirmLabel' => __('layout.delete'),
                    'cancelLabel' => __('layout.cancel'),
                    'action' => 'delete',
                    'dataUrl' => '/api/product-property/:id',
                    'method' => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => __('layout.tooltip_delete_property'),
                    'color' => 'error',
                ])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->size('md')
                ->isIconButton(true)
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('fullscreen-property-header-slot');
        $headerSlot->setSection(
            HeaderSection::make('fullscreen-property-aside-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $headerSlot;
    }

    private static function buildActionsDropdownItems(): array
    {
        return [
            [
                'id' => 'archive-property',
                'label' => __('layout.archive'),
                'icon' => 'archive',
                'action' => 'archive',
                'type' => 'button',
            ],
            [
                'id' => 'mark-verified',
                'label' => __('layout.mark_as_verified'),
                'icon' => 'badgecheck',
                'action' => 'mark-verified',
                'type' => 'button',
            ],
            [
                'id' => 'mark-featured',
                'label' => __('layout.mark_as_featured'),
                'icon' => 'star',
                'action' => 'mark-featured',
                'type' => 'button',
            ],
            ['type' => 'divider'],
            [
                'id' => 'duplicate-property',
                'label' => __('layout.duplicate'),
                'icon' => 'duplicate',
                'action' => 'duplicate',
                'type' => 'button',
            ],
            [
                'id' => 'export-property',
                'label' => __('layout.export_data'),
                'icon' => 'downloadcloud',
                'action' => 'export',
                'type' => 'button',
            ],
        ];
    }

    private static function buildDownloadDropdownItems(): array
    {
        return [
            [
                'id' => 'download-all-photos',
                'label' => __('layout.download_all_photos'),
                'icon' => 'camera',
                'action' => 'download-all-photos',
                'type' => 'button',
            ],
            [
                'id' => 'download-floor-plans',
                'label' => __('layout.download_floor_plans'),
                'icon' => 'layout',
                'action' => 'download-floor-plans',
                'type' => 'button',
            ],
            [
                'id' => 'download-documents',
                'label' => __('layout.download_documents'),
                'icon' => 'documentfull',
                'action' => 'download-documents',
                'type' => 'button',
            ],
            ['type' => 'divider'],
            [
                'id' => 'download-all-files',
                'label' => __('layout.download_all_files'),
                'icon' => 'archive',
                'action' => 'download-all-files',
                'type' => 'button',
            ],
        ];
    }

    // =========================================================================
    // FOOTER SLOT
    // =========================================================================

    private static function buildFooterSlot(): SlotManager
    {
        $leftSlot = SlotManager::make('fullscreen-property-footer-left')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '4',
                'justify' => 'start',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $leftSlot->setComponent(
            TextComponent::make('footer-ref')
                ->content(':{ref}')
                ->variant('caption')
                ->meta([
                    'key' => 'ref_number',
                    'color' => 'text-gray-500',
                    'prefix' => __('product_property.column_ref') . ': #',
                ])
        );

        $rightSlot = SlotManager::make('fullscreen-property-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $rightSlot->setComponent(
            ButtonComponent::make('close-footer-btn')
                ->label(__('layout.close'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerSlot = SlotManager::make('fullscreen-property-footer-slot');
        $footerSlot->setSection(
            FooterSection::make('fullscreen-property-aside-footer')
                ->setLeft($leftSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
