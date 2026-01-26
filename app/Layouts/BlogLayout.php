<?php

namespace App\Layouts;

use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Components\DrawerComponent;
use Litepie\Layout\Components\ModalComponent;
use App\Forms\Blog\BlogForm;
use App\Forms\Blog\BlogViewForm;

/**
 * BlogLayout
 * 
 * Comprehensive blog management layout following UserLayout patterns with:
 * - Header with page header, breadcrumbs, and statistics cards
 * - Filters and data table with sorting and pagination
 * - Create/Edit/View drawers
 * - Delete confirmation modals
 * 
 * @package App\Layouts
 */
class BlogLayout
{
    /**
     * Create blog management layout
     *
     * @param array $masterData Master data for dropdowns and options
     * @return LayoutBuilder
     */
    public static function make($masterData)
    {
        return LayoutBuilder::create('blogs', 'page')
            ->title('Blog Management')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/blogs-master-data',
                'description' => 'Blog Management System',
                'version' => '1.0.0',
                'refreshInterval' => null,
            ])
            ->section('header', fn($section) => self::buildHeaderSection($section))
            ->section('main', fn($section) => self::buildMainSection($section, $masterData))
            ->build();
    }

    /**
     * Build header section with breadcrumbs and statistics
     * 
     * Creates a responsive two-column layout:
     * - Left: Page header with breadcrumb navigation
     * - Right: Statistics cards grid with key metrics
     * 
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @return void
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

        // Left column: Page header with breadcrumbs (5 columns)
        $headerInfoGrid = $headerGrid->grid('header-info-column')
            ->columns(1)
            ->gap('md')
            ->gridColumnSpan(5);

        $headerInfoGrid->pageHeader('page-header')
            ->title('Blog Management')
            ->breadcrumbs([
                ['label' => 'Dashboard', 'link' => '/', 'icon' => 'LiHome'],
                ['label' => 'Blogs', 'active' => true, 'icon' => 'LiFileText'],
            ])
            ->align('left')
            ->spacing('md')
            ->titleVariant('h1')
            ->titleSize('2xl')
            ->titleWeight('bold')
            ->titleGutterBottom(true);

        // Right column: Statistics cards grid (7 columns)
        $statsGrid = $headerGrid->grid('stats-grid')
            ->columns(4)
            ->gap('lg')
            ->responsive(true)
            ->gridColumnSpan(7);

        self::buildStatsCard($statsGrid, 'stat-total-posts', 'Total Posts', '12', 'primary', 'LiList', '+12%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-published', 'Published', '8', 'success', 'LiListCheck', '+8%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-drafts', 'Drafts', '0', 'warning', 'LiPen', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-total-views', 'Total Views', '15', 'info', 'LiEyeOpen', '+15%', 'up', 'trend-1');
    }

    /**
     * Build a statistics card with icon, value, and trend indicator
     * 
     * @param \Litepie\Layout\Sections\GridSection $grid Parent grid section
     * @param string $id Unique card identifier
     * @param string $title Card title/label
     * @param string $value Primary metric value
     * @param string $color Theme color (primary, success, info, warning, error)
     * @param string $icon Lucide icon name
     * @param string $trend Trend percentage (e.g., '+12%')
     * @param string $trendDir Trend direction ('up', 'down', or 'neutral')
     * @return \Litepie\Layout\Components\CardComponent
     */
    private static function buildStatsCard($grid, string $id, string $title, string $value, string $color, string $icon, string $trend, string $trendDir, string $displayType)
    {
        // Theme color mapping for consistent styling
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
            ->content($value)
            ->variant('outlined')
            ->color($color)
            ->gridColumnSpan(3)
            ->meta([
                'icon' => $icon,
                'iconPosition' => 'top',
                'iconColor' => $colors['icon'],
                'iconSize' => 'md',
                'iconBgColor' => $colors['bg'],
                'trend' => $trend,
                'trendDirection' => $trendDir,
                'displayType' => $displayType,
            ]);
    }

    /**
     * Build main section with filters and data table
     * 
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @param array $masterData Master data for filters
     * @return void
     */
    private static function buildMainSection($section, $masterData)
    {
        $section->meta([
            'description' => 'Main content area with filters and data table',
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        // Create main grid
        $mainGrid = $section->grid('main-content-grid')->columns(1)->gap('md');

        // Create controls row with filters and actions
        $controlsRow = $mainGrid->grid('controls-row')
            ->columns(2)
            ->gap('md')
            ->meta(['styling' => 'bg-white rounded-lg shadow-sm p-4']);

        // Add filter column
        self::buildFilterColumn($controlsRow->row('filter-column')->gap('none'), $masterData);

        // Add action column
        self::buildActionColumn($controlsRow->row('actions-column')->gap('sm')->align('center')->justify('end'));

        // Add data table
        $mainGrid->row('table-row')->gap('none')->table('blogs-table')
            // ->asTable()
            ->dataUrl('/api/blogs')
            ->columns(self::getBlogTableColumns())
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->meta([
                'card' => true,
                'responsive' => true,
                'stickyHeader' => true,
                'variant' => 'outlined',
                'displayMode' => 'table',
                'rowClickable' => true,
                'rowActions' => ['type' => 'drawer', 'component' => 'view-blog-drawer', 'dataUrl' => '/api/blogs/:id'],
            ]);

        $mainGrid->row('table-row')->gap('none')->table('blogs-table-lists')
            ->dataUrl('/api/blogs')
            ->columns(self::getBlogTableListColumns())
            // ->asList()
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->meta([
                'card' => true,
                'responsive' => true,
                'stickyHeader' => true,
                'variant' => 'outlined',
                'displayMode' => 'list',
                'rowClickable' => true,
                'rowActions' => ['type' => 'drawer', 'component' => 'view-blog-drawer', 'dataUrl' => '/api/blogs/:id'],
            ]);
    }

    /**
     * Build blog list view component
     * 
     * @param mixed $grid Grid component
     * @return void
     */
    private static function buildBlogListView($grid)
    {
        // Use ListComponent with meta configuration for dynamic data
        $grid->row('list-row')->gap('none')->list('blogs-list')
            ->dense(false)
            ->disablePadding(false)
            ->meta([
                // Data configuration
                'dataUrl' => '/api/blogs',
                'dynamic' => true,
                'fetchOnMount' => true,

                // Item template configuration
                'itemTemplate' => self::getBlogListItemTemplate(),

                // Actions for each item
                'itemActions' => self::getBlogListItemActions(),

                // List behavior
                'selectable' => true,
                'multiSelect' => false,
                'hoverable' => true,
                'itemClickable' => true,
                'clickAction' => [
                    'type' => 'drawer',
                    'component' => 'view-blog-drawer',
                ],

                // Pagination
                'pagination' => true,
                'perPage' => 10,
                'paginationType' => 'standard', // standard, cursor, fast, optimized, cached

                // Layout
                'layout' => 'grid', // grid, list, compact, masonry
                'gridColumns' => 3,
                'gap' => 'md',

                // Styling
                'card' => true,
                'variant' => 'outlined',
                'elevation' => 1,

                // Display options
                'showImage' => true,
                'showMeta' => true,
                'showActions' => true,
                'showSelection' => true,

                // Empty state
                'emptyMessage' => 'No blog posts found',
                'emptyIcon' => 'LiFileText',
                'emptyAction' => [
                    'label' => 'Create Blog Post',
                    'icon' => 'LiPlus',
                    'component' => 'create-blog-drawer',
                    'componentType' => 'drawer',
                ],

                // Loading state
                'loadingMessage' => 'Loading blog posts...',
                'skeletonCount' => 6,
            ]);
    }

    /**
     * Get blog list item template configuration
     * 
     * @return array
     */
    private static function getBlogListItemTemplate(): array
    {
        return [
            'id' => '{{id}}',
            'title' => '{{title}}',
            'subtitle' => '{{excerpt}}',
            'description' => '{{content}}',
            'image' => '{{featured_image}}',
            'status' => '{{status}}',
            'category' => '{{category}}',
            'author' => '{{author.name}}',
            'date' => '{{published_at}}',
            'views' => '{{views_count}}',
            'likes' => '{{likes_count}}',
            'badge' => '{{status}}',
            'icon' => 'LiFileText',
        ];
    }

    /**
     * Get blog list item actions configuration
     * 
     * @return array
     */
    private static function getBlogListItemActions(): array
    {
        return [
            [
                'type' => 'button',
                'name' => 'view',
                'icon' => 'LiEyeOpen',
                'variant' => 'outlined',
                'size' => 'sm',
                'color' => 'primary',
                'tooltip' => 'View Details',
                'action' => 'open',
                'component' => 'view-blog-drawer',
                'componentType' => 'drawer',
            ],
            [
                'type' => 'button',
                'name' => 'edit',
                'icon' => 'LiEdit',
                'variant' => 'outlined',
                'size' => 'sm',
                'color' => 'primary',
                'tooltip' => 'Edit',
                'action' => 'open',
                'component' => 'edit-blog-drawer',
                'componentType' => 'drawer',
            ],
            [
                'type' => 'button',
                'name' => 'delete',
                'icon' => 'LiTrash',
                'variant' => 'outlined',
                'size' => 'sm',
                'color' => 'danger',
                'tooltip' => 'Delete',
                'action' => 'open',
                'component' => 'delete-blog-modal',
                'componentType' => 'modal',
            ],
        ];
    }

    /**
     * Build filter column
     * 
     * @param mixed $row Row component
     * @param array $masterData Master data
     * @return void
     */
    private static function buildFilterColumn($row, $masterData)
    {
        $row->filter('blogs-filter')
            ->addQuickFilter('search', 'Search', 'text')
            ->addQuickFilter('status', 'Status', 'select', array_merge(
                [['value' => '', 'label' => 'All Statuses']],
                [
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'review', 'label' => 'In Review'],
                    ['value' => 'published', 'label' => 'Published'],
                    ['value' => 'archived', 'label' => 'Archived'],
                ]
            ))
            ->addQuickFilter('category', 'Category', 'select', array_merge(
                [['value' => '', 'label' => 'All Categories']],
                $masterData['categories'] ?? []
            ))
            ->addSelectFilter('status', 'Status', [
                ['value' => 'draft', 'label' => 'Draft'],
                ['value' => 'review', 'label' => 'In Review'],
                ['value' => 'published', 'label' => 'Published'],
                ['value' => 'archived', 'label' => 'Archived'],
            ])
            ->addMultiSelectFilter('category', 'Category', $masterData['categories'] ?? [])
            ->addDateRangeFilter('published_at', 'Published Date')
            ->collapsible()
            ->collapsed(true)
            ->showActiveCount()
            ->rememberFilters(true, 'blogs_filter')
            ->liveFilter(true, 300)
            ->submitAction('/api/blogs');
    }

    /**
     * Build action column with buttons
     * 
     * @param mixed $row Row component
     * @return void
     */
    private static function buildActionColumn($row)
    {
        // $row->button('filter-toggle-btn')
        //     ->label('Filter')
        //     ->icon('LiFilter')
        //     ->size('md')
        //     ->variant('outline')
        //     ->meta(['tooltip' => 'Toggle filter panel', 'action' => 'toggle-filter']);

        $row->button('refresh-btn')
            ->label('Refresh')
            ->icon('LiRefresh')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Refresh data']);

        $row->button('create-btn')
            ->label('Create')
            ->icon('LiPlus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'drawer')
            ->data('component', 'create-blog-drawer')
            ->data('action', 'open')
            ->meta(['tooltip' => 'Create new blog']);

        $row->button('create-btn-modal')
            ->label('Create Modal View')
            ->icon('LiPlus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-blog-modal')
            ->data('action', 'open')
            ->meta(['tooltip' => 'Create new blog']);

        $row->button('export-btn')
            ->label('Export')
            ->icon('LiDownloadCloud')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Export data']);

        // View mode toggle button group (Table, Board, List)
        // $row->button('table-view-btn')
        //     ->label('')
        //     ->icon('LiTable')
        //     ->size('md')
        //     ->variant('outlined')
        //     ->data('view-mode', 'table')
        //     ->data('action', 'switch-view')
        //     ->data('group', 'view-mode-group')
        //     ->meta([
        //         'tooltip' => 'Table View',
        //         'active' => true,
        //         'group' => 'view-mode-group',
        //         'groupPosition' => 'first'
        //     ]);

        // $row->button('board-view-btn')
        //     ->label('')
        //     ->icon('LiColumns')
        //     ->size('md')
        //     ->variant('outlined')
        //     ->data('view-mode', 'board')
        //     ->data('action', 'switch-view')
        //     ->data('group', 'view-mode-group')
        //     ->meta([
        //         'tooltip' => 'Board View (Kanban)',
        //         'group' => 'view-mode-group',
        //         'groupPosition' => 'middle'
        //     ]);

        // $row->button('list-view-btn')
        //     ->label('')
        //     ->icon('LiList')
        //     ->size('md')
        //     ->variant('outlined')
        //     ->data('view-mode', 'list')
        //     ->data('action', 'switch-view')
        //     ->data('group', 'view-mode-group')
        //     ->meta([
        //         'tooltip' => 'List View',
        //         'group' => 'view-mode-group',
        //         'groupPosition' => 'last'
        //     ]);

        $row->button('more-btn-icon')
            ->label('')
            ->icon('LiChevronDown')
            ->size('md')
            ->variant('outline')
            ->dropdown([
                'id' => 'more-options',
                'placement' => 'bottom-end',
                'iconOnly' => true,
                'offset' => [0, 8],
                'closeOnClick' => true,
                'defaultIcon' => current(self::getMoreOptionsDropdownIconItems())['icon'] ?? null,
                'items' => self::getMoreOptionsDropdownIconItems(),
            ])
            ->meta(['tooltip' => 'More options']);

        $row->button('more-btn')
            ->label('')
            ->icon('LiChevronDown')
            ->size('md')
            ->variant('outline')
            ->dropdown([
                'id' => 'more-options',
                'placement' => 'bottom-end',
                'offset' => [0, 8],
                'closeOnClick' => true,
                'items' => self::getMoreOptionsDropdownItems(),
            ])
            ->meta(['tooltip' => 'More options']);
    }

    /**
     * Get dropdown items for "More Options" button
     * 
     * @return array
     */
    private static function getMoreOptionsDropdownItems()
    {
        return [
            self::buildDropdownButton('bulk-publish', 'Bulk Publish', 'LiCheckFull', 'bulk-publish'),
            self::buildDropdownButton('bulk-delete', 'Bulk Delete', 'LiBin', 'bulk-delete'),
            self::buildDropdownDivider(),
            self::buildDropdownButton('settings', 'Settings', 'LiSettings', 'settings'),
        ];
    }

    private static function getMoreOptionsDropdownIconItems()
    {
        return [
            self::buildDropdownButton(id: 'list-view', label: '', icon: 'LiList', action: 'list-view', color: 'primary', value: 'list'),
            self::buildDropdownButton(id: 'table-view', label: '', icon: 'LiCols', action: 'table-view', color: 'primary', value: 'table'),
        ];
    }

    /**
     * Build dropdown button item
     * 
     * @param string $id Button identifier
     * @param string $label Button label
     * @param string $icon Icon name
     * @param string $action Action to trigger
     * @param string $color Optional color (primary, danger, etc.)
     * @param string $value Optional value
     * @return array
     */
    private static function buildDropdownButton(string $id, string $label, string $icon, string $action, string $color = null, string $value = null)
    {
        $item = [
            'id' => $id,
            'label' => $label,
            'icon' => $icon,
            'action' => $action,
            'color' => $color,
            'type' => 'button',
            'value' => $value,
        ];

        if ($color) {
            $item['color'] = $color;
        }

        return $item;
    }

    /**
     * Build dropdown divider
     * 
     * @return array
     */
    private static function buildDropdownDivider()
    {
        return ['type' => 'divider'];
    }

    /**
     * Get blog table columns
     * 
     * @return array
     */
    private static function getBlogTableColumns()
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'category', 'label' => 'Category', 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => 'Author', 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => 'Views', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => [
                [
                    'type' => 'button',
                    'name' => 'view',
                    'icon' => 'LiEyeOpen',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'primary',
                    'tooltip' => 'View Details',
                    'dataAttributes' => ['type' => 'drawer', 'component' => 'view-blog-drawer', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'edit',
                    'icon' => 'LiEdit',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'primary',
                    'tooltip' => 'Edit',
                    'dataAttributes' => ['type' => 'drawer', 'component' => 'edit-blog-drawer', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'delete',
                    'icon' => 'LiTrash',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'danger',
                    'tooltip' => 'Delete',
                    'dataAttributes' => ['type' => 'modal', 'component' => 'delete-blog-modal', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
            ]],
        ];
    }

    private static function getBlogTableListColumns()
    {
        return [
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'category', 'label' => 'Category', 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => 'Author', 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => 'Views', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => [
                [
                    'type' => 'button',
                    'name' => 'view',
                    'icon' => 'LiEyeOpen',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'primary',
                    'tooltip' => 'View Details',
                    'dataAttributes' => ['type' => 'drawer', 'component' => 'view-blog-drawer', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'edit',
                    'icon' => 'LiEdit',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'primary',
                    'tooltip' => 'Edit',
                    'dataAttributes' => ['type' => 'drawer', 'component' => 'edit-blog-drawer', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'delete',
                    'icon' => 'LiTrash',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'danger',
                    'tooltip' => 'Delete',
                    'dataAttributes' => ['type' => 'modal', 'component' => 'delete-blog-modal',  'action' =>  'open'],
                    'dataKey' =>  'id',
                ],
            ]],
        ];
    }

    /**
     * Build drawers section with all drawer components
     * 
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @param array $masterData
     * @return void
     */
    private static function buildDrawersSection($section, $masterData)
    {
        self::buildComponentInSection($section, 'drawer', 'view-blog-drawer', $masterData);
        self::buildComponentInSection($section, 'drawer', 'view-blog-drawer-fullscreen', $masterData);
        self::buildComponentInSection($section, 'drawer', 'create-blog-drawer', $masterData);
        self::buildComponentInSection($section, 'drawer', 'create-blog-drawer-fullscreen', $masterData);
        self::buildComponentInSection($section, 'drawer', 'edit-blog-drawer', $masterData);
        self::buildComponentInSection($section, 'drawer', 'edit-blog-drawer-fullscreen', $masterData);
    }

    /**
     * Build modals section with all modal components
     * 
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @param array $masterData
     * @return void
     */
    private static function buildModalsSection($section, $masterData)
    {
        self::buildComponentInSection($section, 'modal', 'create-blog-modal', $masterData);
        self::buildComponentInSection($section, 'modal', 'delete-blog-modal', $masterData);
    }

    /**
     * Build component directly in a section (works for both modals and drawers)
     * This is a unified method that accepts a section and builds any component type
     *
     * @param mixed $section The section object to build the component in
     * @param string $type The component type ('modal' or 'drawer')
     * @param string $component The component name
     * @param array $masterData Master data for form options
     * @return void
     */
    private static function buildComponentInSection($section, $type, $component, $masterData)
    {
        $definition = self::getComponentDefinition($type, $component, $masterData);

        if (!$definition) {
            return;
        }

        // Add the component definition as a custom component with raw array data
        $customComponent = \Litepie\Layout\Components\CustomComponent::make($component, $type)
            ->data($definition);

        $section->addComponent($customComponent);
    }

    /**
     * Get component definition for modals and drawers
     * Called dynamically when components are requested
     * 
     * @param string $type Component type ('modal' or 'drawer')
     * @param string $componentName Component identifier
     * @param array $masterData Master data for forms
     * @return array|null Component definition
     */
    public static function getComponentDefinition($type, $componentName, $masterData)
    {
        if ($type === 'modal') {
            switch ($componentName) {
                case 'create-blog-modal':
                    return self::buildCreateBlogModal($masterData);
                case 'delete-blog-modal':
                    return self::buildDeleteBlogModal();
            }
        }

        if ($type === 'drawer') {
            switch ($componentName) {
                case 'view-blog-drawer':
                    return self::buildViewBlogDrawer($masterData);
                case 'view-blog-drawer-fullscreen':
                    return self::buildViewBlogDrawerFullscreen($masterData);
                case 'create-blog-drawer':
                    return self::buildCreateBlogDrawer($masterData);
                case 'create-blog-drawer-fullscreen':
                    return self::buildCreateBlogDrawerFullscreen($masterData);
                case 'edit-blog-drawer':
                    return self::buildEditBlogDrawer($masterData);
                case 'edit-blog-drawer-fullscreen':
                    return self::buildEditBlogDrawerFullscreen($masterData);
            }
        }

        return null;
    }

    /**
     * Build create blog modal
     */
    private static function buildCreateBlogModal($masterData)
    {
        $formComponent = BlogForm::make('create-blog-form-modal', 'POST', '/api/blogs', $masterData);

        return ModalComponent::make('create-blog-modal')
            ->children([
                [
                    'type' => 'header',
                    'title' => 'Create New Blog Post',
                    'icon' => 'LiFileText',
                ],
                [
                    'type' => 'footer',
                    'buttonGroup' => [
                        'buttons' => [
                            ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                            ['label' => 'Create Post', 'type' => 'submit', 'color' => 'primary', 'icon' => 'LiCheck', 'dataUrl' => '/api/blogs', 'method' => 'POST'],
                        ],
                    ],
                ],
                $formComponent
            ])
            ->ariaLabelledby('create-blog-modal-title')
            ->toArray();
    }

    /**
     * Build delete blog modal
     */
    private static function buildDeleteBlogModal()
    {
        return ModalComponent::make('delete-blog-modal')
            ->children([
                [
                    'type' => 'header',
                    'title' => 'Delete Blog Post',
                    'icon' => 'LiTrash',
                    'color' => 'danger',
                ],
                [
                    'type' => 'footer',
                    'buttonGroup' => [
                        'buttons' => [
                            ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                            ['label' => 'Delete', 'color' => 'danger', 'icon' => 'LiTrash', 'dataUrl' => '/api/blogs/:id', 'method' => 'DELETE'],
                        ],
                    ],
                ],
            ])
            ->ariaLabelledby('delete-blog-modal-title')
            ->ariaDescribedby('delete-blog-modal-description')
            ->toArray();
    }

    /**
     * Build create blog drawer
     */
    private static function buildCreateBlogDrawer($masterData)
    {
        $formComponent = BlogForm::make('create-blog-form', 'POST', '/api/blogs', $masterData);

        return DrawerComponent::make('create-blog-drawer')
            ->anchor('right')
            ->width('800px')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->header([
                'title' => 'Create New Blog Post',
                'subtitle' => 'Add a new blog post to your collection',
                'icon' => 'LiPlus',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Create Post', 'type' => 'submit', 'color' => 'primary', 'icon' => 'LiCheck', 'dataUrl' => '/api/blogs', 'method' => 'POST'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build edit blog drawer
     */
    private static function buildEditBlogDrawer($masterData)
    {
        $formComponent = BlogForm::make('edit-blog-form', 'PUT', '/api/blogs/:id', $masterData, '/api/blogs/:id');

        return DrawerComponent::make('edit-blog-drawer')
            ->anchor('right')
            ->width('800px')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->header([
                'title' => 'Edit Blog Post',
                'subtitle' => 'Update blog post information',
                'icon' => 'LiEdit',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Update Post', 'type' => 'submit', 'color' => 'success', 'icon' => 'LiCheck', 'dataUrl' => '/api/blogs/:id', 'method' => 'PUT'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build view blog drawer
     */
    private static function buildViewBlogDrawer($masterData)
    {
        // Use the dedicated BlogViewForm for read-only display
        $formComponent = BlogViewForm::make('view-blog-form', $masterData, '/api/blogs/:id');

        // Build drawer using DrawerComponent with the BlogViewForm
        return \Litepie\Layout\Components\DrawerComponent::make('view-blog-drawer')
            ->anchor('right')
            ->width('900px')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->header([
                'title' => 'Blog Post Details',
                'subtitle' => 'View complete blog post information',
                'icon' => 'LiFileText',
                'actions' => [
                    [
                        'type' => 'chip',
                        'label' => 'Published',
                        'color' => 'success',
                        'size' => 'sm',
                        'icon' => 'LiCheckCircle',
                    ],
                    [
                        'type' => 'drawer',
                        'actionType' => 'drawer',
                        'label' => 'Edit',
                        'icon' => 'LiPen',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'primary',
                        'action' => 'edit',
                        'tooltip' => 'Edit Blog Post',
                        'component' => 'edit-blog-drawer-fullscreen',
                    ],
                    [
                        'type' => 'button',
                        'label' => 'Delete',
                        'icon' => 'LiBinEmpty',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'error',
                        'action' => 'delete',
                        'tooltip' => 'Delete Blog Post',
                        'confirm' => [
                            'title' => 'Delete Blog Post',
                            'message' => 'Are you sure you want to delete this blog post? This action cannot be undone.',
                            'confirmText' => 'Delete',
                            'cancelText' => 'Cancel',
                            'action' => 'delete',
                            'dataUrl' => '/api/blogs/:id',
                            'method' => 'delete',
                        ],
                    ],
                ],
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Close', 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close'],
                    ['label' => 'View Fullscreen', 'color' => 'primary', 'icon' => 'LiExpand', 'type' => 'drawer', 'component' => 'view-blog-drawer-fullscreen', 'action' => 'view'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build view blog drawer fullscreen
     */
    private static function buildViewBlogDrawerFullscreen($masterData)
    {
        $drawerData = self::buildViewBlogDrawer($masterData);

        if (is_array($drawerData)) {
            $drawerData['name'] = 'view-blog-drawer-fullscreen';
            $drawerData['width'] = '100vw';
            $drawerData['height'] = '100vh';
        }

        return $drawerData;
    }

    /**
     * Build create blog drawer fullscreen
     */
    private static function buildCreateBlogDrawerFullscreen($masterData)
    {
        $formComponent = BlogForm::make('create-blog-drawer-fs', 'POST', '/api/blogs', $masterData);

        return DrawerComponent::make('create-blog-drawer-fullscreen')
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->backdrop(true)
            ->closeOnBackdrop(false)
            ->header([
                'title' => 'Create New Blog Post (Fullscreen)',
                'subtitle' => 'Add a new blog post with full editor',
                'icon' => 'LiPlus',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Create Post', 'type' => 'submit', 'color' => 'primary', 'icon' => 'LiCheck', 'dataUrl' => '/api/blogs', 'method' => 'POST'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build edit blog drawer fullscreen
     */
    private static function buildEditBlogDrawerFullscreen($masterData)
    {
        $formComponent = BlogForm::make('edit-blog-drawer-fs', 'PUT', '/api/blogs/:id', $masterData, '/api/blogs/:id');

        return DrawerComponent::make('edit-blog-drawer-fullscreen')
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->backdrop(true)
            ->closeOnBackdrop(false)
            ->header([
                'title' => 'Edit Blog Post (Fullscreen)',
                'subtitle' => 'Update blog post with full editor',
                'icon' => 'LiEdit',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Update Post', 'type' => 'submit', 'color' => 'success', 'icon' => 'LiCheck', 'dataUrl' => '/api/blogs/:id', 'method' => 'PUT'],
                ],
            ])
            ->toArray();
    }
}
