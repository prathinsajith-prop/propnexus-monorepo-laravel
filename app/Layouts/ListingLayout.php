<?php

namespace App\Layouts;

use App\Forms\Listing\ListingForm;
use App\Forms\Listing\ListingViewForm;
use App\Slots\Listing\ListingCreateAsideSlot;
use App\Slots\Listing\ListingDetailSlot;
use App\Slots\Listing\ListingEditAsideSlot;
use App\Slots\Listing\ListingFooterSlot;
use App\Slots\Listing\ListingHeaderSlot;
use App\Slots\Listing\ListingViewAsideSlot;
use App\Slots\Shared\ModalSlot;
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
                'actions' => [
                    self::buildViewButtonAction(),
                    self::buildEditButtonAction(),
                    self::buildDeleteButtonAction(),
                ],
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
                    'type' => 'body',
                    'content' => 'Are you sure you want to delete this listing? This action cannot be undone.',
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
     * Build view button action using ButtonComponent
     *
     * @return array
     */
    private static function buildViewButtonAction()
    {
        $button = ButtonComponent::make('view')
            ->icon('eyeopen')
            ->variant('outlined')
            ->size('sm')
            ->isIconButton(true)
            ->data('type', 'aside')
            ->data('component', 'view-listing-aside')
            ->data('action', 'open')
            ->meta([
                'tooltip' => 'View Details',
            ])
            ->toArray();

        // Add dataKey for table row binding
        $button['dataKey'] = 'id';

        return $button;
    }

    /**
     * Build edit button action using ButtonComponent
     *
     * @return array
     */
    private static function buildEditButtonAction()
    {
        $button = ButtonComponent::make('edit')
            ->icon('editpen')
            ->variant('outlined')
            ->size('sm')
            ->isIconButton(true)
            ->data('type', 'aside')
            ->data('component', 'edit-listing')
            ->data('action', 'open')
            ->meta([
                'tooltip' => 'Edit Listing',
            ])
            ->toArray();

        // Add dataKey for table row binding
        $button['dataKey'] = 'id';

        return $button;
    }

    /**
     * Build delete button action using ButtonComponent
     *
     * @return array
     */
    private static function buildDeleteButtonAction()
    {
        $button = ButtonComponent::make('delete')
            ->icon('binempty')
            ->variant('outlined')
            ->size('sm')
            ->color('danger')
            ->isIconButton(true)
            ->data('type', 'modal')
            ->data('component', 'delete-listing-modal')
            ->data('action', 'open')
            ->meta([
                'tooltip' => 'Delete',
            ])
            ->toArray();

        // Add dataKey for table row binding
        $button['dataKey'] = 'id';

        return $button;
    }

    /**
     * Build create listing aside
     */
    private static function buildCreateListingAside($masterData)
    {
        return ListingCreateAsideSlot::make($masterData);
    }

    /**
     * Build create listing aside fullscreen
     */
    private static function buildCreateListingAsideFullscreen($masterData)
    {
        return ListingCreateAsideSlot::make($masterData, true);
    }

    /**
     * Build edit listing aside
     */
    private static function buildEditListingAside($masterData)
    {
        return ListingEditAsideSlot::make($masterData);
    }

    /**
     * Build view listing aside
     */
    private static function buildViewListingAside($masterData)
    {
        return ListingViewAsideSlot::make($masterData);
    }

    /**
     * Build view listing aside fullscreen
     */
    private static function buildViewListingAsideFullscreen($masterData)
    {
        return ListingViewAsideSlot::make($masterData, true);
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
        return ListingHeaderSlot::make();
    }

    /**
     * Build aside footer with grid sections for buttons
     */
    private static function buildAsideFooter(): SlotManager
    {
        return ListingFooterSlot::make();
    }

    /**
     * Build edit listing aside fullscreen
     */
    private static function buildEditListingAsideFullscreen($masterData)
    {
        return ListingEditAsideSlot::make($masterData, true);
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
        return ListingDetailSlot::createDetail($masterData);
    }

    /**
     * Build edit listing detail section (grid-based alternative to aside)
     *
     * @param array $masterData Master data for forms
     * @return array Detail section definition
     */
    private static function buildEditListingDetail($masterData)
    {
        return ListingDetailSlot::editDetail($masterData);
    }

    /**
     * Build view listing detail section (grid-based alternative to aside)
     *
     * @param array $masterData Master data for forms
     * @return array Detail section definition
     */
    private static function buildViewListingDetail($masterData)
    {
        return ListingDetailSlot::viewDetail($masterData);
    }
}
