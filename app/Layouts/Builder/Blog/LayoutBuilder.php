<?php

namespace App\Layouts\Builder\Blog;

use App\Forms\Blog\BlogForm;
use App\Layouts\Builder\TableColumnsBuilder;
use App\Layouts\Slot\Blog\CreateAsideSlot;
use App\Layouts\Slot\Blog\DetailSlot;
use App\Layouts\Slot\Blog\EditAsideSlot;
use App\Layouts\Slot\Blog\FooterSlot;
use App\Layouts\Slot\Blog\HeaderSlot;
use App\Layouts\Slot\Blog\LeftSidebarSlot;
use App\Layouts\Slot\Blog\MainContentSlot;
use App\Layouts\Slot\Blog\RightSidebarSlot;
use App\Layouts\Slot\Blog\ViewAsideSlot;
use App\Layouts\Slot\Blog\ModalSlot;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\GridSection;

/**
 * Blog LayoutBuilder
 * 
 * **Purpose**: Orchestrates construction of all blog layout sections and components.
 * 
 * **Architecture Role**:
 * - Receives configuration from BlogLayout (entry point)
 * - Delegates component rendering to Slot classes
 * - Coordinates section assembly and data flow
 * 
 * **Organization** (follows SRP - Single Responsibility Principle):
 * 1. **Section Builders**: Main page sections (Header, Main, Footer, Search, Actions)
 * 2. **Component Builders**: Individual UI components (Stats, Filters, Buttons)
 * 3. **Table Configuration**: Data table setup with columns from TableColumnsBuilder
 * 4. **Aside Builders**: Drawer panels (Create, Edit, View, Forms+Activity)
 * 5. **Modal Builders**: Dialog modals (Create, Delete, Confirmation)
 * 6. **Form Builders**: Form components with various configurations
 * 
 * **Naming Convention**:
 * - No "Blog" prefix in class name (namespace provides context)
 * - Module-specific logic lives here
 * - Shared logic goes in parent Builder or TableColumnsBuilder
 * 
 * @package App\Layouts\Builder\Blog
 * @see \App\Layouts\BlogLayout Main layout entry point
 * @see \App\Layouts\Slot\Blog\* Component slots
 * @see \App\Layouts\Builder\TableColumnsBuilder Shared table configuration
 * 
 * @package App\Layouts\Builder\Blog
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
            'description' => __('layout.blog_header_description'),
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
            ->title(__('layout.blog_management'))
            ->breadcrumbs([
                ['label' => __('layout.dashboard'), 'link' => '/', 'icon' => 'home'],
                ['label' => __('layout.blogs'), 'active' => true, 'icon' => 'documentfull'],
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

        self::buildStatsCard($statsGrid, 'stat-total-posts', __('layout.total_posts'), '12', 'primary', 'list', '+12%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-published', __('layout.published'), '8', 'success', 'listcheck', '+8%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-drafts', __('layout.drafts'), '0', 'warning', 'pen', '+0%', 'neutral', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-total-views', __('layout.total_views'), '15', 'info', 'eyeopen', '+15%', 'up', 'trend-1');
    }

    /**
     * Build main section with data tables (table and list views)
     * 
     * @param mixed $section Section component
     * @param array $masterData Master data for configuration
     * @return void
     */
    public static function buildMainSection($section, array $masterData): void
    {
        $section->meta([
            'description' => __('layout.blog_main_description'),
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        $mainGrid = $section->grid('main-content-grid', 1)->gap('md');

        // Table view
        $mainGrid->row('table-row')->gap('none')->table('blogs-table')
            ->asTable()
            ->dataUrl('/api/blogs')
            ->columns(TableColumnsBuilder::getBlogTableColumns())
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

        // List view
        $mainGrid->row('table-row')->gap('none')->table('blogs-table-lists')
            ->dataUrl('/api/blogs')
            ->columns(TableColumnsBuilder::getBlogTableListColumns())
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
     * @param mixed $section Section component
     * @param array $masterData Master data for filters
     * @return void
     */
    public static function buildSearchComponent($section, array $masterData): void
    {
        $section->meta([
            'description' => __('layout.blog_search_description'),
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
            'description' => __('layout.blog_actions_description'),
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
            'description' => __('layout.blog_footer_description'),
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
            ->content(__('layout.blog_system_copyright'))
            ->size('sm')
            ->color('text-secondary');

        // Right: Links
        $linksRow = $footerGrid->row('links-row')
            ->gap('md')
            ->align('center')
            ->justify('end');

        $linksRow->button('help-link')
            ->label(__('layout.help'))
            ->icon('help')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => __('layout.get_help_doc')]);

        $linksRow->button('about-link')
            ->label(__('layout.about'))
            ->icon('infocircle')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => __('layout.about_this_app')]);

        $linksRow->button('version-link')
            ->label(__('layout.v1_0_0'))
            ->icon('tag')
            ->size('sm')
            ->variant('text')
            ->meta(['tooltip' => __('layout.app_version')]);
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
     * @param string $color Theme color (primary, success, info, warning, error)
     * @param string $icon Lucide icon name
     * @param string $trend Trend percentage (e.g., '+12%')
     * @param string $trendDir Trend direction ('up', 'down', or 'neutral')
     * @param string $displayType Display type
     * @return mixed Card component
     */
    public static function buildStatsCard($grid, string $id, string $title, string $value, string $color, string $icon, string $trend, string $trendDir, string $displayType)
    {
        $colorMap = [
            'primary' => ['icon' => '#3b82f6', 'bg' => '#f0f9ff'],
            'success' => ['icon' => '#10b981', 'bg' => '#f7fef9'],
            'info' => ['icon' => '#8b5cf6', 'bg' => '#faf8ff'],
            'warning' => ['icon' => '#f59e0b', 'bg' => '#fffef7'],
            'error' => ['icon' => '#ef4444', 'bg' => '#fef9f9'],
        ];

        $colors = $colorMap[$color] ?? $colorMap['primary'];

        return $grid->card($id)
            ->title($title)
            ->content($value)
            ->variant('outlined')
            ->color($color)
            ->gridColumnSpan(3)
            ->dataUrl("/api/blogs/stats/{$id}")
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
     * Build filter column with search and filter controls
     * 
     * @param mixed $row Row component
     * @param array $masterData Master data
     * @return void
     */
    public static function buildFilterColumn($row, array $masterData): void
    {
        $row->filter('blogs-filter')
            ->addQuickFilter('search', __('layout.search'), 'text')
            ->addQuickFilter('status', __('layout.status'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_statuses')]],
                [
                    ['value' => 'draft', 'label' => __('layout.draft')],
                    ['value' => 'review', 'label' => __('layout.in_review')],
                    ['value' => 'published', 'label' => __('layout.published')],
                    ['value' => 'archived', 'label' => __('layout.archived')],
                ]
            ))
            ->addQuickFilter('category', __('layout.category'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_categories')]],
                $masterData['categories'] ?? []
            ))
            ->addSelectFilter('status', __('layout.status'), [
                ['value' => 'draft', 'label' => __('layout.draft')],
                ['value' => 'review', 'label' => __('layout.in_review')],
                ['value' => 'published', 'label' => __('layout.published')],
                ['value' => 'archived', 'label' => __('layout.archived')],
            ])
            ->addMultiSelectFilter('category', __('layout.category'), $masterData['categories'] ?? [])
            ->addDateRangeFilter('published_at', __('layout.published_date'))
            ->collapsible()
            ->collapsed(true)
            ->showActiveCount()
            ->rememberFilters(true, 'blogs_filter')
            ->liveFilter(true, 300)
            ->submitAction('/api/blogs');
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
            ->data('component', 'create-blog')
            ->data('action', 'open')
            ->data('config', [
                'width' => '800px',
                'height' => '100vh',
                'anchor' => 'right',
                'backdrop' => true,
            ])
            ->meta(['tooltip' => __('layout.create_new_blog')]);

        $row->button('create-btn-modal')
            ->label(__('layout.create_modal_view'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-blog-modal')
            ->data('action', 'open')
            ->meta(['tooltip' => __('layout.create_new_blog')]);

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
            ->icon('ellipsisVertical')
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
     * Build create blog aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildCreateBlogAside(array $masterData): array
    {
        return CreateAsideSlot::make($masterData);
    }

    /**
     * Build create blog fullscreen aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildCreateBlogAsideFullscreen(array $masterData): array
    {
        return CreateAsideSlot::make($masterData, true);
    }

    /**
     * Build edit blog aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildEditBlogAside(array $masterData): array
    {
        return EditAsideSlot::make($masterData);
    }

    /**
     * Build view blog aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildViewBlogAside(array $masterData): array
    {
        return ViewAsideSlot::make($masterData);
    }

    /**
     * Build view blog fullscreen aside
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildViewBlogAsideFullscreen(array $masterData): array
    {
        return ViewAsideSlot::make($masterData, true);
    }

    /**
     * Build view blog form activity aside (with left/right sidebars)
     * 
     * @param array $masterData Master data for forms
     * @return array Aside definition
     */
    public static function buildViewBlogFormActivityAside(array $masterData): array
    {
        return DetailSection::make('view-blog-form-activity-aside')
            ->setHeader(HeaderSlot::make())
            ->setMain(MainContentSlot::make($masterData, 'blog-form-activity', 'POST', '/api/blogs'))
            ->setLeft(LeftSidebarSlot::make())
            ->setRight(RightSidebarSlot::make())
            ->setFooter(FooterSlot::make())
            ->toArray();
    }

    // ============================================================
    // MODAL BUILDERS - Create, Delete, Confirmation modals
    // ============================================================

    /**
     * Build create blog modal
     * 
     * @param array $masterData Master data for forms
     * @return array Modal definition
     */
    public static function buildCreateBlogModal(array $masterData): array
    {
        return ModalSlot::createBlog([
            'masterData' => $masterData,
        ]);
    }

    /**
     * Build delete confirmation modal
     * 
     * @param string $itemName Item name to display
     * @return array Modal definition
     */
    public static function buildDeleteBlogModal(string $itemName = ''): array
    {
        return ModalSlot::deleteBlog([
            'itemName' => $itemName ?: null,
        ]);
    }

    // ============================================================
    // FORM BUILDERS - Blog forms with various configurations
    // ============================================================

    /**
     * Build comprehensive blog form component
     * 
     * @param string $formId Unique form identifier
     * @param string $method HTTP method (POST, PUT, PATCH)
     * @param string $submitUrl URL for form submission
     * @param array $masterData Master data for form dropdowns and options
     * @param string|null $dataUrl Optional URL to fetch existing data for editing
     * @param array $config Optional configuration overrides
     * @return GridSection Grid section containing the blog form
     */
    public static function buildBlogFormComponent(
        string $formId = 'blog-form',
        string $method = 'POST',
        string $submitUrl = '/api/blogs',
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

        $blogForm = $dataUrl
            ? BlogForm::make($formId, $method, $submitUrl, $masterData, $dataUrl)
            : BlogForm::make($formId, $method, $submitUrl, $masterData);

        $blogForm->gridColumnSpan($config['gridColumnSpan']);
        $formGrid->add($blogForm);

        return $formGrid;
    }

    /**
     * Create a create blog form
     * 
     * @param array $masterData Master data for form
     * @param string $formId Optional form ID
     * @return GridSection
     */
    public static function createBlogForm(array $masterData = [], string $formId = 'create-blog-form'): GridSection
    {
        return self::buildBlogFormComponent(
            formId: $formId,
            method: 'POST',
            submitUrl: '/api/blogs',
            masterData: $masterData
        );
    }

    /**
     * Create an edit blog form
     * 
     * @param array $masterData Master data for form
     * @param string $dataUrl URL to fetch blog data
     * @param string $formId Optional form ID
     * @return GridSection
     */
    public static function editBlogForm(array $masterData = [], string $dataUrl = '/api/blogs/:id', string $formId = 'edit-blog-form'): GridSection
    {
        return self::buildBlogFormComponent(
            formId: $formId,
            method: 'PUT',
            submitUrl: '/api/blogs/:id',
            masterData: $masterData,
            dataUrl: $dataUrl
        );
    }
}
