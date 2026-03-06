<?php

namespace App\Layouts\Builder\Listing;

use App\Forms\Listing\ListingForm;
use App\Layouts\Builder\TableColumnsBuilder;
use App\Layouts\Slot\Listing\CreateAsideSlot;
use App\Layouts\Slot\Listing\EditAsideSlot;
use App\Layouts\Slot\Listing\FullscreenViewAsideSlot;
use App\Layouts\Slot\Listing\ModalSlot;
use App\Layouts\Slot\Listing\ViewAsideSlot;
use Litepie\Layout\Sections\GridSection;

/**
 * Listing LayoutBuilder
 *
 * **Purpose**: Orchestrates construction of all property listing layout sections.
 *
 * **Architecture Role**:
 * - Receives configuration from ListingLayout (entry point)
 * - Delegates component rendering to Slot classes
 * - Coordinates section assembly and data flow
 *
 * **Organization** (mirrors Blog structure for consistency):
 * 1. **Section Builders**: Main page sections (Header, Main, Footer, Search, Actions)
 * 2. **Component Builders**: Individual UI components (Stats, Filters, Buttons)
 * 3. **Table Configuration**: Data table setup with columns from TableColumnsBuilder
 * 4. **Aside Builders**: Drawer panels (Create, Edit, View)
 * 5. **Modal Builders**: Dialog modals (Create, Delete, Confirmation)
 * 6. **Form Builders**: Form components with various configurations
 *
 * **Naming Convention**:
 * - No "Listing" prefix in class name (namespace provides context)
 * - Module-specific logic lives here
 * - Shared logic goes in parent Builder or TableColumnsBuilder
 *
 * @see \App\Layouts\ListingLayout Main layout entry point
 * @see \App\Layouts\Slot\Listing\* Component slots
 * @see \App\Layouts\Builder\TableColumnsBuilder Shared table configuration
 */
class LayoutBuilder
{
    // ============================================================
    // SECTION BUILDERS - Main layout sections
    // ============================================================

    /**
     * Build header section with page title, breadcrumbs, and statistics
     *
     * @param  mixed  $section  Section component
     */
    public static function buildHeaderSection($section): void
    {
        $section->meta([
            'description' => __('layout.listing_header_description'),
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
            ->title(__('layout.listings'))
            ->breadcrumbs([
                ['label' => __('layout.dashboard'), 'link' => '/', 'icon' => 'home'],
                ['label' => __('layout.listings'), 'active' => true, 'icon' => 'building'],
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

        self::buildStatsCard($statsGrid, 'stat-total-listings', __('layout.total_listings'), '0', 'primary', 'home', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-active', __('layout.active_listings'), '0', 'success', 'badgecheck', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-sold', __('layout.sold_rented'), '0', 'info', 'cash', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-total-views', __('layout.total_views'), '0', 'warning', 'eyeopen', '+0%', 'neutral', 'trend-1');
    }

    /**
     * Build main section with data tables
     *
     * @param  mixed  $section  Section component
     * @param  array  $masterData  Master data for configuration
     */
    public static function buildMainSection($section, array $masterData): void
    {
        $section->meta([
            'description' => __('layout.listing_main_description'),
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        $mainGrid = $section->grid('main-content-grid', 1)->gap('md');

        // Table view
        $mainGrid->row('table-row')->gap('none')->table('listings-table')
            ->asTable()
            ->dataUrl('/api/listing')
            ->columns(TableColumnsBuilder::getListingTableColumns())
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-listing',
                dataUrl: '/api/listing/:id',
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

        // List view
        $mainGrid->row('list-row')->gap('none')->table('listings-list')
            ->asList()
            ->dataUrl('/api/listing')
            ->columns(TableColumnsBuilder::getListingTableListColumns())
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-listing',
                dataUrl: '/api/listing/:id',
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
     * Build search section with filters
     *
     * @param  mixed  $section  Section component
     * @param  array  $masterData  Master data for filters
     */
    public static function buildSearchComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => __('layout.listing_search_description'),
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $filterRow = $section->row('filter-row')->gap('none');
        self::buildFilterColumn($filterRow, $masterData);
    }

    /**
     * Build actions section with action buttons
     *
     * @param  mixed  $section  Section component
     * @param  array  $masterData  Master data
     */
    public static function buildActionsComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => __('layout.listing_actions_description'),
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $actionRow = $section->row('actions-row')->gap('sm')->align('center')->justify('end');
        self::buildActionColumn($actionRow);
    }

    /**
     * Build footer section with copyright and links
     *
     * @param  mixed  $section  Section component
     */
    public static function buildFooterSection($section): void
    {
        $section->meta([
            'description' => __('layout.listing_footer_description'),
            'styling' => 'container mx-auto px-4 py-6 border-t border-gray-200',
        ]);

        $footerGrid = $section->grid('footer-grid')
            ->columns(2)
            ->gap('md')
            ->responsive(true);

        // Left: Copyright
        $footerGrid->row('copyright-row')
            ->gap('sm')
            ->align('center')
            ->justify('start')
            ->text('copyright-text')
            ->content(__('layout.listing_system_copyright'))
            ->size('sm')
            ->color('text-secondary');

        // Right: Links
        $linksRow = $footerGrid->row('links-row')
            ->gap('md')
            ->align('center')
            ->justify('end');

        $linksRow->button('help-link')
            ->label(__('layout.help'))
            ->icon('questionhexagon')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => __('layout.get_help')]);

        $linksRow->button('about-link')
            ->label(__('layout.about'))
            ->icon('infosquare')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => __('layout.about_app')]);

        $linksRow->button('version-link')
            ->label(__('layout.v1_0_0'))
            ->icon('tag')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => __('layout.version')]);
    }

