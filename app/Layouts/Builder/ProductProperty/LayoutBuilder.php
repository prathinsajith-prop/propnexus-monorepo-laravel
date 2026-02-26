<?php

namespace App\Layouts\Builder\ProductProperty;

use App\Layouts\Builder\TableColumnsBuilder;
use App\Layouts\Slot\ProductProperty\CreateAsideSlot;
use App\Layouts\Slot\ProductProperty\EditAsideSlot;
use App\Layouts\Slot\ProductProperty\FullscreenViewAsideSlot;
use App\Layouts\Slot\ProductProperty\ModalSlot;
use App\Layouts\Slot\ProductProperty\ViewAsideSlot;

/**
 * ProductProperty LayoutBuilder
 *
 * Orchestrates construction of all product property layout sections.
 */
class LayoutBuilder
{
    /**
     * Build header section with page title, breadcrumbs, and statistics
     */
    public static function buildHeaderSection($section): void
    {
        $section->meta([
            'description' => 'Product Property Management Header',
            'styling' => 'container mx-auto px-4 py-6',
            'background' => 'transparent',
        ]);

        $headerGrid = $section->grid('header-main-grid')
            ->columns(2)
            ->gap('xl')
            ->responsive(true);

        // Left: Page header with breadcrumbs
        $headerInfoGrid = $headerGrid->grid('header-info-column')
            ->columns(1)
            ->gap('md')
            ->gridColumnSpan(5);

        $headerInfoGrid->pageHeader('page-header')
            ->title(__('product_property.title'))
            ->breadcrumbs([
                ['label' => __('product_property.dashboard'), 'link' => '/', 'icon' => 'home'],
                ['label' => __('product_property.title'), 'active' => true, 'icon' => 'building'],
            ])
            ->align('left')
            ->spacing('md')
            ->titleVariant('h1')
            ->titleSize('2xl')
            ->titleWeight('bold')
            ->titleGutterBottom(true);

        // Right: Statistics cards
        $statsGrid = $headerGrid->grid('stats-grid')
            ->columns(4)
            ->gap('lg')
            ->responsive(true)
            ->gridColumnSpan(7);

        self::buildStatsCard($statsGrid, 'stat-total-properties', __('product_property.total_properties'), '0', 'primary', 'home', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-published', __('product_property.published'), '0', 'success', 'checkmark', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-for-sale', __('product_property.for_sale'), '0', 'info', 'cash', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-for-rent', __('product_property.for_rent'), '0', 'warning', 'key', '+0%', 'neutral', 'trend-1');
    }

    /**
     * Build stats card
     */
    private static function buildStatsCard($grid, $id, $label, $value, $color, $icon, $trend, $trendDir, $trendIcon): void
    {
        $colorMap = [
            'primary' => ['color' => '#3b82f6', 'bg' => '#f0f9ff'],
            'success' => ['color' => '#10b981', 'bg' => '#f7fef9'],
            'info' => ['color' => '#8b5cf6', 'bg' => '#faf8ff'],
            'warning' => ['color' => '#f59e0b', 'bg' => '#fffef7'],
            'error' => ['color' => '#ef4444', 'bg' => '#fef9f9'],
        ];

        $colors = $colorMap[$color] ?? $colorMap['primary'];

        $grid->card($id)
            ->title($label)
            ->content($value)
            ->variant('outlined')
            ->color($color)
            ->gridColumnSpan(3)
            ->dataUrl("/api/product-property/stats/{$id}")
            ->meta([
                'icon' => $icon,
                'iconPosition' => 'top',
                'iconColor' => $colors['color'],
                'iconSize' => 'md',
                'iconBgColor' => $colors['bg'],
                'trend' => $trend,
                'trendDirection' => $trendDir,
                'displayType' => $trendIcon,
            ]);
    }

    /**
     * Build main section with data tables
     */
    public static function buildMainSection($section, array $masterData): void
    {
        $section->meta([
            'description' => 'Product properties list',
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        $mainGrid = $section->grid('main-content-grid', 1)->gap('md');
        $getProductPropertyTbl = TableColumnsBuilder::getProductPropertyTableColumns();
        // Table view
        $mainGrid->row('table-row')->gap('none')->table('properties-table')
            ->asTable()
            ->dataUrl('/api/product-property')
            ->columns($getProductPropertyTbl)
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-property-full',
                dataUrl: '/api/product-property/:id',
                config: [
                    'width' => '100vw',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                ]
            )
            ->meta([
                'card' => true,
                'responsive' => true,
                'stickyHeader' => true,
                'variant' => 'outlined',
            ]);

        // List view (optional - can be toggled with a button in the actions section)
        $mainGrid->row('list-row')->gap('none')->table('properties-list')
            ->asList()
            ->dataUrl('/api/product-property')
            ->columns($getProductPropertyTbl)
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-property-full',
                dataUrl: '/api/product-property/:id',
                config: [
                    'width' => '900px',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                ]
            )
            ->meta([
                'card' => true,
                'responsive' => true,
                'stickyHeader' => true,
                'variant' => 'outlined',
            ]);
    }

    /**
     * Build search component
     */
    public static function buildSearchComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => 'Search and filter properties',
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $filterRow = $section->row('filter-row')->gap('none');

        $filterRow->filter('properties-filter')
            ->addQuickFilter('search', __('layout.search'), 'text')
            ->addQuickFilter('category_type', __('layout.category_type'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_types')]],
                $masterData['category_types'] ?? []
            ))
            ->addQuickFilter('property_for', __('layout.property_for'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all')]],
                $masterData['property_for'] ?? []
            ))
            ->addQuickFilter('status', __('layout.status'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_statuses')]],
                $masterData['statuses'] ?? []
            ))
            ->addSelectFilter('property_type', __('layout.property_type'), array_merge(
                [['value' => '', 'label' => __('layout.all')]],
                $masterData['property_types'] ?? []
            ))
            ->addPriceRangeFilter('price', __('layout.price_range'), 0, 100000000)
            ->addRangeFilter('beds', __('layout.beds'), 0, 20)
            ->addRangeFilter('baths', __('layout.baths'), 0, 20)
            ->collapsible()
            ->collapsed(true)
            ->liveFilter(true, 300)
            ->submitAction('/api/product-property');
    }

