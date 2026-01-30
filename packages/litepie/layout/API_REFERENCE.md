# API Reference

Complete API documentation for all Litepie Layout Builder sections and components.

## Table of Contents

- [Layout (Root Container)](#layout-root-container)
- [Sections](#sections)
  - [HeaderSection](#headersection)
  - [LayoutSection](#layoutsection)
  - [GridSection](#gridsection)
  - [TabsSection](#tabssection)
  - [AccordionSection](#accordionsection)
  - [WizardSection](#wizardsection)
  - [ScrollSpySection](#scrollspysection)
- [Components](#components)
  - [FormComponent](#formcomponent)
  - [CardComponent](#cardcomponent)
  - [TableComponent](#tablecomponent)
  - [ListComponent](#listcomponent)
  - [AlertComponent](#alertcomponent)
  - [BadgeComponent](#badgecomponent)
  - [ModalComponent](#modalcomponent)
  - [ChartComponent](#chartcomponent)
  - [TextComponent](#textcomponent)
  - [MediaComponent](#mediacomponent)
  - [StatsComponent](#statscomponent)
  - [TimelineComponent](#timelinecomponent)
  - [CommentComponent](#commentcomponent)
  - [BreadcrumbComponent](#breadcrumbcomponent)
  - [DocumentComponent](#documentcomponent)
  - [CustomComponent](#customcomponent)
- [Common Methods](#common-methods)
- [Traits](#traits)

---

## Layout (Root Container)

The main layout container that manages sections and coordinates rendering.

### Creating a Layout

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create(string $name);
```

### Methods

#### `title(string $title): self`
Set the layout title.

```php
$layout->title('Dashboard');
```

#### `section(string $name, \Closure $callback = null): SectionContainer`
Add or access a section.

```php
$layout->section('main', function ($section) {
    // Configure section
});
```

#### `setSharedData(array $data): self`
Set data shared across all components.

```php
$layout->setSharedData([
    'user' => auth()->user(),
    'config' => config('app'),
]);
```

#### `render(): array`
Render the layout to an array.

```php
$output = $layout->render();
```

#### `cache(): CacheManager`
Enable caching for the layout.

```php
$layout->cache()->ttl(3600)->key('dashboard');
```

#### `resolveAuthorization($user = null): self`
Resolve authorization for all components.

```php
$layout->resolveAuthorization(auth()->user());
```

#### `beforeRender(\Closure $callback): self`
Register a before render hook.

```php
$layout->beforeRender(function ($layout) {
    Log::info('Rendering layout');
});
```

#### `afterRender(\Closure $callback): self`
Register an after render hook.

```php
$layout->afterRender(function ($layout, $output) {
    event(new LayoutRendered($output));
});
```

---

## Sections

Sections are containers that organize other elements using named slots.

### Common Section Methods

All sections inherit these methods from `BaseSection`:

#### `section(string $slot): SectionContainer`
Access a named section slot.

```php
$section->section('header')->text('title')->content('Header');
```

#### `getSectionSlots(): array`
Get all section slots.

```php
$slots = $section->getSectionSlots();
```

#### `hasNamedSection(string $name): bool`
Check if a section slot exists.

```php
if ($section->hasNamedSection('header')) {
    // Header exists
}
```

---

### HeaderSection

Container for page headers with navigation elements.

#### Creation

```php
$layout->section('header', function ($section) {
    $section->header('page-header');
});
```

#### Example

```php
$section->header('page-header')
    ->section('header')
        ->breadcrumb('nav')
            ->addItem('Home', '/')
            ->addItem('Dashboard', '/dashboard');
```

---

### LayoutSection

Main layout container for organizing page structure.

#### Creation

```php
$layout->section('main', function ($section) {
    $section->layout('main-layout');
});
```

#### Example

```php
$section->layout('main-layout')
    ->section('header')
        ->text('title')->content('Dashboard')
    ->endSection()
    ->section('main')
        ->grid('content')->columns(3)
    ->endSection()
    ->section('footer')
        ->text('copyright')->content('© 2025');
```

---

### GridSection

Responsive grid layout for organizing components in columns.

#### Creation

```php
$section->grid(string $name);
```

#### Methods

##### `columns(int $columns): self`
Set number of columns (1-12).

```php
$section->grid('dashboard')->columns(3);
```

##### `gap(string $gap): self`
Set gap between grid items.

```php
$section->grid('dashboard')->gap('1rem');
```

##### `addComponent(Component $component): self`
Add a component to the grid.

```php
$section->grid('dashboard')
    ->addComponent($section->card('stats'));
```

#### Example

```php
$section->grid('dashboard')
    ->columns(3)
    ->gap('2rem')
    ->addComponent($section->card('users')->title('Users'))
    ->addComponent($section->card('orders')->title('Orders'))
    ->addComponent($section->card('revenue')->title('Revenue'));
```

---

### TabsSection

Tabbed interface for organizing content in multiple tabs.

#### Creation

```php
$section->tabs(string $name);
```

#### Methods

##### `addTab(string $id, string $label, \Closure $callback): self`
Add a tab with content.

```php
$section->tabs('content')
    ->addTab('overview', 'Overview', function ($tab) {
        $tab->card('overview-card')->title('Overview');
    });
```

##### `activeTab(string $id): self`
Set the initially active tab.

```php
$section->tabs('content')->activeTab('overview');
```

#### Example

```php
$section->tabs('profile-tabs')
    ->addTab('info', 'Information', function ($tab) {
        $tab->form('user-info')
            ->addField('name', 'text', 'Name')
            ->addField('email', 'email', 'Email');
    })
    ->addTab('settings', 'Settings', function ($tab) {
        $tab->form('user-settings')
            ->addField('notifications', 'checkbox', 'Enable Notifications');
    })
    ->activeTab('info');
```

---

### AccordionSection

Collapsible panels for organizing content in expandable sections.

#### Creation

```php
$section->accordion(string $name);
```

#### Methods

##### `addPanel(string $id, string $title, \Closure $callback): self`
Add a collapsible panel.

```php
$section->accordion('faq')
    ->addPanel('shipping', 'Shipping Information', function ($panel) {
        $panel->text('content')->content('Shipping details...');
    });
```

##### `expandedPanels(array $ids): self`
Set initially expanded panels.

```php
$section->accordion('faq')->expandedPanels(['shipping', 'returns']);
```

##### `allowMultiple(bool $allow): self`
Allow multiple panels to be open simultaneously.

```php
$section->accordion('faq')->allowMultiple(true);
```

#### Example

```php
$section->accordion('help')
    ->addPanel('getting-started', 'Getting Started', function ($panel) {
        $panel->text('intro')->content('Welcome guide...');
    })
    ->addPanel('faq', 'Frequently Asked Questions', function ($panel) {
        $panel->list('questions')
            ->addItem('How to install?')
            ->addItem('How to configure?');
    })
    ->allowMultiple(false)
    ->expandedPanels(['getting-started']);
```

---

### WizardSection

Multi-step workflow container for guiding users through processes.

#### Creation

```php
$section->wizard(string $name);
```

#### Methods

##### `addStep(string $id, string $title, \Closure $callback): self`
Add a wizard step.

```php
$section->wizard('checkout')
    ->addStep('cart', 'Shopping Cart', function ($step) {
        $step->table('items')->dataSource('cart_items');
    });
```

##### `currentStep(string $id): self`
Set the current step.

```php
$section->wizard('checkout')->currentStep('payment');
```

##### `linear(bool $linear): self`
Require completing steps in order.

```php
$section->wizard('checkout')->linear(true);
```

#### Example

```php
$section->wizard('user-registration')
    ->addStep('account', 'Account Details', function ($step) {
        $step->form('account-form')
            ->addField('username', 'text', 'Username')
            ->addField('password', 'password', 'Password');
    })
    ->addStep('profile', 'Profile Information', function ($step) {
        $step->form('profile-form')
            ->addField('name', 'text', 'Full Name')
            ->addField('bio', 'textarea', 'Bio');
    })
    ->addStep('confirm', 'Confirmation', function ($step) {
        $step->text('summary')->content('Review your information...');
    })
    ->currentStep('account')
    ->linear(true);
```

---

### ScrollSpySection

Scroll-based navigation that highlights sections as user scrolls.

#### Creation

```php
$section->scrollspy(string $name);
```

#### Methods

##### `addSection(string $id, string $title, \Closure $callback): self`
Add a scrollspy section.

```php
$section->scrollspy('docs')
    ->addSection('intro', 'Introduction', function ($content) {
        $content->text('intro-text')->content('Getting started...');
    });
```

##### `offset(int $offset): self`
Set scroll offset for activation.

```php
$section->scrollspy('docs')->offset(100);
```

#### Example

```php
$section->scrollspy('documentation')
    ->addSection('overview', 'Overview', function ($content) {
        $content->text('overview-content')->content('Package overview...');
    })
    ->addSection('installation', 'Installation', function ($content) {
        $content->text('install-content')->content('Installation steps...');
    })
    ->addSection('usage', 'Usage', function ($content) {
        $content->text('usage-content')->content('How to use...');
    })
    ->offset(80);
```

---

## Components

Components are leaf nodes that render actual UI content.

### Common Component Methods

All components inherit these from `BaseComponent`:

#### `name(string $name): self`
Set the component name.

#### `title(string $title): self`
Set the component title.

#### `subtitle(string $subtitle): self`
Set the component subtitle.

#### `description(string $description): self`
Set the component description.

#### `icon(string $icon): self`
Set the component icon.

#### `visible(bool $visible): self`
Set visibility.

#### `order(int $order): self`
Set display order.

#### `permissions(array|string $permissions): self`
Set required permissions.

```php
$component->permissions(['view-users', 'edit-users']);
```

#### `roles(array|string $roles): self`
Set required roles.

```php
$component->roles(['admin', 'manager']);
```

#### `canSee(\Closure $callback): self`
Custom visibility logic.

```php
$component->canSee(fn($user) => $user->isAdmin());
```

#### `dataUrl(string $url): self`
Set API endpoint for data loading.

```php
$component->dataUrl('/api/users');
```

#### `dataParams(array $params): self`
Set parameters for data loading.

```php
$component->dataParams(['status' => 'active']);
```

#### `dataSource(string|\Closure $source): self`
Set data source (table name or closure).

```php
$component->dataSource('users');
// or
$component->dataSource(fn() => User::all());
```

#### `dataTransform(\Closure $callback): self`
Transform loaded data.

```php
$component->dataTransform(fn($data) => $data->map(/* ... */));
```

#### `loadOnMount(bool $load): self`
Load data when component mounts.

```php
$component->loadOnMount(true);
```

#### `useSharedData(string $key): self`
Use shared layout data.

```php
$component->useSharedData('user');
```

#### `condition(string $expression): self`
Add conditional visibility.

```php
$component->condition('user.role == "admin"');
```

#### `meta(array $meta): self`
Set custom metadata.

```php
$component->meta(['color' => 'blue', 'size' => 'large']);
```

---

### FormComponent

Renders forms with fields, validation, and submission handling.

#### Creation

```php
$section->form(string $name);
```

#### Methods

##### `action(string $action): self`
Set form action URL.

```php
$form->action('/users/create');
```

##### `method(string $method): self`
Set HTTP method (GET, POST, PUT, PATCH, DELETE).

```php
$form->method('POST');
```

##### `addField(string $name, string $type, string $label, array $config = []): self`
Add a form field.

```php
$form->addField('email', 'email', 'Email Address', [
    'placeholder' => 'user@example.com',
    'required' => true,
]);
```

**Field Types:**
- `text`, `email`, `password`, `number`, `tel`, `url`
- `textarea`, `select`, `checkbox`, `radio`
- `date`, `datetime`, `time`
- `file`, `hidden`

##### `addButton(string $name, string $label, string $type = 'button'): self`
Add a form button.

```php
$form->addButton('submit', 'Create User', 'submit');
```

##### `validationRules(array $rules): self`
Set validation rules.

```php
$form->validationRules([
    'email' => 'required|email|unique:users',
    'name' => 'required|min:3',
]);
```

##### `enctype(string $enctype): self`
Set form encoding type.

```php
$form->enctype('multipart/form-data'); // For file uploads
```

#### Example

```php
$section->form('user-registration')
    ->action('/users')
    ->method('POST')
    ->addField('name', 'text', 'Full Name', ['required' => true])
    ->addField('email', 'email', 'Email', ['required' => true])
    ->addField('password', 'password', 'Password', ['required' => true])
    ->addField('role', 'select', 'Role', [
        'options' => ['user' => 'User', 'admin' => 'Admin'],
        'default' => 'user',
    ])
    ->addField('notifications', 'checkbox', 'Enable Notifications')
    ->addButton('submit', 'Create Account', 'submit')
    ->addButton('cancel', 'Cancel', 'button')
    ->validationRules([
        'name' => 'required|min:3|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);
```

---

### CardComponent

Displays content in a card layout with title, fields, and actions.

#### Creation

```php
$section->card(string $name);
```

#### Methods

##### `addField(string $name, string $label = null): self`
Add a field to display.

```php
$card->addField('username', 'Username');
```

##### `addAction(string $name, string $label, array $config = []): self`
Add an action button.

```php
$card->addAction('edit', 'Edit', ['url' => '/users/1/edit']);
```

##### `variant(string $variant): self`
Set card style variant.

```php
$card->variant('primary'); // primary, secondary, success, warning, danger
```

#### Example

```php
$section->card('user-profile')
    ->title('User Profile')
    ->subtitle('Account Information')
    ->icon('user')
    ->dataUrl('/api/users/1')
    ->addField('name', 'Name')
    ->addField('email', 'Email')
    ->addField('role', 'Role')
    ->addField('created_at', 'Member Since')
    ->addAction('edit', 'Edit Profile', ['url' => '/profile/edit'])
    ->addAction('delete', 'Delete Account', [
        'url' => '/profile/delete',
        'confirm' => 'Are you sure?',
    ])
    ->variant('primary');
```

---

### TableComponent

Displays data in a table with sorting, filtering, and pagination.

#### Creation

```php
$section->table(string $name);
```

#### Methods

##### `addColumn(string $name, string $label, array $config = []): self`
Add a table column.

```php
$table->addColumn('name', 'Name', ['sortable' => true]);
```

##### `sortable(bool $sortable): self`
Enable sorting.

```php
$table->sortable(true);
```

##### `filterable(bool $filterable): self`
Enable filtering.

```php
$table->filterable(true);
```

##### `paginate(int $perPage): self`
Enable pagination.

```php
$table->paginate(20);
```

##### `searchable(bool $searchable): self`
Enable search.

```php
$table->searchable(true);
```

##### `selectable(bool $selectable): self`
Enable row selection.

```php
$table->selectable(true);
```

##### `addAction(string $name, string $label, array $config = []): self`
Add a row action.

```php
$table->addAction('edit', 'Edit', ['icon' => 'pencil']);
```

#### Example

```php
$section->table('users-list')
    ->title('Users')
    ->dataUrl('/api/users')
    ->addColumn('id', 'ID', ['sortable' => true, 'width' => '80px'])
    ->addColumn('name', 'Name', ['sortable' => true])
    ->addColumn('email', 'Email', ['sortable' => true])
    ->addColumn('role', 'Role', ['filterable' => true])
    ->addColumn('status', 'Status', ['filterable' => true])
    ->addColumn('created_at', 'Joined', ['sortable' => true])
    ->addAction('view', 'View', ['icon' => 'eye'])
    ->addAction('edit', 'Edit', ['icon' => 'pencil'])
    ->addAction('delete', 'Delete', ['icon' => 'trash', 'confirm' => true])
    ->sortable(true)
    ->filterable(true)
    ->searchable(true)
    ->selectable(true)
    ->paginate(25);
```

---

### ListComponent

Displays items in various list formats (ordered, unordered, definition).

#### Creation

```php
$section->list(string $name);
```

#### Methods

##### `addItem(string|array $item): self`
Add a list item.

```php
$list->addItem('First item');
// or with configuration
$list->addItem(['label' => 'Item', 'value' => 'Value']);
```

##### `listType(string $type): self`
Set list type (ordered, unordered, definition).

```php
$list->listType('ordered'); // ordered, unordered, definition
```

##### `separator(string $separator): self`
Set item separator.

```php
$list->separator('•');
```

#### Example

```php
$section->list('features')
    ->title('Key Features')
    ->listType('unordered')
    ->addItem('User Management')
    ->addItem('Role-Based Access Control')
    ->addItem('API Integration')
    ->addItem('Real-time Updates')
    ->addItem('Advanced Reporting');
```

---

### AlertComponent

Displays notifications, messages, and alerts.

#### Creation

```php
$section->alert(string $name);
```

#### Methods

##### `content(string $content): self`
Set alert message.

```php
$alert->content('Operation completed successfully!');
```

##### `variant(string $variant): self`
Set alert type.

```php
$alert->variant('success'); // success, info, warning, danger
```

##### `dismissible(bool $dismissible): self`
Make alert dismissible.

```php
$alert->dismissible(true);
```

#### Example

```php
$section->alert('success-message')
    ->title('Success')
    ->content('Your profile has been updated successfully!')
    ->variant('success')
    ->icon('check-circle')
    ->dismissible(true);
```

---

### BadgeComponent

Displays labels, tags, or status indicators.

#### Creation

```php
$section->badge(string $name);
```

#### Methods

##### `content(string $content): self`
Set badge text.

```php
$badge->content('New');
```

##### `variant(string $variant): self`
Set badge style.

```php
$badge->variant('primary'); // primary, secondary, success, warning, danger
```

##### `size(string $size): self`
Set badge size.

```php
$badge->size('small'); // small, medium, large
```

#### Example

```php
$section->badge('status')
    ->content('Active')
    ->variant('success')
    ->size('medium');
```

---

### ModalComponent

Displays dialogs and popup windows.

#### Creation

```php
$section->modal(string $name);
```

#### Methods

##### `content(string $content): self`
Set modal content.

```php
$modal->content('Are you sure you want to delete this item?');
```

##### `trigger(string $trigger): self`
Set trigger element selector.

```php
$modal->trigger('#delete-button');
```

##### `size(string $size): self`
Set modal size.

```php
$modal->size('large'); // small, medium, large, full
```

##### `closable(bool $closable): self`
Allow closing the modal.

```php
$modal->closable(true);
```

##### `addButton(string $name, string $label, array $config = []): self`
Add a modal action button.

```php
$modal->addButton('confirm', 'Delete', ['variant' => 'danger']);
```

#### Example

```php
$section->modal('delete-confirmation')
    ->title('Confirm Deletion')
    ->content('Are you sure you want to delete this user? This action cannot be undone.')
    ->trigger('#delete-user-btn')
    ->size('medium')
    ->closable(true)
    ->addButton('cancel', 'Cancel', ['variant' => 'secondary'])
    ->addButton('confirm', 'Delete', ['variant' => 'danger']);
```

---

### ChartComponent

Displays data visualizations and charts.

#### Creation

```php
$section->chart(string $name);
```

#### Methods

##### `chartType(string $type): self`
Set chart type.

```php
$chart->chartType('line'); // line, bar, pie, doughnut, area, scatter
```

##### `datasets(array $datasets): self`
Set chart datasets.

```php
$chart->datasets([
    ['label' => 'Sales', 'data' => [10, 20, 30, 40]],
    ['label' => 'Revenue', 'data' => [15, 25, 35, 45]],
]);
```

##### `labels(array $labels): self`
Set chart labels.

```php
$chart->labels(['Jan', 'Feb', 'Mar', 'Apr']);
```

##### `options(array $options): self`
Set chart options.

```php
$chart->options(['responsive' => true, 'maintainAspectRatio' => false]);
```

#### Example

```php
$section->chart('sales-chart')
    ->title('Monthly Sales')
    ->chartType('line')
    ->dataUrl('/api/sales/monthly')
    ->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])
    ->datasets([
        [
            'label' => 'This Year',
            'data' => [120, 190, 150, 170, 210, 180],
            'borderColor' => 'rgb(75, 192, 192)',
        ],
        [
            'label' => 'Last Year',
            'data' => [100, 150, 130, 140, 160, 150],
            'borderColor' => 'rgb(255, 99, 132)',
        ],
    ])
    ->options(['responsive' => true, 'tension' => 0.4]);
```

---

### TextComponent

Displays rich text content with formatting support.

#### Creation

```php
$section->text(string $name);
```

#### Methods

##### `content(string $content): self`
Set text content.

```php
$text->content('This is the content');
```

##### `format(string $format): self`
Set content format.

```php
$text->format('markdown'); // plain, markdown, html
```

##### `align(string $align): self`
Set text alignment.

```php
$text->align('center'); // left, center, right, justify
```

#### Example

```php
$section->text('welcome-message')
    ->title('Welcome to Dashboard')
    ->content('# Getting Started\n\nWelcome to your new dashboard...')
    ->format('markdown')
    ->align('left');
```

---

### MediaComponent

Displays images, videos, and media galleries.

#### Creation

```php
$section->media(string $name);
```

#### Methods

##### `mediaType(string $type): self`
Set media type.

```php
$media->mediaType('image'); // image, video, gallery
```

##### `src(string $src): self`
Set media source URL.

```php
$media->src('/images/banner.jpg');
```

##### `items(array $items): self`
Set gallery items.

```php
$media->items([
    ['src' => '/image1.jpg', 'alt' => 'Image 1'],
    ['src' => '/image2.jpg', 'alt' => 'Image 2'],
]);
```

##### `alt(string $alt): self`
Set alt text.

```php
$media->alt('Product image');
```

##### `caption(string $caption): self`
Set media caption.

```php
$media->caption('Beautiful sunset');
```

#### Example

```php
$section->media('product-gallery')
    ->title('Product Images')
    ->mediaType('gallery')
    ->items([
        ['src' => '/products/1-front.jpg', 'alt' => 'Front view'],
        ['src' => '/products/1-side.jpg', 'alt' => 'Side view'],
        ['src' => '/products/1-back.jpg', 'alt' => 'Back view'],
    ]);
```

---

### StatsComponent

Displays statistics and metrics.

#### Creation

```php
$section->stats(string $name);
```

#### Methods

##### `value(string|int|float $value): self`
Set the main statistic value.

```php
$stats->value(1234);
```

##### `change(string|float $change): self`
Set change percentage.

```php
$stats->change('+12.5%');
```

##### `trend(string $trend): self`
Set trend direction.

```php
$stats->trend('up'); // up, down, neutral
```

##### `prefix(string $prefix): self`
Set value prefix.

```php
$stats->prefix('$');
```

##### `suffix(string $suffix): self`
Set value suffix.

```php
$stats->suffix('users');
```

#### Example

```php
$section->stats('total-revenue')
    ->title('Total Revenue')
    ->value(125840)
    ->prefix('$')
    ->change('+15.2%')
    ->trend('up')
    ->icon('dollar-sign')
    ->dataUrl('/api/stats/revenue');
```

---

### TimelineComponent

Displays events in a chronological timeline.

#### Creation

```php
$section->timeline(string $name);
```

#### Methods

##### `addEvent(array $event): self`
Add a timeline event.

```php
$timeline->addEvent([
    'title' => 'Event Title',
    'date' => '2025-01-15',
    'description' => 'Event description',
    'icon' => 'check',
]);
```

##### `orientation(string $orientation): self`
Set timeline orientation.

```php
$timeline->orientation('vertical'); // vertical, horizontal
```

#### Example

```php
$section->timeline('order-history')
    ->title('Order Timeline')
    ->orientation('vertical')
    ->addEvent([
        'title' => 'Order Placed',
        'date' => '2025-01-10 10:30',
        'description' => 'Order #12345 was placed',
        'icon' => 'shopping-cart',
    ])
    ->addEvent([
        'title' => 'Payment Confirmed',
        'date' => '2025-01-10 10:35',
        'description' => 'Payment processed successfully',
        'icon' => 'credit-card',
    ])
    ->addEvent([
        'title' => 'Shipped',
        'date' => '2025-01-11 14:20',
        'description' => 'Order shipped via FedEx',
        'icon' => 'truck',
    ])
    ->addEvent([
        'title' => 'Delivered',
        'date' => '2025-01-13 09:15',
        'description' => 'Order delivered successfully',
        'icon' => 'check-circle',
    ]);
```

---

### CommentComponent

Displays comment threads and discussions.

#### Creation

```php
$section->comment(string $name);
```

#### Methods

##### `author(string $author): self`
Set comment author.

```php
$comment->author('John Doe');
```

##### `content(string $content): self`
Set comment content.

```php
$comment->content('This is a great article!');
```

##### `timestamp(string $timestamp): self`
Set comment timestamp.

```php
$comment->timestamp('2025-01-15 14:30');
```

##### `avatar(string $avatar): self`
Set author avatar URL.

```php
$comment->avatar('/avatars/john.jpg');
```

##### `addReply(array $reply): self`
Add a reply to the comment.

```php
$comment->addReply([
    'author' => 'Jane Smith',
    'content' => 'Thanks for reading!',
    'timestamp' => '2025-01-15 15:00',
]);
```

#### Example

```php
$section->comment('main-comment')
    ->author('John Doe')
    ->avatar('/avatars/john.jpg')
    ->content('This is an excellent package! Very easy to use.')
    ->timestamp('2025-01-15 14:30')
    ->addReply([
        'author' => 'Jane Smith',
        'avatar' => '/avatars/jane.jpg',
        'content' => 'Thank you! Glad you find it useful.',
        'timestamp' => '2025-01-15 15:00',
    ])
    ->addReply([
        'author' => 'Bob Johnson',
        'avatar' => '/avatars/bob.jpg',
        'content' => 'I agree, it\'s very intuitive!',
        'timestamp' => '2025-01-15 15:30',
    ]);
```

---

### BreadcrumbComponent

Displays navigation breadcrumbs.

#### Creation

```php
$section->breadcrumb(string $name);
```

#### Methods

##### `addItem(string $label, string $url = null): self`
Add a breadcrumb item.

```php
$breadcrumb->addItem('Home', '/');
```

##### `separator(string $separator): self`
Set breadcrumb separator.

```php
$breadcrumb->separator('/');
```

#### Example

```php
$section->breadcrumb('page-navigation')
    ->addItem('Home', '/')
    ->addItem('Products', '/products')
    ->addItem('Electronics', '/products/electronics')
    ->addItem('Laptops', '/products/electronics/laptops')
    ->addItem('MacBook Pro') // Current page (no link)
    ->separator('›');
```

---

### DocumentComponent

Manages document uploads, lists, and previews.

#### Creation

```php
$section->document(string $name);
```

#### Methods

##### `uploadUrl(string $url): self`
Set document upload URL.

```php
$document->uploadUrl('/api/documents/upload');
```

##### `listUrl(string $url): self`
Set documents list URL.

```php
$document->listUrl('/api/documents');
```

##### `maxSize(int $size): self`
Set maximum file size (in MB).

```php
$document->maxSize(10);
```

##### `allowedTypes(array $types): self`
Set allowed file types.

```php
$document->allowedTypes(['pdf', 'doc', 'docx', 'xls', 'xlsx']);
```

##### `showPreview(bool $show): self`
Enable document preview.

```php
$document->showPreview(true);
```

#### Example

```php
$section->document('project-files')
    ->title('Project Documents')
    ->uploadUrl('/api/projects/123/documents')
    ->listUrl('/api/projects/123/documents')
    ->maxSize(25)
    ->allowedTypes(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])
    ->showPreview(true);
```

---

### CustomComponent

Allows custom HTML/JSON content for flexibility.

#### Creation

```php
$section->custom(string $name);
```

#### Methods

##### `content(string|array $content): self`
Set custom content.

```php
$custom->content('<div>Custom HTML</div>');
// or
$custom->content(['custom' => 'data']);
```

##### `format(string $format): self`
Set content format.

```php
$custom->format('html'); // html, json
```

#### Example

```php
$section->custom('custom-widget')
    ->title('Custom Widget')
    ->content('<div class="custom-widget">Custom HTML content here</div>')
    ->format('html');

// Or with JSON
$section->custom('custom-data')
    ->content([
        'type' => 'custom',
        'config' => ['option1' => true, 'option2' => 'value'],
        'data' => ['key' => 'value'],
    ])
    ->format('json');
```

---

## Common Methods

These methods are available on all sections and components.

### Authorization

```php
// Set permissions
$component->permissions(['view-users', 'edit-users']);

// Set roles
$component->roles(['admin', 'manager']);

// Custom visibility logic
$component->canSee(function ($user) {
    return $user->id === 1 || $user->hasRole('admin');
});

// Resolve authorization
$layout->resolveAuthorization(auth()->user());
```

### Data Management

```php
// Load from API
$component->dataUrl('/api/endpoint')
    ->dataParams(['filter' => 'active'])
    ->loadOnMount(true);

// Load from database
$component->dataSource('table_name')
    ->dataTransform(function ($query) {
        return $query->where('status', 'active')->get();
    });

// Load from closure
$component->dataSource(function () {
    return ['key' => 'value'];
});

// Use shared data
$component->useSharedData('global_config');
```

### Responsive Design

```php
$component->setDeviceConfig('mobile', [
    'columns' => 1,
    'visible' => true,
]);

$component->setDeviceConfig('tablet', [
    'columns' => 2,
]);

$component->setDeviceConfig('desktop', [
    'columns' => 4,
]);
```

### Conditional Logic

```php
$component->condition('user.role == "admin"');
$component->condition('subscription.status == "active"');
$component->addCondition('user.verified == true');
```

### Metadata

```php
$component->meta([
    'color' => 'blue',
    'size' => 'large',
    'custom_prop' => 'value',
]);
```

### Visibility & Ordering

```php
$component->visible(true);
$component->order(10);
```

---

## Traits

### HasConditionalLogic

Adds conditional visibility based on expressions.

```php
$component->condition('user.role == "admin"');
$component->evaluateConditions(['user' => ['role' => 'admin']]);
```

### HasEvents

Adds lifecycle events.

```php
$layout->beforeRender(function ($layout) {
    // Before render logic
});

$layout->afterRender(function ($layout, $output) {
    // After render logic
});
```

### Responsive

Adds device-specific configuration.

```php
$component->setDeviceConfig('mobile', ['columns' => 1]);
$component->detectDevice(); // Returns current device type
```

### Translatable

Adds internationalization support.

```php
$component->translate();
$component->translateField('title', 'layout.dashboard.title');
```

### Validatable

Adds validation support.

```php
$component->validationRules([
    'name' => 'required|min:3',
    'email' => 'required|email',
]);
```

### Cacheable

Adds caching support.

```php
$layout->cache()->ttl(3600)->key('dashboard')->tags(['layouts']);
```

### Debuggable

Adds debugging information.

```php
$component->enableDebug();
$info = $component->getDebugInfo();
```

### Exportable

Adds JSON export/import.

```php
$json = $layout->export();
$layout = Layout::import($json);
```

---

## Complete Example

Here's a comprehensive example using multiple sections and components:

```php
use Litepie\Layout\Facades\Layout;

$layout = Layout::create('admin-dashboard')
    ->title('Admin Dashboard')
    ->setSharedData(['user' => auth()->user()])
    
    // Header section
    ->section('header', function ($section) {
        $section->breadcrumb('navigation')
            ->addItem('Home', '/')
            ->addItem('Dashboard', '/dashboard');
            
        $section->alert('welcome')
            ->content('Welcome back, ' . auth()->user()->name)
            ->variant('info')
            ->dismissible(true);
    })
    
    // Main content section
    ->section('main', function ($section) {
        // Stats grid
        $section->grid('stats')
            ->columns(4)
            ->addComponent(
                $section->stats('users')
                    ->title('Total Users')
                    ->value(1234)
                    ->change('+12%')
                    ->trend('up')
                    ->icon('users')
            )
            ->addComponent(
                $section->stats('orders')
                    ->title('Orders Today')
                    ->value(89)
                    ->change('+5%')
                    ->trend('up')
                    ->icon('shopping-cart')
            )
            ->addComponent(
                $section->stats('revenue')
                    ->title('Revenue')
                    ->value(45632)
                    ->prefix('$')
                    ->change('+18%')
                    ->trend('up')
                    ->icon('dollar-sign')
            )
            ->addComponent(
                $section->stats('pending')
                    ->title('Pending Tasks')
                    ->value(12)
                    ->change('-3%')
                    ->trend('down')
                    ->icon('clock')
            );
        
        // Tabbed content
        $section->tabs('content')
            ->addTab('overview', 'Overview', function ($tab) {
                $tab->card('recent-activity')
                    ->title('Recent Activity')
                    ->dataUrl('/api/activity');
            })
            ->addTab('users', 'Users', function ($tab) {
                $tab->table('users-table')
                    ->dataUrl('/api/users')
                    ->addColumn('name', 'Name', ['sortable' => true])
                    ->addColumn('email', 'Email')
                    ->addColumn('role', 'Role')
                    ->paginate(20);
            })
            ->addTab('reports', 'Reports', function ($tab) {
                $tab->chart('sales-chart')
                    ->chartType('line')
                    ->dataUrl('/api/reports/sales');
            });
    })
    
    // Cache the layout
    ->cache()->ttl(1800)->key('admin-dashboard-' . auth()->id())
    
    // Resolve authorization
    ->resolveAuthorization(auth()->user());

return $layout->render();
```

---

## Laravel Integration

### Response Macro

The package includes a Laravel response macro for convenient layout rendering in controllers.

#### `response()->layout(Renderable $layout): JsonResponse`

Instead of manually calling `render()` and wrapping it in `response()->json()`, you can use the `layout()` method directly on the response factory.

```php
use Litepie\Layout\Facades\Layout;

public function index()
{
    $layout = Layout::create('dashboard', 'view')
        ->title('Dashboard Overview');

    // ... configure layout ...

    return response()->layout($layout);
}
```

This macro automatically calls `$layout->render()` and returns a JSON response.

---

This API reference provides a complete overview of all available sections, components, and methods in the Litepie Layout Builder package.