    // ============================================================
    // COMPONENT BUILDERS - Smaller reusable components
    // ============================================================

    /**
     * Build a statistics card with icon, value, and trend indicator
     *
     * @param  mixed  $grid  Parent grid section
     * @param  string  $id  Unique card identifier
     * @param  string  $title  Card title/label
     * @param  string  $value  Primary metric value
     * @param  string  $color  Theme color
     * @param  string  $icon  Lucide icon name
     * @param  string  $trend  Trend percentage
     * @param  string  $trendDir  Trend direction
     * @param  string  $displayType  Display type
     * @return mixed Card component
     */
    public static function buildStatsCard($grid, string $id, string $title, string $value, string $color, string $icon, string $trend, string $trendDir, string $displayType)
    {
        $colorMap = [
            'primary' => ['color' => '#3b82f6', 'bg' => '#f0f9ff'],
            'success' => ['color' => '#10b981', 'bg' => '#f7fef9'],
            'info' => ['color' => '#8b5cf6', 'bg' => '#faf8ff'],
            'warning' => ['color' => '#f59e0b', 'bg' => '#fffef7'],
            'error' => ['color' => '#ef4444', 'bg' => '#fef9f9'],
        ];

        $colors = $colorMap[$color] ?? $colorMap['primary'];

        return $grid->card($id)
            ->title($title)
            ->content($value)
            ->variant('outlined')
            ->color($color)
            ->gridColumnSpan(3)
            ->dataUrl("/api/listing/stats/{$id}")
            ->meta([
                'icon' => $icon,
                'iconPosition' => 'top',
                'iconColor' => $colors['color'],
                'iconSize' => 'md',
                'iconBgColor' => $colors['bg'],
                'trend' => $trend,
                'trendDirection' => $trendDir,
                'displayType' => $displayType,
            ]);
    }

