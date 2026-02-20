<?php

namespace App\Layouts\Slot\ProductProperty;

use App\Enums\ProductCategoryType;
use App\Enums\ProductPropertyFor;
use App\Forms\ProductProperty\ProductPropertyForm;
use App\Enums\ProductPropertyStatus;
use App\Forms\Blog\BlogFeedbackForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\CardComponent;
use Litepie\Layout\Components\CommentComponent;
use Litepie\Layout\Components\DividerComponent;
use Litepie\Layout\Components\ListComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Components\TimelineComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * Fullscreen View Aside Slot — Product Property
 *
 * Two-column immersive layout matching the CRM lead detail reference:
 *   Left  (7/12): Identity header, description highlight, property details, location.
 *   Right (5/12): Price overview card, follow-ups, notes, recent activities.
 *
 * @package App\Layouts\Slot\ProductProperty
 */
class FullscreenViewAsideSlot
{
    /**
     * Build fullscreen product property view aside.
     *
     * @param array $masterData Master data for form dropdowns
     * @return array
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
            ->setConfig(['colSpan' => 7]);

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
                    'tooltip' => __('product_property.column_ref')
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
                    'icon' => 'eye',
                    'iconSize' => 'sm',
                    'color' => 'text-gray-500',
                    'suffix' => ' ' . __('layout.views')
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
                    'format' => 'number'
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
                'icon'    => 'pencil',
                'variant' => 'outlined',
                'size'    => 'sm',
                'data'    => [
                    'component' => 'edit-property-full',
                    'type'      => 'aside',
                    'action'    => 'edit',
                    'config'    => [
                        'width' => '800px',
                        'height' => '100vh',
                        'anchor' => 'right',
                        'backdrop' => true
                    ],
                    'params'    => ['id' => ':id'],
                    'url'       => '/api/product-property/:id',
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
                'icon'         => 'plus',
                'variant'      => 'outlined',
                'size'         => 'sm',
                'isIconButton' => true,
                'meta'         => ['action' => 'toggle', 'tooltip' => __('layout.expand')],
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
            ->setConfig(['colSpan' => 5]);

        $slot->setComponent(self::buildOverviewCard());
        $slot->setComponent(self::buildFollowupsCard());
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
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id']);

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
                    'format' => 'number'
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
            ['portal-row',   __('layout.portal'), 'text', 'portal'],
        ];

        foreach ($metaRows as [$id, $label, $valueType, $key]) {
            $card->addComponent(
                TextComponent::make("{$id}-label")
                    ->content($label)
                    ->variant('caption')
                    ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-500'])
                    ->gridColumnSpan(5)
            );

            if ($valueType === 'text' || in_array($key, ['portal'])) {
                $card->addComponent(
                    TextComponent::make("{$id}-value")
                        ->content(":{{$key}}")
                        ->variant('caption')
                        ->meta(['key' => $key, 'color' => 'text-gray-800'])
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
        $card = CardComponent::make('followups-card')
            ->title(__('layout.create_followups'))
            ->variant('outlined')
            ->addHeaderButton(
                ButtonComponent::make('add-followup-btn')
                    ->icon('plus')
                    ->variant('outlined')
                    ->size('sm')
                    ->isIconButton(true)
                    ->data('component', 'create-property-followup')
                    ->data('type', 'modal')
                    ->data('action', 'create')
                    ->data('config', [
                        'width'    => '500px',
                        'height'   => '100vh',
                        'anchor'   => 'right',
                        'backdrop' => true,
                    ])
                    ->data('params', ['property_id' => ':id'])
                    ->meta(['tooltip' => __('layout.add_followup')])
            );

        $card->addComponent(
            ListComponent::make('followups-list')
                ->dataUrl('/api/product-property/:id/followups')
                ->dataParams(['id' => ':id'])
                ->dense(true)
                ->meta([
                    'emptyIcon'    => 'listcheck',
                    'emptyText'    => __('layout.tasks_empty'),
                    'emptySubtext' => __('layout.tasks_empty_hint'),
                ])
                ->gridColumnSpan(12)
        );

        return $card;
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
                ->dataUrl('/api/product-property/:id/notes')
                ->dataParams(['id' => ':id'])
                ->meta([
                    'emptyIcon'    => 'chat',
                    'emptyText'    => __('layout.notes_empty'),
                    'emptySubtext' => __('layout.notes_empty_hint'),
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
            ->addHeaderAction(__('layout.log'), '#', [
                'icon'    => 'clock',
                'variant' => 'outlined',
                'size'    => 'sm',
                'color'   => 'info',
                'data'    => [
                    'component' => 'create-property-activity',
                    'type'      => 'aside',
                    'action'    => 'create',
                    'params'    => ['property_id' => ':id'],
                ],
                'meta' => ['tooltip' => __('layout.log_activity')],
            ]);

        $card->addComponent(
            TimelineComponent::make('activities-timeline')
                ->vertical()
                ->showDates(true)
                ->showIcons(true)
                ->addEvent([])
                ->dateFormat('d M Y, H:i')
                ->dataUrl('/api/product-property/:id/activities')
                ->dataParams(['id' => ':id'])
                ->meta([
                    'emptyIcon'    => 'clock',
                    'emptyText'    => __('layout.activities_empty'),
                    'emptySubtext' => __('layout.activities_empty_hint'),
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
                'gridColumnSpan' => 6,
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

        $rightSlot = SlotManager::make('fullscreen-property-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $rightSlot->setComponent(
            BadgeComponent::make('header-status-badge')
                ->content(':{status}')
                ->badgeConfig(ProductPropertyStatus::badgeConfig())
                ->bordered(true)
                ->variant('standard')
                ->meta(['key' => 'status', 'size' => 'sm'])
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
            ButtonComponent::make('feedback-btn')
                ->icon('message')
                ->variant('outlined')
                ->form(BlogFeedbackForm::make('blog-feedback-form', 'POST', '/api/blogs/:id/feedback')->toArray())
                ->meta(['tooltip' => __('layout.submit_feedback')])
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
                    'backdrop' => true
                ])
                ->data('params', ['id' => ':id'])
                ->data('url', '/api/product-property/:id')
                ->meta([
                    'action' => 'edit',
                    'type' => 'aside',
                    'component' => 'edit-property-full',
                    'tooltip' => __('layout.tooltip_edit_property')
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
                    'title'        => __('layout.delete_property'),
                    'message'      => __('layout.delete_property_confirmation'),
                    'confirmLabel' => __('layout.delete'),
                    'cancelLabel'  => __('layout.cancel'),
                    'action'       => 'delete',
                    'dataUrl'      => '/api/product-property/:id',
                    'method'       => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => __('layout.tooltip_delete_property'),
                    'color' => 'error'
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
                    'prefix' => __('product_property.column_ref') . ': #'
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
