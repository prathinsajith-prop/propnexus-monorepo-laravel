<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Litepie\Layout\Components\DrawerComponent;
use Litepie\Layout\LayoutBuilder;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('general.index');
    }

    public function sample()
    {
        $masterData = $this->getMasterData();

        $layout = LayoutBuilder::create('users', 'page')
            ->title('User Management')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/users-master-data',
                'description' => 'User Management System',
            ])

            // Header Section - Grid with 2 columns (header info + stats)
            ->section('header', function ($section) {
                $section->meta([
                    'description' => 'Page header with breadcrumb navigation and statistics',
                    'styling' => 'container mx-auto px-4 py-6',
                ]);

                // Main Header Grid - 2 columns layout (12-column system)
                $headerGrid = $section->grid('header-main-grid')
                    ->columns(2)
                    ->gap('xl')
                    ->responsive(true);

                // Column 1: Header Information (breadcrumb + title) - spans 8 columns (66.67%)
                $headerInfoGrid = $headerGrid->grid('header-info-column')
                    ->columns(1)
                    ->gap('md')
                    ->gridColumnSpan(5);

                $headerInfoGrid->pageHeader('page-header')
                    ->title('User Management')
                    ->breadcrumbs([
                        ['label' => 'Dashboard', 'link' => '/', 'icon' => 'LiHome'],
                        ['label' => 'Users', 'active' => true, 'icon' => 'LiUsers'],
                    ])
                    ->align('left')
                    ->spacing('md')
                    ->titleVariant('h1')
                    ->titleSize('2xl')
                    ->titleWeight('bold')
                    ->titleGutterBottom(true);

                // Column 2: Statistics Grid - spans 4 columns (33.33%)
                $statsGrid = $headerGrid->grid('stats-grid')
                    ->columns(4)
                    ->gap('lg')
                    ->responsive(true)
                    ->gridColumnSpan(7);

                $statsGrid->card('stat-total-users')
                    ->title('Total Users')
                    ->content('100')
                    ->variant('outlined')
                    ->color('primary')
                    ->gridColumnSpan(3)  // Each card takes 3 out of 12 columns (25%)
                    ->meta([
                        'icon' => 'LiUsers',
                        'iconPosition' => 'top',
                        'iconColor' => '#3b82f6',
                        'iconSize' => 'md',
                        'iconBgColor' => '#eff6ff',
                        'trend' => '+15.2%',
                        'trendDirection' => 'up',
                    ]);

                $statsGrid->card('stat-active')
                    ->title('Active')
                    ->content('85')
                    ->variant('outlined')
                    ->color('success')
                    ->gridColumnSpan(3)  // Each card takes 3 out of 12 columns (25%)
                    ->meta([
                        'icon' => 'LiUserCheck',
                        'iconPosition' => 'top',
                        'iconColor' => '#10b981',
                        'iconSize' => 'md',
                        'iconBgColor' => '#f0fdf4',
                        'trend' => '+5.1%',
                        'trendDirection' => 'up',
                    ]);

                $statsGrid->card('stat-new-users')
                    ->title('New This Month')
                    ->content('12')
                    ->variant('outlined')
                    ->color('info')
                    ->gridColumnSpan(3)  // Each card takes 3 out of 12 columns (25%)
                    ->meta([
                        'icon' => 'LiUserPlus',
                        'iconPosition' => 'top',
                        'iconColor' => '#8b5cf6',
                        'iconSize' => 'md',
                        'iconBgColor' => '#f5f3ff',
                        'trend' => '+25.0%',
                        'trendDirection' => 'up',
                    ]);

                $statsGrid->card('stat-inactive')
                    ->title('Inactive')
                    ->content('3')
                    ->variant('outlined')
                    ->color('warning')
                    ->gridColumnSpan(3)  // Each card takes 3 out of 12 columns (25%)
                    ->meta([
                        'icon' => 'LiUserMinus',
                        'iconPosition' => 'top',
                        'iconColor' => '#f59e0b',
                        'iconSize' => 'md',
                        'iconBgColor' => '#fffbeb',
                        'trend' => '-2.4%',
                        'trendDirection' => 'down',
                    ]);
            })

            // Main Content Section - Grid with 2 Rows
            ->section('main', function ($section) use ($masterData) {
                $section->meta([
                    'description' => 'Main content area with filters and data table',
                    'styling' => 'container mx-auto px-4 py-6',
                ]);

                // Main Layout Grid - 2 rows structure
                $mainGrid = $section->grid('main-content-grid')
                    ->columns(1)
                    ->gap('md');

                // Row 1: Filter Controls (Left) + Action Buttons (Right)
                $controlsRow = $mainGrid->grid('controls-row')
                    ->columns(2)  // 2 columns: left (filter) + right (actions)
                    ->gap('md')
                    ->meta([
                        'styling' => 'bg-white rounded-lg shadow-sm p-4',
                    ]);

                // Left Column: Filter Component Only
                $filterColumn = $controlsRow->row('filter-column')
                    ->gap('none');

                // Advanced Filter Component (collapsible)
                $filterColumn->filter('users-filter')
                    // ->addSearchFilter('search', 'Search users...')
                    // Quick Filters - Most commonly used filters at the top
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
                    ->addPreset('full_time', 'Full-time Employees', [
                        'employee_type' => 'Full-time',
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

                // Right Column: Action Buttons (including filter controls)
                $actionsColumn = $controlsRow->row('actions-column')
                    ->gap('sm')
                    ->align('center')
                    ->justify('end');  // Align to right

                // Filter control buttons
                $actionsColumn->button('filter-toggle-btn')
                    ->label('Filter')
                    ->icon('LiFilter')
                    ->size('md')
                    ->variant('outline')
                    ->meta(['tooltip' => 'Toggle filter panel', 'action' => 'toggle-filter']);

                $actionsColumn->button('refresh-btn')
                    ->label('Refresh')
                    ->icon('LiRefresh')
                    ->size('md')
                    ->variant('outline')
                    ->meta(['tooltip' => 'Refresh data']);

                // Main action buttons
                $actionsColumn->button('create-btn')
                    ->label('Create')
                    ->icon('LiPlus')
                    ->size('md')
                    ->color('primary')
                    ->variant('lt-contained')
                    ->data('type', 'modal')
                    ->data('component', 'create-user-modal')
                    ->data('action', 'open')
                    ->meta(['tooltip' => 'Create new user']);

                // Create User Fullscreen Drawer
                $actionsColumn->button('create-fullscreen-btn')
                    ->label('Create Fullscreen')
                    ->icon('LiPlus')
                    ->size('md')
                    ->color('primary')
                    ->data('type', 'drawer')
                    ->data('component', 'create-user-drawer-fullscreen')
                    ->data('action', 'open')
                    ->variant('lt-contained')
                    ->meta(['tooltip' => 'Create new user in fullscreen']);

                $actionsColumn->button('export-btn')
                    ->label('Export')
                    ->icon('LiDownloadCloud')
                    ->size('md')
                    ->variant('outline')
                    ->meta(['tooltip' => 'Export data']);

                // More Options - Button with Dropdown Menu
                $actionsColumn->button('more-btn')
                    ->icon('LiEllipsisVertical')
                    ->label('')
                    ->size('md')
                    ->variant('outline')
                    ->data('type', 'dropdown')
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
                                'icon' => 'LiUploadCloud',
                                'action' => 'import',
                                'type' => 'button',
                            ],
                            [
                                'id' => 'bulk-actions',
                                'label' => 'Bulk Actions',
                                'icon' => 'LiListCheck',
                                'action' => 'bulk-actions',
                                'type' => 'button',
                            ],
                            [
                                'type' => 'divider',
                            ],
                            [
                                'id' => 'print',
                                'label' => 'Print',
                                'icon' => 'LiPrinter',
                                'action' => 'print',
                                'type' => 'button',
                            ],
                            [
                                'id' => 'archive',
                                'label' => 'Archive',
                                'icon' => 'LiArchive',
                                'action' => 'archive',
                                'type' => 'button',
                            ],
                            [
                                'type' => 'divider',
                            ],
                            [
                                'id' => 'settings',
                                'label' => 'Settings',
                                'icon' => 'LiSettings',
                                'action' => 'settings',
                                'type' => 'button',
                            ],
                        ],
                    ])
                    ->meta(['tooltip' => 'More options']);

                // Row 2: Data Table - Full Width
                $tableRow = $mainGrid->row('table-row')
                    ->gap('none');

                $tableRow->table('users-table')
                    ->dataUrl('/api/users')
                    ->columns([
                        [
                            'key' => 'id',
                            'label' => 'ID',
                            'sortable' => true,
                            'width' => '80px',
                            'align' => 'center',
                        ],
                        [
                            'key' => 'user_id',
                            'label' => 'User ID',
                            'type' => 'badge',
                            'sortable' => true,
                            'width' => '120px',
                        ],
                        [
                            'key' => 'name',
                            'label' => 'Name',
                            'sortable' => true,
                            'filterable' => true,
                            'filter_key' => 'name',
                        ],
                        [
                            'key' => 'email',
                            'label' => 'Email',
                            'sortable' => true,
                            'filterable' => true,
                            'filter_key' => 'email',
                        ],
                        [
                            'key' => 'phone',
                            'label' => 'Phone',
                            'sortable' => true,
                            'width' => '140px',
                        ],
                        [
                            'key' => 'department',
                            'label' => 'Department',
                            'sortable' => true,
                            'filterable' => true,
                            'filter_key' => 'department',
                        ],
                        [
                            'key' => 'designation',
                            'label' => 'Designation',
                            'sortable' => true,
                            'filterable' => true,
                            'filter_key' => 'designation',
                        ],
                        [
                            'key' => 'status',
                            'label' => 'Status',
                            'type' => 'badge',
                            'sortable' => true,
                            'filterable' => true,
                            'filter_key' => 'status',
                            'width' => '100px',
                        ],
                        [
                            'key' => 'joining_date',
                            'label' => 'Joining Date',
                            'sortable' => true,
                            'filterable' => true,
                            'filter_key' => 'joining_date',
                            'width' => '120px',
                        ],
                        [
                            'key' => 'actions',
                            'label' => 'Actions',
                            'sortable' => false,
                            'width' => '150px',
                            'align' => 'center',
                            'type' => 'actions',
                            'actions' => [
                                [
                                    'type' => 'button',
                                    'name' => 'view',
                                    'label' => '',
                                    'icon' => 'LiEyeOpen',
                                    'variant' => 'outlined',
                                    'size' => 'sm',
                                    'color' => 'primary',
                                    'tooltip' => 'View Details',
                                    'dataAttributes' => [
                                        'type' => 'drawer',
                                        'component' => 'view-user-drawer',
                                        'action' => 'open',
                                    ],
                                    'dataKey' => 'id',
                                ],
                                [
                                    'type' => 'button',
                                    'name' => 'edit',
                                    'label' => '',
                                    'icon' => 'LiPen',
                                    'variant' => 'outlined',
                                    'size' => 'sm',
                                    'color' => 'success',
                                    'tooltip' => 'Edit User',
                                    'dataAttributes' => [
                                        'type' => 'modal',
                                        'component' => 'edit-user-modal',
                                        'action' => 'open',
                                    ],
                                    'dataKey' => 'id',
                                ],
                                [
                                    'type' => 'button',
                                    'name' => 'delete',
                                    'label' => '',
                                    'icon' => 'LiBinEmpty',
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
                                        'action' => 'delete',
                                        'dataUrl' => '/api/users/:id',
                                        'method' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                    ])
                    ->pagination(true)
                    ->perPage(10)
                    ->hoverable(true)
                    ->striped(true)
                    // ->selection(true)
                    // ->batchActions()
                    ->meta([
                        'card' => true,
                        'responsive' => true,
                        'stickyHeader' => true,
                        'variant' => 'outlined',
                        'rowClickable' => true,
                        'rowActions' => [
                            'type' => 'drawer',
                            'component' => 'view-user-drawer',
                            'dataUrl' => '/api/users/:id',
                        ],
                        'batchActions' => [
                            ['label' => 'Deactivate', 'action' => 'batch_deactivate', 'icon' => 'LiUserMinus', 'color' => 'warning'],
                            ['label' => 'Delete', 'action' => 'batch_delete', 'icon' => 'LiBinEmpty', 'color' => 'error', 'confirm' => true],
                        ],
                    ]);
            })

            // Drawers Section - Contains all drawer components
            ->section('drawers', function ($section) use ($masterData) {
                $section->meta([
                    'description' => 'Drawer components for user management',
                    'type' => 'drawers',
                    'componentKey' => 'component', // Common key for finding components
                ]);

                // Build drawers using unified helper method
                $this->buildComponentInSection($section, 'drawer', 'view-user-drawer', $masterData);
                $this->buildComponentInSection($section, 'drawer', 'create-user-drawer-fullscreen', $masterData);
                $this->buildComponentInSection($section, 'drawer', 'view-user-drawer-fullscreen', $masterData);
                $this->buildComponentInSection($section, 'drawer', 'edit-user-drawer-fullscreen', $masterData);
            })

            // Modals Section - Contains all modal components
            ->section('modals', function ($section) use ($masterData) {
                $section->meta([
                    'description' => 'Modal components for user management',
                    'type' => 'modals',
                    'componentKey' => 'component', // Common key for finding components
                ]);

                // Build modal using unified helper method
                $this->buildComponentInSection($section, 'modal', 'create-user-modal', $masterData);
                $this->buildComponentInSection($section, 'modal', 'edit-user-modal', $masterData);
            })

            ->build();

        return response()->layout($layout);
    }

    /**
     * Method users - API endpoint for users data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function users(Request $request)
    {
        // Load users data from JSON file
        $jsonPath = storage_path('app/users.json');

        if (! file_exists($jsonPath)) {
            return response()->json([
                'error' => 'Users data not found',
            ], 404);
        }

        $allData = json_decode(file_get_contents($jsonPath), true);

        if (! is_array($allData)) {
            return response()->json([
                'error' => 'Invalid data format',
            ], 500);
        }

        // Get request parameters with proper type casting
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'user_id');
        $sortDirection = strtolower($request->input('sort_direction', 'asc'));
        $search = $request->input('search', '');

        // Validate sort direction
        if (! in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Validate per_page
        $perPage = max(1, min($perPage, 100)); // Limit between 1 and 100

        // Get filter parameters
        $filters = [
            'user_id' => $request->input('filter_user_id', ''),
            'name' => $request->input('filter_name', ''),
            'email' => $request->input('filter_email', ''),
            'phone' => $request->input('filter_phone', ''),
            'department' => $request->input('filter_department', ''),
            'designation' => $request->input('filter_designation', ''),
            'status' => $request->input('filter_status', ''),
            'employee_type' => $request->input('filter_employee_type', ''),
            'joining_date' => $request->input('filter_joining_date', ''),
        ];

        // Apply search filter (searches across all fields)
        if (! empty($search)) {
            $allData = array_filter($allData, function ($item) use ($search) {
                $searchLower = strtolower($search);
                foreach ($item as $value) {
                    if (stripos(strtolower((string) $value), $searchLower) !== false) {
                        return true;
                    }
                }

                return false;
            });
        }

        // Apply column-specific filters
        foreach ($filters as $column => $filterValue) {
            if (! empty($filterValue)) {
                $allData = array_filter($allData, function ($item) use ($column, $filterValue) {
                    if (! isset($item[$column])) {
                        return false;
                    }
                    $itemValue = strtolower((string) $item[$column]);
                    $filterLower = strtolower((string) $filterValue);

                    return stripos($itemValue, $filterLower) !== false;
                });
            }
        }

        // Sorting
        if (! empty($sortBy) && isset($allData[0][$sortBy])) {
            usort($allData, function ($a, $b) use ($sortBy, $sortDirection) {
                $aVal = $a[$sortBy] ?? '';
                $bVal = $b[$sortBy] ?? '';

                if ($sortDirection === 'asc') {
                    return $aVal <=> $bVal;
                } else {
                    return $bVal <=> $aVal;
                }
            });
        }

        // Pagination
        $total = count($allData);
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($allData, $offset, $perPage);

        return response()->json([
            'data' => array_values($paginatedData),
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ],
            'filters' => $filters,
            'sort' => [
                'by' => $sortBy,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Store a new user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Relaxed validation to support partial or dynamic payloads
        $validated = $request->validate([
            'user_id' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'employee_type' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $jsonPath = storage_path('app/users.json');

        // Load existing data
        $allData = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        if (! is_array($allData)) {
            $allData = [];
        }

        // Generate new ID
        $maxId = collect($allData)->max('id') ?? 100;
        $newId = $maxId + 1;

        // Capture ALL request data to support dynamic fields (like skills, notifications, etc.)
        $userData = $request->all();

        // Ensure core identifiers are set
        $userData['id'] = $newId;
        $userData['user_id'] = $userData['user_id'] ?? 'USR-' . str_pad($newId, 3, '0', STR_PAD_LEFT);
        $userData['created_at'] = now()->format('Y-m-d');
        $userData['updated_at'] = now()->format('Y-m-d');

        // Add to array
        $allData[] = $userData;

        // Save to file
        file_put_contents($jsonPath, json_encode($allData, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $userData,
        ], 201);
    }

    /**
     * Get individual user details
     *
     * @param  string  $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request, $identifier)
    {
        $jsonPath = storage_path('app/users.json');

        if (! file_exists($jsonPath)) {
            return response()->json([
                'error' => 'Users data not found',
            ], 404);
        }

        $allData = json_decode(file_get_contents($jsonPath), true);

        if (! is_array($allData)) {
            return response()->json([
                'error' => 'Invalid data format',
            ], 500);
        }

        // Find the user by ID or user_id
        $user = collect($allData)->first(function ($item) use ($identifier) {
            return $item['id'] == $identifier || $item['user_id'] == $identifier;
        });

        if (! $user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Get master data for sales orders
     * This endpoint returns all master data (customers, products, sales_reps, etc.)
     * for use in forms and filters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function masterData()
    {
        $masterData = $this->getMasterData();

        return response()->json([
            'success' => true,
            'data' => $masterData,
        ]);
    }

    /**
     * Get component section data by type and component name (from route parameters)
     * Returns the specific section configuration for modals, drawers, etc.
     * Used by: /layouts/{type}/{component}
     *
     * @param  string  $type
     * @param  string  $component
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComponentSection(Request $request, $type = null, $component = null)
    {
        // Support both route parameters and query parameters
        $type = $type ?? $request->input('type');
        $component = $component ?? $request->input('component');

        if (! $type || ! $component) {
            return response()->json([
                'success' => false,
                'message' => 'Type and component parameters are required',
            ], 400);
        }

        // Get master data for options
        $masterData = $this->getMasterData();

        // Build the section data based on type and component
        $sectionData = $this->getComponentDefinition($type, $component, $masterData);

        if (! $sectionData) {
            return response()->json([
                'success' => false,
                'message' => 'Component not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sectionData,
        ]);
    }

    /**
     * Build component directly in a section (works for both modals and drawers)
     * This is a unified method that accepts a section and builds any component type
     * Returns the complete section-based structure built with LayoutBuilder
     *
     * @param  mixed  $section  The section object to build the component in
     * @param  string  $type  The component type ('modal' or 'drawer')
     * @param  string  $component  The component name
     * @param  array  $masterData  Master data for form options
     * @return void
     */
    private function buildComponentInSection($section, $type, $component, $masterData)
    {
        $definition = $this->getComponentDefinition($type, $component, $masterData);

        if (! $definition) {
            return;
        }

        // Add the component definition as a custom component with raw array data
        $customComponent = \Litepie\Layout\Components\CustomComponent::make($component, $type)
            ->data($definition);

        $section->addComponent($customComponent);
    }

    /**
     * Get component definition structure (unified for all component types)
     * This is the single source of truth for all component structures
     *
     * @param  string  $type  Component type ('modal' or 'drawer')
     * @param  string  $componentName  Component name
     * @param  array  $masterData  Master data for form options
     * @return array|null
     */
    private function getComponentDefinition($type, $componentName, $masterData)
    {
        if ($type === 'modal') {
            return $this->getModalDefinition($type, $componentName, $masterData);
        } elseif ($type === 'drawer') {
            return $this->getDrawerDefinition($type, $componentName, $masterData);
        }

        return null;
    }

    /**
     * Get modal definition structure
     */
    private function getModalDefinition($modalName, $masterData)
    {
        switch ($modalName) {
            case 'create-user-modal':
                return $this->buildCreateUserModal($masterData);
            case 'edit-user-modal':
                return $this->buildEditUserModal($masterData);
        }

        return null;
    }

    /**
     * Build create user modal
     */
    /**
     * Get a comprehensive user form structure with diverse field types
     */
    private function getUserForm($formId, $method, $action, $masterData, $dataUrl = null)
    {
        $formLayout = LayoutBuilder::create($formId . '-layout', 'form');

        $formLayout->section('content', function ($section) use ($formId, $method, $action, $masterData, $dataUrl) {
            if ($dataUrl) {
                $section->meta([
                    'dataUrl' => $dataUrl,
                    'dataKey' => 'data',
                ]);
            }

            $form = $section->form($formId)
                ->action($action)
                ->method($method)
                ->columns(2)
                ->gap('lg');

            // --- Personal Information Section ---
            $personalGroup = $form->group('personal-info')
                ->title('Personal Information')
                ->icon('LiUser')
                ->variant('bordered')
                ->columns(2);

            $personalGroup->text('name')->label('Full Name')->placeholder('John Doe')->required(true)->width(6);
            $personalGroup->email('email')->label('Email Address')->placeholder('john@example.com')->required(true)->width(6);

            // Radio buttons for Gender
            $personalGroup->radio('gender')
                ->label('Gender')
                ->options($masterData['genders'])
                ->inline()   // Makes it horizontal
                ->width(6);

            $personalGroup->date('date_of_birth')
                ->label('Date of Birth')
                ->width(6);

            $personalGroup->text('phone')->label('Phone Number')->placeholder('+1-202-555-0123')->width(6);
            $personalGroup->select('blood_group')->label('Blood Group')->options($masterData['blood_groups'])->width(6);

            // --- Employment Section ---
            $employmentGroup = $form->group('employment-info')
                ->title('Employment Info')
                ->icon('LiBriefcase')
                ->variant('bordered')
                ->columns(2);

            $employmentGroup->select('department')->label('Department')->options($masterData['departments'])->width(6);
            $employmentGroup->select('designation')->label('Designation')->options($masterData['designations'])->width(6);

            // Using a Switch for Status if it's binary, or stay with Select for multiple
            $employmentGroup->select('status')->label('Account Status')->options($masterData['statuses'])->width(6);
            $employmentGroup->date('joining_date')->label('Joining Date')->required(true)->width(6);

            // --- Media & Files ---
            $mediaGroup = $form->group('media-info')
                ->title('Documents & Media')
                ->icon('LiImage')
                ->variant('bordered')
                ->columns(1);

            $mediaGroup->file('profile_image')
                ->label('Profile Image')
                ->accept('image/*')
                ->maxSize(2048);

            $mediaGroup->file('resume')
                ->label('Expertise Resume (PDF)')
                ->accept('.pdf')
                ->maxSize(5120);

            // --- Account & Security ---
            $securityGroup = $form->group('security-info')
                ->title('Account & Security')
                ->icon('LiLock')
                ->variant('bordered')
                ->columns(2);

            $securityGroup->password('password')->label('Initial Password')->placeholder('••••••••')->width(6);
            $securityGroup->select('roles')
                ->label('Assigned Roles')
                ->options($masterData['roles'])
                ->multiple(true)
                ->width(6);

            // --- Technical Skills ---
            $skillsGroup = $form->group('skills-info')
                ->title('Technical Skills')
                ->icon('LiStar')
                ->variant('bordered')
                ->columns(3);

            foreach ($masterData['skills'] as $skill) {
                $skillsGroup->rating('skills[' . $skill['value'] . ']')
                    ->label($skill['label'])
                    ->max(5)
                    ->width(4);
            }

            // --- Address & Additional Info ---
            $addressGroup = $form->group('address-info')
                ->title('Address & Preferences')
                ->icon('LiMapPin')
                ->variant('bordered')
                ->columns(2);

            $addressGroup->textarea('address')
                ->label('Home Address')
                ->placeholder('Enter full residential address...')
                ->rows(3)
                ->width(12);

            $addressGroup->number('expected_salary')
                ->label('Expected Salary')
                ->prefix('$')
                ->width(6);

            $addressGroup->range('experience')
                ->label('Years of Experience')
                ->min(0)
                ->max(40)
                ->step(1)
                ->width(6);

            $addressGroup->color('theme_preference')
                ->label('UI Theme Color')
                ->width(6);

            $addressGroup->url('linkedin_profile')
                ->label('LinkedIn Profile')
                ->placeholder('https://linkedin.com/in/...')
                ->width(6);

            $addressGroup->checkbox('notifications')
                ->label('Enable Email Notifications')
                ->width(6);

            $addressGroup->textarea('notes')
                ->label('Administrative Notes')
                ->placeholder('Add any relevant background information...')
                ->rows(2)
                ->width(12);
        });

        $builtForm = $formLayout->build();
        $formArray = $builtForm->toArray();

        return $formArray['sections']['content']['components'][0] ?? null;
    }

    /**
     * Build create user modal
     */
    private function buildCreateUserModal($masterData)
    {
        $formComponent = $this->getUserForm('create-user-form', 'POST', '/api/users', $masterData);

        $modal = \Litepie\Layout\Components\ModalComponent::make('create-user-modal')
            ->title('Create New User');

        $modalArray = $modal->toArray();
        $modalArray['children'] = [
            ['type' => 'header', 'title' => 'New User', 'subtitle' => 'Add a new member to the team', 'icon' => 'LiUserPlus'],
            $formComponent,
            ['type' => 'footer', 'buttons' => [
                ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                ['label' => 'Create User', 'type' => 'submit', 'color' => 'primary', 'icon' => 'LiCheck', 'dataUrl' => '/api/users', 'method' => 'POST'],
            ]],
        ];

        return $modalArray;
    }

    /**
     * Get drawer definition structure using LayoutBuilder components
     */
    private function getDrawerDefinition($drawerName, $masterData)
    {
        switch ($drawerName) {
            case 'view-user-drawer':
                return $this->buildViewUserDrawer($masterData);
            case 'view-user-drawer-fullscreen':
                return $this->buildViewUserDrawerFullscreen($masterData);
            case 'create-user-drawer-fullscreen':
                return $this->buildCreateUserDrawerFullscreen($masterData);
            case 'edit-user-drawer-fullscreen':
                return $this->buildEditUserDrawerFullscreen($masterData);
        }

        return null;
    }

    /**
     * Build edit user modal
     */
    private function buildEditUserModal($masterData)
    {
        $formComponent = $this->getUserForm('edit-user-form', 'PUT', '/api/users/:id', $masterData, '/api/users/:id');

        $modal = \Litepie\Layout\Components\ModalComponent::make('edit-user-modal')
            ->title('Edit User');

        $modalArray = $modal->toArray();
        $modalArray['children'] = [
            ['type' => 'header', 'title' => 'Edit User', 'subtitle' => 'Update member information', 'icon' => 'LiPen'],
            $formComponent,
            ['type' => 'footer', 'buttons' => [
                ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                ['label' => 'Update User', 'type' => 'submit', 'color' => 'success', 'icon' => 'LiCheck', 'dataUrl' => '/api/users/:id', 'method' => 'PUT'],
            ]],
        ];

        return $modalArray;
    }

    /**
     * Build view user drawer
     */
    private function buildViewUserDrawer($masterData)
    {
        // Build the content layout using LayoutBuilder
        $contentLayout = LayoutBuilder::create('view-user-content', 'view');

        $contentLayout->section('content', function ($section) {
            // Data source configuration
            $section->meta([
                'dataUrl' => '/api/users/:id',
                'dataKey' => 'data',
            ]);

            $viewForm = $section->form('view-user-details-form')
                ->columns(1)
                ->gap('lg');

            // --- Section 1: Personal Profile ---
            $personalGroup = $viewForm->group('personal-information')
                ->title('Personal Information')
                ->icon('LiUser')
                ->variant('bordered')
                ->columns(2);

            $personalGroup->text('name')->label('Full Name')->readonly(true);
            $personalGroup->text('user_id')->label('Employee ID')->readonly(true);
            $personalGroup->text('gender')->label('Gender')->readonly(true);
            $personalGroup->text('date_of_birth')->label('Date of Birth')->readonly(true);
            $personalGroup->text('blood_group')->label('Blood Group')->readonly(true);
            $personalGroup->text('phone')->label('Primary Phone')->readonly(true);

            // --- Section 2: Family Details ---
            $familyGroup = $viewForm->group('family-details')
                ->title('Family Details')
                ->icon('LiUsers')
                ->variant('bordered')
                ->columns(2);

            $familyGroup->text('father_name')->label('Father\'s Name')->readonly(true);
            $familyGroup->text('mother_name')->label('Mother\'s Name')->readonly(true);

            // --- Section 3: Employment & Status ---
            $employmentGroup = $viewForm->group('employment-details')
                ->title('Employment Record')
                ->icon('LiBriefcase')
                ->variant('bordered')
                ->columns(2);

            $employmentGroup->text('department')->label('Department')->readonly(true);
            $employmentGroup->text('designation')->label('Designation')->readonly(true);
            $employmentGroup->text('employee_type')->label('Engagement Type')->readonly(true);
            $employmentGroup->text('joining_date')->label('Commencement Date')->readonly(true);
            $employmentGroup->text('status')->label('Current Status')->readonly(true);

            // --- Section 4: Contact & Location ---
            $contactGroup = $viewForm->group('contact-address')
                ->title('Contact & Address')
                ->icon('LiMapPin')
                ->variant('bordered')
                ->columns(2);

            $contactGroup->text('email')->label('Professional Email')->readonly(true)->width(12);
            $contactGroup->textarea('address')->label('Residential Address')->readonly(true)->rows(2)->width(12);
            $contactGroup->text('city')->label('City')->readonly(true);
            $contactGroup->text('state')->label('Province/State')->readonly(true);
            $contactGroup->text('country')->label('Country')->readonly(true);
            $contactGroup->text('postal_code')->label('Postal Code')->readonly(true);

            // --- Section 5: Documents & Media ---
            $docsGroup = $viewForm->group('documents-info')
                ->title('Verification Documents')
                ->icon('documentfull')
                ->variant('bordered')
                ->columns(1);

            $docsGroup->text('profile_image')->label('Profile Picture')->readonly(true);
            $docsGroup->text('resume')->label('Expertise Resume')->readonly(true);
            $docsGroup->text('certificate')->label('Certification')->readonly(true);

            // --- Section 6: Administrative Notes ---
            $notesGroup = $viewForm->group('additional-info')
                ->title('Administrative Insights')
                ->icon('LiInfoCircle')
                ->variant('bordered')
                ->columns(1);

            $notesGroup->textarea('notes')->label('System Remarks')->readonly(true)->rows(3);

            // --- Section 7: Audit Metadata ---
            $auditGroup = $viewForm->group('audit-metadata')
                ->title('Audit Trail')
                ->icon('LiClock')
                ->variant('bordered')
                ->columns(2);

            $auditGroup->text('created_at')->label('System Onboarding')->readonly(true);
            $auditGroup->text('updated_at')->label('Last Record Update')->readonly(true);
        });

        // Build the content and get its array representation
        $builtContent = $contentLayout->build();
        $contentArray = $builtContent->toArray();

        // Extract the form component
        $formComponent = $contentArray['sections']['content']['components'][0] ?? null;

        // Now build drawer using DrawerComponent with the built content
        return DrawerComponent::make('view-user-drawer')
            ->anchor('right')
            ->width('900px')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->header([
                'title' => 'User Details',
                'subtitle' => 'View complete employee information',
                'icon' => 'LiUser',
                'actions' => [
                    [
                        'type' => 'chip',
                        'label' => 'Active',
                        'color' => 'success',
                        'size' => 'sm',
                        'icon' => 'LiCheck',
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
                        'tooltip' => 'Edit User',
                        'component' => 'edit-user-drawer-fullscreen',
                    ],
                    [
                        'type' => 'button',
                        'label' => 'Delete',
                        'icon' => 'LiBinEmpty',
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
                        'icon' => 'LiExternal',
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
                        'icon' => 'LiDownloadCloud',
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
                    ['label' => 'View User', 'color' => 'primary', 'icon' => 'LiEye', 'type' => 'drawer', 'component' => 'view-user-drawer-fullscreen', 'action' => 'view'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build view user drawer fullscreen
     */
    private function buildViewUserDrawerFullscreen($masterData)
    {
        // Reuse the logic from the standard drawer but modify specifics for fullscreen
        $drawerData = $this->buildViewUserDrawer($masterData);

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
    private function buildCreateUserDrawerFullscreen($masterData)
    {
        $formComponent = $this->getUserForm('create-user-draw-fs', 'POST', '/api/users', $masterData);

        return DrawerComponent::make('create-user-drawer-fullscreen')
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->header([
                'title' => 'New User (Full Screen)',
                'subtitle' => 'Complete onboarding walkthrough',
                'icon' => 'LiUserPlus',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Create User', 'type' => 'submit', 'color' => 'primary', 'icon' => 'LiCheck', 'dataUrl' => '/api/users', 'method' => 'POST'],
                ],
            ])
            ->toArray();
    }

    /**
     * Build edit user drawer fullscreen
     */
    private function buildEditUserDrawerFullscreen($masterData)
    {
        $formComponent = $this->getUserForm('edit-user-draw-fs', 'PUT', '/api/users/:id', $masterData, '/api/users/:id');

        return DrawerComponent::make('edit-user-drawer-fullscreen')
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->header([
                'title' => 'Edit User (Full Screen)',
                'subtitle' => 'Update detailed employee record',
                'icon' => 'LiPen',
            ])
            ->content([
                'component' => $formComponent,
            ])
            ->footer([
                'type' => 'button-group',
                'buttons' => [
                    ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                    ['label' => 'Update User', 'type' => 'submit', 'color' => 'success', 'icon' => 'LiCheck', 'dataUrl' => '/api/users/:id', 'method' => 'PUT'],
                ],
            ])
            ->toArray();
    }

    /**
     * Get master data for sales orders
     *
     * @return array
     */
    private function getMasterData()
    {
        return [
            'departments' => [
                ['value' => 'Engineering', 'label' => 'Engineering'],
                ['value' => 'Product', 'label' => 'Product'],
                ['value' => 'Design', 'label' => 'Design'],
                ['value' => 'Marketing', 'label' => 'Marketing'],
                ['value' => 'Sales', 'label' => 'Sales'],
                ['value' => 'Human Resources', 'label' => 'Human Resources'],
                ['value' => 'Analytics', 'label' => 'Analytics'],
                ['value' => 'Finance', 'label' => 'Finance'],
                ['value' => 'Operations', 'label' => 'Operations'],
                ['value' => 'Customer Support', 'label' => 'Customer Support'],
            ],
            'designations' => [
                ['value' => 'Senior Developer', 'label' => 'Senior Developer'],
                ['value' => 'Junior Developer', 'label' => 'Junior Developer'],
                ['value' => 'Product Manager', 'label' => 'Product Manager'],
                ['value' => 'UI/UX Designer', 'label' => 'UI/UX Designer'],
                ['value' => 'Marketing Specialist', 'label' => 'Marketing Specialist'],
                ['value' => 'DevOps Engineer', 'label' => 'DevOps Engineer'],
                ['value' => 'HR Manager', 'label' => 'HR Manager'],
                ['value' => 'Data Analyst', 'label' => 'Data Analyst'],
                ['value' => 'QA Engineer', 'label' => 'QA Engineer'],
                ['value' => 'Sales Manager', 'label' => 'Sales Manager'],
                ['value' => 'Team Lead', 'label' => 'Team Lead'],
                ['value' => 'Intern', 'label' => 'Intern'],
            ],
            'employee_types' => [
                ['value' => 'Full-time', 'label' => 'Full-time'],
                ['value' => 'Part-time', 'label' => 'Part-time'],
                ['value' => 'Contract', 'label' => 'Contract'],
                ['value' => 'Intern', 'label' => 'Intern'],
            ],
            'statuses' => [
                ['value' => 'Active', 'label' => 'Active', 'color' => 'success'],
                ['value' => 'Inactive', 'label' => 'Inactive', 'color' => 'warning'],
                ['value' => 'Suspended', 'label' => 'Suspended', 'color' => 'error'],
            ],
            'genders' => [
                ['value' => 'Male', 'label' => 'Male'],
                ['value' => 'Female', 'label' => 'Female'],
                ['value' => 'Other', 'label' => 'Other'],
            ],
            'blood_groups' => [
                ['value' => 'A+', 'label' => 'A+'],
                ['value' => 'A-', 'label' => 'A-'],
                ['value' => 'B+', 'label' => 'B+'],
                ['value' => 'B-', 'label' => 'B-'],
                ['value' => 'AB+', 'label' => 'AB+'],
                ['value' => 'AB-', 'label' => 'AB-'],
                ['value' => 'O+', 'label' => 'O+'],
                ['value' => 'O-', 'label' => 'O-'],
            ],
            'countries' => [
                ['value' => 'United States', 'label' => 'United States'],
                ['value' => 'Canada', 'label' => 'Canada'],
                ['value' => 'United Kingdom', 'label' => 'United Kingdom'],
                ['value' => 'India', 'label' => 'India'],
                ['value' => 'Australia', 'label' => 'Australia'],
            ],
            'customers' => [
                ['value' => 'CUST-001', 'label' => 'Acme Corp'],
                ['value' => 'CUST-002', 'label' => 'Global Industries'],
                ['value' => 'CUST-003', 'label' => 'Tech Solutions'],
            ],
            'sales_reps' => [
                ['value' => 'REP-001', 'label' => 'John Doe'],
                ['value' => 'REP-002', 'label' => 'Jane Smith'],
            ],
            'products' => [
                ['value' => 'PROD-001', 'label' => 'Premium Plan'],
                ['value' => 'PROD-002', 'label' => 'Standard Plan'],
                ['value' => 'PROD-003', 'label' => 'Basic Plan'],
            ],
            'payment_methods' => [
                ['value' => 'Credit Card', 'label' => 'Credit Card'],
                ['value' => 'Bank Transfer', 'label' => 'Bank Transfer'],
                ['value' => 'PayPal', 'label' => 'PayPal'],
            ],
            'skills' => [
                ['value' => 'php', 'label' => 'PHP'],
                ['value' => 'laravel', 'label' => 'Laravel'],
                ['value' => 'javascript', 'label' => 'JavaScript'],
                ['value' => 'vue', 'label' => 'Vue.js'],
                ['value' => 'react', 'label' => 'React'],
                ['value' => 'python', 'label' => 'Python'],
                ['value' => 'sql', 'label' => 'SQL'],
                ['value' => 'docker', 'label' => 'Docker'],
                ['value' => 'aws', 'label' => 'AWS'],
            ],
            'roles' => [
                ['value' => 'admin', 'label' => 'Administrator'],
                ['value' => 'user', 'label' => 'Standard User'],
                ['value' => 'manager', 'label' => 'Manager'],
                ['value' => 'editor', 'label' => 'Editor'],
            ],
        ];
    }

    /**
     * Update an existing user
     */
    public function update(Request $request, $identifier)
    {
        // Relaxed validation to support partial updates
        $validated = $request->validate([
            'user_id' => 'sometimes|string|max:50',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
        ]);

        $jsonPath = storage_path('app/users.json');
        if (! file_exists($jsonPath)) {
            return response()->json(['error' => 'Users data not found'], 404);
        }

        $allData = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($allData)) {
            return response()->json(['error' => 'Invalid data format'], 500);
        }

        $userIndex = null;
        foreach ($allData as $index => $item) {
            if ($item['id'] == $identifier || $item['user_id'] == $identifier) {
                $userIndex = $index;
                break;
            }
        }

        if ($userIndex === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Merge existing user data with ALL request data to preserve all keys
        $allData[$userIndex] = array_merge($allData[$userIndex], $request->all());
        $allData[$userIndex]['updated_at'] = now()->format('Y-m-d');

        file_put_contents($jsonPath, json_encode($allData, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $allData[$userIndex]
        ]);
    }

    /**
     * Delete a user
     */
    public function destroy(Request $request, $identifier)
    {
        $jsonPath = storage_path('app/users.json');
        if (! file_exists($jsonPath)) {
            return response()->json(['error' => 'Users data not found'], 404);
        }
        $allData = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($allData)) {
            return response()->json(['error' => 'Invalid data format'], 500);
        }
        $userIndex = null;
        foreach ($allData as $index => $item) {
            if ($item['id'] == $identifier || $item['user_id'] == $identifier) {
                $userIndex = $index;
                break;
            }
        }
        if ($userIndex === null) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $deletedUser = $allData[$userIndex];
        array_splice($allData, $userIndex, 1);
        file_put_contents($jsonPath, json_encode($allData, JSON_PRETTY_PRINT));

        return response()->json(['success' => true, 'message' => 'User deleted successfully', 'data' => $deletedUser]);
    }
}
