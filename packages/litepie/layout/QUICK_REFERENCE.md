# Quick Reference: 4-Level Architecture

## Hierarchy at a Glance

```
┌─────────────────────────────────────────┐
│ Layout (Root)                           │
│ LayoutBuilder::create()                 │
│                                         │
│  ┌───────────────────────────────────┐ │
│  │ Section (Container)               │ │
│  │ HeaderSection, GridSection, etc.  │ │
│  │                                   │ │
│  │  ┌─────────────────────────────┐ │ │
│  │  │ Slot (Named Area)           │ │ │
│  │  │ ->slot('left'), ->slot('items')│ │
│  │  │                             │ │ │
│  │  │  ┌───────────────────────┐ │ │ │
│  │  │  │ Component (Content)   │ │ │ │
│  │  │  │ CardComponent, etc.   │ │ │ │
│  │  │  └───────────────────────┘ │ │ │
│  │  └─────────────────────────────┘ │ │
│  └───────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

## Cheat Sheet

### Creating a Layout

```php
$layout = LayoutBuilder::create('name', 'context')
    ->title('Page Title')
    ->addComponent($section);
```

### Section Types & Their Slots

| Section | Allowed Slots | Example |
|---------|---------------|---------|
| **HeaderSection** | `left`, `center`, `right` | Navigation bar |
| **LayoutSection** | `header`, `sidebar`, `body`, `footer`, `aside` | Master layout |
| **GridSection** | `items` (single slot) | Card grid |
| **TabsSection** | Dynamic (tab names) | Tabbed interface |
| **AccordionSection** | Dynamic (panel names) | FAQ accordion |
| **WizardSection** | Dynamic (step names) | Multi-step form |
| **ScrollSpySection** | Dynamic (section names) | Documentation |

### Common Patterns

#### 1. Header with Logo and Menu

```php
$header = HeaderSection::make('header');
$header->slot('left')->add(LogoComponent::make('logo'));
$header->slot('right')->add(MenuComponent::make('menu'));
$layout->addComponent($header);
```

#### 2. Stats Grid

```php
$grid = GridSection::make('stats')->columns(4);
$grid->slot('items')
    ->add(StatsComponent::make('users')->value(1000))
    ->add(StatsComponent::make('revenue')->value(50000))
    ->add(StatsComponent::make('orders')->value(250))
    ->add(StatsComponent::make('growth')->value('+15%'));
$layout->addComponent($grid);
```

#### 3. Tabbed Content

```php
$tabs = TabsSection::make('content')
    ->addTab('overview', 'Overview')
    ->addTab('details', 'Details')
    ->activeTab('overview');

$tabs->slot('overview')->add(CardComponent::make('summary'));
$tabs->slot('details')->add(TableComponent::make('data'));
$layout->addComponent($tabs);
```

#### 4. Multi-Step Wizard

```php
$wizard = WizardSection::make('setup')
    ->addStep('account', 'Account', ['icon' => 'user'])
    ->addStep('profile', 'Profile', ['icon' => 'user-circle'])
    ->addStep('done', 'Done', ['icon' => 'check']);

$wizard->slot('account')->add(FormComponent::make('signup'));
$wizard->slot('profile')->add(FormComponent::make('profile'));
$wizard->slot('done')->add(TextComponent::make('success'));
$layout->addComponent($wizard);
```

#### 5. Nested Sections

```php
$mainGrid = GridSection::make('main')->columns(2);

// Add tabs within grid
$tabs = TabsSection::make('tabs')
    ->addTab('tab1', 'Tab 1')
    ->addTab('tab2', 'Tab 2');
$tabs->slot('tab1')->add(CardComponent::make('card'));

// Add tabs and chart to grid
$mainGrid->slot('items')
    ->add($tabs)
    ->add(ChartComponent::make('chart'));

