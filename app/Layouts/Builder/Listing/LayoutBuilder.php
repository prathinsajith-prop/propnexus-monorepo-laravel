<?php

namespace App\Layouts\Builder\Listing;

use App\Forms\Listing\ListingForm;
use App\Layouts\Builder\TableColumnsBuilder;
use App\Layouts\Slot\Listing\CreateAsideSlot;
use App\Layouts\Slot\Listing\DetailSlot;
use App\Layouts\Slot\Listing\EditAsideSlot;
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
 * @package App\Layouts\Builder\Listing
 * @see \App\Layouts\ListingLayout Main layout entry point
 * @see \App\Layouts\Slot\Listing\* Component slots
 * @see \App\Layouts\Builder\TableColumnsBuilder Shared table configuration
 * 
 * @package App\Layouts\Listing\Builders
 */
class LayoutBuilder
{
    // ============================================================
    // SECTION BUILDERS - Main layout sections
    // ============================================================

    /**
     * Build header section with page title, breadcrumbs, and statistics
     * 
     * @param mixed $section Section component
     * @return void
     */
    public static function buildHeaderSection($section): void
    {
        $section->meta([
            'description' => 'Page header with breadcrumb navigation and statistics',
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
            ->title('Property Listings')
            ->breadcrumbs([
                ['label' => 'Dashboard', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Listings', 'active' => true, 'icon' => 'building'],
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

        self::buildStatsCard($statsGrid, 'stat-total-listings', 'Total Listings', '0', 'primary', 'home', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-active', 'Active', '0', 'success', 'checkmark', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-sold', 'Sold/Rented', '0', 'info', 'cash', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-total-views', 'Total Views', '0', 'warning', 'eyeopen', '+0%', 'neutral', 'trend-1');
    }

    /**
     * Build main section with data tables
     * 
     * @param mixed $section Section component
     * @param array $masterData Master data for configuration
     * @return void
     */
    public static function buildMainSection($section, array $masterData): void
    {
        $section->meta([
            'description' => 'Main content area with filters and listing table',
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
     * @param mixed $section Section component
     * @param array $masterData Master data for filters
     * @return void
     */
    public static function buildSearchComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => 'Search and filter controls',
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $filterRow = $section->row('filter-row')->gap('none');
        self::buildFilterColumn($filterRow, $masterData);
    }

    /**
     * Build actions section with action buttons
     * 
     * @param mixed $section Section component
     * @param array $masterData Master data
     * @return void
     */
    public static function buildActionsComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => 'Action buttons and controls',
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $actionRow = $section->row('actions-row')->gap('sm')->align('center')->justify('end');
        self::buildActionColumn($actionRow);
    }

    /**
     * Build footer section with copyright and links
     * 
     * @param mixed $section Section component
     * @return void
     */
    public static function buildFooterSection($section): void
    {
        $section->meta([
            'description' => 'Page footer with copyright and links',
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
            ->content('© 2026 Listing Management System. All rights reserved.')
            ->size('sm')
            ->color('text-secondary');

        // Right: Links
        $linksRow = $footerGrid->row('links-row')
            ->gap('md')
            ->align('center')
            ->justify('end');

        $linksRow->button('help-link')
            ->label('Help')
            ->icon('help')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => 'Get help and documentation']);

        $linksRow->button('about-link')
            ->label('About')
            ->icon('infocircle')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => 'About this application']);

        $linksRow->button('version-link')
            ->label('v1.0.0')
            ->icon('tag')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => 'Application version']);
    }

    // ============================================================
    // COMPONENT BUILDERS - Smaller reusable components
    // ============================================================

    /**
     * Build a statistics card with icon, value, and trend indicator
     * 
     * @param mixed $grid Parent grid section
     * @param string $id Unique card identifier
     * @param string $title Card title/label
     * @param string $value Primary metric value
     * @param string $color Theme color
     * @param string $icon Lucide icon name
     * @param string $trend Trend percentage
     * @param string $trendDir Trend direction
     * @param string $displayType Display type
     * @return mixed Card component
     */
    public static function buildStatsCard($grid, string $id, string $title, string $value, string $color, string $icon, string $trend, string $trendDir, string $displayType)
    {
        $colorMap = [
            'primary' => ['icon' => '#3b82f6', 'bg' => '#eff6ff'],
            'success' => ['icon' => '#10b981', 'bg' => '#f0fdf4'],
            'info' => ['icon' => '#8b5cf6', 'bg' => '#f5f3ff'],
            'warning' => ['icon' => '#f59e0b', 'bg' => '#fffbeb'],
            'error' => ['icon' => '#ef4444', 'bg' => '#fef2f2'],
        ];

        $colors = $colorMap[$color] ?? $colorMap['primary'];

        return $grid->card($id)
            ->title($title)
            ->variant('outlined')
            ->gridColumnSpan(1)
            ->meta([
                'collapsible' => false,
                'icon' => $icon,
                'iconColor' => $colors['icon'],
                'iconBg' => $colors['bg'],
                'value' => $value,
                'dataUrl' => "/api/listing/stats/{$id}",
                'displayType' => $displayType,
                'trend' => $trend,
                'trendDirection' => $trendDir,
                'description' => $title,
                'styling' => 'hover:shadow-lg transition-shadow cursor-pointer',
            ]);
    }

    /**
     * Build filter column with search and filter controls
     * 
     * @param mixed $row Row component
     * @param array $masterData Master data
     * @return void
     */
    public static function buildFilterColumn($row, array $masterData): void
    {
        $row->filter('listings-filter')
            ->addQuickFilter('search', 'Search', 'text')
            ->addQuickFilter('property_type', 'Property Type', 'select', array_merge(
                [['value' => '', 'label' => 'All Types']],
                [
                    ['value' => 'residential', 'label' => 'Residential'],
                    ['value' => 'commercial', 'label' => 'Commercial'],
                    ['value' => 'land', 'label' => 'Land'],
                    ['value' => 'industrial', 'label' => 'Industrial'],
                ]
            ))
            ->addQuickFilter('listing_type', 'Listing Type', 'select', array_merge(
                [['value' => '', 'label' => 'All']],
                [
                    ['value' => 'sale', 'label' => 'For Sale'],
                    ['value' => 'rent', 'label' => 'For Rent'],
                    ['value' => 'lease', 'label' => 'For Lease'],
                ]
            ))
            ->addQuickFilter('status', 'Status', 'select', array_merge(
                [['value' => '', 'label' => 'All Statuses']],
                [
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'sold', 'label' => 'Sold'],
                    ['value' => 'rented', 'label' => 'Rented'],
                ]
            ))
            ->addSelectFilter('bedrooms', 'Bedrooms', [
                ['value' => '', 'label' => 'Any'],
                ['value' => '1', 'label' => '1+'],
                ['value' => '2', 'label' => '2+'],
                ['value' => '3', 'label' => '3+'],
                ['value' => '4', 'label' => '4+'],
                ['value' => '5', 'label' => '5+'],
            ])
            ->addPriceRangeFilter('price', 'Price Range', 0, 10000000)
            ->addDateRangeFilter('created_at', 'Listed Date')
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
     * @param mixed $row Row component
     * @return void
     */
    public static function buildActionColumn($row): void
    {
        $row->button('refresh-btn')
            ->label('Refresh')
            ->icon('refresh')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Refresh data']);

        $row->button('create-btn')
            ->label('Create')
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'aside')
            ->data('component', 'create-listing')
            ->data('action', 'open')
            ->data('config', [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ])
            ->meta(['tooltip' => 'Create new listing']);

        $row->button('create-btn-modal')
            ->label('Create Modal View')
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-listing-modal')
            ->data('action', 'open')
            ->meta(['tooltip' => 'Create new listing']);

        $row->button('export-btn')
            ->label('Export')
            ->icon('downloadcloud')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Export data']);
    }

    // ============================================================
    // ASIDE BUILDERS - Create, Edit, View asides
    // ============================================================

    /**
     * Build create listing aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildCreateListingAside(array $masterData): array
    {
        return CreateAsideSlot::make($masterData);
    }

    /**
     * Build create listing fullscreen aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildCreateListingAsideFullscreen(array $masterData): array
    {
        return CreateAsideSlot::make($masterData, true);
    }

    /**
     * Build edit listing aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildEditListingAside(array $masterData): array
    {
        return EditAsideSlot::make($masterData);
    }

    /**
     * Build view listing aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildViewListingAside(array $masterData): array
    {
        return ViewAsideSlot::make($masterData);
    }

    /**
     * Build view listing fullscreen aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildViewListingAsideFullscreen(array $masterData): array
    {
        return ViewAsideSlot::make($masterData, true);
    }

    // ============================================================
    // MODAL BUILDERS - Create, Delete, Confirmation modals
    // ============================================================

    /**
     * Build create listing modal
     * 
     * @param array $masterData Master data for forms
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
     * @param string $itemName Item name to display
     * @return array Modal definition
     */
    public static function buildDeleteListingModal(string $itemName = 'this listing'): array
    {
        return ModalSlot::deleteListing([
            'itemName' => $itemName,
        ]);
    }

    // ============================================================
    // FORM BUILDERS - Listing forms with various configurations
    // ============================================================

    /**
     * Build comprehensive listing form component
     * 
     * @param string $formId Unique form identifier
     * @param string $method HTTP method (POST, PUT, PATCH)
     * @param string $submitUrl URL for form submission
     * @param array $masterData Master data for form dropdowns and options
     * @param string|null $dataUrl Optional URL to fetch existing data for editing
     * @param array $config Optional configuration overrides
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
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
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
     * @param array $masterData Master data for form
     * @param string $dataUrl URL to fetch listing data
     * @param string $formId Optional form ID
     * @return GridSection
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