    /**
     * Build filter column with search and filter controls
     *
     * @param  mixed  $row  Row component
     * @param  array  $masterData  Master data
     */
    public static function buildFilterColumn($row, array $masterData): void
    {
        $row->filter('listings-filter')
            ->addQuickFilter('search', __('layout.search'), 'text')
            ->addQuickFilter('property_type', __('layout.property_type'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_types')]],
                [
                    ['value' => 'residential', 'label' => __('layout.residential')],
                    ['value' => 'commercial', 'label' => __('layout.commercial')],
                    ['value' => 'land', 'label' => __('layout.land')],
                    ['value' => 'industrial', 'label' => __('layout.industrial')],
                ]
            ))
            ->addQuickFilter('listing_type', __('layout.listing_type'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all')]],
                [
                    ['value' => 'sale', 'label' => __('layout.for_sale')],
                    ['value' => 'rent', 'label' => __('layout.for_rent')],
                    ['value' => 'lease', 'label' => __('layout.for_lease')],
                ]
            ))
            ->addQuickFilter('status', __('layout.status'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_statuses')]],
                [
                    ['value' => 'draft', 'label' => __('layout.draft')],
                    ['value' => 'active', 'label' => __('layout.active')],
                    ['value' => 'pending', 'label' => __('layout.pending')],
                    ['value' => 'sold', 'label' => __('layout.sold')],
                    ['value' => 'rented', 'label' => __('layout.rented')],
                ]
            ))
            ->addSelectFilter('status', __('layout.status'), [
                ['value' => 'draft', 'label' => __('layout.draft')],
                ['value' => 'active', 'label' => __('layout.active')],
                ['value' => 'pending', 'label' => __('layout.pending')],
                ['value' => 'sold', 'label' => __('layout.sold')],
                ['value' => 'rented', 'label' => __('layout.rented')],
                ['value' => 'completed', 'label' => __('layout.completed')],
                ['value' => 'cancelled', 'label' => __('layout.cancelled')],
            ])
            ->addMultiSelectFilter('property_type', __('layout.property_type'), $masterData['property_types'] ?? [])
            ->addMultiSelectFilter('listing_type', __('layout.listing_type'), $masterData['listing_types'] ?? [])
            ->addSelectFilter('availability', __('layout.availability'), array_merge(
                [['value' => '', 'label' => __('layout.all')]],
                $masterData['availabilities'] ?? []
            ))
            ->addMultiSelectFilter('city', __('layout.city'), $masterData['cities'] ?? [])
            ->addMultiSelectFilter('area', __('layout.area'), $masterData['areas'] ?? [])
            ->addSelectFilter('bedrooms', __('layout.bedrooms'), [
                ['value' => '', 'label' => __('layout.any')],
                ['value' => '1', 'label' => '1+'],
                ['value' => '2', 'label' => '2+'],
                ['value' => '3', 'label' => '3+'],
                ['value' => '4', 'label' => '4+'],
                ['value' => '5', 'label' => '5+'],
            ])
            ->addSelectFilter('bathrooms', __('layout.bathrooms'), [
                ['value' => '', 'label' => __('layout.any')],
                ['value' => '1', 'label' => '1+'],
                ['value' => '2', 'label' => '2+'],
                ['value' => '3', 'label' => '3+'],
                ['value' => '4', 'label' => '4+'],
                ['value' => '5', 'label' => '5+'],
            ])
            ->addSelectFilter('furnishing_status', __('layout.furnishing_status'), array_merge(
                [['value' => '', 'label' => __('layout.any')]],
                $masterData['furnishing_statuses'] ?? []
            ))
            ->addPriceRangeFilter('price', __('layout.price_range'), 0, 10000000)
            ->addSelectFilter('agent_id', __('layout.agent'), array_merge(
                [['value' => '', 'label' => __('layout.all_agents')]],
                $masterData['agents'] ?? []
            ))
            ->addDateRangeFilter('created_at', __('layout.listed_date'))
            ->addDateRangeFilter('published_at', __('layout.published_date'))
            ->collapsible()
            ->collapsed(true)
            ->showActiveCount()
            ->rememberFilters(true, 'listings_filter')
            ->liveFilter(true, 300)
            ->submitAction('/api/listing');
    }

