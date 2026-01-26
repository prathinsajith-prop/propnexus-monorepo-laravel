<?php

namespace App\Layouts;

use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Components\DrawerComponent;
use Litepie\Layout\Components\ModalComponent;
use App\Forms\User\UserForm;
use App\Forms\User\UserViewForm;

/**
 * UserLayout
 * 
 * Comprehensive layout configuration for user management module.
 * Implements header with statistics, main content with filters and table,
 * and interactive components (drawers, modals) for CRUD operations.
 * 
 * @package App\Layouts
 */
class UserLayout
{
    /**
     * Create the complete user management layout
     * 
     * Builds a full-featured dashboard with:
     * - Header section with breadcrumbs and statistics cards
     * - Main content section with advanced filters and data table
     * - Drawer components for viewing/editing user details
     * - Modal components for quick user operations
     * 
     * @param array $masterData Master data for dropdowns and filters
     * @return array Complete layout configuration
     */
    public static function make($masterData)
    {
        return LayoutBuilder::create('users', 'page')
            ->title('User Management')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/users-master-data',
                'description' => 'User Management System',
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

        // Main header grid - responsive 12-column layout
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
            ->title('User Management')
            ->breadcrumbs([
                ['label' => 'Dashboard', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Users', 'active' => true, 'icon' => 'users'],
            ])
            ->align('left')
            ->spacing('md')
            ->titleVariant('h1')
            ->titleSize('2xl')
            ->titleWeight('bold')
            ->titleGutterBottom(true);

        $statsGrid = $headerGrid->grid('stats-grid')
            ->columns(4)
            ->gap('lg')
            ->responsive(true)
            ->gridColumnSpan(7);

        self::buildStatsCard($statsGrid, 'stat-total-users', 'Total Users', '1,284', 'primary', 'users', '+12.5%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-active', 'Active Users', '1,156', 'success', 'usercheck', '+3.2%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-new-users', 'New Hires', '42', 'info', 'userplus', '+15.8%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-pending', 'Pending Review', '15', 'warning', 'userwarning', '-5.4%', 'down', 'trend-1');
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
     * @param string $trend Trend percentage (e.g., '+12.5%')
     * @param string $trendDir Trend direction ('up' or 'down')
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

    private static function buildMainSection($section, $masterData)
    {
        $section->meta([
            'description' => 'Main content area with filters and data table',
            'styling' => 'container mx-auto px-4 py-6',
        ]);

        $mainGrid = $section->grid('main-content-grid')->columns(1)->gap('md');

        $controlsRow = $mainGrid->grid('controls-row')
            ->columns(2)
            ->gap('md')
            ->meta(['styling' => 'bg-white rounded-lg shadow-sm p-4']);

        self::buildFilterColumn($controlsRow->row('filter-column')->gap('none'), $masterData);
        self::buildActionColumn($controlsRow->row('actions-column')->gap('sm')->align('center')->justify('end'));

        $mainGrid->row('table-row')->gap('none')->table('users-table')
            ->dataUrl('/api/users')
            ->columns(self::getUserTableColumns())
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
                'rowActions' => ['type' => 'drawer', 'component' => 'view-user-drawer', 'dataUrl' => '/api/users/:id'],
            ]);
    }

