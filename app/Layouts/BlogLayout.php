<?php

namespace App\Layouts;

use App\Forms\Blog\BlogChatForm;
use App\Forms\Blog\BlogFollowUpsForm;
use App\Forms\Blog\BlogForm;
use App\Forms\Blog\BlogNotesForm;
use App\Forms\Blog\BlogViewForm;
use App\Slots\Blog\BlogCreateAsideSlot;
use App\Slots\Blog\BlogDetailSlot;
use App\Slots\Blog\BlogEditAsideSlot;
use App\Slots\Blog\BlogFooterSlot;
use App\Slots\Blog\BlogFormActivityAsideSlot;
use App\Slots\Blog\BlogHeaderSlot;
use App\Slots\Blog\BlogLeftSidebarSlot;
use App\Slots\Blog\BlogMainContentSlot;
use App\Slots\Blog\BlogRightSidebarSlot;
use App\Slots\Blog\BlogViewAsideSlot;
use App\Slots\Shared\ModalSlot;
use Litepie\Layout\Components\AlertComponent;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\DividerComponent;
use Litepie\Layout\Components\DocumentComponent;
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
 * BlogLayout
 *
 * Comprehensive blog management layout following UserLayout patterns with:
 * - Header with page header, breadcrumbs, and statistics cards
 * - Filters and data table with sorting and pagination
 * - Create/Edit/View asides
 * - Delete confirmation modals
 * 
 * @package App\Layouts
 */
class BlogLayout
{
    // Default Card Config
    private const DEFAULT_CARD_COLLAPSIBLE = true;

