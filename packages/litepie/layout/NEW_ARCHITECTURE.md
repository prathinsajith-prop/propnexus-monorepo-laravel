# New 4-Level Architecture

## Overview

The Litepie Layout Builder now uses a clean, consistent **4-level hierarchy**:

```
Layout → Section → Slot → Component
```

This architecture enforces clear separation of concerns and makes the structure predictable and maintainable.

## Hierarchy Rules

### Level 1: Layout (Root Container)

**Can contain:** Sections only

```php
$layout = LayoutBuilder::create('dashboard', 'view')
    ->title('Dashboard')
    ->addComponent($headerSection)
    ->addComponent($gridSection);
```

### Level 2: Section (Structural Container)

**Can contain:** Slots only (not Components directly)

**Available Section Types:**
- `HeaderSection` - Page headers with left/center/right slots
- `LayoutSection` - Master layouts with header/sidebar/body/footer/aside slots
- `GridSection` - Grid layouts with 'items' slot
- `TabsSection` - Tabbed interfaces with named slots per tab
- `AccordionSection` - Collapsible panels with named slots per panel
- `WizardSection` - Multi-step wizards with named slots per step
- `ScrollSpySection` - Scroll-spy navigation with dynamic slots

```php
$header = HeaderSection::make('main-header')
    ->title('Dashboard')
    ->sticky();
```

### Level 3: Slot (Named Content Area)

**Can contain:** Components and nested Sections

**Purpose:** Named placeholders for content within a Section

```php
$header->slot('left')
    ->add($logoComponent)
    ->add($breadcrumbComponent);

$header->slot('right')
    ->add($userMenuComponent);
```

### Level 4: Component (Content Leaf Node)

**Can contain:** Nothing (pure leaf nodes)

**Available Component Types:**
- `FormComponent` - Forms
- `CardComponent` - Cards
- `TableComponent` - Tables
- `ChartComponent` - Charts
- `StatsComponent` - Statistics
- `AlertComponent` - Alerts
- `BadgeComponent` - Badges
- `ModalComponent` - Modals
- `TextComponent` - Text content
- `AvatarComponent` - Avatars
- `BreadcrumbComponent` - Breadcrumbs
- `ListComponent` - Lists
- `TimelineComponent` - Timelines
- `MediaComponent` - Media
- `DocumentComponent` - Documents
- `DividerComponent` - Dividers
- `CustomComponent` - Custom components

```php
$card = CardComponent::make('user-card')
    ->title('User Info')
    ->addField('name', 'John Doe')
    ->addField('email', 'john@example.com');
```

## Usage Examples

### Example 1: Simple Dashboard

```php
$layout = LayoutBuilder::create('dashboard', 'view');

// Add header section
$header = HeaderSection::make('main-header');
$header->slot('left')->add(
    LogoComponent::make('logo')->src('/logo.png')
);
$header->slot('right')->add(
    UserMenuComponent::make('user-menu')
);
$layout->addComponent($header);

// Add stats grid
$grid = GridSection::make('stats')->columns(4);
$grid->slot('items')
    ->add(StatsComponent::make('users')->value(1234))
    ->add(StatsComponent::make('revenue')->value(98650))
    ->add(StatsComponent::make('orders')->value(456))
    ->add(StatsComponent::make('growth')->value('+12%'));
$layout->addComponent($grid);
```

### Example 2: Tabbed Interface

```php
$tabs = TabsSection::make('profile-tabs')
    ->addTab('info', 'Personal Info', ['icon' => 'user'])
    ->addTab('settings', 'Settings', ['icon' => 'settings'])
    ->addTab('security', 'Security', ['icon' => 'lock'])
    ->activeTab('info');

// Add content to each tab slot
$tabs->slot('info')->add(
    FormComponent::make('personal-form')
        ->addFormField('name', 'text', 'Name')
        ->addFormField('email', 'email', 'Email')
);

$tabs->slot('settings')->add(
    FormComponent::make('settings-form')
        ->addFormField('theme', 'select', 'Theme')
);

$tabs->slot('security')->add(
    FormComponent::make('security-form')
        ->addFormField('password', 'password', 'New Password')
);

$layout->addComponent($tabs);
```

### Example 3: Multi-Step Wizard

