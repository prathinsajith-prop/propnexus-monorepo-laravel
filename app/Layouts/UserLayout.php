<?php

namespace App\Layouts;

use App\Forms\User\UserForm;
use App\Forms\User\UserViewForm;
use Litepie\Layout\Components\DrawerComponent;
use Litepie\Layout\Components\ModalComponent;
use Litepie\Layout\LayoutBuilder;

/**
 * UserLayout
 *
 * Comprehensive layout configuration for user management module.
 * Implements header with statistics, main content with filters and table,
 * and interactive components (drawers, modals) for CRUD operations.
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
     * @param  array  $masterData  Master data for dropdowns and filters
     * @return array Complete layout configuration
     */
    public static function make($masterData)
    {
        return LayoutBuilder::create('users', 'page')
            ->title(__('layout.user_management'))
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/users-master-data',
                'description' => __('layout.user_management_system'),
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
     * @param  \Litepie\Layout\Sections\LayoutSection  $section
     * @return void
     */
    private static function buildHeaderSection($section)
    {
        $section->meta([
            'description' => __('layout.page_header_description'),
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
            ->title(__('layout.user_management'))
            ->breadcrumbs([
                ['label' => __('layout.dashboard'), 'link' => '/', 'icon' => 'home'],
                ['label' => __('layout.users'), 'active' => true, 'icon' => 'users'],
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

        self::buildStatsCard($statsGrid, 'stat-total-users', __('layout.total_users'), '1,284', 'primary', 'users', '+12.5%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-active', __('layout.active_users'), '1,156', 'success', 'usercheck', '+3.2%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-new-users', __('layout.new_hires'), '42', 'info', 'userplus', '+15.8%', 'up', 'trend-1');
        self::buildStatsCard($statsGrid, 'stat-pending', __('layout.pending_review'), '15', 'warning', 'exclamationsquare', '-5.4%', 'down', 'trend-1');
    }

    /**
     * Build a statistics card with icon, value, and trend indicator
     *
     * @param  \Litepie\Layout\Sections\GridSection  $grid  Parent grid section
     * @param  string  $id  Unique card identifier
     * @param  string  $title  Card title/label
     * @param  string  $value  Primary metric value
     * @param  string  $color  Theme color (primary, success, info, warning, error)
     * @param  string  $icon  Lucide icon name
     * @param  string  $trend  Trend percentage (e.g., '+12.5%')
     * @param  string  $trendDir  Trend direction ('up' or 'down')
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
            'description' => __('layout.main_content_description'),
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
            ->addQuickFilter('joining_date', __('layout.joining_date'), 'daterange')
            ->addQuickFilter('status', __('layout.user_status'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_statuses')]],
                $masterData['statuses']
            ))
            ->addQuickFilter('department', __('layout.department'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_departments')]],
                $masterData['departments']
            ))
            ->addQuickFilter('employee_type', __('layout.employment_type'), 'select', array_merge(
                [['value' => '', 'label' => __('layout.all_types')]],
                $masterData['employee_types']
            ))

            // Quick Presets for common filter combinations
            ->addPreset('active_users', __('layout.active_users'), [
                'status' => 'Active',
            ])
            ->addPreset('new_hires', __('layout.new_hires_this_month'), [
                'joining_date' => ['start' => 'first day of this month', 'end' => 'today'],
            ])
            ->addPreset('engineering', __('layout.engineering_team'), [
                'department' => 'Engineering',
            ])

            // Status with all comparison operators
            ->addSelectFilter('status', __('layout.status'), $masterData['statuses'])
            ->addOperators('status', [
                ['value' => 'equal', 'label' => __('layout.equal_to')],
                ['value' => 'not_equal', 'label' => __('layout.not_equal_to')],
                ['value' => 'in', 'label' => __('layout.in')],
                ['value' => 'not_in', 'label' => __('layout.not_in')],
            ])
            ->setDefaultOperator('status', 'equal')

            // Department filter
            ->addMultiSelectFilter('department', __('layout.department'), $masterData['departments'])
            ->addOperators('department', [
                ['value' => 'in', 'label' => __('layout.in_any_of')],
                ['value' => 'not_in', 'label' => __('layout.not_in_none_of')],
            ])
            ->setDefaultOperator('department', 'in')

            // Designation filter
            ->addMultiSelectFilter('designation', __('layout.designation'), $masterData['designations'])
            ->addOperators('designation', [
                ['value' => 'in', 'label' => __('layout.in_any_of')],
                ['value' => 'not_in', 'label' => __('layout.not_in_none_of')],
                ['value' => 'like', 'label' => __('layout.like')],
                ['value' => 'not_like', 'label' => __('layout.not_like')],
            ])
            ->setDefaultOperator('designation', 'in')

            // Date range with comprehensive date operators
            ->addDateRangeFilter('joining_date', __('layout.joining_date'))
            ->addOperators('joining_date', [
                ['value' => 'equal', 'label' => __('layout.on_date')],
                ['value' => 'not_equal', 'label' => __('layout.not_equal_to')],
                ['value' => 'before', 'label' => __('layout.before')],
                ['value' => 'after', 'label' => __('layout.after')],
                ['value' => 'on_or_before', 'label' => __('layout.on_or_before')],
                ['value' => 'on_or_after', 'label' => __('layout.on_or_after')],
                ['value' => 'between', 'label' => __('layout.between')],
                ['value' => 'not_between', 'label' => __('layout.not_between')],
            ])
            ->setDefaultOperator('joining_date', 'between')
            ->addQuickOptions('joining_date', [
                ['label' => __('layout.this_month'), 'value' => ['start' => 'first day of this month', 'end' => 'today'], 'operator' => 'between'],
                ['label' => __('layout.last_30_days'), 'value' => ['start' => '-30 days', 'end' => 'today'], 'operator' => 'between'],
                ['label' => __('layout.this_year'), 'value' => ['start' => 'first day of january this year', 'end' => 'today'], 'operator' => 'between'],
                ['label' => __('layout.last_year'), 'value' => ['start' => 'first day of january last year', 'end' => 'last day of december last year'], 'operator' => 'between'],
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
            ->label(__('layout.filter'))
            ->icon('filter')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => __('layout.toggle_filter_panel'), 'action' => 'toggle-filter']);

        $row->button('refresh-btn')
            ->label(__('layout.refresh'))
            ->icon('refresh')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => __('layout.refresh_data')]);

        // Main action buttons
        $row->button('create-btn')
            ->label(__('layout.create'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->variant('lt-contained')
            ->data('type', 'modal')
            ->data('component', 'create-user-modal')
            ->data('action', 'create')
            ->meta(['tooltip' => __('layout.create_new_user')]);

        // Create User Fullscreen Drawer
        $row->button('create-fullscreen-btn')
            ->label(__('layout.create_fullscreen'))
            ->icon('plus')
            ->size('md')
            ->color('primary')
            ->data('type', 'drawer')
            ->data('component', 'create-user-drawer-fullscreen')
            ->data('action', 'create')
            ->variant('lt-contained')
            ->meta(['tooltip' => __('layout.create_new_user_fullscreen')]);

        $row->button('export-btn')
            ->label(__('layout.export'))
            ->icon('downloadcloud')
            ->size('md')
            ->variant('outline')
            ->meta(['tooltip' => __('layout.export_data')]);

        // More Options - Button with Dropdown Menu
        $row->button('more-btn')
            ->label('')
            ->icon('ellipsisvertical')
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
                        'label' => __('layout.import_data'),
                        'icon' => 'uploadcloud',
                        'action' => 'import',
                        'type' => 'button',
                    ],
                    [
                        'id' => 'bulk-actions',
                        'label' => __('layout.bulk_actions'),
                        'icon' => 'listcheck',
                        'action' => 'bulk-actions',
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

    private static function getUserTableColumns()
    {
        return [
            ['key' => 'id', 'label' => __('layout.id'), 'sortable' => true, 'width' => '80px', 'align' => 'center'],
            ['key' => 'user_id', 'label' => __('layout.user_id'), 'type' => 'badge', 'sortable' => true, 'width' => '120px'],
            ['key' => 'name', 'label' => __('layout.name'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'name'],
            ['key' => 'email', 'label' => __('layout.email'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'email'],
            ['key' => 'phone', 'label' => __('layout.phone'), 'sortable' => true, 'width' => '140px'],
            ['key' => 'department', 'label' => __('layout.department'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'department'],
            ['key' => 'designation', 'label' => __('layout.designation'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'designation'],
            ['key' => 'status', 'label' => __('layout.status'), 'type' => 'badge', 'sortable' => true, 'filterable' => true, 'filter_key' => 'status', 'width' => '100px'],
            ['key' => 'joining_date', 'label' => __('layout.joining_date'), 'sortable' => true, 'filterable' => true, 'filter_key' => 'joining_date', 'width' => '120px'],
            ['key' => 'actions', 'label' => __('layout.actions'), 'sortable' => false, 'width' => '150px', 'align' => 'center', 'type' => 'actions', 'actions' => [
                [
                    'type' => 'button',
                    'name' => 'view',
                    'icon' => 'eyeopen',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'primary',
                    'tooltip' => __('layout.view_details'),
                    'dataAttributes' => ['type' => 'drawer', 'component' => 'view-user-drawer', 'action' => 'view'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'edit',
                    'icon' => 'pen',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'success',
                    'tooltip' => __('layout.edit_user'),
                    'dataAttributes' => ['type' => 'modal', 'component' => 'edit-user-modal', 'action' => 'view'],
                    'dataKey' => 'id',
                ],
                [
                    'type' => 'button',
                    'name' => 'delete',
                    'icon' => 'binempty',
                    'variant' => 'outlined',
                    'size' => 'sm',
                    'color' => 'error',
                    'tooltip' => __('layout.delete_user'),
                    'action' => 'delete',
                    'dataKey' => 'id',
                    'confirm' => [
                        'title' => __('layout.delete_user'),
                        'message' => __('layout.delete_user_confirmation'),
                        'confirmText' => __('layout.delete'),
                        'cancelText' => __('layout.cancel'),
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
                ['type' => 'header', 'title' => __('layout.create_new_user'), 'icon' => 'userplus'],
                ['type' => 'content', 'component' => $formComponent],
                ['type' => 'footer', 'buttons' => [
                    ['label' => __('layout.cancel'), 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => __('layout.create_user'), 'type' => 'submit', 'color' => 'primary', 'icon' => 'check', 'dataUrl' => '/api/users', 'method' => 'POST'],
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
                ['type' => 'header', 'title' => __('layout.edit_user'), 'icon' => 'pen'],
                ['type' => 'content', 'component' => $formComponent],
                ['type' => 'footer', 'buttons' => [
                    ['label' => __('layout.cancel'), 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => __('layout.save_changes'), 'type' => 'submit', 'color' => 'success', 'icon' => 'check', 'dataUrl' => '/api/users/:id', 'method' => 'PUT'],
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
                'title' => __('layout.user_details'),
                'subtitle' => __('layout.view_complete_employee_information'),
                'icon' => 'user',
                'actions' => [
                    [
                        'type' => 'chip',
                        'label' => __('layout.active'),
                        'color' => 'success',
                        'size' => 'sm',
                        'icon' => 'check',
                    ],
                    [
                        'type' => 'drawer',
                        'actionType' => 'drawer',
                        'label' => __('layout.edit'),
                        'icon' => 'pen',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'primary',
                        'action' => 'edit',
                        'tooltip' => __('layout.edit_user'),
                        'component' => 'edit-user-drawer-fullscreen',
                    ],
                    [
                        'type' => 'button',
                        'label' => __('layout.delete'),
                        'icon' => 'binempty',
                        'variant' => 'outlined',
                        'size' => 'sm',
                        'color' => 'error',
                        'action' => 'delete',
                        'tooltip' => __('layout.delete_user'),
                        'confirm' => [
                            'title' => __('layout.delete_user'),
                            'message' => __('layout.delete_user_confirmation'),
                            'confirmText' => __('layout.delete'),
                            'cancelText' => __('layout.cancel'),
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
                        'tooltip' => __('layout.open_profile_in_new_tab'),
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
                        'tooltip' => __('layout.download_user_profile'),
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
                    ['label' => __('layout.close'), 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close'],
                    ['label' => __('layout.view_fullscreen'), 'color' => 'primary', 'icon' => 'expand', 'type' => 'drawer', 'component' => 'view-user-drawer-fullscreen', 'action' => 'view'],
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
                'title' => __('layout.new_user_full_screen'),
                'subtitle' => __('layout.complete_onboarding_walkthrough'),
                'icon' => 'userplus',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => __('layout.cancel'), 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => __('layout.create_user'), 'type' => 'submit', 'color' => 'primary', 'icon' => 'check', 'dataUrl' => '/api/users', 'method' => 'POST'],
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
                'title' => __('layout.edit_user_full_screen'),
                'subtitle' => __('layout.update_detailed_employee_record'),
                'icon' => 'pen',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => __('layout.cancel'), 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => __('layout.save_changes'), 'type' => 'submit', 'color' => 'success', 'icon' => 'check', 'dataUrl' => '/api/users/:id', 'method' => 'PUT'],
                ],
            ])
            ->toArray();
    }
}