    /**
     * Build actions component with buttons
     */
    public static function buildActionsComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => 'Action buttons',
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $actionRow = $section->row('actions-row')->gap('sm')->align('center')->justify('end');

        $actionRow->button('refresh-btn')
            ->label(__('layout.refresh'))
            ->icon('refresh')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => __('layout.refresh_data')]);

        $actionRow->button('create-property-btn')
            ->label(__('layout.create'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'aside')
            ->data('component', 'create-property')
            ->data('action', 'create')
            ->data('config', [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ])
            ->meta(['tooltip' => __('layout.create_new_property')]);

        $actionRow->button('create-btn-modal')
            ->label(__('layout.create_modal_view'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-property-modal')
            ->data('action', 'create')
            ->data('config', [
                'width' => '1500px',
                'height' => 'auto',
                'anchor' => 'center',
                'backdrop' => true,
            ])
            ->meta(['tooltip' => __('layout.create_new_property')]);

        // View Options - Dropdown
        $actionRow->button('view-btn')
            ->label('')
            ->icon('cols')
            ->size('md')
            ->variant('outline')
            ->dropdown([
                'id' => 'view-options',
                'placement' => 'bottom-end',
                'iconOnly' => true,
                'items' => [
                    [
                        'id' => 'view-table',
                        'label' => __('layout.table_view'),
                        'icon' => 'cols',
                        'action' => 'table-view',
                        'type' => 'button',
                    ],
                    [
                        'id' => 'view-list',
                        'label' => __('layout.list_view'),
                        'icon' => 'list',
                        'action' => 'list-view',
                        'type' => 'button',
                    ],
                ],
            ])
            ->meta(['tooltip' => __('layout.switch_view')]);
    }

    /**
     * Build footer section
     */
    public static function buildFooterSection($section): void
    {
        $section->meta([
            'description' => 'Footer section',
            'styling' => 'container mx-auto px-4 py-6',
        ]);
    }

    /**
     * Build create property modal
     */
    public static function buildCreatePropertyModal(array $masterData): array
    {
        return ModalSlot::createProperty([
            'masterData' => $masterData,
            'apiUrl' => '/api/product-property',
            'method' => 'POST',
        ]);
    }

    /**
     * Build create property followup modal
     */
    public static function buildCreatePropertyFollowupModal(): array
    {
        return ModalSlot::createFollowup([
            'apiUrl' => '/api/product-property/:id/followups',
            'method' => 'POST',
        ]);
    }

    /**
     * Build edit property followup modal
     */
    public static function buildEditPropertyFollowupModal(): array
    {
        return ModalSlot::editFollowup([
            'apiUrl' => '/api/product-property/:id/followups/:followup_id',
            'dataUrl' => '/api/product-property/:id/followups/:followup_id',
            'method' => 'PUT',
        ]);
    }

    /**
     * Build delete property modal
     */
    public static function buildDeletePropertyModal(string $itemName = ''): array
    {
        return ModalSlot::deleteProperty([
            'itemName' => $itemName ?: null,
            'apiUrl' => '/api/product-property/:id',
            'method' => 'DELETE',
        ]);
    }

    /**
     * Build view property aside
     */
    public static function buildViewPropertyAside(array $masterData): array
    {
        return ViewAsideSlot::make($masterData);
    }

    /**
     * Build view property aside fullscreen
     */
    public static function buildViewPropertyAsideFullscreen(array $masterData): array
    {
        return FullscreenViewAsideSlot::make($masterData);
    }

    /**
     * Build create property aside
     */
    public static function buildCreatePropertyAside(array $masterData): array
    {
        return CreateAsideSlot::make($masterData);
    }

    /**
     * Build create property aside fullscreen
     */
    public static function buildCreatePropertyAsideFullscreen(array $masterData): array
    {
        return CreateAsideSlot::make($masterData, true);
    }

    /**
     * Build edit property aside
     */
    public static function buildEditPropertyAside(array $masterData): array
    {
        return EditAsideSlot::make($masterData);
    }

    /**
     * Build edit property aside fullscreen
     */
    public static function buildEditPropertyAsideFullscreen(array $masterData): array
    {
        return EditAsideSlot::make($masterData, true);
    }
}
