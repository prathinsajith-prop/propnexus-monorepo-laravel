<?php

namespace App\Layout\Builders;

use Litepie\Layout\Components\ButtonComponent;

/**
 * TableColumnsBuilder
 * 
 * Centralized configuration for table columns across different layouts.
 * Provides column definitions for both table and list views.
 * 
 * @package App\Layout\Builders
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
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'category', 'label' => 'Category', 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => 'Author', 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => 'Views', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getBlogTableActions()],
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
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'category', 'label' => 'Category', 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => 'Author', 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => 'Views', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getBlogTableActions()],
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
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '120px'],
            ['key' => 'property_type', 'label' => 'Type', 'sortable' => true, 'filterable' => true, 'filter_key' => 'property_type', 'width' => '120px'],
            ['key' => 'price', 'label' => 'Price', 'type' => 'currency', 'sortable' => true, 'width' => '120px', 'align' => 'right'],
            ['key' => 'location', 'label' => 'Location', 'sortable' => true, 'width' => '180px'],
            ['key' => 'bedrooms', 'label' => 'Beds', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'bathrooms', 'label' => 'Baths', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getListingTableActions()],
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
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '120px'],
            ['key' => 'property_type', 'label' => 'Type', 'sortable' => true, 'width' => '120px'],
            ['key' => 'price', 'label' => 'Price', 'type' => 'currency', 'sortable' => true, 'width' => '120px', 'align' => 'right'],
            ['key' => 'location', 'label' => 'Location', 'sortable' => true, 'width' => '180px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getListingTableActions()],
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
        ];

        $params = array_merge($defaults, $options);

        $button = ButtonComponent::make($params['id'])
            ->icon($params['icon'])
            ->variant('outlined')
            ->size('sm')
            ->color($params['color'])
            ->isIconButton(true)
            ->data('type', $params['type'])
            ->data('component', $params['component'])
            ->data('action', 'open')
            ->dataKey('id')
            ->meta(['tooltip' => $params['tooltip']]);

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
            'tooltip' => 'View Details',
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
            'tooltip' => 'Edit',
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
            'tooltip' => 'Delete',
            'type' => 'modal',
            'component' => 'delete-blog-modal',
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
            'tooltip' => 'View Details',
            'type' => 'aside',
            'component' => 'view-listing',
            'config' => ['width' => '900px'],
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
            'tooltip' => 'Edit',
            'type' => 'aside',
            'component' => 'edit-listing',
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
            'tooltip' => 'Delete',
            'type' => 'modal',
            'component' => 'delete-listing-modal',
        ]);
    }
}
