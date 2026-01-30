# Card Component - Comprehensive Documentation

## Overview
The CardComponent is a versatile container component for the Litepie Layout package. It provides a comprehensive solution for displaying structured content with headers, footers, actions, and dynamic data fields. Cards are ideal for showcasing user profiles, product information, dashboards, and any structured content that benefits from visual separation.

## Table of Contents
- [Basic Usage](#basic-usage)
- [Core Features](#core-features)
- [Header Configuration](#header-configuration)
- [Footer Configuration](#footer-configuration)
- [Fields & Content](#fields--content)
- [Actions & Dropdowns](#actions--dropdowns)
- [Variants & Styling](#variants--styling)
- [Data Source Integration](#data-source-integration)
- [Authorization & Permissions](#authorization--permissions)
- [Complete Examples](#complete-examples)
- [API Reference](#api-reference)

## Basic Usage

### Creating a Card
```php
use Litepie\Layout\Components\CardComponent;

// Using the static factory
$card = CardComponent::make('user-card')
    ->title('User Profile')
    ->subtitle('Premium Member')
    ->description('Active user since 2024');

// Using section helper
$section->card('user-card')
    ->title('User Profile')
    ->subtitle('Premium Member');
```

### Minimal Example
```php
CardComponent::make('simple-card')
    ->title('Simple Card')
    ->addField('name', 'Name', 'John Doe')
    ->addField('email', 'Email', 'john@example.com');
```

## Core Features

### Basic Properties

#### Title, Subtitle & Description
```php
$card->title('User Profile')
    ->subtitle('Premium Member')
    ->description('Manage user information and settings')
    ->icon('user');
```

#### Image Support
```php
$card->image('/images/profile-banner.jpg')
    ->title('User Profile');
```

#### Visibility & Order
```php
$card->order(1)
    ->visible(true)
    ->canSee(fn($user) => $user->hasPermission('view-users'));
```

## Header Configuration

The card header can contain badges, text, actions, and dropdown menus.

### Basic Header Items
```php
$card->addHeader('badge', 'Status', [
    'variant' => 'success',
    'text' => 'Active'
]);

$card->addHeader('text', 'SKU', [
    'text' => 'PRD-12345',
    'class' => 'font-mono'
]);

$card->addHeader('icon', 'Verified', [
    'icon' => 'check-circle',
    'color' => 'green'
]);
```

### Header Action Buttons
```php
// Single action button
$card->addHeaderAction('Edit', '/users/1/edit', [
    'variant' => 'primary',
    'icon' => 'edit',
    'size' => 'sm'
]);

// Multiple actions
$card->addHeaderAction('Edit', '/users/1/edit', ['icon' => 'edit'])
    ->addHeaderAction('Delete', '/users/1/delete', [
        'variant' => 'danger',
        'icon' => 'trash',
        'confirm' => 'Are you sure?'
    ]);
```

### Header Dropdown Menus
```php
$card->addHeaderDropdown('Actions', [
    ['label' => 'Edit', 'action' => '/users/1/edit', 'icon' => 'edit'],
    ['label' => 'Duplicate', 'action' => '/users/1/duplicate', 'icon' => 'copy'],
    'divider',
    ['label' => 'Archive', 'action' => '/users/1/archive', 'icon' => 'archive'],
    ['label' => 'Delete', 'action' => '/users/1/delete', 'variant' => 'danger', 'icon' => 'trash']
], ['icon' => 'more-vertical', 'variant' => 'ghost']);
```

### Complete Header Example
```php
$card->title('Product Details')
    ->subtitle('SKU: PRD-001')
    ->addHeader('badge', 'Status', ['variant' => 'success', 'text' => 'In Stock'])
    ->addHeader('badge', 'Category', ['variant' => 'info', 'text' => 'Electronics'])
    ->addHeaderAction('Edit', '/products/1/edit', ['icon' => 'edit', 'size' => 'sm'])
    ->addHeaderDropdown('More', [
        ['label' => 'Duplicate', 'action' => '/products/1/duplicate', 'icon' => 'copy'],
        ['label' => 'Export', 'action' => '/products/1/export', 'icon' => 'download'],
        'divider',
        ['label' => 'Delete', 'action' => '/products/1/delete', 'variant' => 'danger']
    ], ['icon' => 'more-horizontal']);
```

## Footer Configuration

The card footer typically contains action buttons and dropdown menus.

### Footer Action Buttons
```php
// Single action
$card->addFooterAction('Save', '/users/1/save', [
    'variant' => 'primary',
    'icon' => 'save'
]);

// Multiple actions with different variants
$card->addFooterAction('Approve', '/orders/1/approve', [
        'variant' => 'primary',
        'icon' => 'check'
    ])
    ->addFooterAction('Reject', '/orders/1/reject', [
        'variant' => 'danger',
        'icon' => 'x'
    ])
    ->addFooterAction('Hold', '/orders/1/hold', [
        'variant' => 'secondary',
        'icon' => 'pause'
    ]);
```

### Footer Dropdown Menus
```php
$card->addFooterDropdown('Export', [
    ['label' => 'PDF', 'action' => '/export/pdf', 'icon' => 'file-pdf'],
    ['label' => 'Excel', 'action' => '/export/xlsx', 'icon' => 'file-excel'],
    ['label' => 'CSV', 'action' => '/export/csv', 'icon' => 'file-csv'],
    ['label' => 'JSON', 'action' => '/export/json', 'icon' => 'file-code']
], ['icon' => 'download', 'variant' => 'secondary']);
```

### Complete Footer Example
```php
$card->addFooterAction('View Details', '/items/1', [
        'variant' => 'secondary',
        'icon' => 'eye'
    ])
    ->addFooterAction('Edit', '/items/1/edit', [
        'variant' => 'primary',
        'icon' => 'edit'
    ])
    ->addFooterDropdown('More Actions', [
        ['label' => 'Duplicate', 'action' => '/items/1/duplicate', 'icon' => 'copy'],
        ['label' => 'Archive', 'action' => '/items/1/archive', 'icon' => 'archive'],
        'divider',
        ['label' => 'Delete', 'action' => '/items/1/delete', 'variant' => 'danger', 'icon' => 'trash']
    ]);
```

## Fields & Content

### Adding Fields
```php
// Static field values
$card->addField('name', 'Full Name', 'John Doe')
    ->addField('email', 'Email Address', 'john@example.com')
    ->addField('phone', 'Phone', '+1 234 567 8900')
    ->addField('joined', 'Member Since', 'January 2024');

// Dynamic fields (value from data source)
$card->addField('name', 'Full Name')
    ->addField('email', 'Email Address')
    ->dataUrl('/api/users/1');
```

### Field Types & Formatting
```php
// The frontend can format based on field name or add metadata
$card->addField('price', 'Price', '$99.99')
    ->addField('quantity', 'In Stock', '145 units')
    ->addField('rating', 'Rating', '4.5/5.0')
    ->addField('status', 'Status', 'Active');
```

## Actions & Dropdowns

### Action Button Options
```php
[
    'variant' => 'primary|secondary|danger|warning|success|info|ghost|outline',
    'icon' => 'icon-name',
    'size' => 'xs|sm|md|lg',
    'class' => 'custom-class',
    'method' => 'GET|POST|PUT|DELETE',
    'target' => '_blank|_self',
    
    // Confirmation dialog
    'confirmation' => [
        'title' => 'Confirm Delete',
        'message' => 'Are you sure you want to delete this item?',
        'button' => 'Delete',
        'variant' => 'danger'
    ],
    
    // Modal with form (ActionModal object)
    'modal' => ActionModal::make('hold-modal')
        ->title('Hold Item')
        ->description('Please provide a reason')
        ->addFormFields([...])
        ->submitLabel('Submit')
]
```

### Confirmation Dialogs
Actions can trigger a confirmation dialog before execution:

```php
// Simple confirmation
$card->addFooterAction('Delete', '/users/1/delete', [
    'variant' => 'danger',
    'icon' => 'trash',
    'confirmation' => [
        'title' => 'Confirm Delete',
        'message' => 'Are you sure you want to delete this user? This action cannot be undone.',
        'button' => 'Delete User',
        'variant' => 'danger'
    ]
]);

// Confirmation with custom styling
$card->addHeaderAction('Archive', '/items/1/archive', [
    'variant' => 'warning',
    'icon' => 'archive',
    'confirmation' => [
        'title' => 'Archive Item',
        'message' => 'This item will be moved to archives. You can restore it later.',
        'button' => 'Archive',
        'variant' => 'warning'
    ]
]);
```

### Modal Forms with ActionModal
For actions that require user input, use the ActionModal object:

```php
use Litepie\Layout\ActionModal;

// Modal with form fields
$card->addFooterAction('Hold', '/orders/1/hold', [
    'variant' => 'warning',
    'icon' => 'pause',
    'modal' => ActionModal::make('hold-order-modal')
        ->title('Hold Order')
        ->description('Please provide details for holding this order')
        ->addFormFields([
            // Add Litepie\Form field instances here
        ])
        ->submitLabel('Hold Order')
        ->submitClass('btn btn-warning')
]);

// Complete example with form fields
$holdModal = ActionModal::make('hold-modal')
    ->title('Hold Item')
    ->description('Specify the reason for holding this item')
    ->submitLabel('Hold Item')
    ->cancelLabel('Cancel');

$card->addHeaderAction('Hold', '/items/1/hold', [
    'variant' => 'warning',
    'icon' => 'pause',
    'modal' => $holdModal
]);

// Modal for collecting feedback
$feedbackModal = ActionModal::make('feedback-modal')
    ->title('Provide Feedback')
    ->description('Help us improve by sharing your thoughts')
    ->submitLabel('Submit Feedback')
    ->submitClass('btn btn-primary');

$card->addFooterAction('Feedback', '/feedback', [
    'icon' => 'message-circle',
    'modal' => $feedbackModal
]);
```

### Dropdown Item Options
```php
[
    'label' => 'Item Label',
    'action' => '/url/to/action',
    'icon' => 'icon-name',
    'variant' => 'default|danger|warning',
    'method' => 'GET|POST',
    'disabled' => false,
    'divider' => false,  // Or use string 'divider' for separator
    
    // Confirmation for dropdown items
    'confirmation' => [
        'title' => 'Confirm Action',
        'message' => 'Are you sure?',
        'button' => 'Confirm',
        'variant' => 'danger'
    ]
]
```

### Dropdown with Dividers and Confirmations
```php
$card->addHeaderDropdown('Menu', [
    ['label' => 'View', 'action' => '/view', 'icon' => 'eye'],
    ['label' => 'Edit', 'action' => '/edit', 'icon' => 'edit'],
    'divider',  // Separator line
    ['label' => 'Share', 'action' => '/share', 'icon' => 'share'],
    'divider',
    [
        'label' => 'Delete', 
        'action' => '/delete', 
        'variant' => 'danger', 
        'icon' => 'trash',
        'confirmation' => [
            'title' => 'Delete Item',
            'message' => 'This action cannot be undone.',
            'button' => 'Delete',
            'variant' => 'danger'
        ]
    ]
]);
```

## Variants & Styling

### Card Variants
```php
// Default variant (filled background)
$card->variant('default');

// Outlined variant (border with light background)
$card->variant('outlined');

// Elevated variant (shadow effect)
$card->variant('elevated');
```

### Using Variants
```php
// Subtle card with border
CardComponent::make('stats-card')
    ->variant('outlined')
    ->title('Total Sales')
    ->addField('amount', 'Amount', '$12,345');

// Prominent card with shadow
CardComponent::make('featured-product')
    ->variant('elevated')
    ->image('/products/featured.jpg')
    ->title('Featured Product')
    ->addField('price', 'Price', '$299.99');
```

## Data Source Integration

### Static Data
```php
$card->addField('name', 'Name', 'John Doe')
    ->addField('email', 'Email', 'john@example.com');
```

### Dynamic Data from URL
```php
$card->dataUrl('/api/users/1')
    ->dataParams(['include' => 'profile,settings'])
    ->loadOnMount(true)
    ->addField('name', 'Name')
    ->addField('email', 'Email')
    ->addField('phone', 'Phone');
```

### Shared Data
```php
// Use data from parent layout's shared data
$card->useSharedData(true)
    ->dataKey('currentUser')
    ->addField('name', 'Name')
    ->addField('role', 'Role');
```

### Data Callback
```php
$card->dataSource(function() {
    return User::with('profile')->find(1);
});
```

### Reload on Change
```php
$card->dataUrl('/api/dashboard/stats')
    ->reloadOnChange(true)  // Reload when dependencies change
    ->dataParams(['date' => '{{ selectedDate }}']);
```

## Authorization & Permissions

### Permission-Based Visibility
```php
$card->permissions(['view-users', 'manage-users'])
    ->roles(['admin', 'manager']);
```

### Conditional Visibility
```php
$card->canSee(function($user) {
    return $user->isAdmin() || $user->owns($this->resource);
});
```

### Combining Authorization
```php
CardComponent::make('admin-card')
    ->title('Admin Controls')
    ->permissions(['admin-access'])
    ->canSee(fn($user) => $user->subscription->isActive())
    ->addFooterAction('Manage', '/admin', ['variant' => 'primary']);
```

## Complete Examples

### User Profile Card
```php
CardComponent::make('user-profile')
    ->title('John Doe')
    ->subtitle('Senior Developer')
    ->icon('user')
    ->image('/images/users/john-banner.jpg')
    ->variant('elevated')
    ->addHeaderAction('Edit Profile', '/users/1/edit', [
        'variant' => 'primary',
        'icon' => 'edit',
        'size' => 'sm'
    ])
    ->addHeaderDropdown('Settings', [
        ['label' => 'Privacy', 'action' => '/users/1/privacy', 'icon' => 'shield'],
        ['label' => 'Security', 'action' => '/users/1/security', 'icon' => 'lock'],
        'divider',
        ['label' => 'Deactivate', 'action' => '/users/1/deactivate', 'variant' => 'danger']
    ], ['icon' => 'settings'])
    ->addField('email', 'Email', 'john@example.com')
    ->addField('phone', 'Phone', '+1 234 567 8900')
    ->addField('location', 'Location', 'San Francisco, CA')
    ->addField('joined', 'Member Since', 'January 2024')
    ->addFooterAction('View Full Profile', '/users/1', [
        'variant' => 'secondary',
        'icon' => 'arrow-right'
    ]);
```

### Product Card
```php
CardComponent::make('product-card')
    ->title('Wireless Headphones')
    ->subtitle('Premium Audio')
    ->image('/products/headphones.jpg')
    ->variant('elevated')
    ->addHeader('badge', 'Status', ['variant' => 'success', 'text' => 'In Stock'])
    ->addHeader('badge', 'Category', ['variant' => 'info', 'text' => 'Electronics'])
    ->addHeaderDropdown('Actions', [
        ['label' => 'Edit', 'action' => '/products/1/edit', 'icon' => 'edit'],
        ['label' => 'Duplicate', 'action' => '/products/1/duplicate', 'icon' => 'copy'],
        'divider',
        ['label' => 'Archive', 'action' => '/products/1/archive', 'icon' => 'archive']
    ])
    ->addField('price', 'Price', '$299.99')
    ->addField('sku', 'SKU', 'WH-2024-001')
    ->addField('quantity', 'In Stock', '45 units')
    ->addField('rating', 'Rating', '4.8/5.0')
    ->addFooterAction('Add to Cart', '/cart/add/1', [
        'variant' => 'primary',
        'icon' => 'shopping-cart'
    ])
    ->addFooterAction('View Details', '/products/1', [
        'variant' => 'secondary',
        'icon' => 'eye'
    ]);
```

### Dashboard Stats Card
```php
CardComponent::make('sales-stats')
    ->title('Total Sales')
    ->subtitle('Last 30 days')
    ->icon('trending-up')
    ->variant('outlined')
    ->addHeader('badge', 'Change', ['variant' => 'success', 'text' => '+12.5%'])
    ->addField('total', 'Total Revenue', '$125,430')
    ->addField('orders', 'Total Orders', '1,234')
    ->addField('avg_order', 'Avg. Order Value', '$101.64')
    ->dataUrl('/api/stats/sales')
    ->reloadOnChange(true)
    ->loadOnMount(true)
    ->addFooterAction('View Report', '/reports/sales', [
        'variant' => 'secondary',
        'icon' => 'file-text'
    ])
    ->addFooterDropdown('Export', [
        ['label' => 'PDF', 'action' => '/export/pdf', 'icon' => 'file-pdf'],
        ['label' => 'Excel', 'action' => '/export/xlsx', 'icon' => 'file-excel']
    ]);
```

### Order Management Card
```php
CardComponent::make('order-card')
    ->title('Order #ORD-2024-001')
    ->subtitle('Placed on Dec 15, 2024')
    ->icon('shopping-bag')
    ->addHeader('badge', 'Status', ['variant' => 'warning', 'text' => 'Pending'])
    ->addHeaderAction('View Invoice', '/orders/1/invoice', [
        'icon' => 'receipt',
        'size' => 'sm'
    ])
    ->addField('customer', 'Customer', 'Jane Smith')
    ->addField('total', 'Total Amount', '$456.78')
    ->addField('items', 'Items', '3 items')
    ->addField('payment', 'Payment Method', 'Credit Card')
    ->dataUrl('/api/orders/1')
    ->addFooterAction('Approve', '/orders/1/approve', [
        'variant' => 'primary',
        'icon' => 'check',
        'confirm' => 'Approve this order?'
    ])
    ->addFooterAction('Reject', '/orders/1/reject', [
        'variant' => 'danger',
        'icon' => 'x',
        'confirm' => 'Reject this order?'
    ])
    ->addFooterDropdown('More', [
        ['label' => 'Edit', 'action' => '/orders/1/edit', 'icon' => 'edit'],
        ['label' => 'Print', 'action' => '/orders/1/print', 'icon' => 'printer'],
        'divider',
        ['label' => 'Refund', 'action' => '/orders/1/refund', 'icon' => 'dollar-sign']
    ])
    ->permissions(['manage-orders']);
```

### Task Card with Complex Actions
```php
CardComponent::make('task-card')
    ->title('Implement Payment Gateway')
    ->subtitle('Due: Dec 20, 2025')
    ->icon('check-square')
    ->variant('outlined')
    ->addHeader('badge', 'Priority', ['variant' => 'danger', 'text' => 'High'])
    ->addHeader('badge', 'Sprint', ['variant' => 'info', 'text' => 'Sprint 12'])
    ->addHeaderAction('Edit', '/tasks/1/edit', ['icon' => 'edit', 'size' => 'sm'])
    ->addHeaderDropdown('More', [
        ['label' => 'Assign', 'action' => '/tasks/1/assign', 'icon' => 'user-plus'],
        ['label' => 'Move', 'action' => '/tasks/1/move', 'icon' => 'move'],
        ['label' => 'Duplicate', 'action' => '/tasks/1/duplicate', 'icon' => 'copy'],
        'divider',
        ['label' => 'Archive', 'action' => '/tasks/1/archive', 'icon' => 'archive'],
        ['label' => 'Delete', 'action' => '/tasks/1/delete', 'variant' => 'danger', 'icon' => 'trash']
    ], ['icon' => 'more-horizontal'])
    ->addField('assignee', 'Assigned To', 'John Doe')
    ->addField('status', 'Status', 'In Progress')
    ->addField('progress', 'Progress', '65%')
    ->addField('estimate', 'Estimate', '8 hours')
    ->addFooterAction('Mark Complete', '/tasks/1/complete', [
        'variant' => 'primary',
        'icon' => 'check-circle'
    ])
    ->addFooterDropdown('Change Status', [
        ['label' => 'To Do', 'action' => '/tasks/1/status/todo'],
        ['label' => 'In Progress', 'action' => '/tasks/1/status/progress'],
        ['label' => 'In Review', 'action' => '/tasks/1/status/review'],
        ['label' => 'On Hold', 'action' => '/tasks/1/status/hold'],
        ['label' => 'Blocked', 'action' => '/tasks/1/status/blocked']
    ])
    ->addFooterAction('Add Comment', '/tasks/1/comment', [
        'variant' => 'secondary',
        'icon' => 'message-circle'
    ]);
```

## API Reference

### Factory Methods
```php
CardComponent::make(string $name): self
```

### Core Configuration
```php
title(string $title): self
subtitle(?string $subtitle): self
description(?string $description): self
icon(?string $icon): self
image(string $image): self
variant(string $variant): self  // 'default', 'outlined', 'elevated'
order(?int $order): self
visible(bool $visible): self
```

### Header Methods
```php
addHeader(string $type, string $label, array $options = []): self
addHeaderAction(string $label, string $action, array $options = []): self
addHeaderDropdown(string $label, array $items, array $options = []): self
header(array $header): self
```

### Footer Methods
```php
addFooter(string $label, string $action, array $options = []): self
addFooterAction(string $label, string $action, array $options = []): self
addFooterDropdown(string $label, array $items, array $options = []): self
footer(array $footer): self
```

### Fields Methods
```php
addField(string $name, string $label, mixed $value = null): self
```

### Data Source Methods
```php
dataUrl(?string $url): self
dataParams(array $params): self
dataSource(callable $callback): self
dataTransform(?string $transform): self
loadOnMount(bool $load): self
reloadOnChange(bool $reload): self
useSharedData(bool $use): self
dataKey(?string $key): self
```

### Authorization Methods
```php
permissions(array $permissions): self
roles(array $roles): self
canSee(callable $callback): self
```

### Inherited from BaseComponent
```php
meta(array $meta): self
addMeta(string $key, mixed $value): self
when(bool|callable $condition, callable $callback): self
conditional(string $expression): self
onBeforeRender(callable $callback): self
onAfterRender(callable $callback): self
```

### Serialization
```php
toArray(): array
```

## Output Structure

The `toArray()` method returns:
```php
[
    'type' => 'card',
    'name' => 'card-name',
    'title' => 'Card Title',
    'subtitle' => 'Card Subtitle',
    'icon' => 'icon-name',
    'description' => 'Card description',
    'image' => '/path/to/image.jpg',
    'variant' => 'default',
    'fields' => [
        ['name' => 'field1', 'label' => 'Label 1', 'value' => 'Value 1'],
        // ...
    ],
    'header' => [
        ['type' => 'badge', 'label' => 'Status', 'variant' => 'success', 'text' => 'Active'],
        ['type' => 'action', 'label' => 'Edit', 'action' => '/edit', 'icon' => 'edit'],
        ['type' => 'dropdown', 'label' => 'More', 'items' => [...]],
        // ...
    ],
    'footer' => [
        ['type' => 'action', 'label' => 'Save', 'action' => '/save', 'variant' => 'primary'],
        ['type' => 'dropdown', 'label' => 'Export', 'items' => [...]],
        // ...
    ],
    'data_source' => null,
    'data_url' => '/api/endpoint',
    'data_params' => ['key' => 'value'],
    'load_on_mount' => true,
    'reload_on_change' => false,
    'use_shared_data' => false,
    'data_key' => null,
    'actions' => [],  // From BaseComponent
    'order' => 1,
    'visible' => true,
    'permissions' => [],
    'roles' => [],
    'authorized_to_see' => true,
    'meta' => []
]
```

## Best Practices

### 1. Keep Cards Focused
```php
// Good - focused on one entity
CardComponent::make('user-profile')
    ->title('User Details')
    ->addField('name', 'Name', 'John Doe')
    ->addField('email', 'Email', 'john@example.com');

// Avoid - mixing unrelated data
// Don't put user info and order info in the same card
```

### 2. Use Appropriate Variants
```php
// Stats/metrics - use outlined
$statsCard->variant('outlined');

// Featured/important content - use elevated
$featuredCard->variant('elevated');

// Standard content - use default
$standardCard->variant('default');
```

### 3. Consistent Action Placement
```php
// Primary actions in footer
$card->addFooterAction('Save', '/save', ['variant' => 'primary']);

// Secondary/contextual actions in header
$card->addHeaderAction('Edit', '/edit', ['size' => 'sm']);
```

### 4. Use Dropdowns for Multiple Actions
```php
// Good - use dropdown for 3+ actions
$card->addHeaderDropdown('Actions', [
    ['label' => 'Edit', 'action' => '/edit'],
    ['label' => 'Duplicate', 'action' => '/duplicate'],
    ['label' => 'Delete', 'action' => '/delete']
]);

// Avoid - too many individual buttons
// Don't add 5+ individual action buttons
```

### 5. Meaningful Field Labels
```php
// Good - clear labels
$card->addField('created_at', 'Member Since', 'Jan 2024')
    ->addField('last_login', 'Last Active', '2 hours ago');

// Avoid - technical names as labels
// Don't use 'created_at' as the label
```

### 6. Leverage Data Sources
```php
// Good - let the backend provide dynamic data
$card->dataUrl('/api/users/1')
    ->addField('name', 'Name')
    ->addField('email', 'Email');

// Avoid - hardcoding changing data
// Don't hardcode values that change frequently
```

### 7. Use Authorization Wisely
```php
// Good - hide sensitive cards
CardComponent::make('billing-info')
    ->permissions(['view-billing'])
    ->canSee(fn($user) => $user->subscription->isActive());

// Avoid - showing unauthorized content
// Always check permissions for sensitive data
```

## Integration with Frontend

The CardComponent data structure is designed to be consumed by React, Vue, or other frontend frameworks:

```typescript
// Example TypeScript interface
interface CardData {
  type: 'card';
  name: string;
  title?: string;
  subtitle?: string;
  icon?: string;
  description?: string;
  image?: string;
  variant: 'default' | 'outlined' | 'elevated';
  fields: Array<{
    name: string;
    label: string;
    value?: any;
  }>;
  header: Array<HeaderItem>;
  footer: Array<FooterItem>;
  data_url?: string;
  // ... other properties
}
```

## Next Steps

1. **Frontend Implementation** - Create React/Vue card components
2. **CSS Styling** - Implement visual styles for all variants
3. **Unit Tests** - Add PHPUnit tests for the component
4. **Integration Examples** - Add more real-world examples
5. **Performance Optimization** - Optimize data loading and caching

## Related Components

- [BaseComponent](src/Components/BaseComponent.php) - Parent class
- [ModalComponent](src/Components/ModalComponent.php) - Similar structure with footer
- [AvatarComponent](AVATAR_COMPONENT.md) - Can be used within cards
- [BadgeComponent](src/Components/BadgeComponent.php) - Can be used in headers

## Status

✅ **Implementation Complete**
- Core component implemented
- Header and footer support added
- Action buttons and dropdowns added
- Comprehensive documentation created
- Ready for frontend integration

---

**Version:** 1.0.0  
**Last Updated:** December 17, 2025  
**Component Location:** `src/Components/CardComponent.php`
