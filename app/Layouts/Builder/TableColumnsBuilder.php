<?php

namespace App\Layouts\Builder;

use App\Enums\BlogStatus;
use App\Enums\ListingStatus;
use App\Enums\ProductCategoryType;
use App\Enums\ProductPropertyFor;
use App\Enums\ProductPropertyStatus;
use App\Enums\PropertyType;
use Litepie\Layout\Components\ButtonComponent;

/**
 * TableColumnsBuilder
 * 
 * **Purpose**: Shared table column configurations for all modules.
 * 
 * **Responsibilities**:
 * - Define table column structures (Blog, Listing)
 * - Configure action buttons (View, Edit, Delete)
 * - Provide consistent column definitions across views
 * - Centralize button builder logic to eliminate duplication
 * 
 * **Architecture**:
 * - Shared across multiple modules (Blog, Listing)
 * - Reusable button builder pattern
 * - Consistent action configurations
 * 
 * **Organization**:
 * 1. **Blog Columns**: getBlogTableColumns(), getBlogTableListColumns()
 * 2. **Listing Columns**: getListingTableColumns(), getListingTableListColumns()
 * 3. **Generic Builder**: buildTableActionButton() - eliminates code duplication
 * 4. **Action Builders**: Module-specific button configurations
 * 
 * **Usage Example**:
 * ```php
 * ->columns(TableColumnsBuilder::getBlogTableColumns())
 * ```
 * 
 * @package App\Layouts\Builder
 */
class TableColumnsBuilder
{
    // ============================================================
    // BLOG TABLE COLUMNS
    // ============================================================

