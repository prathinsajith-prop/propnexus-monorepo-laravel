# Architecture Guide

This document provides an in-depth look at the Litepie Layout Builder architecture, design patterns, and best practices.

## Table of Contents

- [Overview](#overview)
- [Design Philosophy](#design-philosophy)
- [Core Architecture](#core-architecture)
- [Sections vs Components](#sections-vs-components)
- [Section Slots](#section-slots)
- [Nesting Rules](#nesting-rules)
- [Data Flow](#data-flow)
- [Rendering Pipeline](#rendering-pipeline)
- [Extension Points](#extension-points)
- [Design Patterns](#design-patterns)
- [Best Practices](#best-practices)

## Overview

The Litepie Layout Builder is designed around a clear separation of concerns:

1. **Structure (Sections)** - Define layout organization and hierarchy
2. **Content (Components)** - Render actual UI elements
3. **Data** - Load and transform data from various sources
4. **Authorization** - Control access and visibility
5. **Presentation** - Generate framework-agnostic output

## Design Philosophy

### 1. Declarative over Imperative

Build layouts by declaring *what* you want, not *how* to build it:

```php
// ✅ Declarative (Good)
$layout->section('header', function ($section) {
    $section->breadcrumb('nav')->addItem('Home', '/');
});

// ❌ Imperative (Avoid)
$breadcrumb = new BreadcrumbComponent();
$breadcrumb->addItem('Home', '/');
$section->addComponent($breadcrumb);
```

### 2. Composition over Inheritance

Compose complex layouts from simple building blocks:

```php
// Compose a dashboard from reusable components
$layout->section('main', function ($section) {
    $section->grid('content')
        ->columns(3)
        ->addComponent($this->statsCard('users'))
        ->addComponent($this->statsCard('orders'))
        ->addComponent($this->statsCard('revenue'));
});
```

### 3. Separation of Concerns

Each layer has a single responsibility:

- **Layout** - Structure and organization
- **Components** - UI rendering
- **Data Sources** - Data fetching
- **Transformers** - Data processing
- **Authorization** - Access control
- **Cache** - Performance optimization

### 4. Framework Agnostic Output

Render to JSON/arrays that any frontend can consume:

```php
$output = $layout->render();
// Can be consumed by React, Vue, Alpine, Blade, or any framework
```

## Core Architecture

### Class Hierarchy

```
┌─────────────────────────────────────┐
│         Layout (Root)               │
│  - Manages sections                 │
│  - Coordinates rendering            │
│  - Handles caching                  │
└─────────────────────────────────────┘
                 │
                 ├─────────────────────────────┐
                 ▼                             ▼
┌─────────────────────────────┐   ┌───────────────────────────┐
│     BaseSection             │   │    BaseComponent          │
│  (Abstract Container)       │   │  (Abstract Leaf Node)     │
│  - Has section slots        │   │  - Renders content        │
│  - Can contain elements     │   │  - Cannot contain others  │
│  - Organizes structure      │   │  - Has fields/actions     │
└─────────────────────────────┘   └───────────────────────────┘
         │                                     │
         ├────────────────────┐               ├──────────────────┐
         ▼                    ▼               ▼                  ▼
┌─────────────┐    ┌─────────────────┐  ┌──────────┐    ┌──────────┐
│HeaderSection│    │  GridSection    │  │FormComp. │    │CardComp. │
│LayoutSection│    │  TabsSection    │  │TableComp.│    │ListComp. │
│ etc...      │    │  etc...         │  │ etc...   │    │ etc...   │
└─────────────┘    └─────────────────┘  └──────────┘    └──────────┘
```

### Key Interfaces

#### Component Interface

All sections and components implement this:

```php
interface Component
{
    public function getName(): string;
    public function getType(): string;
    public function toArray(): array;
    public function visible(bool $visible): self;
    public function order(int $order): self;
}
```

#### Renderable Interface

Defines rendering capability:

```php
interface Renderable
{
    public function render(): array;
}
```

## Sections vs Components

This is the most important architectural distinction.

### Sections (Containers)

**Purpose:** Organize and structure layout using named slots.

**Characteristics:**
- Extend `BaseSection`
- Have `$sectionSlots` array for organizing content
- Use `section()` method to access slots
- Can contain both Sections and Components
- Do not render content themselves

**Example:**

```php
class GridSection extends BaseSection
{
    protected int $columns = 3;
    
    public function columns(int $columns): self
    {
        $this->columns = $columns;
        return $this;
    }
    
    public function addComponent(Component $component): self
    {
        $this->section('body')->addComponent($component);
        return $this;
    }
}
```

**Available Sections:**

| Section | Purpose | Typical Slots |
|---------|---------|---------------|
| `HeaderSection` | Page headers | header, body, footer |
| `LayoutSection` | Main layouts | header, sidebar, main, footer |
| `GridSection` | Grid layouts | body (contains grid items) |
| `TabsSection` | Tabbed interfaces | tabs (each tab has content) |
| `AccordionSection` | Collapsible panels | panels (each panel has content) |
| `WizardSection` | Multi-step workflows | steps (each step has content) |
| `ScrollSpySection` | Scroll navigation | sections (each section has content) |

### Components (Content)

**Purpose:** Render actual UI content.

**Characteristics:**
- Extend `BaseComponent`
- Have `$fields` array for data
- Have `$actions` array for interactions
- Cannot contain other elements (leaf nodes)
- Render to JSON/array output

**Example:**

```php
class CardComponent extends BaseComponent
{
    protected string $type = 'card';
    
    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), [
            'fields' => $this->fields,
            'actions' => $this->actions,
        ]);
    }
}
```

**Available Components:**

| Component | Purpose | Key Features |
|-----------|---------|--------------|
| `FormComponent` | Forms | Fields, validation, submission |
| `CardComponent` | Content cards | Title, fields, actions |
| `TableComponent` | Data tables | Columns, sorting, pagination |
| `ListComponent` | Lists | Items, ordering, types |
| `AlertComponent` | Notifications | Types (success, warning, etc.) |
| `BadgeComponent` | Labels/tags | Colors, sizes |
| `ModalComponent` | Dialogs | Trigger, content, actions |
| `ChartComponent` | Visualizations | Chart types, datasets |
| `TextComponent` | Rich text | Markdown, HTML support |
| `CodeComponent` | Code blocks | Syntax highlighting, languages |
| `MediaComponent` | Media | Images, videos, galleries |
| `StatsComponent` | Statistics | Metrics, trends |
| `TimelineComponent` | Timelines | Events, dates |
| `CommentComponent` | Comments | Threads, replies |
| `BreadcrumbComponent` | Navigation | Path, links |
| `DocumentComponent` | Documents | Upload, list, preview |
| `CustomComponent` | Custom HTML/JSON | Flexible content |

## Section Slots

Sections organize content using named slots. Each slot is a `SectionContainer` that holds components and nested sections.

### Common Slot Names

- `header` - Top content (titles, breadcrumbs)
- `body` - Main content (default slot)
- `footer` - Bottom content (actions, info)
- `sidebar` - Side content (navigation, filters)
- `main` - Primary content area

### Using Section Slots

```php
// Explicit slot access
$section->section('header')->text('title')->content('Dashboard');
$section->section('body')->card('content')->title('Main Content');
$section->section('footer')->text('info')->content('Footer');

// Shorthand for body slot (default)
$section->card('content')->title('Goes to body slot');

// Check if slot has content
if ($section->hasNamedSection('header')) {
    // Header slot is populated
}

// Get all slots
$slots = $section->getSectionSlots();
```

### Custom Slots

Create sections with custom slots:

```php
class CustomSection extends BaseSection
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        
        // Initialize custom slots
        $this->section('left');
        $this->section('center');
        $this->section('right');
    }
    
    public function leftContent(Component $component): self
    {
        $this->section('left')->addComponent($component);
        return $this;
    }
}
```

## Nesting Rules

Understanding nesting rules is critical for clean architecture.

### ✅ Valid Nesting

**1. Sections can contain Sections**

```php
$layout->section('main', function ($section) {
    $section->grid('dashboard')
        ->columns(3)
        ->addComponent(/* ... */);
});
```

**2. Sections can contain Components**

```php
$section->grid('content')
    ->addComponent($section->card('stats'))
    ->addComponent($section->table('users'));
```

**3. Deep nesting is allowed**

```php
Layout
└── LayoutSection ('main')
    └── TabsSection ('content-tabs')
        ├── Tab 1
        │   └── GridSection
        │       ├── CardComponent
        │       └── TableComponent
        └── Tab 2
            └── FormComponent
```

### ❌ Invalid Nesting

**Components cannot contain anything** - they are leaf nodes:

```php
// ❌ WRONG - Components cannot have nested content
$section->card('parent')
    ->addSection($section->grid('nested')); // Method doesn't exist!

// ✅ CORRECT - Use a Section instead
$section->grid('parent')
    ->addComponent($section->card('child'));
```

### Why This Matters

This architecture enforces:

1. **Clear separation** - Structure (Sections) vs Content (Components)
2. **Type safety** - Components are always leaves
3. **Predictable behavior** - No ambiguity about what can contain what
4. **Easier reasoning** - Clear mental model

## Data Flow

### Data Loading Pipeline

```
┌──────────────┐
│ Data Source  │ (API, Database, Closure)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Data Params  │ (Query parameters, filters)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Fetch Data │ (HTTP request, DB query, function call)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Transform   │ (Apply data_transform closure)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Component   │ (Render with data)
└──────────────┘
```

### Data Source Examples

**1. API Endpoint**

```php
$component->dataUrl('/api/users')
    ->dataParams(['status' => 'active', 'limit' => 10])
    ->loadOnMount(true);
```

**2. Database Query**

```php
$component->dataSource('users')
    ->dataTransform(function ($query) {
        return $query->where('active', true)
            ->orderBy('created_at', 'desc')
            ->limit(100);
    });
```

**3. Closure**

```php
$component->dataSource(function () {
    return [
        'total_users' => User::count(),
        'active_today' => User::whereDate('last_login', today())->count(),
        'revenue' => Order::sum('total'),
    ];
});
```

**4. Shared Data**

```php
// Set shared data at layout level
$layout->setSharedData([
    'user' => auth()->user(),
    'settings' => config('app'),
]);

// Components can access shared data
$component->useSharedData('user');
```

## Rendering Pipeline

### Rendering Flow

```
┌──────────────┐
│   Layout     │
│  ->render()  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ BeforeRender │ Event
│   (Hook)     │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│Authorization │ (Check permissions/roles)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Load Data   │ (Fetch from sources)
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Evaluate   │ (Conditional logic)
│  Conditions  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   Sections   │ (Recursively render)
│  ->toArray() │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Components  │ (Render to array)
│  ->toArray() │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ AfterRender  │ Event
│   (Hook)     │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│    Output    │ (JSON/Array)
└──────────────┘
```

### Rendering Methods

**1. Full Render**

```php
$output = $layout->render();
// Returns complete nested array structure
```

**2. Component Render**

```php
$output = $component->toArray();
// Returns component as array
```

**3. Cached Render**

```php
$output = $layout->cache()
    ->ttl(3600)
    ->render();
// Returns cached output or renders and caches
```

## Extension Points

### 1. Custom Sections

Create custom section types:

```php
namespace App\Layout\Sections;

use Litepie\Layout\Sections\BaseSection;

class DashboardSection extends BaseSection
{
    protected string $type = 'dashboard';
    
    public function __construct(string $name)
    {
        parent::__construct($name);
        
        // Define slots
        $this->section('metrics');
        $this->section('charts');
        $this->section('tables');
    }
    
    public function addMetric($metric): self
    {
        $this->section('metrics')->addComponent($metric);
        return $this;
    }
    
    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), [
            'section_slots' => $this->serializeSectionSlots(),
        ]);
    }
}
```

### 2. Custom Components

Create custom component types:

```php
namespace App\Layout\Components;

use Litepie\Layout\Components\BaseComponent;

class WeatherComponent extends BaseComponent
{
    protected string $type = 'weather';
    protected string $location = '';
    protected string $units = 'metric';
    
    public function location(string $location): self
    {
        $this->location = $location;
        return $this;
    }
    
    public function units(string $units): self
    {
        $this->units = $units;
        return $this;
    }
    
    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), [
            'location' => $this->location,
            'units' => $this->units,
        ]);
    }
}
```

### 3. Custom Data Transformers

```php
class UserDataTransformer
{
    public function transform($data)
    {
        return collect($data)->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'status' => $user->active ? 'Active' : 'Inactive',
                'avatar' => $user->avatar_url,
            ];
        })->toArray();
    }
}

// Use in component
$component->dataSource('users')
    ->dataTransform([new UserDataTransformer, 'transform']);
```

### 4. Custom Validators

```php
class CustomLayoutValidator extends LayoutValidator
{
    protected function validateCardComponent(array $data): array
    {
        // Custom validation logic
        if (empty($data['title'])) {
            return ['Card must have a title'];
        }
        
        return [];
    }
}
```

## Design Patterns

### 1. Builder Pattern

Fluent interface for constructing layouts:

```php
$layout = Layout::create('dashboard')
    ->title('Dashboard')
    ->section('main', function ($section) {
        $section->grid('content')->columns(3);
    })
    ->cache()->ttl(3600)
    ->beforeRender(function ($layout) { /* ... */ });
```

### 2. Factory Pattern

Create components via factory methods:

```php
class ComponentFactory
{
    public static function statsCard(string $title, $value, string $icon): CardComponent
    {
        return CardComponent::create('stats-' . Str::slug($title))
            ->title($title)
            ->addField('value', $value)
            ->icon($icon);
    }
}

// Usage
$section->addComponent(ComponentFactory::statsCard('Users', 1234, 'users'));
```

### 3. Strategy Pattern

Different data loading strategies:

```php
interface DataLoader
{
    public function load(array $params): array;
}

class ApiDataLoader implements DataLoader
{
    public function load(array $params): array
    {
        return Http::get('/api/data', $params)->json();
    }
}

class DatabaseDataLoader implements DataLoader
{
    public function load(array $params): array
    {
        return User::where($params)->get()->toArray();
    }
}
```

### 4. Decorator Pattern

Enhance components with additional behavior:

```php
class CachedComponent
{
    public function __construct(
        protected Component $component,
        protected int $ttl = 3600
    ) {}
    
    public function render(): array
    {
        $key = 'component:' . $this->component->getName();
        
        return Cache::remember($key, $this->ttl, function () {
            return $this->component->render();
        });
    }
}
```

### 5. Observer Pattern

Layout events:

```php
// Register observers
$layout->beforeRender(function ($layout) {
    Log::info('Rendering: ' . $layout->getName());
});

$layout->afterRender(function ($layout, $output) {
    event(new LayoutRendered($layout, $output));
});
```

## Best Practices

### 1. Component Organization

```php
// ✅ Good - Organized by section slots
$layout->section('main', function ($section) {
    // Header content
    $section->section('header')->breadcrumb('nav')/* ... */;
    
    // Main content
    $section->grid('content')/* ... */;
    
    // Footer content
    $section->section('footer')->text('info')/* ... */;
});

// ❌ Avoid - Mixed without organization
$layout->section('main', function ($section) {
    $section->text('info');
    $section->breadcrumb('nav');
    $section->grid('content');
});
```

### 2. Reusable Components

```php
// Create reusable component builders
class LayoutComponents
{
    public static function statsCard(string $name, string $title, string $dataUrl)
    {
        return CardComponent::create($name)
            ->title($title)
            ->dataUrl($dataUrl)
            ->addField('current', 'Current')
            ->addField('change', 'Change')
            ->addField('trend', 'Trend');
    }
}

// Use throughout your app
$section->addComponent(LayoutComponents::statsCard('users', 'Users', '/api/stats/users'));
```

### 3. Authorization

```php
// Set permissions at the appropriate level
$layout->permissions(['view-dashboard']); // Entire layout
$section->permissions(['view-reports']);  // Specific section
$component->permissions(['view-users']);  // Specific component

// Resolve before rendering
$layout->resolveAuthorization(auth()->user());
```

### 4. Caching Strategy

```php
// Cache expensive layouts
$layout->cache()
    ->ttl(3600)
    ->key("dashboard:{$userId}")
    ->tags(['dashboards', "user:{$userId}"]);

// Invalidate when needed
Cache::tags(['dashboards', "user:{$userId}"])->flush();
```

### 5. Error Handling

```php
try {
    $layout = Layout::create('dashboard')
        ->section('main', function ($section) {
            $section->card('data')
                ->dataUrl('/api/stats')
                ->dataTransform(function ($data) {
                    if (empty($data)) {
                        throw new \Exception('No data available');
                    }
                    return $data;
                });
        });
    
    return $layout->render();
} catch (\Exception $e) {
    Log::error('Layout render failed', ['error' => $e->getMessage()]);
    return ['error' => 'Failed to load dashboard'];
}
```

### 6. Type Safety

```php
// Use type hints
public function addSection(string $name, \Closure $callback): self
{
    $section = $this->section($name);
    $callback($section);
    return $this;
}

// Validate inputs
public function columns(int $columns): self
{
    if ($columns < 1 || $columns > 12) {
        throw new \InvalidArgumentException('Columns must be between 1 and 12');
    }
    $this->columns = $columns;
    return $this;
}
```

### 7. Testing

```php
use Litepie\Layout\Testing\LayoutAssertions;

class DashboardLayoutTest extends TestCase
{
    use LayoutAssertions;
    
    public function test_dashboard_has_stats_section()
    {
        $layout = $this->createDashboardLayout();
        
        $this->assertLayoutHasSection($layout, 'main');
        $this->assertSectionHasComponent($layout, 'main', 'stats-grid');
        $this->assertComponentHasField($layout, 'stats-card', 'total_users');
    }
}
```

## Conclusion

The Litepie Layout Builder architecture provides:

- **Clear separation** between structure (Sections) and content (Components)
- **Flexible composition** with infinite nesting via section slots
- **Type safety** with strict nesting rules
- **Extensibility** through custom sections and components
- **Testability** with comprehensive assertion helpers
- **Performance** via intelligent caching strategies

By following these architectural principles and best practices, you can build maintainable, scalable, and performant layout systems for your Laravel applications.
