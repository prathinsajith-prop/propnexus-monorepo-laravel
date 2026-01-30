# Examples

Comprehensive examples demonstrating common patterns and use cases with the Litepie Layout Builder.

## Table of Contents

- [Basic Examples](#basic-examples)
  - [Simple Card](#simple-card)
  - [Basic Form](#basic-form)
  - [Data Table](#data-table)
- [Intermediate Examples](#intermediate-examples)
  - [Dashboard with Stats](#dashboard-with-stats)
  - [User Profile Page](#user-profile-page)
  - [Tabbed Interface](#tabbed-interface)
- [Advanced Examples](#advanced-examples)
  - [Multi-Step Wizard](#multi-step-wizard)
  - [E-commerce Product Page](#e-commerce-product-page)
  - [Admin Dashboard](#admin-dashboard)
- [Pattern Examples](#pattern-examples)
  - [Master-Detail View](#master-detail-view)
  - [Nested Grids](#nested-grids)
  - [Conditional Content](#conditional-content)
  - [Real-time Data](#real-time-data)

---

## Basic Examples

### Simple Card

Display information in a card component.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('simple-card-example')
    ->section('main', function ($section) {
        $section->card('user-info')
            ->title('John Doe')
            ->subtitle('Software Developer')
            ->icon('user')
            ->addField('email', 'Email', 'john@example.com')
            ->addField('phone', 'Phone', '+1 234 567 8900')
            ->addField('location', 'Location', 'San Francisco, CA')
            ->addAction('edit', 'Edit Profile', ['url' => '/profile/edit'])
            ->addAction('message', 'Send Message', ['url' => '/messages/new']);
    });

return $layout->render();
```

**Output Structure:**
```json
{
  "name": "simple-card-example",
  "sections": {
    "main": {
      "components": [
        {
          "type": "card",
          "name": "user-info",
          "title": "John Doe",
          "subtitle": "Software Developer",
          "icon": "user",
          "fields": [
            {"name": "email", "label": "Email", "value": "john@example.com"},
            {"name": "phone", "label": "Phone", "value": "+1 234 567 8900"},
            {"name": "location", "label": "Location", "value": "San Francisco, CA"}
          ],
          "actions": [
            {"name": "edit", "label": "Edit Profile", "url": "/profile/edit"},
            {"name": "message", "label": "Send Message", "url": "/messages/new"}
          ]
        }
      ]
    }
  }
}
```

---

### Basic Form

Create a simple contact form.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('contact-form')
    ->section('main', function ($section) {
        $section->form('contact')
            ->title('Contact Us')
            ->description('Send us a message and we\'ll get back to you soon.')
            ->action('/contact/submit')
            ->method('POST')
            ->addField('name', 'text', 'Your Name', [
                'placeholder' => 'John Doe',
                'required' => true,
            ])
            ->addField('email', 'email', 'Email Address', [
                'placeholder' => 'john@example.com',
                'required' => true,
            ])
            ->addField('subject', 'text', 'Subject', [
                'placeholder' => 'What is this regarding?',
                'required' => true,
            ])
            ->addField('message', 'textarea', 'Message', [
                'placeholder' => 'Your message here...',
                'rows' => 6,
                'required' => true,
            ])
            ->addButton('submit', 'Send Message', 'submit')
            ->validationRules([
                'name' => 'required|min:2|max:100',
                'email' => 'required|email',
                'subject' => 'required|max:200',
                'message' => 'required|min:10',
            ]);
    });

return $layout->render();
```

---

### Data Table

Display a list of users in a table.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('users-table')
    ->section('main', function ($section) {
        $section->table('users')
            ->title('Users')
            ->description('Manage system users')
            ->dataUrl('/api/users')
            ->addColumn('id', 'ID', ['width' => '60px', 'sortable' => true])
            ->addColumn('name', 'Name', ['sortable' => true])
            ->addColumn('email', 'Email', ['sortable' => true])
            ->addColumn('role', 'Role', ['filterable' => true])
            ->addColumn('status', 'Status', ['filterable' => true])
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
            ->paginate(25);
    });

return $layout->render();
```

---

## Intermediate Examples

### Dashboard with Stats

Create a dashboard with statistics cards.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('dashboard')
    ->title('Dashboard')
    
    // Header with breadcrumbs
    ->section('header', function ($section) {
        $section->breadcrumb('navigation')
            ->addItem('Home', '/')
            ->addItem('Dashboard');
    })
    
    // Main content
    ->section('main', function ($section) {
        // Stats grid
        $section->grid('stats-grid')
            ->columns(4)
            ->addComponent(
                $section->stats('total-users')
                    ->title('Total Users')
                    ->icon('users')
                    ->dataUrl('/api/stats/users')
                    ->value(15234)
                    ->change('+12.5%')
                    ->trend('up')
            )
            ->addComponent(
                $section->stats('active-sessions')
                    ->title('Active Sessions')
                    ->icon('activity')
                    ->dataUrl('/api/stats/sessions')
                    ->value(892)
                    ->change('+5.2%')
                    ->trend('up')
            )
            ->addComponent(
                $section->stats('revenue')
                    ->title('Total Revenue')
                    ->icon('dollar-sign')
                    ->dataUrl('/api/stats/revenue')
                    ->value(98650)
                    ->prefix('$')
                    ->change('+18.3%')
                    ->trend('up')
            )
            ->addComponent(
                $section->stats('pending-tasks')
                    ->title('Pending Tasks')
                    ->icon('check-square')
                    ->dataUrl('/api/stats/tasks')
                    ->value(23)
                    ->change('-8.1%')
                    ->trend('down')
            );
        
        // Recent activity card
        $section->card('recent-activity')
            ->title('Recent Activity')
            ->dataUrl('/api/activity/recent')
            ->addField('activity_log', 'Activity Log');
    });

return $layout->render();
```

---

### User Profile Page

Complete user profile with multiple sections.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('user-profile')
    ->title('User Profile')
    
    ->section('main', function ($section) {
        // Profile header card
        $section->card('profile-header')
            ->title('Profile Information')
            ->dataUrl('/api/users/1')
            ->addField('avatar', 'Avatar')
            ->addField('name', 'Name')
            ->addField('email', 'Email')
            ->addField('role', 'Role')
            ->addField('bio', 'Bio')
            ->addField('joined', 'Member Since')
            ->addAction('edit', 'Edit Profile', ['url' => '/profile/edit']);
        
        // Two-column grid
        $section->grid('profile-content')
            ->columns(2)
            
            // Left column - Account details
            ->addComponent(
                $section->card('account-details')
                    ->title('Account Details')
                    ->dataUrl('/api/users/1/account')
                    ->addField('username', 'Username')
                    ->addField('email_verified', 'Email Verified')
                    ->addField('two_factor', 'Two-Factor Auth')
                    ->addField('last_login', 'Last Login')
                    ->addAction('security', 'Security Settings', ['url' => '/profile/security'])
            )
            
            // Right column - Activity timeline
            ->addComponent(
                $section->timeline('activity')
                    ->title('Recent Activity')
                    ->dataUrl('/api/users/1/activity')
                    ->orientation('vertical')
            );
    });

return $layout->render();
```

---

### Tabbed Interface

Content organized in tabs.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('user-management')
    ->title('User Management')
    
    ->section('main', function ($section) {
        $section->tabs('user-tabs')
            
            // Overview tab
            ->addTab('overview', 'Overview', function ($tab) {
                $tab->grid('overview-grid')
                    ->columns(3)
                    ->addComponent(
                        $tab->stats('total')
                            ->title('Total Users')
                            ->value(523)
                            ->icon('users')
                    )
                    ->addComponent(
                        $tab->stats('active')
                            ->title('Active')
                            ->value(487)
                            ->icon('check-circle')
                    )
                    ->addComponent(
                        $tab->stats('inactive')
                            ->title('Inactive')
                            ->value(36)
                            ->icon('x-circle')
                    );
                
                $tab->chart('user-growth')
                    ->title('User Growth')
                    ->chartType('line')
                    ->dataUrl('/api/charts/user-growth');
            })
            
            // Users list tab
            ->addTab('users', 'All Users', function ($tab) {
                $tab->table('users-table')
                    ->dataUrl('/api/users')
                    ->addColumn('name', 'Name', ['sortable' => true])
                    ->addColumn('email', 'Email')
                    ->addColumn('role', 'Role', ['filterable' => true])
                    ->addColumn('status', 'Status', ['filterable' => true])
                    ->searchable(true)
                    ->paginate(20);
            })
            
            // Pending approvals tab
            ->addTab('pending', 'Pending Approvals', function ($tab) {
                $tab->alert('info')
                    ->content('These users are awaiting approval.')
                    ->variant('info');
                
                $tab->table('pending-users')
                    ->dataUrl('/api/users/pending')
                    ->addColumn('name', 'Name')
                    ->addColumn('email', 'Email')
                    ->addColumn('requested_at', 'Requested')
                    ->addAction('approve', 'Approve', ['icon' => 'check'])
                    ->addAction('reject', 'Reject', ['icon' => 'x']);
            })
            
            ->activeTab('overview');
    });

return $layout->render();
```

---

## Advanced Examples

### Multi-Step Wizard

Complete multi-step registration wizard.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('registration-wizard')
    ->title('Create Account')
    
    ->section('main', function ($section) {
        $section->wizard('registration')
            
            // Step 1: Account Information
            ->addStep('account', 'Account', function ($step) {
                $step->form('account-form')
                    ->action('/register/account')
                    ->method('POST')
                    ->addField('username', 'text', 'Username', [
                        'placeholder' => 'Choose a username',
                        'required' => true,
                    ])
                    ->addField('email', 'email', 'Email', [
                        'placeholder' => 'your@email.com',
                        'required' => true,
                    ])
                    ->addField('password', 'password', 'Password', [
                        'required' => true,
                    ])
                    ->addField('password_confirmation', 'password', 'Confirm Password', [
                        'required' => true,
                    ])
                    ->validationRules([
                        'username' => 'required|min:3|max:50|unique:users',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|min:8|confirmed',
                    ])
                    ->addButton('next', 'Next Step', 'submit');
            })
            
            // Step 2: Profile Information
            ->addStep('profile', 'Profile', function ($step) {
                $step->form('profile-form')
                    ->action('/register/profile')
                    ->method('POST')
                    ->addField('first_name', 'text', 'First Name', ['required' => true])
                    ->addField('last_name', 'text', 'Last Name', ['required' => true])
                    ->addField('phone', 'tel', 'Phone Number')
                    ->addField('avatar', 'file', 'Profile Photo', [
                        'accept' => 'image/*',
                    ])
                    ->addField('bio', 'textarea', 'Bio', [
                        'placeholder' => 'Tell us about yourself',
                        'rows' => 4,
                    ])
                    ->validationRules([
                        'first_name' => 'required|max:100',
                        'last_name' => 'required|max:100',
                        'phone' => 'nullable|regex:/^[0-9+\-\s()]+$/',
                    ])
                    ->addButton('back', 'Back', 'button')
                    ->addButton('next', 'Next Step', 'submit');
            })
            
            // Step 3: Preferences
            ->addStep('preferences', 'Preferences', function ($step) {
                $step->form('preferences-form')
                    ->action('/register/preferences')
                    ->method('POST')
                    ->addField('language', 'select', 'Language', [
                        'options' => [
                            'en' => 'English',
                            'es' => 'Spanish',
                            'fr' => 'French',
                            'de' => 'German',
                        ],
                        'default' => 'en',
                    ])
                    ->addField('timezone', 'select', 'Timezone', [
                        'options' => [
                            'America/New_York' => 'Eastern Time',
                            'America/Chicago' => 'Central Time',
                            'America/Denver' => 'Mountain Time',
                            'America/Los_Angeles' => 'Pacific Time',
                        ],
                    ])
                    ->addField('notifications_email', 'checkbox', 'Email Notifications')
                    ->addField('notifications_sms', 'checkbox', 'SMS Notifications')
                    ->addField('newsletter', 'checkbox', 'Subscribe to Newsletter')
                    ->addButton('back', 'Back', 'button')
                    ->addButton('next', 'Next Step', 'submit');
            })
            
            // Step 4: Confirmation
            ->addStep('confirm', 'Confirm', function ($step) {
                $step->text('confirmation-message')
                    ->content('Please review your information before completing registration.')
                    ->format('plain');
                
                $step->card('summary')
                    ->title('Registration Summary')
                    ->dataSource(function () {
                        return session('registration_data', []);
                    })
                    ->addField('username', 'Username')
                    ->addField('email', 'Email')
                    ->addField('name', 'Full Name')
                    ->addField('language', 'Language')
                    ->addField('timezone', 'Timezone');
                
                $step->form('confirm-form')
                    ->action('/register/complete')
                    ->method('POST')
                    ->addField('terms', 'checkbox', 'I agree to the Terms of Service', [
                        'required' => true,
                    ])
                    ->addField('privacy', 'checkbox', 'I agree to the Privacy Policy', [
                        'required' => true,
                    ])
                    ->addButton('back', 'Back', 'button')
                    ->addButton('complete', 'Complete Registration', 'submit');
            })
            
            ->currentStep('account')
            ->linear(true);
    });

return $layout->render();
```

---

### E-commerce Product Page

Complete product detail page.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('product-detail')
    ->title('Product Details')
    ->setSharedData(['product_id' => $productId])
    
    // Breadcrumbs
    ->section('header', function ($section) {
        $section->breadcrumb('navigation')
            ->addItem('Home', '/')
            ->addItem('Products', '/products')
            ->addItem('Electronics', '/products/electronics')
            ->addItem('Laptops', '/products/electronics/laptops')
            ->addItem('MacBook Pro');
    })
    
    ->section('main', function ($section) use ($productId) {
        // Product layout
        $section->layout('product-layout')
            
            // Left side - Product images
            ->section('sidebar')
                ->media('product-images')
                    ->title('Product Images')
                    ->mediaType('gallery')
                    ->dataUrl("/api/products/{$productId}/images")
            ->endSection()
            
            // Main content - Product details
            ->section('main')
                
                // Product info card
                ->card('product-info')
                    ->dataUrl("/api/products/{$productId}")
                    ->addField('name', 'Product Name')
                    ->addField('brand', 'Brand')
                    ->addField('price', 'Price')
                    ->addField('rating', 'Rating')
                    ->addField('availability', 'Availability')
                
                // Description
                ->text('description')
                    ->title('Description')
                    ->dataSource(function () use ($productId) {
                        return Product::find($productId)->description;
                    })
                    ->format('markdown')
                
                // Purchase form
                ->form('purchase')
                    ->action('/cart/add')
                    ->method('POST')
                    ->addField('quantity', 'number', 'Quantity', [
                        'min' => 1,
                        'default' => 1,
                    ])
                    ->addField('color', 'select', 'Color', [
                        'options' => ['silver' => 'Silver', 'gray' => 'Space Gray'],
                    ])
                    ->addField('warranty', 'select', 'Warranty', [
                        'options' => [
                            'none' => 'No Extended Warranty',
                            '1year' => '1 Year Extended (+$99)',
                            '2year' => '2 Years Extended (+$179)',
                        ],
                    ])
                    ->addButton('add_cart', 'Add to Cart', 'submit')
                    ->addButton('buy_now', 'Buy Now', 'submit')
            ->endSection();
        
        // Reviews section
        $section->tabs('product-tabs')
            ->addTab('features', 'Features', function ($tab) use ($productId) {
                $tab->list('features')
                    ->title('Key Features')
                    ->dataUrl("/api/products/{$productId}/features")
                    ->listType('unordered');
            })
            ->addTab('specs', 'Specifications', function ($tab) use ($productId) {
                $tab->table('specifications')
                    ->dataUrl("/api/products/{$productId}/specs")
                    ->addColumn('spec', 'Specification')
                    ->addColumn('value', 'Value');
            })
            ->addTab('reviews', 'Reviews', function ($tab) use ($productId) {
                $tab->stats('review-stats')
                    ->title('Average Rating')
                    ->dataUrl("/api/products/{$productId}/rating")
                    ->suffix('/ 5');
                
                $tab->card('review-list')
                    ->title('Customer Reviews')
                    ->dataUrl("/api/products/{$productId}/reviews");
            })
            ->activeTab('features');
    });

return $layout->render();
```

---

### Admin Dashboard

Comprehensive admin dashboard with multiple sections.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('admin-dashboard')
    ->title('Admin Dashboard')
    ->setSharedData(['user' => auth()->user()])
    
    // Header
    ->section('header', function ($section) {
        $section->breadcrumb('navigation')
            ->addItem('Home', '/')
            ->addItem('Dashboard');
        
        $section->alert('system-status')
            ->content('All systems operational')
            ->variant('success')
            ->icon('check-circle')
            ->dismissible(false);
    })
    
    // Main content
    ->section('main', function ($section) {
        
        // Top stats row
        $section->grid('top-stats')
            ->columns(4)
            ->addComponent(
                $section->stats('total-users')
                    ->title('Total Users')
                    ->icon('users')
                    ->dataUrl('/api/admin/stats/users')
                    ->permissions(['view-users'])
            )
            ->addComponent(
                $section->stats('total-orders')
                    ->title('Total Orders')
                    ->icon('shopping-cart')
                    ->dataUrl('/api/admin/stats/orders')
                    ->permissions(['view-orders'])
            )
            ->addComponent(
                $section->stats('revenue')
                    ->title('Revenue')
                    ->icon('dollar-sign')
                    ->dataUrl('/api/admin/stats/revenue')
                    ->prefix('$')
                    ->permissions(['view-financials'])
            )
            ->addComponent(
                $section->stats('tickets')
                    ->title('Open Tickets')
                    ->icon('help-circle')
                    ->dataUrl('/api/admin/stats/tickets')
                    ->permissions(['view-tickets'])
            );
        
        // Main content grid
        $section->grid('main-content')
            ->columns(3)
            ->gap('1.5rem')
            
            // Left column (2/3 width)
            ->addComponent(
                $section->layout('left-column')
                    
                    // Sales chart
                    ->section('body')
                        ->chart('sales-chart')
                            ->title('Sales Overview')
                            ->chartType('line')
                            ->dataUrl('/api/admin/charts/sales')
                            ->permissions(['view-analytics'])
                    
                    // Recent orders table
                    ->section('body')
                        ->table('recent-orders')
                            ->title('Recent Orders')
                            ->dataUrl('/api/admin/orders/recent')
                            ->addColumn('id', 'Order #', ['width' => '100px'])
                            ->addColumn('customer', 'Customer')
                            ->addColumn('total', 'Total')
                            ->addColumn('status', 'Status')
                            ->addColumn('date', 'Date')
                            ->addAction('view', 'View', ['icon' => 'eye'])
                            ->paginate(10)
                            ->permissions(['view-orders'])
            )
            
            // Right column (1/3 width)
            ->addComponent(
                $section->layout('right-column')
                    
                    // Quick actions
                    ->section('body')
                        ->card('quick-actions')
                            ->title('Quick Actions')
                            ->addAction('new_user', 'Add User', [
                                'icon' => 'user-plus',
                                'url' => '/admin/users/create',
                            ])
                            ->addAction('new_product', 'Add Product', [
                                'icon' => 'package',
                                'url' => '/admin/products/create',
                            ])
                            ->addAction('reports', 'View Reports', [
                                'icon' => 'bar-chart',
                                'url' => '/admin/reports',
                            ])
                    
                    // Recent activity timeline
                    ->section('body')
                        ->timeline('activity')
                            ->title('Recent Activity')
                            ->dataUrl('/api/admin/activity')
                            ->orientation('vertical')
                    
                    // System health
                    ->section('body')
                        ->card('system-health')
                            ->title('System Health')
                            ->dataUrl('/api/admin/system/health')
                            ->addField('cpu', 'CPU Usage')
                            ->addField('memory', 'Memory Usage')
                            ->addField('disk', 'Disk Usage')
                            ->addField('uptime', 'Uptime')
            );
    })
    
    // Cache for 15 minutes
    ->cache()
        ->ttl(900)
        ->key('admin-dashboard-' . auth()->id())
        ->tags(['admin', 'dashboard'])
    
    // Resolve authorization
    ->resolveAuthorization(auth()->user());

return $layout->render();
```

---

## Pattern Examples

### Master-Detail View

Display a list with detail view.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('master-detail')
    ->section('main', function ($section) {
        $section->grid('master-detail')
            ->columns(2)
            
            // Master list
            ->addComponent(
                $section->list('items-list')
                    ->title('Items')
                    ->dataUrl('/api/items')
                    ->listType('unordered')
            )
            
            // Detail view
            ->addComponent(
                $section->card('item-detail')
                    ->title('Item Details')
                    ->description('Select an item to view details')
                    ->dataUrl('/api/items/selected')
                    ->addField('name', 'Name')
                    ->addField('description', 'Description')
                    ->addField('created_at', 'Created')
                    ->addField('updated_at', 'Updated')
                    ->addAction('edit', 'Edit', ['icon' => 'pencil'])
                    ->addAction('delete', 'Delete', ['icon' => 'trash'])
            );
    });

return $layout->render();
```

---

### Nested Grids

Complex nested grid layouts.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('nested-grids')
    ->section('main', function ($section) {
        // Outer grid
        $section->grid('outer')
            ->columns(2)
            
            // Left column with nested grid
            ->addComponent(
                $section->grid('left-nested')
                    ->columns(2)
                    ->addComponent($section->stats('stat1')->title('Stat 1')->value(100))
                    ->addComponent($section->stats('stat2')->title('Stat 2')->value(200))
                    ->addComponent($section->stats('stat3')->title('Stat 3')->value(300))
                    ->addComponent($section->stats('stat4')->title('Stat 4')->value(400))
            )
            
            // Right column with chart
            ->addComponent(
                $section->chart('main-chart')
                    ->title('Analytics')
                    ->chartType('bar')
                    ->dataUrl('/api/analytics')
            );
    });

return $layout->render();
```

---

### Conditional Content

Show/hide content based on conditions.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('conditional-content')
    ->setSharedData(['user' => auth()->user()])
    
    ->section('main', function ($section) {
        // Admin-only content
        $section->card('admin-panel')
            ->title('Admin Panel')
            ->description('Admin-only features')
            ->permissions(['admin'])
            ->addAction('settings', 'Settings', ['url' => '/admin/settings'])
            ->addAction('logs', 'View Logs', ['url' => '/admin/logs']);
        
        // Premium users only
        $section->card('premium-features')
            ->title('Premium Features')
            ->condition('user.subscription == "premium"')
            ->addField('feature1', 'Advanced Analytics')
            ->addField('feature2', 'Priority Support')
            ->addField('feature3', 'Custom Branding');
        
        // Verified users only
        $section->alert('verification-required')
            ->content('Please verify your email to access all features')
            ->variant('warning')
            ->canSee(function ($user) {
                return !$user->email_verified_at;
            });
        
        // Public content (always visible)
        $section->card('public-content')
            ->title('Welcome')
            ->content('This content is visible to everyone');
    })
    
    ->resolveAuthorization(auth()->user());

return $layout->render();
```

---

### Real-time Data

Components with auto-updating data.

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('realtime-dashboard')
    ->section('main', function ($section) {
        // Stats that refresh every 5 seconds
        $section->grid('realtime-stats')
            ->columns(4)
            ->addComponent(
                $section->stats('active-users')
                    ->title('Active Users')
                    ->dataUrl('/api/realtime/active-users')
                    ->loadOnMount(true)
                    ->meta(['refresh_interval' => 5000])
            )
            ->addComponent(
                $section->stats('current-sessions')
                    ->title('Current Sessions')
                    ->dataUrl('/api/realtime/sessions')
                    ->loadOnMount(true)
                    ->meta(['refresh_interval' => 5000])
            )
            ->addComponent(
                $section->stats('requests-per-min')
                    ->title('Requests/min')
                    ->dataUrl('/api/realtime/requests')
                    ->loadOnMount(true)
                    ->meta(['refresh_interval' => 1000])
            )
            ->addComponent(
                $section->stats('server-load')
                    ->title('Server Load')
                    ->dataUrl('/api/realtime/load')
                    ->loadOnMount(true)
                    ->meta(['refresh_interval' => 2000])
            );
        
        // Live chart
        $section->chart('live-chart')
            ->title('Live Metrics')
            ->chartType('line')
            ->dataUrl('/api/realtime/metrics')
            ->loadOnMount(true)
            ->meta(['refresh_interval' => 3000, 'streaming' => true]);
        
        // Activity feed
        $section->card('activity-feed')
            ->title('Live Activity Feed')
            ->dataUrl('/api/realtime/activity')
            ->loadOnMount(true)
            ->meta(['refresh_interval' => 5000]);
    });

return $layout->render();
```

---

## Integration Examples

### API Response

Return layout as JSON API response.

```php
// In your controller
public function dashboard(Request $request)
{
    $layout = Layout::create('dashboard')
        ->section('main', function ($section) {
            $section->card('stats')->dataUrl('/api/stats');
        })
        ->resolveAuthorization($request->user());
    
    return response()->json($layout->render());
}
```

### Blade Integration

Use in Blade templates.

```blade
<!-- resources/views/dashboard.blade.php -->
<div id="app" data-layout="{{ json_encode($layout->render()) }}">
    <!-- Your frontend framework will render this -->
</div>

@push('scripts')
<script>
    const layoutData = JSON.parse(document.getElementById('app').dataset.layout);
    // Pass to React, Vue, Alpine, etc.
</script>
@endpush
```

### Livewire Component

```php
use Livewire\Component;
use Litepie\Layout\Facades\Layout;

class Dashboard extends Component
{
    public function render()
    {
        $layout = Layout::create('dashboard')
            ->section('main', function ($section) {
                $section->stats('users')
                    ->dataSource(fn() => User::count());
            });
        
        return view('livewire.dashboard', [
            'layout' => $layout->render(),
        ]);
    }
}
```

---

These examples demonstrate the flexibility and power of the Litepie Layout Builder for creating complex, data-driven layouts in Laravel applications. Combine these patterns to build custom solutions for your specific needs.