    /**
     * Get blog table columns configuration (table view)
     * 
     * @return array Column definitions for table view
     */
    public static function getBlogTableColumns(): array
    {
        return [
            ['key' => 'blog_id', 'label' => __('layout.blog_id'), 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'title', 'label' => __('layout.title'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            [
                'key' => 'status',
                'label' => __('layout.status'),
                'type' => 'badge',
                'sortable' => true,
                'filterable' => true,
                'filter_key' => 'status',
                'width' => '100px',
                'badgeConfig' => BlogStatus::badgeConfig(),
            ],
            ['key' => 'category', 'label' => __('layout.category'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => __('layout.author'), 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => __('layout.views'), 'type' => 'abbreviated', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => __('layout.published'), 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => __('layout.actions'), 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getBlogTableActions()],
        ];
    }

    /**
     * Get blog table list columns configuration (list view)
     * 
     * @return array Column definitions for list view
     */
    public static function getBlogTableListColumns(): array
    {
        return [
            ['key' => 'title', 'label' => __('layout.title'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            [
                'key' => 'status',
                'label' => __('layout.status'),
                'type' => 'badge',
                'sortable' => true,
                'filterable' => true,
                'filter_key' => 'status',
                'width' => '100px',
                'badgeConfig' => BlogStatus::badgeConfig(),
            ],
            ['key' => 'category', 'label' => __('layout.category'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => __('layout.author'), 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => __('layout.views'), 'type' => 'abbreviated', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => __('layout.published'), 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => __('layout.actions'), 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getBlogTableActions()],
        ];
    }

    /**
     * Get blog table action buttons (view, edit, delete)
     * 
     * @return array Action button configurations
     */
    public static function getBlogTableActions(): array
    {
        return [
            self::buildViewButtonAction(),
            self::buildEditButtonAction(),
            self::buildDeleteButtonAction(),
        ];
    }

    // ============================================================
    // LISTING TABLE COLUMNS
    // ============================================================

    /**
     * Get listing table columns configuration (table view)
     * 
     * @return array Column definitions for table view
     */
    public static function getListingTableColumns(): array
    {
        return [
            ['key' => 'mls_number', 'label' => __('layout.mls_number'), 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'title', 'label' => __('layout.title'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            [
                'key' => 'status',
                'label' => __('layout.status'),
                'type' => 'badge',
                'sortable' => true,
                'filterable' => true,
                'filter_key' => 'status',
                'width' => '120px',
                'badgeConfig' => ListingStatus::badgeConfig(),
                'bordered' => true,
            ],
            [
                'key' => 'property_type',
                'label' => __('layout.type'),
                'type' => 'badge',
                'sortable' => true,
                'filterable' => true,
                'filter_key' => 'property_type',
                'width' => '120px',
                'badgeConfig' => PropertyType::badgeConfig(),
                'bordered' => false,
            ],
            ['key' => 'price', 'label' => __('layout.price'), 'type' => 'currency', 'sortable' => true, 'width' => '120px', 'align' => 'right'],
            ['key' => 'location', 'label' => __('layout.location'), 'sortable' => true, 'width' => '180px'],
            ['key' => 'bedrooms', 'label' => __('layout.beds'), 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'bathrooms', 'label' => __('layout.baths'), 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'views_count', 'label' => __('layout.views'), 'type' => 'abbreviated', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'actions', 'label' => __('layout.actions'), 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getListingTableActions()],
        ];
    }

    /**
     * Get listing table list columns configuration (list view)
     * 
     * @return array Column definitions for list view
     */
    public static function getListingTableListColumns(): array
    {
        return [
            ['key' => 'title', 'label' => __('layout.title'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            [
                'key' => 'status',
                'label' => __('layout.status'),
                'type' => 'badge',
                'sortable' => true,
                'filterable' => true,
                'filter_key' => 'status',
                'width' => '120px',
                'badgeConfig' => ListingStatus::badgeConfig(),
                'bordered' => true,
            ],
            [
                'key' => 'property_type',
                'label' => __('layout.type'),
                'type' => 'badge',
                'sortable' => true,
                'width' => '120px',
                'badgeConfig' => PropertyType::badgeConfig(),
                'bordered' => false,
            ],
            ['key' => 'price', 'label' => __('layout.price'), 'type' => 'currency', 'sortable' => true, 'width' => '120px', 'align' => 'right'],
            ['key' => 'views_count', 'label' => __('layout.views'), 'type' => 'abbreviated', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'location', 'label' => __('layout.location'), 'sortable' => true, 'width' => '180px'],
            ['key' => 'actions', 'label' => __('layout.actions'), 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getListingTableActions()],
        ];
    }

    /**
     * Get listing table action buttons
     * 
     * @return array Action button configurations
     */
    public static function getListingTableActions(): array
    {
        return [
            self::buildViewListingButtonAction(),
            self::buildEditListingButtonAction(),
            self::buildDeleteListingButtonAction(),
        ];
    }

    // ============================================================
    // GENERIC BUTTON BUILDER - Reusable across all modules
    // ============================================================

    /**
     * Build a generic table action button
     * 
     * Centralized button builder that eliminates code duplication.
     * All action buttons (view, edit, delete) use this method with different configurations.
     * 
     * @param array $options [
     *   'id' => string,         // Button identifier (required)
     *   'icon' => string,       // Lucide icon name (required)
     *   'color' => string,      // Button color (default: 'primary')
     *   'tooltip' => string,    // Tooltip text (required)
     *   'type' => string,       // Component type: 'aside' or 'modal' (required)
     *   'component' => string,  // Component name to open (required)
     *   'config' => array,      // Additional configuration like width, height
     * ]
     * @return array Button configuration
     */
    private static function buildTableActionButton(array $options = []): array
    {
        $defaults = [
            'color' => 'primary',
            'config' => [],
            'type' => null,
            'component' => null,
            'confirm' => null,
            'variant' => 'standard',
        ];

        $params = array_merge($defaults, $options);

        /** @var \Litepie\Layout\Components\ButtonComponent $button */
        $button = ButtonComponent::make($params['id'])
            ->icon($params['icon'])
            ->variant($params['variant'])
            ->size('sm')
            ->color($params['color'])
            ->isIconButton(true)
            ->data('action', 'open')
            ->dataKey('id')
            ->meta(['tooltip' => $params['tooltip']]);

        // Add type and component if provided
        if (!empty($params['type']) && !empty($params['component'])) {
            $button->data('type', $params['type'])
                ->data('component', $params['component']);
        }

        // Add confirm if provided
        if (!empty($params['confirm'])) {
            $button->confirm($params['confirm'])
                ->data('action', 'delete'); // Override action for delete
        }

        // Add config if provided
        if (!empty($params['config'])) {
            $button->data('config', $params['config']);
        }

        return $button->toArray();
    }

    // ============================================================
    // BUTTON ACTION BUILDERS - Blog
    // ============================================================

    /**
     * Build view button action for blog
     * 
     * @return array View button configuration
     */
    private static function buildViewButtonAction(): array
    {
        return self::buildTableActionButton([
            'id' => 'view',
            'icon' => 'eyeopen',
            'color' => 'primary',
            'tooltip' => __('layout.view_details'),
            'type' => 'aside',
            'component' => 'view-blog',
            'config' => ['width' => '900px'],
        ]);
    }

    /**
     * Build edit button action for blog
     * 
     * @return array Edit button configuration
     */
    private static function buildEditButtonAction(): array
    {
        return self::buildTableActionButton([
            'id' => 'edit',
            'icon' => 'pen',
            'color' => 'primary',
            'tooltip' => __('layout.edit'),
            'type' => 'aside',
            'component' => 'edit-blog',
            'config' => [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ],
        ]);
    }

    /**
     * Build delete button action for blog
     * 
     * @return array Delete button configuration
     */
    private static function buildDeleteButtonAction(): array
    {
        return self::buildTableActionButton([
            'id' => 'delete',
            'icon' => 'binempty',
            'color' => 'danger',
            'tooltip' => __('layout.delete'),
            'confirm' => [
                'title' => __('layout.delete_blog_post'),
                'message' => __('layout.delete_blog_post_confirmation'),
                'confirmLabel' => __('layout.delete'),
                'cancelLabel' => __('layout.cancel'),
                'action' => 'delete',
                'dataUrl' => '/api/blogs/:id',
                'method' => 'delete',
            ],
        ]);
    }

    // ============================================================
    // BUTTON ACTION BUILDERS - Listing
    // ============================================================

    /**
     * Build view button action for listing
     * 
     * @return array View button configuration
     */
    private static function buildViewListingButtonAction(): array
    {
        return self::buildTableActionButton([
            'id' => 'view',
            'icon' => 'eyeopen',
            'color' => 'primary',
            'tooltip' => __('layout.view_details'),
            'type' => 'aside',
            'component' => 'view-listing-full',
            'config' => [
                'width' => '100vw',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
                'componentType' => 'aside',
            ],
            'variant' => 'standard',
        ]);
    }

    /**
     * Build edit button action for listing
     * 
     * @return array Edit button configuration
     */
    private static function buildEditListingButtonAction(): array
    {
        return self::buildTableActionButton([
            'id' => 'edit',
            'icon' => 'pen',
            'color' => 'primary',
            'tooltip' => __('layout.edit'),
            'type' => 'aside',
            'component' => 'edit-listing',
            'variant' => 'standard',
            'config' => [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ],
        ]);
    }

    /**
     * Build delete button action for listing
     * 
     * @return array Delete button configuration
     */
    private static function buildDeleteListingButtonAction(): array
    {
        return self::buildTableActionButton([
            'id' => 'delete',
            'icon' => 'binempty',
            'color' => 'danger',
            'tooltip' => __('layout.delete'),
            'variant' => 'standard',
            'confirm' => [
                'title' => __('layout.delete_listing'),
                'message' => __('layout.delete_listing_confirmation'),
                'confirmLabel' => __('layout.delete'),
                'cancelLabel' => __('layout.cancel'),
                'action' => 'delete',
                'dataUrl' => '/api/listing/:id',
                'method' => 'delete',
            ],
        ]);
    }

    // ============================================================
    // PRODUCT PROPERTY TABLE COLUMNS
    // ============================================================

    /**
     * Get product property table columns configuration
     *
     * @return array Column definitions for table view
     */
    public static function getProductPropertyTableColumns(): array
    {
        return [
            ['key' => 'ref', 'label' => __('product_property.column_ref'), 'sortable' => true, 'width' => '120px'],
            ['key' => 'title', 'label' => __('product_property.property_title'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            [
                'key'         => 'category_type',
                'label'       => __('layout.type'),
                'type'        => 'badge',
                'sortable'    => true,
                'filterable'  => true,
                'filter_key'  => 'category_type',
                'width'       => '130px',
                'badgeConfig' => ProductCategoryType::badgeConfig(),
                'bordered'    => false,
            ],
            [
                'key'         => 'property_for',
                'label'       => __('product_property.column_for'),
                'type'        => 'badge',
                'sortable'    => true,
                'filterable'  => true,
                'filter_key'  => 'property_for',
                'width'       => '110px',
                'badgeConfig' => ProductPropertyFor::badgeConfig(),
                'bordered'    => false,
            ],
            [
                'key'         => 'status',
                'label'       => __('layout.status'),
                'type'        => 'badge',
                'sortable'    => true,
                'filterable'  => true,
                'filter_key'  => 'status',
                'width'       => '140px',
                'badgeConfig' => ProductPropertyStatus::badgeConfig(),
                'bordered'    => true,
            ],
            ['key' => 'price', 'label' => __('product_property.price'), 'type' => 'currency', 'sortable' => true, 'width' => '120px', 'align' => 'right'],
            ['key' => 'beds', 'label' => __('product_property.beds'), 'sortable' => true, 'width' => '70px', 'align' => 'center'],
            ['key' => 'baths', 'label' => __('product_property.baths'), 'sortable' => true, 'width' => '70px', 'align' => 'center'],
            ['key' => 'bua', 'label' => __('product_property.column_bua'), 'sortable' => true, 'width' => '90px', 'align' => 'right'],
            ['key' => 'created_at_formatted', 'label' => __('product_property.column_created'), 'sortable' => false, 'width' => '130px'],
            ['key' => 'actions', 'label' => __('layout.actions'), 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getProductPropertyTableActions()],
        ];
    }

    /**
     * Get product property table action buttons
     *
     * @return array Action button configurations
     */
    public static function getProductPropertyTableActions(): array
    {
        return [
            self::buildViewPropertyButtonAction(),
            self::buildEditPropertyButtonAction(),
            self::buildDeletePropertyButtonAction(),
        ];
    }

    // ============================================================
    // BUTTON ACTION BUILDERS - ProductProperty
    // ============================================================

    private static function buildViewPropertyButtonAction(): array
    {
        return self::buildTableActionButton([
            'id'        => 'view',
            'icon'      => 'eyeopen',
            'color'     => 'primary',
            'variant'   => 'standard',
            'tooltip'   => __('layout.view_details'),
            'type'      => 'aside',
            'component' => 'view-property-full',
            'config'    => [
                'width'         => '900px',
                'height'        => '100vh',
                'anchor'        => 'right',
                'backdrop'      => true,
                'componentType' => 'aside',
            ],
        ]);
    }

    private static function buildEditPropertyButtonAction(): array
    {
        return self::buildTableActionButton([
            'id'        => 'edit',
            'icon'      => 'pen',
            'color'     => 'primary',
            'variant'   => 'standard',
            'tooltip'   => __('layout.edit'),
            'type'      => 'aside',
            'component' => 'edit-property',
            'config'    => [
                'width'   => '800px',
                'height'  => '100vh',
                'anchor'  => 'right',
                'backdrop' => true,
            ],
        ]);
    }

    private static function buildDeletePropertyButtonAction(): array
    {
        return self::buildTableActionButton([
            'id'      => 'delete',
            'icon'    => 'binempty',
            'color'   => 'danger',
            'variant' => 'standard',
            'tooltip' => __('layout.delete'),
            'confirm' => [
                'title'        => __('layout.delete_property'),
                'message'      => __('layout.delete_property_confirmation'),
                'confirmLabel' => __('layout.delete'),
                'cancelLabel'  => __('layout.cancel'),
                'action'       => 'delete',
                'dataUrl'      => '/api/product-property/:id',
                'method'       => 'delete',
            ],
        ]);
    }
}