$layout->addComponent($mainGrid);
```

### Method Reference

#### Layout/LayoutBuilder

```php
->title(string $title)
->setSharedData(array $data)
->addComponent(Component $section)
->meta(array $meta)
```

#### BaseSection (all sections)

```php
->slot(string $name): Slot
->title(string $title)
->subtitle(string $subtitle)
->description(string $description)
->icon(string $icon)
->addAction(string $label, string $url)
->order(int $order)
->visible(bool $visible)
```

#### GridSection

```php
GridSection::make(string $name)
->columns(int $columns)
->gap(string $gap)
->slot('items')  // Only slot available
```

#### TabsSection

```php
TabsSection::make(string $name)
->addTab(string $id, string $label, array $options)
->activeTab(string $id)
->position(string $position)  // top, left, right, bottom
->lazy(bool $lazy)
->slot($tabId)  // Access tab content
```

#### AccordionSection

```php
AccordionSection::make(string $name)
->addPanel(string $id, string $label, array $options)
->expanded(string $panelId)
->multiple(bool $allow)
->collapsible(bool $collapsible)
->slot($panelId)  // Access panel content
```

#### WizardSection

```php
WizardSection::make(string $name)
->addStep(string $key, string $label, array $options)
->currentStep(int|string $step)
->linear(bool $linear)
->showStepNumbers(bool $show)
->orientation(string $orientation)  // horizontal, vertical
->slot($stepKey)  // Access step content
```

#### Slot

```php
->add(Component $component)
->addMany(array $components)
->meta(array $meta)
->end()  // Return to parent section
->endSlot()  // Return to parent's parent

// Fluent component creation
->card(string $name)
->form(string $name)
->table(string $name)
->chart(string $name)
// ... etc for all component types
```

### Component Types

```php
// Content Components
AlertComponent::make($name)
AvatarComponent::make($name)
BadgeComponent::make($name)
BreadcrumbComponent::make($name)
CardComponent::make($name)
ChartComponent::make($name)
CommentComponent::make($name)
DividerComponent::make($name)
DocumentComponent::make($name)
FormComponent::make($name)
ListComponent::make($name)
MediaComponent::make($name)
ModalComponent::make($name)
StatsComponent::make($name)
TableComponent::make($name)
TextComponent::make($name)
TimelineComponent::make($name)
CustomComponent::make($name, $type)
```

## Migration Quick Tips

### Old → New

```php
// Old: Direct component addition to grid
$grid->addComponent($card)

// New: Add to slot
$grid->slot('items')->add($card)

// Old: Tab callback
$tabs->addTab('id', 'Label', function($tab) {
    $tab->add($component);
})

// New: Define tab, then add to slot
$tabs->addTab('id', 'Label')
$tabs->slot('id')->add($component)

// Old: Accordion callback
$accordion->addPanel('id', 'Label', function($panel) {
    $panel->add($component);
})

// New: Define panel, then add to slot
$accordion->addPanel('id', 'Label')
$accordion->slot('id')->add($component)
```

## Rules to Remember

1. **Layout contains Sections** (only)
2. **Section contains Slots** (only)
3. **Slot contains Components or Sections**
4. **Component contains nothing** (leaf node)

## Validation

Sections with `allowedSlots` will throw `InvalidArgumentException` if you use invalid slot names:

```php
$header->slot('main')  // ❌ Error! HeaderSection only allows: left, center, right
$header->slot('left')  // ✅ Valid
```

## Tips

1. **Use descriptive names**: `->slot('sidebar')` not `->slot('slot1')`
2. **Keep it simple**: Don't over-nest sections
3. **Leverage validation**: Let `allowedSlots` catch mistakes
4. **Check examples**: See `examples/` folder for patterns
5. **Read NEW_ARCHITECTURE.md**: For comprehensive guide

## Get Help

- See [NEW_ARCHITECTURE.md](NEW_ARCHITECTURE.md) for detailed guide
- See [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) for changes
- Check `examples/` folder for working code
- Review section class files for specific options