    private const MAX_CARD_HEIGHT = '500px';

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
            ->section('search', fn($section) => self::buildSearchComponent($section, $masterData))
            ->section('actions', fn($section) => self::buildActionsComponent($section, $masterData))
            ->section('footer', fn($section) => self::buildFooterSection($section))
            ->build();
    }

    /**
     * Build content component with both form and cards
     *
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @param array $masterData
     * @return void
     */
    private static function buildContentComponent($section, $masterData)
    {
        $section->meta([
            'description' => 'Content section with form and cards',
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        // Create a grid with 2 columns: left for form, right for cards
        $contentGrid = $section->grid('content-grid', 2, 1)
            ->gap('xl')
            ->meta([
                'styling' => 'h-full',
            ]);

        // LEFT COLUMN: Blog Form
        $leftCol = $contentGrid->grid('form-column', 1, 1)
            ->gap('md')
            ->meta([
                'styling' => 'h-full flex flex-col',
                'gridColumn' => '1',
            ]);
        $formComponent = \App\Forms\Blog\BlogForm::make('blog-content-form', 'POST', '/api/blogs', $masterData);
        $leftCol->card('blog-form-card')
            ->title('Blog Form')
            ->variant('outlined')
            ->meta([
                'collapsible' => true,
                'defaultExpanded' => true,
                'icon' => 'documentfull',
                'content' => $formComponent,
            ]);

        // RIGHT COLUMN: Cards (Activity History, Chat History)
        $rightCol = $contentGrid->grid('cards-column', 1, 2)
            ->gap('md')
            ->meta([
                'styling' => 'h-full flex flex-col space-y-4 overflow-y-auto',
                'gridColumn' => '2',
            ]);
        self::buildActivityHistoryCard($rightCol);
        self::buildChatHistoryCard($rightCol);
    }

    /**
     * Build header section with breadcrumbs and statistics.
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
                ['label' => 'Dashboard', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Blogs', 'active' => true, 'icon' => 'documentfull'],
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

        self::buildStatsCard($statsGrid, 'stat-total-posts', 'Total Posts', '12', 'primary', 'list', '+12%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-published', 'Published', '8', 'success', 'listcheck', '+8%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-drafts', 'Drafts', '0', 'warning', 'pen', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-total-views', 'Total Views', '15', 'info', 'eyeopen', '+15%', 'up', 'trend-1');
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
        $mainGrid = $section->grid('main-content-grid', 1)
            ->gap('md');

        // Add table view
        $mainGrid->row('table-row')->gap('none')->table('blogs-table')
            ->asTable()
            ->dataUrl('/api/blogs')
            ->columns(self::getBlogTableColumns())
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-blog-forms-full',
                dataUrl: '/api/blogs/:id',
                config: [
                    'width' => '100vw',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                    'componentType' => 'aside',
                ]
            )
            ->meta([
                'card' => true,
                'responsive' => true,
                'stickyHeader' => true,
                'variant' => 'outlined',
            ]);

        // Add list view
        $mainGrid->row('table-row')->gap('none')->table('blogs-table-lists')
            ->dataUrl('/api/blogs')
            ->columns(self::getBlogTableListColumns())
            ->asList()
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->clickableRows(
                type: 'aside',
                component: 'view-blog-forms-full',
                dataUrl: '/api/blogs/:id',
                config: [
                    'width' => '100vw',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                    'componentType' => 'aside',
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
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @param array $masterData Master data for filters
     * @return void
     */
    private static function buildSearchComponent($section, $masterData)
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
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @param array $masterData Master data
     * @return void
     */
    private static function buildActionsComponent($section, $masterData)
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
     * @param \Litepie\Layout\Sections\LayoutSection $section
     * @return void
     */
    private static function buildFooterSection($section)
    {
        $section->meta([
            'description' => 'Page footer with copyright and links',
            'styling' => 'container mx-auto px-4 py-6 border-t border-gray-200',
        ]);

        // Create footer grid with 2 columns: left for copyright, right for links
        $footerGrid = $section->grid('footer-grid')
            ->columns(2)
            ->gap('md')
            ->responsive(true);

        // Left column: Copyright and version info
        $footerGrid->row('copyright-row')
            ->gap('sm')
            ->align('center')
            ->justify('start')
            ->text('copyright-text')
            ->content('© 2026 Blog Management System. All rights reserved.')
            ->size('sm')
            ->color('text-secondary');

        // Right column: Footer links
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
        //     ->icon('filter')
        //     ->size('md')
        //     ->variant('outline')
        //     ->meta(['tooltip' => 'Toggle filter panel', 'action' => 'toggle-filter']);

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
            ->data('component', 'create-blog')
            ->data('action', 'open')
            ->data('config', [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ])
            ->meta(['tooltip' => 'Create new blog']);

        $row->button('create-btn-modal')
            ->label('Create Modal View')
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-blog-modal')
            ->data('action', 'open')
            ->meta(['tooltip' => 'Create new blog']);

        $row->button('export-btn')
            ->label('Export')
            ->icon('downloadcloud')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Export data']);

        // View mode toggle button group (Table, Board, List)
        // $row->button('table-view-btn')
        //     ->label('')
        //     ->icon('table')
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
        //     ->icon('columns')
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
        //     ->icon('list')
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
            ->icon('chevrondown')
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
            ->icon('chevrondown')
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
            self::buildDropdownButton('bulk-publish', 'Bulk Publish', 'checkfull', 'bulk-publish'),
            self::buildDropdownButton('bulk-delete', 'Bulk Delete', 'binempty', 'bulk-delete'),
            self::buildDropdownDivider(),
            self::buildDropdownButton('settings', 'Settings', 'settings', 'settings'),
        ];
    }

    private static function getMoreOptionsDropdownIconItems()
    {
        return [
            self::buildDropdownButton(id: 'list-view', label: '', icon: 'list', action: 'list-view', color: 'primary', value: 'list'),
            self::buildDropdownButton(id: 'table-view', label: '', icon: 'cols', action: 'table-view', color: 'primary', value: 'table'),
        ];
    }

    /**
     * Build dropdown button item using ButtonComponent
     * 
     * @param string $id Button identifier
     * @param string $label Button label
     * @param string $icon Icon name
     * @param string $action Action to trigger
     * @param string $color Optional color (primary, danger, etc.)
     * @param string $value Optional value
     * @return array
     */
    private static function getBlogTableColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'category', 'label' => 'Category', 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => 'Author', 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => 'Views', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getTableActions()],
        ];
    }

    /**
     * Get blog table list columns configuration
     *
     * @return array Column definitions for list view
     */
    private static function getBlogTableListColumns(): array
    {
        return [
            ['key' => 'title', 'label' => 'Title', 'sortable' => true, 'filterable' => true, 'filter_key' => 'title'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'category', 'label' => 'Category', 'sortable' => true, 'filterable' => true, 'filter_key' => 'category'],
            ['key' => 'author', 'label' => 'Author', 'sortable' => true, 'width' => '120px'],
            ['key' => 'views_count', 'label' => 'Views', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true, 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => self::getTableActions()],
        ];
    }

    /**
     * Get table action buttons (view, edit, delete)
     *
     * @return array Action button configurations
     */
    private static function getTableActions(): array
    {
        return [
            self::buildViewButtonAction(),
            self::buildEditButtonAction(),
            self::buildDeleteButtonAction(),
        ];
    }

    /**
     * Build view button action
     *
     * @return array View button configuration
     */
    private static function buildViewButtonAction(): array
    {
        return ButtonComponent::make('view')
            ->icon('eyeopen')
            ->variant('outlined')
            ->size('sm')
            ->color('primary')
            ->isIconButton(true)
            ->data('type', 'aside')
            ->data('component', 'view-blog')
            ->data('action', 'open')
            ->data('config', [
                'width' => '900px',
            ])
            ->dataKey('id')
            ->meta([
                'tooltip' => 'View Details',
            ])
            ->toArray();
    }

    /**
     * Build edit button action
     *
     * @return array Edit button configuration
     */
    private static function buildEditButtonAction(): array
    {
        return ButtonComponent::make('edit')
            ->icon('pen')
            ->variant('outlined')
            ->size('sm')
            ->color('primary')
            ->isIconButton(true)
            ->data('type', 'aside')
            ->data('component', 'edit-blog')
            ->data('action', 'open')
            ->data('config', [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ])
            ->dataKey('id')
            ->meta([
                'tooltip' => 'Edit',
            ])
            ->toArray();
    }

    /**
     * Build delete button action
     *
     * @return array Delete button configuration
     */
    private static function buildDeleteButtonAction(): array
    {
        return ButtonComponent::make('delete')
            ->icon('binempty')
            ->variant('outlined')
            ->size('sm')
            ->color('danger')
            ->isIconButton(true)
            ->data('type', 'modal')
            ->data('component', 'delete-blog-modal')
            ->data('action', 'open')
            ->meta([
                'tooltip' => 'Delete',
            ])
            ->dataKey('id')
            ->toArray();
    }

    /**
     * Build dropdown button item using ButtonComponent
     * 
     * @param string $id Button identifier
     * @param string $label Button label
     * @param string $icon Icon name
     * @param string $action Action to trigger
     * @param string $color Optional color (primary, danger, etc.)
     * @param string $value Optional value
     * @return array
     */
    private static function buildDropdownButton(string $id, string $label, string $icon, string $action, ?string $color = null, ?string $value = null)
    {
        $button = ButtonComponent::make($id)
            ->label($label)
            ->icon($icon);

        // If label is empty, set as icon button
        if (empty($label)) {
            $button->isIconButton(true);
        }

        if ($color) {
            $button->color($color);
        }

        $buttonArray = $button
            ->meta(['action' => $action])
            ->toArray();

        // Add type and value to the array
        $buttonArray['type'] = 'button';

        if ($value !== null) {
            $buttonArray['value'] = $value;
        }

        return $buttonArray;
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
     * Get dropdown items for header "More Options" button
     *
     * @return array
     */
    private static function getHeaderMoreOptionsDropdownItems()
    {
        return [
            self::buildDropdownButton('settings', 'Settings', 'settings', 'open-settings'),
            self::buildDropdownButton('notifications', 'Notifications', 'bell', 'open-notifications'),
            self::buildDropdownButton('help', 'Help & Support', 'help', 'open-help'),
            self::buildDropdownDivider(),
            self::buildDropdownButton('export', 'Export Data', 'downloadcloud', 'export-data'),
            self::buildDropdownButton('import', 'Import Data', 'uploadcloud', 'import-data'),
            self::buildDropdownDivider(),
            self::buildDropdownButton('fullscreen', 'Toggle Fullscreen', 'expand', 'toggle-fullscreen'),
        ];
    }

    /**
     * Build view blog with form and activity aside (fullscreen)
     *
     * Creates a comprehensive fullscreen aside with:
     * - Left column: Forms (Notes, Follow-ups, Chat)
     * - Center column: Main Blog Form
     * - Right column: History (Activity timeline, Chat history)
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    private static function buildViewBlogFormActivityAside($masterData)
    {
        // Build aside using DetailSection with slot classes
        return DetailSection::make('view-blog-form-activity-aside')
            ->setHeader(
                BlogHeaderSlot::make()
            )
            ->setMain(
                BlogMainContentSlot::make($masterData, 'blog-form-activity', 'POST', '/api/blogs')
            )
            ->setLeft(
                BlogLeftSidebarSlot::make()
            )
            ->setRight(
                BlogRightSidebarSlot::make()
            )
            ->setFooter(
                BlogFooterSlot::make()
            )
            ->toArray();
    }

    /**
     * Build comprehensive blog form component with all features
     * 
     * Returns a fully-featured blog form wrapped in a grid section for easy integration
     * into any layout (asides, modals, detail sections, etc.)
     *
     * @param string $formId Unique form identifier
     * @param string $method HTTP method (POST, PUT, PATCH)
     * @param string $submitUrl URL for form submission
     * @param array $masterData Master data for form dropdowns and options
     * @param string|null $dataUrl Optional URL to fetch existing data for editing
     * @param array $config Optional configuration overrides
     * @return GridSection Grid section containing the blog form
     */
    private static function buildBlogFormComponent(
        string $formId = 'blog-form',
        string $method = 'POST',
        string $submitUrl = '/api/blogs',
        array $masterData = [],
        ?string $dataUrl = null,
        array $config = []
    ): GridSection {
        // Default configuration
        $defaultConfig = [
            'columns' => 1,
            'rows' => 1,
            'gap' => 'md',
            'gridColumnSpan' => 12,
            'styling' => 'w-full',
        ];

        // Merge with provided config
        $config = array_merge($defaultConfig, $config);

        // Create grid container for the form
        $formGrid = GridSection::make("{$formId}-grid", $config['columns'])
            ->rows($config['rows'])
            ->gap($config['gap']);

        // Apply meta styling if provided
        if (isset($config['styling'])) {
            $formGrid->meta(['styling' => $config['styling']]);
        }

        // Create the blog form with all parameters
        $blogForm = $dataUrl
            ? BlogForm::make($formId, $method, $submitUrl, $masterData, $dataUrl)
            : BlogForm::make($formId, $method, $submitUrl, $masterData);

        // Apply column span
        $blogForm->gridColumnSpan($config['gridColumnSpan']);

        // Add form to grid
        $formGrid->add($blogForm);

        return $formGrid;
    }

    /**
     * Quick helper to create a create blog form
     *
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    private static function createBlogForm(array $masterData = [], string $formId = 'create-blog-form'): GridSection
    {
        return self::buildBlogFormComponent(
            formId: $formId,
            method: 'POST',
            submitUrl: '/api/blogs',
            masterData: $masterData
        );
    }

    /**
     * Quick helper to create an edit blog form
     *
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    private static function editBlogForm(array $masterData = [], string $formId = 'edit-blog-form'): GridSection
    {
        return self::buildBlogFormComponent(
            formId: $formId,
            method: 'PUT',
            submitUrl: '/api/blogs/:id',
            masterData: $masterData,
            dataUrl: '/api/blogs/:id'
        );
    }

    /**
     * Quick helper to create a view-only blog form
     *
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    private static function viewBlogForm(array $masterData = [], string $formId = 'view-blog-form'): GridSection
    {
        return self::buildBlogFormComponent(
            formId: $formId,
            method: 'GET',
            submitUrl: '/api/blogs/:id',
            masterData: $masterData,
            dataUrl: '/api/blogs/:id',
            config: ['readonly' => true]
        );
    }

    /**
     * Build aside header with grid layout in center section
     *
     * @return SlotManager
     */
    private static function buildAsideHeaderWithGrid(): SlotManager
    {
        return BlogHeaderSlot::make();
    }

    /**
     * Build left sidebar grid with forms
     *
     * @return SlotManager
     */
    private static function buildLeftSidebarGrid(): SlotManager
    {
        return BlogLeftSidebarSlot::make();
    }

    /**
     * Build right sidebar grid with forms
     *
     * @return SlotManager
     */
    private static function buildRightSidebarGrid(): SlotManager
    {
        return BlogRightSidebarSlot::make();
    }

    /**
     * Build aside footer with grid sections for buttons
     */
    private static function buildAsideFooter(): SlotManager
    {
        return BlogFooterSlot::make();
    }

    /**
     * Build left column with form cards (Notes, Follow-ups, Chat)
     * 
     * @param mixed $grid Parent grid
     * @param array $masterData Master data
     * @return void
     */
    private static function buildFormsColumn($grid, $masterData)
    {
        $formsColumn = $grid->grid('forms-column', 1, 8)
            ->gap('md')
            ->gridColumnSpan(8)
            ->meta([
                'styling' => 'h-full flex flex-col space-y-4 col-span-8',
                'gridColumn' => '1 / 9',
            ]);

        // === ADD NOTES FORM CARD ===
        self::buildAddNotesFormCard($formsColumn);

        // === FOLLOW-UPS FORM CARD ===
        self::buildFollowUpsFormCard($formsColumn);

        // === CHAT INPUT FORM CARD ===
        self::buildChatInputFormCard($formsColumn);

        // === ADDITIONAL COMPONENTS SHOWCASE ===
        self::buildAlertsAndBadgesCard($formsColumn);

        self::buildStepperCard($formsColumn);

        // === NEW COMPONENTS ===
        self::buildDocumentTableTextCard($formsColumn);
    }

    /**
     * Build right column with activity and chat history cards
     * 
     * @param mixed $grid Parent grid
     * @return void
     */
    private static function buildHistoryColumn($grid)
    {
        $historyColumn = $grid->grid('history-column', 1, 4)
            ->gap('md')
            ->gridColumnSpan(4)
            ->meta([
                'styling' => 'h-full flex flex-col space-y-4 overflow-y-auto col-span-4',
                'gridColumn' => '9 / 13',
            ]);

        // === ACTIVITY HISTORY CARD ===
        self::buildActivityHistoryCard($historyColumn);

        // === CHAT HISTORY CARD ===
        self::buildChatHistoryCard($historyColumn);

        // === STATS AND CHARTS CARD ===
        self::buildStatsChartCard($historyColumn);

        // === AVATARS AND COMMENTS CARD ===
        self::buildAvatarsCommentsCard($historyColumn);
    }

    /**
     * Helper: Get standard card meta configuration with optional header actions
     * 
     * @param string $icon Icon name
     * @param bool $expanded Whether card is expanded
     * @param string|null $action Optional header action handler
     * @return array Card meta configuration
     */
    private static function getCardMetaWithActions(string $icon, bool $expanded = true, ?string $action = null): array
    {
        $meta = [
            'collapsible' => self::DEFAULT_CARD_COLLAPSIBLE,
            'defaultExpanded' => $expanded,
            'icon' => $icon,
            'maxHeight' => self::MAX_CARD_HEIGHT,
            'overflowY' => 'auto',
        ];

        if ($action) {
            $meta['headerActions'] = [
                [
                    'type' => 'button',
                    'label' => '',
                    'icon' => 'ellipsisvertical',
                    'variant' => 'text',
                    'size' => 'sm',
                    'tooltip' => 'More options',
                    'action' => $action,
                ],
            ];
        }

        return $meta;
    }

    /**
     * Build Add Notes form card
     * Uses BlogNotesForm class
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildAddNotesFormCard($column)
    {
        $formComponent = BlogNotesForm::make('add-notes-form', 'POST', '/api/blogs/:id/notes')->gridColumnSpan(12);

        $column->addComponent($formComponent);
    }

    /**
     * Build Follow-ups form card
     * Uses BlogFollowUpsForm class
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildFollowUpsFormCard($column)
    {
        $formComponent = BlogFollowUpsForm::make('add-followup-form', 'POST', '/api/blogs/:id/followups')->gridColumnSpan(12);

        $column->addComponent($formComponent);
    }

    /**
     * Build Chat input form card
     * Uses BlogChatForm class
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildChatInputFormCard($column)
    {
        $formComponent = BlogChatForm::make('new-chat-form', 'POST', '/api/blogs/:id/chats')->gridColumnSpan(12);
        $column->addComponent($formComponent);
    }

    /**
     * Build Activity History card
     * Uses layout package components only (read-only)
     *
     * @return array Timeline component array
     */
    private static function buildActivityHistoryCard()
    {
        // Build timeline component
        $timeline = \Litepie\Layout\Components\TimelineComponent::make('activity-timeline')
            ->vertical()
            ->position('left')
            ->showDates(true)
            ->showIcons(true)
            ->dateFormat('relative')
            ->addEvent([
                'key' => 'activity-1',
                'title' => 'Blog post published',
                'description' => 'Post was published and is now live',
                'date' => '2 hours ago',
                'icon' => 'checksquare',
                'color' => 'success',
            ])
            ->addEvent([
                'key' => 'activity-2',
                'title' => 'Content updated',
                'description' => 'Main content section was revised',
                'date' => '4 hours ago',
                'icon' => 'pen',
                'color' => 'info',
            ])
            ->addEvent([
                'key' => 'activity-3',
                'title' => 'Featured image changed',
                'description' => 'New featured image uploaded',
                'date' => '1 day ago',
                'icon' => 'image',
                'color' => 'warning',
            ])
            ->addEvent([
                'key' => 'activity-4',
                'title' => 'Draft created',
                'description' => 'Initial draft of the blog post',
                'date' => '3 days ago',
                'icon' => 'filetext',
                'color' => 'default',
            ])
            ->meta([
                'dataUrl' => '/api/blogs/:id/activity',
                'emptyMessage' => 'No activity yet',
                'showTimestamps' => true,
                'compact' => false,
            ]);

        return $timeline->toArray();
    }

    /**
     * Build Chat History card
     * Uses layout package components only (read-only)
     *
     * @return ListComponent Chat history list component
     */
    private static function buildChatHistoryCard()
    {
        // Build chat list component
        return ListComponent::make('chat-history-list')
            ->dense(false)
            ->disablePadding(false)
            ->items([[
                'id' => 'chat-1',
                'primary' => 'John Doe',
                'secondary' => 'Can we review this before publishing?',
                'timestamp' => '2 hours ago',
                'avatar' => 'JD',
                'color' => 'primary',
            ], [
                'id' => 'chat-2',
                'primary' => 'Jane Smith',
                'secondary' => 'I made some edits to the introduction.',
                'timestamp' => '4 hours ago',
                'avatar' => 'JS',
                'color' => 'success',
            ], [
                'id' => 'chat-3',
                'primary' => 'John Doe',
                'secondary' => 'Looks great! Just need to update the images.',
                'timestamp' => '1 day ago',
                'avatar' => 'JD',
                'color' => 'primary',
            ], [
                'id' => 'chat-4',
                'primary' => 'System',
                'secondary' => 'Draft auto-saved successfully.',
                'timestamp' => '2 days ago',
                'avatar' => 'SYS',
                'color' => 'default',
            ]])
            ->gridColumnSpan(6)
            ->meta([
                'dataUrl' => '/api/blogs/:id/chats',
                'emptyMessage' => 'No messages yet',
                'showTimestamps' => true,
                'showAvatars' => true,
            ]);
    }

    /**
     * Build Alerts and Badges showcase card
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildAlertsAndBadgesCard($column)
    {
        // Alert Components
        $alertComponent = AlertComponent::make('success-alert')
            ->title('Success!')
            ->message('Your blog post has been published successfully.')
            ->success()
            ->filled(true)
            ->dismissible(true)
            ->icon('checksquare');

        $warningAlert = AlertComponent::make('warning-alert')
            ->title('Warning')
            ->message('Please review your SEO settings before publishing.')
            ->warning()
            ->dismissible(true)
            ->icon('alerttriangle');

        $infoAlert = AlertComponent::make('info-alert')
            ->message('Remember to add relevant tags for better discoverability.')
            ->info()
            ->bordered(true)
            ->icon('infocircle');

        // Badge Components
        $statusBadge = BadgeComponent::make('status-badge')
            ->content('Published')
            ->color('success')
            ->variant('standard')
            ->meta(['size' => 'md']);

        $countBadge = BadgeComponent::make('count-badge')
            ->content('24')
            ->color('primary')
            ->variant('standard')
            ->meta(['size' => 'sm']);

        // Divider
        $divider = DividerComponent::make('alerts-divider')
            ->orientation('horizontal')
            ->variant('fullWidth')
            ->spacing('md')
            ->children('Notification Center');

        $card = $column->card('alerts-badges-card')
            ->title('Alerts and Badges Showcase')
            ->variant('outlined')
            ->meta(self::getCardMetaWithActions('bell', true, 'open-alerts-badges-options'));

        $card->addComponent($alertComponent);
        $card->addComponent($warningAlert);
        $card->addComponent($infoAlert);
        $card->addComponent($divider);
        $card->addComponent($statusBadge);
        $card->addComponent($countBadge);
    }

    /**
     * Build Stepper showcase card
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildStepperCard($column)
    {
        $stepper = \Litepie\Layout\Components\StepperComponent::make('publish-stepper')
            ->orientation('vertical')
            ->activeStep(1)
            ->steps([[
                'id' => 'step-1',
                'label' => 'Draft Creation',
                'description' => 'Create and edit your blog content',
                'status' => 'completed',
                'icon' => 'pen',
            ], [
                'id' => 'step-2',
                'label' => 'Review & Preview',
                'description' => 'Review content and preview layout',
                'status' => 'active',
                'icon' => 'eyeopen',
            ], [
                'id' => 'step-3',
                'label' => 'SEO Optimization',
                'description' => 'Optimize meta tags and keywords',
                'status' => 'pending',
                'icon' => 'search',
            ], [
                'id' => 'step-4',
                'label' => 'Publish',
                'description' => 'Make your post live to the world',
                'status' => 'pending',
                'icon' => 'rocket',
            ]])
            ->meta([
                'showConnector' => true,
                'allowClickNavigation' => true,
            ]);

        $column->addComponent($stepper);
    }

    /**
     * Build Stats and Chart showcase card
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildStatsChartCard($column)
    {
        // Stats Component
        $stats = \Litepie\Layout\Components\StatsComponent::make('blog-stats')
            ->layout('grid')
            ->columns(3)
            ->meta([
                'stats' => [
                    [
                        'id' => 'views',
                        'label' => 'Total Views',
                        'value' => '2,345',
                        'change' => '+12.5%',
                        'trend' => 'up',
                        'icon' => 'eyeopen',
                        'color' => 'primary',
                    ],
                    [
                        'id' => 'likes',
                        'label' => 'Likes',
                        'value' => '156',
                        'change' => '+8.2%',
                        'trend' => 'up',
                        'icon' => 'heart',
                        'color' => 'error',
                    ],
                    [
                        'id' => 'shares',
                        'label' => 'Shares',
                        'value' => '89',
                        'change' => '-3.1%',
                        'trend' => 'down',
                        'icon' => 'share',
                        'color' => 'success',
                    ],
                ],
            ]);

        // Chart Component
        $chart = \Litepie\Layout\Components\ChartComponent::make('views-chart')
            ->type('line')
            ->data([
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'datasets' => [
                    [
                        'label' => 'Views',
                        'data' => [120, 150, 180, 220, 190, 250, 280],
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    ],
                ],
            ])
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
            ])
            ->height(200)
            ->meta([
                'title' => 'Weekly Views Trend',
            ]);

        // Create card and add components
        $card = $column->card('stats-chart-card')
            ->title('Blog Statistics & Trends')
            ->variant('outlined')
            ->meta([
                'collapsible' => true,
                'defaultExpanded' => true,
                'icon' => 'chartline',
            ]);

        $card->addComponent($stats);
        $card->addComponent($chart);
    }

    /**
     * Build Avatars and Comments showcase card
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildAvatarsCommentsCard($column)
    {
        // Avatar Group Component
        $avatarGroup = \Litepie\Layout\Components\AvatarGroupComponent::make('contributors')
            ->max(4)
            ->spacing('medium')
            ->meta(['size' => 'md'])
            ->addAvatar([
                'id' => 'user-1',
                'src' => 'https://i.pravatar.cc/150?img=1',
                'alt' => 'John Doe',
                'fallback' => 'JD',
            ])
            ->addAvatar([
                'id' => 'user-2',
                'src' => 'https://i.pravatar.cc/150?img=2',
                'alt' => 'Jane Smith',
                'fallback' => 'JS',
            ])
            ->addAvatar([
                'id' => 'user-3',
                'src' => 'https://i.pravatar.cc/150?img=3',
                'alt' => 'Bob Wilson',
                'fallback' => 'BW',
            ])
            ->addAvatar([
                'id' => 'user-4',
                'src' => 'https://i.pravatar.cc/150?img=4',
                'alt' => 'Alice Brown',
                'fallback' => 'AB',
            ])
            ->addAvatar([
                'id' => 'user-5',
                'fallback' => '+5',
            ])
            ->meta([
                'title' => '9 Contributors',
            ]);

        // Breadcrumb Component
        $breadcrumb = \Litepie\Layout\Components\BreadcrumbComponent::make('nav-breadcrumb')
            ->addItem('Home', '/', ['icon' => 'home'])
            ->addItem('Blogs', '/blogs', ['icon' => 'documentfull'])
            ->addItem('Current Post')
            ->separator('/')
            ->meta(['showIcons' => true]);

        // Comment Component
        $comment = \Litepie\Layout\Components\CommentComponent::make('top-comment')
            ->editing(false)
            ->deleting(false)
            ->meta([
                'author' => 'Sarah Johnson',
                'avatar' => 'https://i.pravatar.cc/150?img=5',
                'content' => 'Great article! Very informative and well-written. Looking forward to more posts like this.',
                'timestamp' => '2 hours ago',
                'likes' => 24,
                'showReply' => true,
                'showLike' => true,
            ]);

        // Create card and add components
        $card = $column->card('avatars-comments-card')
            ->title('Contributors & Feedback')
            ->variant('outlined')
            ->meta([
                'collapsible' => true,
                'defaultExpanded' => false,
                'icon' => 'users',
            ]);

        $card->addComponent($avatarGroup);
        $card->addComponent($breadcrumb);
        $card->addComponent($comment);
    }

    /**
     * Build Document/Table/Text showcase card
     * 
     * @param mixed $column Parent column
     * @return void
     */
    private static function buildDocumentTableTextCard($column)
    {
        // Text Component
        $text = TextComponent::make('intro-text')
            ->content('This section showcases Document, Table, and Text components from the Litepie Layout package.')
            ->size('md')
            ->color('text-secondary')
            ->weight('normal')
            ->gutterBottom(true);

        // Table Component
        $table = TableComponent::make('blog-stats-table')
            ->addColumn('metric', 'Metric', ['width' => '40%'])
            ->addColumn('value', 'Value', ['width' => '30%', 'align' => 'right'])
            ->addColumn('change', 'Change', ['width' => '30%', 'align' => 'right'])
            ->data([
                ['metric' => 'Total Posts', 'value' => '145', 'change' => '+12'],
                ['metric' => 'Published', 'value' => '120', 'change' => '+8'],
                ['metric' => 'Drafts', 'value' => '25', 'change' => '+4'],
                ['metric' => 'Total Views', 'value' => '45,892', 'change' => '+2.3k'],
            ])
            ->striped(true)
            ->hoverable(true)
            ->meta([
                'size' => 'sm',
                'bordered' => true,
            ]);

        // Document Component (Upload)
        $document = DocumentComponent::make('blog-attachments')
            ->upload()
            ->allowedTypes(['pdf', 'doc', 'docx', 'txt', 'md'])
            ->maxSize(10)
            ->maxFiles(5)
            ->multiple(true)
            ->dragDrop(true)
            ->meta([
                'uploadUrl' => '/api/blogs/:id/documents',
                'placeholder' => 'Drag and drop files or click to browse',
                'showPreview' => true,
            ]);

        // Create card and add components
        $card = $column->card('documents-tables-text-card')
            ->title('Documents, Tables & Text')
            ->variant('outlined')
            ->meta([
                'collapsible' => true,
                'defaultExpanded' => false,
                'icon' => 'description',
            ]);

        $card->addComponent($text);
        $card->addComponent($table);
        $card->addComponent($document);
    }

    /**
     * Build component directly in a section (works for both modals and asides)
     * This is a unified method that accepts a section and builds any component type
     *
     * @param mixed $section The section object to build the component in
     * @param string $type The component type ('modal' or 'aside')
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
     * Get component definition for modals and asides
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
                case 'create-blog-modal':
                    return self::buildCreateBlogModal($masterData);
                case 'delete-blog-modal':
                    return self::buildDeleteBlogModal();
                case 'blog-feedback-modal':
                    return self::buildBlogFeedbackModal();
            }
        }

        if ($type === 'aside') {
            switch ($componentName) {
                case 'view-blog':
                    return self::buildViewBlogAside($masterData);
                case 'view-blog-full':
                    return self::buildViewBlogAsideFullscreen($masterData);
                case 'view-blog-forms':
                    return self::buildViewBlogFormActivityAside($masterData);
                case 'view-blog-forms-full':
                    return self::buildViewBlogFormActivityAside($masterData); // Same as regular since it's already fullscreen
                case 'view-blog-fa-full':
                    return self::buildViewBlogFAAsideFullscreen($masterData);
                case 'create-blog':
                    return self::buildCreateBlogAside($masterData);
                case 'create-blog-full':
                    return self::buildCreateBlogAsideFullscreen($masterData);
                case 'edit-blog':
                    return self::buildEditBlogAside($masterData);
                case 'edit-blog-full':
                    return self::buildEditBlogAsideFullscreen($masterData);
            }
        }

        return null;
    }

    /**
     * Build create blog modal
     */
    private static function buildCreateBlogModal($masterData)
    {
        return ModalSlot::createBlog($masterData);
    }

    /**
     * Build delete blog modal
     */
    private static function buildDeleteBlogModal()
    {
        return ModalSlot::deleteConfirmation('blog post');
    }

    /**
     * Build blog feedback modal
     */
    private static function buildBlogFeedbackModal()
    {
        return ModalSlot::feedback();
    }

    /**
     * Build create blog aside
     */
    private static function buildCreateBlogAside($masterData)
    {
        return BlogCreateAsideSlot::make($masterData);
    }

    /**
     * Build edit blog aside
     */
    private static function buildEditBlogAside($masterData)
    {
        return BlogEditAsideSlot::make($masterData);
    }

    /**
     * Build view blog aside
     */
    private static function buildViewBlogAside($masterData)
    {
        return BlogViewAsideSlot::make($masterData);
    }

    /**
     * Build view blog aside
     */
    private static function buildViewBlogFAAside($masterData)
    {
        return BlogFormActivityAsideSlot::make($masterData);
    }

    /**
     * Build view blog aside fullscreen
     */
    private static function buildViewBlogAsideFullscreen($masterData)
    {
        $asideData = self::buildViewBlogAside($masterData);

        if (is_array($asideData)) {
            $asideData['name'] = 'view-blog-full';
        }

        return $asideData;
    }

    /**
     * Build view blog aside fullscreen
     */
    private static function buildViewBlogFAAsideFullscreen($masterData)
    {
        return BlogFormActivityAsideSlot::make($masterData, true);
    }

    /**
     * Build create blog aside fullscreen
     */
    private static function buildCreateBlogAsideFullscreen($masterData)
    {
        return BlogCreateAsideSlot::make($masterData, true);
    }

    /**
     * Build edit blog aside fullscreen
     */
    private static function buildEditBlogAsideFullscreen($masterData)
    {
        return BlogEditAsideSlot::make($masterData, true);
    }

    // ========================================================================
    // NEW DETAIL SECTION METHODS (GRID-BASED LAYOUT)
    // ========================================================================

    /**
     * Build create blog detail section
     *
     * @param  array  $masterData
     * @return DetailSection
     */
    private static function buildCreateBlogDetail($masterData)
    {
        return BlogDetailSlot::createDetail($masterData);
    }

    /**
     * Build edit blog detail section
     *
     * @param  array  $masterData
     * @return DetailSection
     */
    private static function buildEditBlogDetail($masterData)
    {
        return BlogDetailSlot::editDetail($masterData);
    }

    /**
     * Build view blog detail section
     *
     * @param  array  $masterData
     * @return DetailSection
     */
    private static function buildViewBlogDetail($masterData)
    {
        return BlogDetailSlot::viewDetail($masterData);
    }
}
