<?php

/**
 * Complex Layout Example
 *
 * Demonstrates advanced layout patterns including nested sections,
 * tabs, accordions, modals, and conditional content.
 */

use Litepie\Layout\Components\ChartComponent;
use Litepie\Layout\Components\ListComponent;
use Litepie\Layout\Components\StatsComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\LayoutBuilder;

// Mock auth() helper for standalone script
if (! function_exists('auth')) {
    function auth()
    {
        return new class
        {
            public function id()
            {
                return 1;
            }

            public function user()
            {
                return (object) [
                    'id' => 1,
                    'name' => 'Admin User',
                    'permissions' => ['view_dashboard', 'manage_users'],
                ];
            }
        };
    }
}

// Mock now() helper
if (! function_exists('now')) {
    function now()
    {
        return new class
        {
            public function format($format)
            {
                return date($format);
            }
        };
    }
}

// Create complex admin panel layout
$layout = LayoutBuilder::create('advanced-admin-panel', 'view')
    ->title('Advanced Admin Panel')
    ->setSharedData([
        'user' => auth()->user(),
        'permissions' => auth()->user()->permissions ?? [],
        'current_date' => now()->format('F j, Y'),
    ])

// ===========================
// HEADER SECTION
// ===========================
    ->section('header', function ($section) {
        // Breadcrumb navigation
        $section->breadcrumb('navigation')
            ->addItem('Home', '/')
            ->addItem('Admin', '/admin')
            ->addItem('Dashboard')
            ->separator('›');

        // System alerts
        $section->alert('system-alert')
            ->content('System maintenance scheduled for tomorrow at 2:00 AM EST')
            ->variant('warning')
            ->icon('alert-triangle')
            ->dismissible(true)
            ->canSee(fn ($user) => $user->isAdmin());
    })