    private static function buildFilterColumn($row, $masterData)
    {
        $row->filter('users-filter')
            // Quick access filters at the top
            ->addQuickFilter('joining_date', 'Joining Date', 'daterange')
            ->addQuickFilter('status', 'User Status', 'select', array_merge(
                [['value' => '', 'label' => 'All Statuses']],
                $masterData['statuses']
            ))
            ->addQuickFilter('department', 'Department', 'select', array_merge(
                [['value' => '', 'label' => 'All Departments']],
                $masterData['departments']
            ))
            ->addQuickFilter('employee_type', 'Employment Type', 'select', array_merge(
                [['value' => '', 'label' => 'All Types']],
                $masterData['employee_types']
            ))

            // Quick Presets for common filter combinations
            ->addPreset('active_users', 'Active Users', [
                'status' => 'Active',
            ])
            ->addPreset('new_hires', 'New Hires (This Month)', [
                'joining_date' => ['start' => 'first day of this month', 'end' => 'today'],
            ])
            ->addPreset('engineering', 'Engineering Team', [
                'department' => 'Engineering',
            ])

            // Status with all comparison operators
            ->addSelectFilter('status', 'Status', $masterData['statuses'])
            ->addOperators('status', [
                ['value' => 'equal', 'label' => 'Equal To'],
                ['value' => 'not_equal', 'label' => 'Not Equal To'],
                ['value' => 'in', 'label' => 'In'],
                ['value' => 'not_in', 'label' => 'Not In'],
            ])
            ->setDefaultOperator('status', 'equal')

            // Department filter
            ->addMultiSelectFilter('department', 'Department', $masterData['departments'])
            ->addOperators('department', [
                ['value' => 'in', 'label' => 'In (Any Of)'],
                ['value' => 'not_in', 'label' => 'Not In (None Of)'],
            ])
            ->setDefaultOperator('department', 'in')

            // Designation filter
            ->addMultiSelectFilter('designation', 'Designation', $masterData['designations'])
            ->addOperators('designation', [
                ['value' => 'in', 'label' => 'In (Any Of)'],
                ['value' => 'not_in', 'label' => 'Not In (None Of)'],
                ['value' => 'like', 'label' => 'Like'],
                ['value' => 'not_like', 'label' => 'Not Like'],
            ])
            ->setDefaultOperator('designation', 'in')

            // Date range with comprehensive date operators
            ->addDateRangeFilter('joining_date', 'Joining Date')
            ->addOperators('joining_date', [
                ['value' => 'equal', 'label' => 'Equal To (On Date)'],
                ['value' => 'not_equal', 'label' => 'Not Equal To'],
                ['value' => 'before', 'label' => 'Before'],
                ['value' => 'after', 'label' => 'After'],
                ['value' => 'on_or_before', 'label' => 'On or Before'],
                ['value' => 'on_or_after', 'label' => 'On or After'],
                ['value' => 'between', 'label' => 'Between (Date Range)'],
                ['value' => 'not_between', 'label' => 'Not Between'],
            ])
            ->setDefaultOperator('joining_date', 'between')
            ->addQuickOptions('joining_date', [
                ['label' => 'This Month', 'value' => ['start' => 'first day of this month', 'end' => 'today'], 'operator' => 'between'],
                ['label' => 'Last 30 Days', 'value' => ['start' => '-30 days', 'end' => 'today'], 'operator' => 'between'],
                ['label' => 'This Year', 'value' => ['start' => 'first day of january this year', 'end' => 'today'], 'operator' => 'between'],
                ['label' => 'Last Year', 'value' => ['start' => 'first day of january last year', 'end' => 'last day of december last year'], 'operator' => 'between'],
            ])

            // Filter configuration
            ->collapsible()
            ->collapsed(true)  // Start collapsed
            ->showActiveCount()
            ->rememberFilters(true, 'users_filter')
            ->liveFilter(true, 300)
            ->submitAction('/api/users');
    }