    /**
     * Build action column with action buttons
     *
     * @param  mixed  $row  Row component
     */
    public static function buildActionColumn($row): void
    {
        $row->button('refresh-btn')
            ->label(__('layout.refresh'))
            ->icon('refresh')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => __('layout.refresh_data')]);

        $row->button('create-btn')
            ->label(__('layout.create'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'aside')
            ->data('component', 'create-listing')
            ->data('action', 'create')
            ->data('config', [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ])
            ->meta(['tooltip' => __('layout.create_new_listing')]);

        $row->button('create-btn-modal')
            ->label(__('layout.create_modal_view'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-listing-modal')
            ->data('action', 'create')
            ->meta(['tooltip' => __('layout.create_new_listing')]);

        // View Options - Dropdown
        $row->button('view-btn')
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

        // More Options - Dropdown
        $row->button('more-btn')
            ->label('')
            ->icon('ellipsisvertical')
            ->size('md')
            ->variant('outline')
            ->dropdown([
                'id' => 'more-options',
                'placement' => 'bottom-end',
                'offset' => [0, 8],
                'closeOnClick' => true,
                'closeOnEscape' => true,
                'items' => [
                    [
                        'id' => 'export-data',
                        'label' => __('layout.export'),
                        'icon' => 'downloadcloud',
                        'action' => 'export',
                        'type' => 'button',
                    ],
                    [
                        'id' => 'import-data',
                        'label' => __('layout.import'),
                        'icon' => 'uploadcloud',
                        'action' => 'import',
                        'type' => 'button',
                    ],
                    [
                        'type' => 'divider',
                    ],
                    [
                        'id' => 'print',
                        'label' => __('layout.print'),
                        'icon' => 'printer',
                        'action' => 'print',
                        'type' => 'button',
                    ],
                    [
                        'id' => 'archive',
                        'label' => __('layout.archive'),
                        'icon' => 'archive',
                        'action' => 'archive',
                        'type' => 'button',
                    ],
                    [
                        'type' => 'divider',
                    ],
                    [
                        'id' => 'settings',
                        'label' => __('layout.settings'),
                        'icon' => 'settings',
                        'action' => 'settings',
                        'type' => 'button',
                    ],
                ],
            ])
            ->meta(['tooltip' => __('layout.more_options')]);
    }

    // ============================================================
    // ASIDE BUILDERS - Create, Edit, View asides
    // ============================================================

    /**
     * Build create listing aside
     *
     * @param  array  $masterData  Master data for forms
     * @return array Aside definition
     */
    public static function buildCreateListingAside(array $masterData): array
    {
        return CreateAsideSlot::make($masterData);
    }

    /**
     * Build create listing fullscreen aside
     *
     * @param  array  $masterData  Master data for forms
     * @return array Aside definition
     */
    public static function buildCreateListingAsideFullscreen(array $masterData): array
    {
        return CreateAsideSlot::make($masterData, true);
    }

    /**
     * Build edit listing aside
     *
     * @param  array  $masterData  Master data for forms
     * @return array Aside definition
     */
    public static function buildEditListingAside(array $masterData): array
    {
        return EditAsideSlot::make($masterData);
    }

    /**
     * Build view listing aside
     *
     * @param  array  $masterData  Master data for forms
     * @return array Aside definition
     */
    public static function buildViewListingAside(array $masterData): array
    {
        return ViewAsideSlot::make($masterData);
    }

    /**
     * Build view listing fullscreen aside
     *
     * Optimized fullscreen layout with two-column design:
     * - Left: Large image gallery with carousel/lightbox
     * - Right: Property details form
     *
     * @param  array  $masterData  Master data for forms
     * @return array Aside definition
     */
    public static function buildViewListingAsideFullscreen(array $masterData): array
    {
        return FullscreenViewAsideSlot::make($masterData);
    }

    // ============================================================
    // MODAL BUILDERS - Create, Delete, Confirmation modals
    // ============================================================

    /**
     * Build create listing modal
     *
     * @param  array  $masterData  Master data for forms
     * @return array Modal definition
     */
    public static function buildCreateListingModal(array $masterData): array
    {
        return ModalSlot::createListing([
            'masterData' => $masterData,
        ]);
    }

    /**
     * Build delete confirmation modal
     *
     * @param  string  $itemName  Item name to display
     * @return array Modal definition
     */
    public static function buildDeleteListingModal(string $itemName = ''): array
    {
        return ModalSlot::deleteListing([
            'itemName' => $itemName ?: __('layout.this_listing'),
        ]);
    }

    // ============================================================
    // FORM BUILDERS - Listing forms with various configurations
    // ============================================================

    /**
     * Build comprehensive listing form component
     *
     * @param  string  $formId  Unique form identifier
     * @param  string  $method  HTTP method (POST, PUT, PATCH)
     * @param  string  $submitUrl  URL for form submission
     * @param  array  $masterData  Master data for form dropdowns and options
     * @param  string|null  $dataUrl  Optional URL to fetch existing data for editing
     * @param  array  $config  Optional configuration overrides
     * @return GridSection Grid section containing the listing form
     */
    public static function buildListingFormComponent(
        string $formId = 'listing-form',
        string $method = 'POST',
        string $submitUrl = '/api/listing',
        array $masterData = [],
        ?string $dataUrl = null,
        array $config = []
    ): GridSection {
        $defaultConfig = [
            'columns' => 1,
            'rows' => 1,
            'gap' => 'md',
            'gridColumnSpan' => 12,
            'styling' => 'w-full',
        ];

        $config = array_merge($defaultConfig, $config);

        $formGrid = GridSection::make("{$formId}-grid", $config['columns'])
            ->rows($config['rows'])
            ->gap($config['gap']);

        if (isset($config['styling'])) {
            $formGrid->meta(['styling' => $config['styling']]);
        }

        $listingForm = $dataUrl
            ? ListingForm::make($formId, $method, $submitUrl, $masterData, $dataUrl)
            : ListingForm::make($formId, $method, $submitUrl, $masterData);

        $listingForm->gridColumnSpan($config['gridColumnSpan']);
        $formGrid->add($listingForm);

        return $formGrid;
    }

    /**
     * Create a create listing form
     *
     * @param  array  $masterData  Master data for form
     * @param  string  $formId  Optional form ID
     */
    public static function createListingForm(array $masterData = [], string $formId = 'create-listing-form'): GridSection
    {
        return self::buildListingFormComponent(
            formId: $formId,
            method: 'POST',
            submitUrl: '/api/listing',
            masterData: $masterData
        );
    }

    /**
     * Create an edit listing form
     *
     * @param  array  $masterData  Master data for form
     * @param  string  $dataUrl  URL to fetch listing data
     * @param  string  $formId  Optional form ID
     */
    public static function editListingForm(array $masterData = [], string $dataUrl = '/api/listing/:id', string $formId = 'edit-listing-form'): GridSection
    {
        return self::buildListingFormComponent(
            formId: $formId,
            method: 'PUT',
            submitUrl: '/api/listing/:id',
            masterData: $masterData,
            dataUrl: $dataUrl
        );
    }
}