// ===========================
// BODY CONTENT SECTION
// ===========================
    ->section('body', function ($section) {
        // Main layout with sidebar
        $layout = $section->layout('main-layout');

        // ===========================
        // SIDEBAR
        // ===========================
        $sidebar = $layout->section('sidebar');

        // User profile card
        $sidebar->card('user-profile')
            ->title(auth()->user()->name ?? 'User')
            ->subtitle(auth()->user()->role ?? 'Member')
            ->icon('user')
            ->addField('email', 'Email', auth()->user()->email ?? '')
            ->addField('member_since', 'Member Since', 'Jan 2024')
            ->addAction('profile', 'View Profile', [
                'url' => '/profile',
                'icon' => 'user',
            ])
            ->addAction('settings', 'Settings', [
                'url' => '/settings',
                'icon' => 'settings',
            ])
            ->addAction('logout', 'Logout', [
                'url' => '/logout',
                'icon' => 'log-out',
            ]);

        // Quick stats
        $sidebar->stats('notifications')
            ->title('Notifications')
            ->value(12)
            ->icon('bell')
            ->addAction('view_all', 'View All', ['url' => '/notifications']);

        $sidebar->stats('messages')
            ->title('Messages')
            ->value(5)
            ->icon('mail')
            ->addAction('view_all', 'View All', ['url' => '/messages']);

        // ===========================
        // MAIN CONTENT AREA
        // ===========================
        $main = $layout->section('body');

        // Stats overview
        $main->grid('overview-stats')
            ->columns(4)
            ->gap('1rem')
            ->addComponent(
                StatsComponent::make('users-stat')
                    ->title('Users')
                    ->value(15234)
                    ->change('+12.5%')
                    ->trend('up')
                    ->icon('users')
                    ->permissions(['view-users'])
            )
            ->addComponent(
                StatsComponent::make('revenue-stat')
                    ->title('Revenue')
                    ->value(98650)
                    ->prefix('$')
                    ->change('+18.3%')
                    ->trend('up')
                    ->icon('dollar-sign')
                    ->permissions(['view-financials'])
            )
            ->addComponent(
                StatsComponent::make('orders-stat')
                    ->title('Orders')
                    ->value(523)
                    ->change('+7.2%')
                    ->trend('up')
                    ->icon('shopping-cart')
                    ->permissions(['view-orders'])
            )
            ->addComponent(
                StatsComponent::make('support-stat')
                    ->title('Support Tickets')
                    ->value(45)
                    ->change('-15.8%')
                    ->trend('down')
                    ->icon('help-circle')
                    ->permissions(['view-support'])
            );

        // Tabbed content area
        $main->tabs('main-tabs')

        // ====================
        // TAB 1: Overview
        // ====================
            ->addTab('overview', 'Overview', function ($tab) {
                // Charts grid
                $tab->grid('charts')
                    ->columns(2)

                // Sales chart
                    ->addComponent(
                        ChartComponent::make('sales-chart')
                            ->title('Sales Trends')
                            ->chartType('line')
                            ->dataUrl('/api/charts/sales')
                            ->permissions(['view-analytics'])
                    )

                // Revenue chart
                    ->addComponent(
                        ChartComponent::make('revenue-chart')
                            ->title('Revenue by Category')
                            ->chartType('doughnut')
                            ->dataUrl('/api/charts/revenue')
                            ->permissions(['view-financials'])
                    );

                // Recent activity
                $tab->card('recent-activity')
                    ->title('Recent Activity')
                    ->dataUrl('/api/activity/recent')
                    ->addField('activity_log', 'Activity');
            })

        // ====================
        // TAB 2: Users Management
        // ====================
            ->addTab('users', 'Users', function ($tab) {
                // User management toolbar
                $tab->card('user-toolbar')
                    ->title('User Management')
                    ->addAction('add_user', 'Add New User', [
                        'icon' => 'user-plus',
                        'url' => '/admin/users/create',
                        'variant' => 'primary',
                    ])
                    ->addAction('import', 'Import Users', [
                        'icon' => 'upload',
                        'url' => '/admin/users/import',
                    ])
                    ->addAction('export', 'Export Users', [
                        'icon' => 'download',
                        'url' => '/admin/users/export',
                    ])
                    ->permissions(['manage-users']);

                // Users table
                $tab->table('users-table')
                    ->dataUrl('/api/admin/users')
                    ->addColumn('id', 'ID', ['width' => '80px', 'sortable' => true])
                    ->addColumn('name', 'Name', ['sortable' => true])
                    ->addColumn('email', 'Email', ['sortable' => true])
                    ->addColumn('role', 'Role', ['filterable' => true])
                    ->addColumn('status', 'Status', ['filterable' => true])
                    ->addColumn('last_login', 'Last Login', ['sortable' => true])
                    ->addColumn('created_at', 'Joined', ['sortable' => true])
                    ->addAction('view', 'View', ['icon' => 'eye'])
                    ->addAction('edit', 'Edit', ['icon' => 'pencil'])
                    ->addAction('delete', 'Delete', [
                        'icon' => 'trash',
                        'confirm' => 'Are you sure you want to delete this user?',
                    ])
                    ->sortable(true)
                    ->filterable(true)
                    ->searchable(true)
                    ->selectable(true)
                    ->paginate(25)
                    ->permissions(['view-users']);
            })

        // ====================
        // TAB 3: Content Management
        // ====================
            ->addTab('content', 'Content', function ($tab) {
                // Accordion for different content types
                $tab->accordion('content-accordion')

                // Posts panel
                    ->addPanel('posts', 'Blog Posts', function ($panel) {
                        $panel->table('posts-table')
                            ->dataUrl('/api/admin/posts')
                            ->addColumn('title', 'Title', ['sortable' => true])
                            ->addColumn('author', 'Author')
                            ->addColumn('status', 'Status', ['filterable' => true])
                            ->addColumn('published_at', 'Published', ['sortable' => true])
                            ->addAction('edit', 'Edit', ['icon' => 'pencil'])
                            ->addAction('delete', 'Delete', ['icon' => 'trash'])
                            ->searchable(true)
                            ->paginate(15);
                    })

                // Pages panel
                    ->addPanel('pages', 'Pages', function ($panel) {
                        $panel->table('pages-table')
                            ->dataUrl('/api/admin/pages')
                            ->addColumn('title', 'Title', ['sortable' => true])
                            ->addColumn('slug', 'Slug')
                            ->addColumn('status', 'Status', ['filterable' => true])
                            ->addColumn('updated_at', 'Updated', ['sortable' => true])
                            ->addAction('edit', 'Edit', ['icon' => 'pencil'])
                            ->searchable(true)
                            ->paginate(15);
                    })

                // Media panel
                    ->addPanel('media', 'Media Library', function ($panel) {
                        $panel->document('media-manager')
                            ->title('Media Files')
                            ->uploadUrl('/api/admin/media/upload')
                            ->listUrl('/api/admin/media')
                            ->maxSize(10)
                            ->allowedTypes(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'])
                            ->showPreview(true);
                    })

                    ->allowMultiple(false)
                    ->expandedPanels(['posts']);
            })

        // ====================
        // TAB 4: Reports
        // ====================
            ->addTab('reports', 'Reports', function ($tab) {
                // Report filters
                $tab->form('report-filters')
                    ->method('GET')
                    ->action('/admin/reports')
                    ->addField('date_from', 'date', 'From Date')
                    ->addField('date_to', 'date', 'To Date')
                    ->addField('report_type', 'select', 'Report Type', [
                        'options' => [
                            'sales' => 'Sales Report',
                            'users' => 'Users Report',
                            'revenue' => 'Revenue Report',
                            'activity' => 'Activity Report',
                        ],
                    ])
                    ->addButton('generate', 'Generate Report', 'submit')
                    ->permissions(['view-reports']);

                // Report results
                $tab->card('report-results')
                    ->title('Report Results')
                    ->dataUrl('/api/admin/reports/results')
                    ->permissions(['view-reports']);
            })

        // ====================
        // TAB 5: Settings
        // ====================
            ->addTab('settings', 'Settings', function ($tab) {
                // Settings accordion
                $tab->accordion('settings-accordion')

                // General settings
                    ->addPanel('general', 'General Settings', function ($panel) {
                        $panel->form('general-settings')
                            ->action('/admin/settings/general')
                            ->method('POST')
                            ->addField('site_name', 'text', 'Site Name', ['required' => true])
                            ->addField('site_description', 'textarea', 'Description')
                            ->addField('admin_email', 'email', 'Admin Email', ['required' => true])
                            ->addField('timezone', 'select', 'Timezone', [
                                'options' => [
                                    'America/New_York' => 'Eastern',
                                    'America/Chicago' => 'Central',
                                    'America/Denver' => 'Mountain',
                                    'America/Los_Angeles' => 'Pacific',
                                ],
                            ])
                            ->addButton('save', 'Save Changes', 'submit');
                    })

                // Email settings
                    ->addPanel('email', 'Email Settings', function ($panel) {
                        $panel->form('email-settings')
                            ->action('/admin/settings/email')
                            ->method('POST')
                            ->addField('mail_driver', 'select', 'Mail Driver', [
                                'options' => ['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun'],
                            ])
                            ->addField('mail_host', 'text', 'SMTP Host')
                            ->addField('mail_port', 'number', 'SMTP Port')
                            ->addField('mail_username', 'text', 'Username')
                            ->addField('mail_password', 'password', 'Password')
                            ->addButton('save', 'Save Changes', 'submit');
                    })

                // Security settings
                    ->addPanel('security', 'Security Settings', function ($panel) {
                        $panel->form('security-settings')
                            ->action('/admin/settings/security')
                            ->method('POST')
                            ->addField('two_factor', 'checkbox', 'Enable Two-Factor Authentication')
                            ->addField('password_expiry', 'number', 'Password Expiry (days)')
                            ->addField('session_timeout', 'number', 'Session Timeout (minutes)')
                            ->addField('max_login_attempts', 'number', 'Max Login Attempts')
                            ->addButton('save', 'Save Changes', 'submit');
                    })

                    ->allowMultiple(false)
                    ->expandedPanels(['general']);
            })
            ->permissions(['manage-settings'])
            ->activeTab('overview');
    })

    // ===========================
    // FOOTER SECTION
    // ===========================
    ->section('footer', function ($section) {
        $section->grid('footer-content')
            ->columns(2)

        // Copyright
            ->addComponent(
                TextComponent::make('copyright')
                    ->content('© 2025 Your Company. All rights reserved.')
                    ->align('left')
            )

        // Footer links
            ->addComponent(
                ListComponent::make('footer-links')
                    ->listType('unordered')
                    ->addItem(['label' => 'Documentation', 'url' => '/docs'])
                    ->addItem(['label' => 'Support', 'url' => '/support'])
                    ->addItem(['label' => 'Privacy Policy', 'url' => '/privacy'])
                    ->addItem(['label' => 'Terms of Service', 'url' => '/terms'])
            );
    })

        // ===========================
        // MODALS
        // ===========================
    ->section('modals', function ($section) {
        // Confirmation modal
        $section->modal('confirm-delete')
            ->title('Confirm Deletion')
            ->content('Are you sure you want to delete this item? This action cannot be undone.')
            ->size('medium')
            ->closable(true)
            ->addButton('cancel', 'Cancel', ['variant' => 'secondary'])
            ->addButton('delete', 'Delete', ['variant' => 'danger']);

        // Help modal
        $section->modal('help-modal')
            ->title('Help & Documentation')
            ->content('Find answers to common questions and learn how to use the system.')
            ->size('large')
            ->closable(true)
            ->addButton('close', 'Close', ['variant' => 'primary']);
    })

        // Performance optimization with caching
    ->cache()
    ->ttl(600) // 10 minutes
    ->key('admin-panel-'.auth()->id())
    ->tags(['admin', 'dashboard', 'user-'.auth()->id()])

        // Event hooks
    ->beforeRender(function ($layout) {
        \Log::info('Admin panel rendering', [
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);
    })
    ->afterRender(function ($layout, $output) {
        \Log::info('Admin panel rendered', [
            'sections' => count($output['sections'] ?? []),
            'render_time' => microtime(true),
        ]);
    })

        // Resolve authorization for all components
    ->resolveAuthorization(auth()->user());

// Render the complex layout
return $layout->render();