    private static function buildActionColumn($row)
    {
        // Filter control buttons
        $row->button('filter-toggle-btn')
            ->label('Filter')
            ->icon('filter')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Toggle filter panel', 'action' => 'toggle-filter']);

        $row->button('refresh-btn')
            ->label('Refresh')
            ->icon('refresh')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Refresh data']);

        // Main action buttons
        $row->button('create-btn')
            ->label('Create')
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-user-modal')
            ->data('action', 'open')
            ->meta(['tooltip' => 'Create new user']);

        // Create User Fullscreen Drawer
        $row->button('create-fullscreen-btn')
            ->label('Create Fullscreen')
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->data('type', 'drawer')
            ->data('component', 'create-user-drawer-fullscreen')
            ->data('action', 'open')
            ->variant('lt-contained')
            ->meta(['tooltip' => 'Create new user in fullscreen']);

        $row->button('export-btn')
            ->label('Export')
            ->icon('downloadcloud')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => 'Export data']);

        // More Options - Button with Dropdown Menu
        $row->button('more-btn')
            ->label('')
            ->icon('morehorizontal')
            ->size('md')
            ->variant('outline')
            // ->isIconButton(true)
            ->dropdown([
                'id' => 'more-options',
                'placement' => 'bottom-end',
                'offset' => [0, 8],
                'closeOnClick' => true,
                'closeOnEscape' => true,
                'items' => [
                    [
                        'id' => 'import-data',
                        'label' => 'Import Data',
                        'icon' => 'uploadcloud',
                        'action' => 'import',
                        'type' => 'button',
                    ],
                    [
                        'id' => 'bulk-actions',
                        'label' => 'Bulk Actions',
                        'icon' => 'listcheck',
                        'action' => 'bulk-actions',
                        'type' => 'button',
                    ],
                    [
                        'type' => 'divider',
                    ],
                    [
                        'id' => 'print',
                        'label' => 'Print',
                        'icon' => 'printer',
                        'action' => 'print',
                        'type' => 'button',
                    ],
                    [
                        'id' => 'archive',
                        'label' => 'Archive',
                        'icon' => 'archive',
                        'action' => 'archive',
                        'type' => 'button',
                    ],
                    [
                        'type' => 'divider',
                    ],
                    [
                        'id' => 'settings',
                        'label' => 'Settings',
                        'icon' => 'settings',
                        'action' => 'settings',
                        'type' => 'button',
                    ],
                ],
            ])
            ->meta(['tooltip' => 'More options']);
    }

    private static function getUserTableColumns()
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'user_id', 'label' => 'User ID', 'type' => 'badge', 'sortable' => true, 'width' => '120px'],
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true, 'filter_key' => 'name'],
            ['key' => 'email', 'label' => 'Email', 'sortable' => true, 'filterable' => true, 'filter_key' => 'email'],
            ['key' => 'phone', 'label' => 'Phone', 'sortable' => true, 'width' => '140px'],
            ['key' => 'department', 'label' => 'Department', 'sortable' => true, 'filterable' => true, 'filter_key' => 'department'],
            ['key' => 'designation', 'label' => 'Designation', 'sortable' => true, 'filterable' => true, 'filter_key' => 'designation'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'joining_date', 'label' => 'Joining Date', 'sortable' => true, 'filterable' => true, 'filter_key' => 'joining_date', 'width' => '120px'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => [
                [
                    'type' => 'button',
                    'name' => 'view',
                    'icon' => 'eyeopen',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'primary',
                    'tooltip' => 'View Details',
                    'dataAttributes' => ['type' => 'drawer', 'component' => 'view-user-drawer', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'edit',
                    'icon' => 'pen',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'success',
                    'tooltip' => 'Edit User',
                    'dataAttributes' => ['type' => 'modal', 'component' => 'edit-user-modal', 'action' => 'open'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'delete',
                    'icon' => 'binempty',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'error',
                    'tooltip' => 'Delete User',
                    'action' => 'delete',
                    'dataKey' => 'id',
                    'confirm' => [
                        'title' => 'Delete User',
                        'message' => 'Are you sure you want to delete this user? This action cannot be undone.',
                        'confirmText' => 'Delete',
                        'cancelText' => 'Cancel',
                        'dataUrl' => '/api/users/:id',
                        'method' => 'delete',
                    ],
                ],
            ]],
        ];
    }

    public static function getComponentDefinition($type, $componentName, $masterData)
    {
        if ($type === 'modal') {
            switch ($componentName) {
                case 'create-user-modal':
                    return self::buildCreateUserModal($masterData);
                case 'edit-user-modal':
                    return self::buildEditUserModal($masterData);
            }
        }

        if ($type === 'drawer') {
            switch ($componentName) {
                case 'view-user-drawer':
                    return self::buildViewUserDrawer($masterData);
                case 'view-user-drawer-fullscreen':
                    return self::buildViewUserDrawerFullscreen($masterData);
                case 'create-user-drawer-fullscreen':
                    return self::buildCreateUserDrawerFullscreen($masterData);
                case 'edit-user-drawer-fullscreen':
                    return self::buildEditUserDrawerFullscreen($masterData);
            }
        }

        return null;
    }

    private static function buildCreateUserModal($masterData)
    {
        $formComponent = UserForm::make('create-user-form', 'POST', '/api/users', $masterData);

        return ModalComponent::make('create-user-modal')
            ->children([
                ['type' => 'header', 'title' => 'Create New User', 'icon' => 'userplus'],
                ['type' => 'content', 'component' => $formComponent],
                ['type' => 'footer', 'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Create User', 'type' => 'submit', 'color' => 'primary', 'icon' => 'check', 'dataUrl' => '/api/users', 'method' => 'POST'],
                ]],
            ])
            ->ariaLabelledby('create-user-modal-title')
            ->toArray();
    }

    private static function buildEditUserModal($masterData)
    {
        $formComponent = UserForm::make('edit-user-form', 'PUT', '/api/users/:id', $masterData, '/api/users/:id');

        return ModalComponent::make('edit-user-modal')
            ->children([
                ['type' => 'header', 'title' => 'Edit User', 'icon' => 'pen'],
                ['type' => 'content', 'component' => $formComponent],
                ['type' => 'footer', 'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Update User', 'type' => 'submit', 'color' => 'success', 'icon' => 'check', 'dataUrl' => '/api/users/:id', 'method' => 'PUT'],
                ]],
            ])
            ->ariaLabelledby('edit-user-modal-title')
            ->toArray();
    }

    /**
     * Build view user drawer
     */
    private static function buildViewUserDrawer($masterData)
    {
        // Use the dedicated UserViewForm for read-only display
        $formComponent = UserViewForm::make('view-user-form', $masterData, '/api/users/:id');

        // Build drawer using DrawerComponent with the UserViewForm
        return DrawerComponent::make('view-user-drawer')
            ->anchor('right')
            ->width('900px')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->header([
                'title' => 'User Details',
                'subtitle' => 'View complete employee information',
                'icon' => 'user',
                'actions' => [
                    [
                        'type' => 'chip',
                        'label' => 'Active',
                        'color' => 'success',
                        'size' => 'sm',
                        'icon' => 'check',
                    ],
                    [
                        'type' => 'drawer',
                        'actionType' => 'drawer',
                        'label' => 'Edit',
                        'icon' => 'pen',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'primary',
                        'action' => 'edit',
                        'tooltip' => 'Edit User',
                        'component' => 'edit-user-drawer-fullscreen',
                    ],
                    [
                        'type' => 'button',
                        'label' => 'Delete',
                        'icon' => 'binempty',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'error',
                        'action' => 'delete',
                        'tooltip' => 'Delete User',
                        'confirm' => [
                            'title' => 'Delete User',
                            'message' => 'Are you sure you want to delete this user? This action cannot be undone.',
                            'confirmText' => 'Delete',
                            'cancelText' => 'Cancel',
                            'action' => 'delete',
                            'dataUrl' => '/api/users/:id',
                            'method' => 'delete',
                        ],
                    ],
                    [
                        'type' => 'button',
                        'label' => '',
                        'icon' => 'external',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'info',
                        'action' => 'openInNewTab',
                        'tooltip' => 'Open Profile in New Tab',
                        'isIconButton' => true,
                    ],
                    [
                        'type' => 'button',
                        'label' => '',
                        'icon' => 'downloadcloud',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'success',
                        'action' => 'download',
                        'tooltip' => 'Download User Profile',
                        'isIconButton' => true,
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
                    ['label' => 'View Fullscreen', 'color' => 'primary', 'icon' => 'expand', 'type' => 'drawer', 'component' => 'view-user-drawer-fullscreen', 'action' => 'view'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build view user drawer fullscreen
     */
    private static function buildViewUserDrawerFullscreen($masterData)
    {
        // Reuse the logic from the standard drawer but modify specifics for fullscreen
        $drawerData = self::buildViewUserDrawer($masterData);

        // Ensure it's not null and is an array before modifying
        if (is_array($drawerData)) {
            $drawerData['name'] = 'view-user-drawer-fullscreen';
            $drawerData['width'] = '100vw';
            $drawerData['height'] = '100vh';
        }

        return $drawerData;
    }

    /**
     * Build create user drawer fullscreen
     */
    private static function buildCreateUserDrawerFullscreen($masterData)
    {
        $formComponent = UserForm::make('create-user-draw-fs', 'POST', '/api/users', $masterData);

        return DrawerComponent::make('create-user-drawer-fullscreen')
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->header([
                'title' => 'New User (Full Screen)',
                'subtitle' => 'Complete onboarding walkthrough',
                'icon' => 'userplus',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Create User', 'type' => 'submit', 'color' => 'primary', 'icon' => 'check', 'dataUrl' => '/api/users', 'method' => 'POST'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build edit user drawer fullscreen
     */
    private static function buildEditUserDrawerFullscreen($masterData)
    {
        $formComponent = UserForm::make('edit-user-draw-fs', 'PUT', '/api/users/:id', $masterData, '/api/users/:id');

        return DrawerComponent::make('edit-user-drawer-fullscreen')
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->header([
                'title' => 'Edit User (Full Screen)',
                'subtitle' => 'Update detailed employee record',
                'icon' => 'pen',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Update User', 'type' => 'submit', 'color' => 'success', 'icon' => 'check', 'dataUrl' => '/api/users/:id', 'method' => 'PUT'],
                ],
            ])
            ->toArray();
    }
}