```php
$wizard = WizardSection::make('onboarding')
    ->addStep('account', 'Account', ['icon' => 'user'])
    ->addStep('profile', 'Profile', ['icon' => 'user-circle'])
    ->addStep('complete', 'Done', ['icon' => 'check'])
    ->linear(true);

// Add content to each step slot
$wizard->slot('account')->add(
    FormComponent::make('account-form')
        ->addFormField('email', 'email', 'Email')
        ->addFormField('password', 'password', 'Password')
);

$wizard->slot('profile')->add(
    FormComponent::make('profile-form')
        ->addFormField('name', 'text', 'Full Name')
        ->addFormField('bio', 'textarea', 'Bio')
);

$wizard->slot('complete')->add(
    TextComponent::make('success')
        ->content('Setup complete!')
);

$layout->addComponent($wizard);
```

### Example 4: Nested Sections

Slots can contain nested Sections for complex layouts:

```php
$mainGrid = GridSection::make('main')->columns(2);

// Add a tabs section within the grid
$tabs = TabsSection::make('content-tabs')
    ->addTab('overview', 'Overview')
    ->addTab('details', 'Details');

$tabs->slot('overview')->add(
    CardComponent::make('summary')->title('Summary')
);

$tabs->slot('details')->add(
    TableComponent::make('data')->title('Data Table')
);

// Add the tabs section to the grid slot
$mainGrid->slot('items')
    ->add($tabs)
    ->add(ChartComponent::make('chart')->type('line'));

$layout->addComponent($mainGrid);
```

## Key Benefits

### 1. **Consistency**
All sections work the same way - they all use slots. No more special cases like GridSection's `$components` array or TabsSection's `$tabs` array.

### 2. **Predictability**
The hierarchy is always: Layout → Section → Slot → Component. No ambiguity about where content goes.

### 3. **Explicit Placement**
Every component must be placed in a named slot, making layouts self-documenting:
```php
$header->slot('left')   // Clear: this goes in the left area
$grid->slot('items')    // Clear: these are grid items
$tabs->slot('profile')  // Clear: this is the profile tab content
```

### 4. **Better Semantics**
Slot names are meaningful: `left`, `right`, `body`, `items`, `tab-name`, etc.

### 5. **Industry Standard**
Matches modern framework patterns (Vue slots, React composition, Web Components).

## Migration Notes

### Breaking Changes

1. **SectionContainer renamed to Slot**
   ```php
   // Old (still works as deprecated)
   $section->section('left')
   
   // New (preferred)
   $section->slot('left')
   ```

2. **GridSection no longer has addComponent()**
   ```php
   // Old
   $grid->addComponent($card)
   
   // New
   $grid->slot('items')->add($card)
   ```

3. **TabsSection uses slots instead of callback arrays**
   ```php
   // Old
   $tabs->addTab('profile', 'Profile', function($tab) {
       $tab->add($form);
   })
   
   // New
   $tabs->addTab('profile', 'Profile')
   $tabs->slot('profile')->add($form)
   ```

4. **AccordionSection and WizardSection follow same pattern**
   - Define panels/steps with `addPanel()`/`addStep()`
   - Add content to slots with `slot($name)->add()`

### Backward Compatibility

Several backward compatibility features are maintained:
- `section()` method still works (alias for `slot()`)
- `getAllowedSections()` still works (alias for `getAllowedSlots()`)
- Legacy Section/Subsection structure still supported

## Architecture Validation

The new architecture enforces these rules automatically:

✅ **Valid:**
- Layout contains Sections
- Section contains Slots
- Slot contains Components
- Slot contains Sections (for nesting)

❌ **Invalid:**
- Layout directly contains Components (use a Section wrapper)
- Section directly contains Components (must use Slot)
- Component contains anything (components are leaf nodes)

## Best Practices

1. **Use descriptive slot names**
   ```php
   $section->slot('main-content')  // Good
   $section->slot('slot1')         // Bad
   ```

2. **Keep slot structure shallow when possible**
   ```php
   // Good - direct placement
   $header->slot('left')->add($logo);
   
   // Over-engineered - too much nesting
   $header->slot('left')->add(
       GridSection::make('logo-grid')->slot('items')->add($logo)
   );
   ```

3. **Use appropriate section types**
   ```php
   // Good - use GridSection for grids
   $grid = GridSection::make('cards')->columns(3);
   
   // Bad - trying to make GridSection work like HeaderSection
   $grid->slot('left')->slot('right') // GridSection only has 'items' slot
   ```

4. **Leverage slot validation**
   ```php
   // HeaderSection only allows: left, center, right
   $header->slot('sidebar')  // Throws InvalidArgumentException
   ```

This enforces correct usage and prevents bugs.
