<?php

namespace App\Layouts;

use App\Forms\Listing\ListingForm;
use App\Forms\Listing\ListingViewForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\ListComponent;
use Litepie\Layout\Components\ModalComponent;
use Litepie\Layout\Components\TableComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Components\TimelineComponent;
use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

/**
 * ListingLayout
 *
 * Comprehensive property listing management layout with:
 * - Header with page header, breadcrumbs, and statistics cards
 * - Filters for property search (type, price, bedrooms, location)
 * - Data table with sorting and pagination
 * - Create/Edit/View asides
 * - Delete confirmation modals
 * 
 * @package App\Layouts
 */
class ListingLayout
{
    // Default Card Config
    private const DEFAULT_CARD_COLLAPSIBLE = true;
    private const MAX_CARD_HEIGHT = '500px';

    /**
     * Create listing management layout
     *
     * @param array $masterData Master data for dropdowns and options
     * @return LayoutBuilder
     */
    public static function make($masterData)
    {
        return LayoutBuilder::create('listings', 'page')
            ->title('Property Listings')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/listing-master-data',
                'description' => 'Property Listing Management System',
                'version' => '1.0.0',
                'refreshInterval' => null,
            ])
            ->section('header', fn($section) => self::buildHeaderSection($section))
            ->section('main', fn($section) => self::buildMainSection($section, $masterData))
            ->section('search', fn($section) => self::buildSearchComponent($section, $masterData))
            ->section('actions', fn($section) => self::buildActionsComponent($section, $masterData))
            ->section('footer', fn($section) => self::buildFooterSection($section))
            ->build();
    }

    /**
     * Build header section with breadcrumbs and statistics.
     */
    private static function buildHeaderSection($section)
    {
        $section->meta([
            'description' => 'Page header with breadcrumb navigation and statistics',
            'styling' => 'container mx-auto px-4 py-6',
            'background' => 'transparent',
        ]);

        // Main header grid - responsive 2-column layout
        $headerGrid = $section->grid('header-main-grid')
            ->columns(2)
            ->gap('xl')
            ->responsive(true);

        // Left column: Page header with breadcrumbs
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

        // Right column: Statistics cards grid
        $statsGrid = $headerGrid->grid('stats-grid')
            ->columns(4)
            ->gap('lg')
            ->responsive(true)
            ->gridColumnSpan(7);

        self::buildStatsCard($statsGrid, 'stat-total-listings', 'Total Listings', '0', 'primary', 'home', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-active', 'Active', '0', 'success', 'checkmark', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-sold', 'Sold/Rented', '0', 'info', 'cash', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-total-views', 'Total Views', '0', 'warning', 'eyeopen', '+0%', 'neutral', 'trend-1');

        return $section;
    }

    /**
     * Build a statistics card with icon, value, and trend indicator
     */
    private static function buildStatsCard($grid, string $id, string $title, string $value, string $color, string $icon, string $trend, string $trendDir, string $displayType)
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
     * Build main content section with filters and table
     */
    private static function buildMainSection($section, $masterData)
    {
        $section->meta([
            'description' => 'Main content area with filters and listing table',
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        // Filters Section
        $filterRow = $section->row('filter-row')->gap('none');
        $filterRow->filter('listings-filter')
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
            ->addSelectFilter('city', 'City', $masterData['cities'] ?? [])
            ->addSelectFilter('area', 'Area', $masterData['areas'] ?? [])
            ->collapsible()
            ->collapsed(true)
            ->showActiveCount()
            ->rememberFilters(true, 'listings_filter')
            ->liveFilter(true, 300)
            ->submitAction('/api/listing');

        // Create main grid for table
        $mainGrid = $section->grid('main-content-grid')->columns(1)->gap('md');

        // Add data table
        $mainGrid->row('table-row')->gap('none')->table('listings-table')
            ->asTable()
            ->dataUrl('/api/listing')
            ->columns(self::getListingTableColumns())
            ->selectable(true)
            ->pagination(true)
            ->perPage(20)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-listing-aside',
                dataUrl: '/api/listing/:id',
                config: [
                    'width' => '900px',
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

        return $section;
    }

    /**
     * Get listing table columns configuration
     */
    private static function getListingTableColumns()
    {
        return [
            [
                'key' => 'id',
                'label' => 'ID',
                'sortable' => true,
                'width' => '80px',
            ],
            [
                'key' => 'title',
                'label' => 'Property Title',
                'sortable' => true,
                'width' => '300px',
            ],
            [
                'key' => 'property_type',
                'label' => 'Type',
                'sortable' => true,
                'width' => '120px',
            ],
            [
                'key' => 'listing_type',
                'label' => 'Listing',
                'sortable' => true,
                'width' => '100px',
            ],
            [
                'key' => 'price',
                'label' => 'Price',
                'sortable' => true,
                'width' => '150px',
            ],
            [
                'key' => 'bedrooms',
                'label' => 'Beds',
                'sortable' => true,
                'width' => '80px',
            ],
            [
                'key' => 'bathrooms',
                'label' => 'Baths',
                'sortable' => true,
                'width' => '80px',
            ],
            [
                'key' => 'city',
                'label' => 'City',
                'sortable' => true,
                'width' => '150px',
            ],
            [
                'key' => 'area',
                'label' => 'Area',
                'sortable' => true,
                'width' => '180px',
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'sortable' => true,
                'width' => '120px',
            ],
            [
                'key' => 'actions',
                'label' => 'Actions',
                'sortable' => false,
                'width' => '120px',
            ],
        ];
    }

    /**
     * Build old listings table (deprecated)
     */
    private static function buildListingsTable($parent, $masterData)
    {
        // This method is deprecated and no longer used
        return;
    }

    /**
     * Build search component for mobile/responsive view
     */
    private static function buildSearchComponent($section, $masterData)
    {
        $section->meta([
            'description' => 'Quick search component',
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $searchForm = $section->form('quick-search')
            ->columns(1)
            ->gap('sm');

        $searchForm->text('q')
            ->placeholder('Search listings...')
            ->width(12);

        return $section;
    }

    /**
     * Build actions component (Create button and bulk actions)
     */
    private static function buildActionsComponent($section, $masterData)
    {
        $section->meta([
            'description' => 'Action buttons and controls',
            'styling' => 'container mx-auto px-4 py-2',
        ]);

        $actionRow = $section->row('actions-row')->gap('sm')->align('center')->justify('end');

        $actionRow->button('create-listing-btn')
            ->label('Create Listing')
            ->icon('plus')
            ->size('md')
            ->variant('filled')
            ->meta(['action' => 'create', 'tooltip' => 'Create new listing']);

        $actionRow->button('refresh-btn')
            ->label('Refresh')
            ->icon('refresh')
            ->size('md')
            ->variant('outlined')
            ->meta(['action' => 'refresh', 'tooltip' => 'Refresh listing data']);

        return $section;
    }

    /**
     * Build footer section
     */
    private static function buildFooterSection($section)
    {
        $section->meta([
            'description' => 'Page footer',
            'styling' => 'container mx-auto px-4 py-6 text-center text-sm text-gray-500',
        ]);

        $section->text('footer-text')
            ->content('Property Listing Management System © 2026');

        return $section;
    }

    /**
     * Get component definition dynamically
     * Called dynamically when components are requested
     * 
     * @param string $type Component type ('modal' or 'aside')
     * @param string $componentName Component identifier
     * @param array $masterData Master data for forms
     * @return array|null Component definition
     */
    public static function getComponentDefinition($type, $componentName, $masterData)
    {
        if ($type === 'modal') {
            switch ($componentName) {
                case 'create-listing-modal':
                    return self::buildCreateListingModal($masterData);
                case 'delete-listing-modal':
                    return self::buildDeleteListingModal();
            }
        }

        if ($type === 'aside') {
            switch ($componentName) {
                case 'view-listing-aside':
                    return self::buildViewListingAside($masterData);
                case 'view-listing-full':
                    return self::buildViewListingAsideFullscreen($masterData);
                case 'view-listing-activity-aside':
                    return self::buildViewListingFormActivityAside($masterData);
                case 'create-listing':
                    return self::buildCreateListingAside($masterData);
                case 'create-listing-full':
                    return self::buildCreateListingAsideFullscreen($masterData);
                case 'edit-listing':
                    return self::buildEditListingAside($masterData);
                case 'edit-listing-full':
                    return self::buildEditListingAsideFullscreen($masterData);
            }
        }

        return null;
    }

    /**
     * Build create listing modal
     */
    private static function buildCreateListingModal($masterData)
    {
        $formComponent = ListingForm::make('create-listing-form-modal', 'POST', '/api/listing', $masterData);

        return ModalComponent::make('create-listing-modal')
            ->children([
                [
                    'type' => 'header',
                    'title' => 'Create New Listing',
                    'icon' => 'home',
                ],
                [
                    'type' => 'footer',
                    'buttonGroup' => [
                        'buttons' => [
                            ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                            ['label' => 'Create Listing', 'type' => 'submit', 'color' => 'primary', 'icon' => 'check', 'dataUrl' => '/api/listing', 'method' => 'POST'],
                        ],
                    ],
                ],
                $formComponent,
            ])
            ->ariaLabelledby('create-listing-modal-title')
            ->toArray();
    }

    /**
     * Build delete listing modal
     */
    private static function buildDeleteListingModal()
    {
        return ModalComponent::make('delete-listing-modal')
            ->children([
                [
                    'type' => 'header',
                    'title' => 'Delete Listing',
                    'icon' => 'binempty',
                    'color' => 'danger',
                ],
                [
                    'type' => 'footer',
                    'buttonGroup' => [
                        'buttons' => [
                            ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                            ['label' => 'Delete', 'color' => 'danger', 'icon' => 'binempty', 'dataUrl' => '/api/listing/:id', 'method' => 'DELETE'],
                        ],
                    ],
                ],
            ])
            ->ariaLabelledby('delete-listing-modal-title')
            ->ariaDescribedby('delete-listing-modal-description')
            ->toArray();
    }

    /**
     * Build create listing aside
     */
    private static function buildCreateListingAside($masterData)
    {
        $formComponent = ListingForm::make('create-listing-form', 'POST', '/api/listing', $masterData);

        // Create main grid for form
        $mainGrid = GridSection::make('create-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header center grid
        $centerGrid = GridSection::make('create-header-center', 1)
            ->rows(2)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('title')
                ->content('Create New Listing')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerGrid->add(
            TextComponent::make('subtitle')
                ->content('Add a new property to your listings')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid
        $rightGrid = GridSection::make('create-header-right', 1)
            ->gap('sm');

        // Create header right row for buttons
        $rightRow = RowSection::make('create-header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');
        $rightRow->add(
            ButtonComponent::make('close-btn')
                ->icon('x')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $rightGrid->add($rightRow);

        // Create footer right grid
        $footerRightGrid = GridSection::make('create-footer-right', 2)
            ->gap('sm');
        // Create footer right row for buttons
        $footerRightRow = RowSection::make('create-footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');
        $footerRightRow->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightRow->add(
            ButtonComponent::make('create-btn')
                ->label('Create Listing')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing', 'method' => 'POST', 'color' => 'primary'])
        );

        $footerRightGrid->add($footerRightRow);
        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('create-aside-header')
                ->setCenter($centerGrid)
                ->setRight($rightGrid)
                ->variant('elevated')
                ->padding('md')
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('create-aside-footer')
                ->setRight($footerRightGrid)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('create-listing')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('create-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build edit listing aside
     */
    private static function buildEditListingAside($masterData)
    {
        $formComponent = ListingForm::make('edit-listing-aside', 'PUT', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('edit-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header center grid
        $centerGrid = GridSection::make('edit-header-center', 1)
            ->rows(2)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('title')
                ->content('Edit Listing')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerGrid->add(
            TextComponent::make('subtitle')
                ->content('Update property information')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid
        $rightGrid = GridSection::make('edit-header-right', 1)
            ->gap('sm');

        // Create header right row for buttons
        $rightRow = RowSection::make('edit-header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $rightRow->add(
            ButtonComponent::make('close-btn')
                ->icon('x')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $rightGrid->add($rightRow);

        // Create footer right grid
        $footerRightGrid = GridSection::make('edit-footer-right', 2)
            ->gap('sm');

        // Create footer right row for buttons
        $footerRightRow = RowSection::make('edit-footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');
        $footerRightRow->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightRow->add(
            ButtonComponent::make('save-btn')
                ->label('Save Changes')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing/:id', 'method' => 'PUT', 'color' => 'primary'])
        );


        $footerRightGrid->add($footerRightRow);

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('edit-aside-header')
                ->setCenter($centerGrid)
                ->setRight($rightGrid)
                ->variant('elevated')
                ->padding('md')
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('edit-aside-footer')
                ->setRight($footerRightGrid)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('edit-listing')

            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('edit-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build view listing aside
     */
    private static function buildViewListingAside($masterData)
    {
        // Use the dedicated ListingViewForm for read-only display
        $formComponent = ListingForm::make('view-listing-form', 'PUT', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('view-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header with title and action buttons
        $centerGrid = GridSection::make('view-header-center', 1)
            ->rows(2)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('title')
                ->content('Listing Details')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerGrid->add(
            TextComponent::make('subtitle')
                ->content('View property information')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid with action buttons
        $rightGrid = GridSection::make('view-header-right', 3)
            ->gap('sm');

        // Create header right row with action buttons
        $rightRow = RowSection::make('view-header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $rightRow->add(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->variant('outlined')
                ->meta(['action' => 'edit', 'tooltip' => 'Edit listing'])
        );
        $rightRow->add(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->meta(['action' => 'delete', 'color' => 'danger', 'tooltip' => 'Delete listing'])
        );
        $rightRow->add(
            ButtonComponent::make('close-btn')
                ->icon('x')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $rightGrid->add($rightRow);

        // Create footer right grid
        $footerRightGrid = GridSection::make('view-footer-right', 1)
            ->gap('sm');

        // Create footer right row for buttons
        $footerRightRow = RowSection::make('view-footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('close-footer-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightGrid->add($footerRightRow);

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('view-aside-footer')
                ->setRight($footerRightGrid)
                ->variant('elevated')
                ->padding('md')
        );

        // Build aside using DetailSection
        return DetailSection::make('view-listing')
            ->setHeader(
                SlotManager::make('view-header-slot')
                    ->setSection(
                        HeaderSection::make('view-aside-header')
                            ->setLeft($centerGrid)
                            ->setRight($rightGrid)
                            ->variant('elevated')
                            ->padding('md')
                    )
            )
            ->setMain(
                SlotManager::make('view-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build view listing aside fullscreen
     */
    private static function buildViewListingAsideFullscreen($masterData)
    {
        $asideData = self::buildViewListingAside($masterData);

        if (is_array($asideData)) {
            $asideData['name'] = 'view-listing-full';
        }

        return $asideData;
    }

    /**
     * Build comprehensive listing form component with all features
     * 
     * Returns a fully-featured listing form wrapped in a grid section for easy integration
     * into any layout (asides, modals, detail sections, etc.)
     *
     * @param string $formId Unique form identifier
     * @param string $method HTTP method (POST, PUT, PATCH)
     * @param string $submitUrl URL for form submission
     * @param array $masterData Master data for form dropdowns and options
     * @param string|null $dataUrl Optional URL to fetch existing data for editing
     * @param array $config Optional configuration overrides
     * @return GridSection Grid section containing the listing form
     */
    private static function buildListingFormComponent(
        string $formId = 'listing-form',
        string $method = 'POST',
        string $submitUrl = '/api/listing',
        array $masterData = [],
        ?string $dataUrl = null,
        array $config = []
    ): GridSection {
        // Default configuration
        $defaultConfig = [
            'columns' => 1,
            'gap' => 'md',
            'gridColumnSpan' => 1,
            'styling' => null,
            'view' => false,
        ];

        // Merge with provided config
        $config = array_merge($defaultConfig, $config);

        // Create grid container for the form
        $formGrid = GridSection::make("{$formId}-grid", $config['columns'])
            ->gap($config['gap'])
            ->rows(1);

        // Apply meta styling if provided
        if (isset($config['styling'])) {
            $formGrid->meta(['styling' => $config['styling']]);
        }

        // Create the listing form with all parameters
        $listingForm = $dataUrl
            ? ListingForm::make($formId, $method, $submitUrl, $masterData, $dataUrl)
            : ListingForm::make($formId, $method, $submitUrl, $masterData);

        // Apply column span
        $listingForm->gridColumnSpan($config['gridColumnSpan']);

        // Add form to grid
        $formGrid->add($listingForm);

        return $formGrid;
    }

    /**
     * Quick helper to create a create listing form
     *
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    private static function createListingForm(array $masterData = [], string $formId = 'create-listing-form'): GridSection
    {
        return self::buildListingFormComponent(
            formId: $formId,
            method: 'POST',
            submitUrl: '/api/listing',
            masterData: $masterData
        );
    }

    /**
     * Quick helper to create an edit listing form
     *
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    private static function editListingForm(array $masterData = [], string $formId = 'edit-listing-form'): GridSection
    {
        return self::buildListingFormComponent(
            formId: $formId,
            method: 'PUT',
            submitUrl: '/api/listing/:id',
            masterData: $masterData,
            dataUrl: '/api/listing/:id'
        );
    }

    /**
     * Quick helper to create a view-only listing form
     *
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    private static function viewListingForm(array $masterData = [], string $formId = 'view-listing-form'): GridSection
    {
        return self::buildListingFormComponent(
            formId: $formId,
            method: 'PUT',
            submitUrl: '/api/listing/:id',
            masterData: $masterData,
            dataUrl: '/api/listing/:id',
            config: ['view' => true]
        );
    }

    /**
     * Method tableComponent
     *
     * @return TableComponent
     */
    private static function tableComponent(): TableComponent
    {
        return TableComponent::make('info-table')
            ->dataUrl('/api/listing/:id/info')
            ->meta([
                'variant' => 'simple',
                'card' => false,
                'columns' => [
                    ['key' => 'property', 'label' => 'Property'],
                    ['key' => 'value', 'label' => 'Value'],
                ]
            ]);
    }

    /**
     * Method textComponent
     *
     * @return TextComponent
     */
    private static function textComponent(): TextComponent
    {
        return TextComponent::make('info-text')
            ->content('Property information will be displayed here')
            ->variant('body1')
            ->meta(['color' => 'text-gray-700']);
    }

    /**
     * Build left sidebar grid with notes and activity forms
     *
     * @return SlotManager
     */
    private static function buildLeftSidebarGrid(): SlotManager
    {
        $leftSlot = SlotManager::make('left-sidebar-slot');

        $leftGrid = GridSection::make('left-sidebar-grid', 1)
            ->gap('md')
            ->rows(2);

        // Notes section
        $leftGrid->add(
            TextComponent::make('notes-placeholder')
                ->content('Notes section')
                ->variant('body2')
                ->meta(['color' => 'text-gray-600'])
        );

        // Activity section
        $leftGrid->add(
            TextComponent::make('activity-placeholder')
                ->content('Activity tracking')
                ->variant('body2')
                ->meta(['color' => 'text-gray-600'])
        );

        return $leftSlot->setSection($leftGrid);
    }

    /**
     * Build right sidebar grid with timeline and history
     *
     * @return SlotManager
     */
    private static function buildRightSidebarGrid(): SlotManager
    {
        $rightGridSlot = SlotManager::make('right-sidebar-slot');
        $rightGrid = GridSection::make('right-sidebar-grid', 1)
            ->gap('md')
            ->rows(2);

        // Timeline component
        $rightGrid->add(
            TimelineComponent::make('listing-timeline')
                ->meta([
                    'card' => true,
                    'cardTitle' => 'Activity Timeline',
                    'cardIcon' => 'clock',
                    'variant' => 'default',
                    'items' => [
                        [
                            'id' => 1,
                            'title' => 'Listing Created',
                            'timestamp' => '2 hours ago',
                            'description' => 'Property listing was created',
                            'icon' => 'plus',
                            'iconColor' => '#10b981',
                        ],
                        [
                            'id' => 2,
                            'title' => 'Price Updated',
                            'timestamp' => '1 day ago',
                            'description' => 'Price changed to $500,000',
                            'icon' => 'cash',
                            'iconColor' => '#3b82f6',
                        ],
                    ],
                ])
        );

        // Chat/Comments history
        $rightGrid->add(
            ListComponent::make('listing-comments')
                ->items([
                    [
                        'id' => 1,
                        'title' => 'Interested buyer inquiry',
                        'description' => 'Great property, would like to schedule viewing',
                        'timestamp' => '3 hours ago',
                        'avatar' => ['name' => 'John Doe', 'color' => '#3b82f6'],
                    ],
                ])
                ->variant('default')
                ->meta([
                    'card' => true,
                    'cardTitle' => 'Comments & Inquiries',
                    'cardIcon' => 'message',
                ])
        );

        return $rightGridSlot->setSection($rightGrid);
    }

    /**
     * Build view listing with form and activity aside (fullscreen)
     *
     * Creates a comprehensive fullscreen aside with:
     * - Left column: Notes and activity forms
     * - Center column: Main Listing Form
     * - Right column: Timeline and comments history
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    private static function buildViewListingFormActivityAside($masterData)
    {
        // Use the reusable listing form component builder
        $listingFormGrid = self::buildListingFormComponent(
            formId: 'view-listing-activity-form',
            method: 'PUT',
            submitUrl: '/api/listing/:id',
            masterData: $masterData,
            dataUrl: '/api/listing/:id',
            config: [
                'columns' => 1,
                'gap' => 'md',
                'gridColumnSpan' => 1,
            ]
        );

        // Build aside using DetailSection
        return DetailSection::make('view-listing-form-activity-aside')
            ->setHeader(self::buildAsideHeaderWithGrid())
            ->setLeft(self::buildLeftSidebarGrid())
            ->setMain(
                SlotManager::make('main-slot')
                    ->setSection($listingFormGrid)
            )
            ->setRight(self::buildRightSidebarGrid())
            ->setFooter(self::buildAsideFooter())
            ->toArray();
    }

    /**
     * Build aside header with grid layout in center section
     *
     * @return SlotManager
     */
    private static function buildAsideHeaderWithGrid(): SlotManager
    {
        $headerSlot = SlotManager::make('header-slot');

        // Create grid section for center with 3 columns
        $leftGrid = GridSection::make('header-center-grid', 3)
            ->gap('md')
            ->alignItems('center');

        // Column 2: Title and Subtitle (create a nested grid for vertical stacking)
        $titleGrid = GridSection::make('title-section', 1)
            ->gap('xs')
            ->rows(2);

        $titleGrid->add(
            TextComponent::make('title')
                ->content('Listing Details')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $titleGrid->add(
            TextComponent::make('subtitle')
                ->content('View and manage property information')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $leftGrid->add($titleGrid);

        // Create right grid for action buttons
        $rightGrid = GridSection::make('header-right-grid', 1)
            ->gap('sm');

        // Create right section for action buttons
        $rightRow = RowSection::make('header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $rightRow->add(
            ButtonComponent::make('edit-header-btn')
                ->icon('pen')
                ->variant('outlined')
                ->meta(['action' => 'edit', 'tooltip' => 'Edit listing'])
        );

        $rightRow->add(
            ButtonComponent::make('share-header-btn')
                ->icon('share')
                ->variant('outlined')
                ->meta(['action' => 'share', 'tooltip' => 'Share listing'])
        );

        $rightRow->add(
            ButtonComponent::make('print-header-btn')
                ->icon('print')
                ->variant('outlined')
                ->meta(['action' => 'print', 'tooltip' => 'Print listing'])
        );

        $rightRow->add(
            ButtonComponent::make('delete-header-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->meta(['action' => 'delete', 'color' => 'danger', 'tooltip' => 'Delete listing'])
        );

        $rightRow->add(
            ButtonComponent::make('more-header-btn')
                ->icon('more')
                ->variant('outlined')
                ->meta(['action' => 'more', 'tooltip' => 'More options'])
        );

        $rightRow->add(
            ButtonComponent::make('close-header-btn')
                ->icon('x')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $rightGrid->add($rightRow);

        // Create header component with row in right section
        return $headerSlot->setSection(
            HeaderSection::make('aside-header')
                ->setCenter($leftGrid)
                ->setRight($rightGrid)
                ->variant('elevated')
                ->padding('md')
        );
    }

    /**
     * Build aside footer with grid sections for buttons
     */
    private static function buildAsideFooter(): SlotManager
    {
        $footerSlot = SlotManager::make('footer-slot');

        // Create left grid for help button
        $footerGrid = GridSection::make('footer-left-grid', 1)
            ->gap('sm')
            ->alignItems('center');

        $footerGrid->add(
            ButtonComponent::make('help-btn')
                ->label('Help')
                ->icon('help')
                ->variant('text')
                ->meta(['action' => 'help', 'tooltip' => 'Get help'])
        );

        // Create right grid for action buttons
        $footerRightGrid = GridSection::make('footer-right-grid', 2)
            ->gap('sm')
            ->alignItems('center');

        $footerRightRow = RowSection::make('footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('cancel-footer-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightRow->add(
            ButtonComponent::make('save-footer-btn')
                ->label('Save Changes')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'color' => 'primary'])
        );

        $footerRightGrid->add($footerRightRow);

        // Create footer component with grid sections
        return $footerSlot->setSection(
            FooterSection::make('aside-footer')
                ->setLeft($footerGrid)
                ->setRight($footerRightGrid)
                ->variant('elevated')
                ->padding('md')
        );
    }

    /**
     * Build create listing aside fullscreen
     */
    private static function buildCreateListingAsideFullscreen($masterData)
    {
        $formComponent = ListingForm::make('create-listing-aside-fs', 'POST', '/api/listing', $masterData);

        // Create main grid for form
        $mainGrid = GridSection::make('create-fs-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header
        $centerGrid = GridSection::make('create-fs-header-center', 1)
            ->rows(2)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('title')
                ->content('Create New Listing (Fullscreen)')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerGrid->add(
            TextComponent::make('subtitle')
                ->content('Add a new property with full editor')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $rightGrid = GridSection::make('create-fs-header-right', 1)
            ->gap('sm');

        $rightRow = RowSection::make('create-fs-header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $rightRow->add(
            ButtonComponent::make('close-btn')
                ->icon('x')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        // Create footer right row for buttons
        $footerRightRow = RowSection::make('create-fs-footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightGrid = GridSection::make('create-fs-footer-right', 1)
            ->gap('sm');

        $footerRightRow->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightRow->add(
            ButtonComponent::make('create-btn')
                ->label('Create Listing')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing', 'method' => 'POST', 'color' => 'primary'])
        );

        $footerRightGrid->add($footerRightRow);

        return DetailSection::make('create-listing-full')
            ->setHeader(
                SlotManager::make('create-fs-header-slot')
                    ->setSection(
                        HeaderSection::make('create-fs-aside-header')
                            ->setLeft($centerGrid)
                            ->setRight($rightGrid)
                            ->variant('elevated')
                            ->padding('md')
                    )
            )
            ->setMain(
                SlotManager::make('create-fs-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter(
                SlotManager::make('create-fs-footer-slot')
                    ->setSection(
                        FooterSection::make('create-fs-aside-footer')
                            ->setRight($footerRightGrid)
                            ->variant('elevated')
                            ->padding('md')
                    )
            )
            ->toArray();
    }

    /**
     * Build edit listing aside fullscreen
     */
    private static function buildEditListingAsideFullscreen($masterData)
    {
        $formComponent = ListingForm::make('edit-listing-aside-fs', 'PUT', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('edit-fs-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header
        $centerGrid = GridSection::make('edit-fs-header-center', 1)
            ->rows(2)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('title')
                ->content('Edit Listing (Fullscreen)')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerGrid->add(
            TextComponent::make('subtitle')
                ->content('Update property with full editor')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $rightGrid = GridSection::make('edit-fs-header-right', 1)
            ->gap('sm');

        $rightRow = RowSection::make('edit-fs-header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $rightRow->add(
            ButtonComponent::make('close-btn')
                ->icon('x')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $rightGrid->add($rightRow);

        // Create footer
        $footerRightGrid = GridSection::make('edit-fs-footer-right', 2)
            ->gap('sm');

        // Create footer right row for buttons
        $footerRightRow = RowSection::make('edit-fs-footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightRow->add(
            ButtonComponent::make('save-btn')
                ->label('Save Changes')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing/:id', 'method' => 'PUT', 'color' => 'primary'])
        );

        $footerRightGrid->add($footerRightRow);

        return DetailSection::make('edit-listing-full')
            ->setHeader(
                SlotManager::make('edit-fs-header-slot')
                    ->setSection(
                        HeaderSection::make('edit-fs-aside-header')
                            ->setLeft($centerGrid)
                            ->setRight($rightGrid)
                            ->variant('elevated')
                            ->padding('md')
                    )
            )
            ->setMain(
                SlotManager::make('edit-fs-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter(
                SlotManager::make('edit-fs-footer-slot')
                    ->setSection(
                        FooterSection::make('edit-fs-aside-footer')
                            ->setRight($footerRightGrid)
                            ->variant('elevated')
                            ->padding('md')
                    )
            )
            ->toArray();
    }

    // ========================================================================
    // DETAIL SECTION METHODS (GRID-BASED LAYOUT)
    // ========================================================================

    /**
     * Build create listing detail section (grid-based alternative to aside)
     *
     * @param array $masterData Master data for forms
     * @return array Detail section definition
     */
    private static function buildCreateListingDetail($masterData)
    {
        // Create main grid for form
        $mainGrid = GridSection::make('create-detail-main-grid', 1)
            ->rows(1)
            ->gap('lg');

        // Add listing form using reusable builder
        $mainGrid->add(
            self::createListingForm($masterData, 'create-listing-detail-form')
        );

        // Build header
        $headerSlot = SlotManager::make('header-slot');
        $centerGrid = GridSection::make('create-detail-header-center', 1)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('header-title')
                ->content('Create New Listing')
                ->variant('h3')
                ->meta(['fontWeight' => 'bold'])
        );
        $headerSlot->setSection(
            HeaderSection::make('create-detail-header')
                ->setCenter($centerGrid)
                ->variant('default')
                ->padding('lg')
        );

        // Build footer
        $footerSlot = SlotManager::make('footer-slot');
        $footerRightGrid = GridSection::make('create-detail-footer-right', 2)
            ->gap('sm');

        $footerRightRow = RowSection::make('create-detail-footer-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('cancel-detail-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'cancel'])
        );

        $footerRightRow->add(
            ButtonComponent::make('create-detail-btn')
                ->label('Create Listing')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing', 'method' => 'POST', 'color' => 'primary'])
        );

        $footerRightGrid->add($footerRightRow);

        $footerSlot->setSection(
            FooterSection::make('create-detail-footer')
                ->setRight($footerRightGrid)
                ->variant('default')
                ->padding('lg')
        );

        return DetailSection::make('create-listing-detail')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('create-detail-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build edit listing detail section (grid-based alternative to aside)
     *
     * @param array $masterData Master data for forms
     * @return array Detail section definition
     */
    private static function buildEditListingDetail($masterData)
    {
        // Create main grid for form
        $mainGrid = GridSection::make('edit-detail-main-grid', 1)
            ->rows(1)
            ->gap('lg');

        // Add listing form using reusable builder
        $mainGrid->add(
            self::editListingForm($masterData, 'edit-listing-detail-form')
        );

        // Build header
        $headerSlot = SlotManager::make('header-slot');
        $centerGrid = GridSection::make('edit-detail-header-center', 1)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('header-title')
                ->content('Edit Listing')
                ->variant('h3')
                ->meta(['fontWeight' => 'bold'])
        );
        $headerSlot->setSection(
            HeaderSection::make('edit-detail-header')
                ->setCenter($centerGrid)
                ->variant('default')
                ->padding('lg')
        );

        // Build footer
        $footerSlot = SlotManager::make('footer-slot');
        $footerRightGrid = GridSection::make('edit-detail-footer-right', 2)
            ->gap('sm');

        $footerRightRow = RowSection::make('edit-detail-footer-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('cancel-detail-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'cancel'])
        );

        $footerRightRow->add(
            ButtonComponent::make('save-detail-btn')
                ->label('Save Changes')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing/:id', 'method' => 'PUT', 'color' => 'primary'])
        );

        $footerRightGrid->add($footerRightRow);

        $footerSlot->setSection(
            FooterSection::make('edit-detail-footer')
                ->setRight($footerRightGrid)
                ->variant('default')
                ->padding('lg')
        );

        return DetailSection::make('edit-listing-detail')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('edit-detail-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build view listing detail section (grid-based alternative to aside)
     *
     * @param array $masterData Master data for forms
     * @return array Detail section definition
     */
    private static function buildViewListingDetail($masterData)
    {
        // Create main grid for form
        $mainGrid = GridSection::make('view-detail-main-grid', 1)
            ->rows(1)
            ->gap('lg');

        // Add listing form using reusable builder
        $mainGrid->add(
            self::viewListingForm($masterData, 'view-listing-detail-form')
        );

        // Build header
        $headerSlot = SlotManager::make('header-slot');
        $centerGrid = GridSection::make('view-detail-header-center', 1)
            ->gap('xs');
        $centerGrid->add(
            TextComponent::make('header-title')
                ->content('Listing Details')
                ->variant('h3')
                ->meta(['fontWeight' => 'bold'])
        );
        $headerSlot->setSection(
            HeaderSection::make('view-detail-header')
                ->setCenter($centerGrid)
                ->variant('default')
                ->padding('lg')
        );

        // Build footer
        $footerSlot = SlotManager::make('footer-slot');
        $footerRightGrid = GridSection::make('view-detail-footer-right', 1)
            ->gap('sm');

        $footerRightRow = RowSection::make('view-detail-footer-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('close-detail-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightGrid->add($footerRightRow);

        $footerSlot->setSection(
            FooterSection::make('view-detail-footer')
                ->setRight($footerRightGrid)
                ->variant('default')
                ->padding('lg')
        );

        return DetailSection::make('view-listing-detail')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('view-detail-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
