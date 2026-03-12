# Layouts Architecture

This directory contains all layout-related components following a clean, modular architecture.

## 📁 Directory Structure

```
app/Layouts/
├── Builder/                      # Layout builders (business logic)
│   ├── Blog/
│   │   └── LayoutBuilder.php    # Blog layout construction logic
│   ├── Listing/
│   │   └── LayoutBuilder.php    # Listing layout construction logic
│   └── TableColumnsBuilder.php  # Shared table column configurations
│
├── Slot/                        # UI component slots (reusable components)
│   ├── Blog/                    # Blog-specific slots
│   │   ├── CreateAsideSlot.php
│   │   ├── EditAsideSlot.php
│   │   ├── ViewAsideSlot.php
│   │   ├── DetailSlot.php
│   │   ├── ModalSlot.php
│   │   ├── HeaderSlot.php
│   │   ├── FooterSlot.php
│   │   ├── MainContentSlot.php
│   │   ├── LeftSidebarSlot.php
│   │   ├── RightSidebarSlot.php
│   │   └── FormActivityAsideSlot.php
│   │
│   ├── Listing/                 # Listing-specific slots
│   │   ├── CreateAsideSlot.php
│   │   ├── EditAsideSlot.php
│   │   ├── ViewAsideSlot.php
│   │   ├── DetailSlot.php
│   │   ├── ModalSlot.php
│   │   ├── HeaderSlot.php
│   │   ├── FooterSlot.php
│   │   └── MainContentSlot.php
│   │
│   └── Shared/                  # Shared generic slots
│       └── ModalSlot.php        # Generic modal configurations
│
├── BlogLayout.php               # Main Blog layout entry point
├── ListingLayout.php            # Main Listing layout entry point
└── UserLayout.php               # User management layout
```

## 🏗️ Architecture Principles

### 1. Separation of Concerns
- **Layouts**: Entry points that wire everything together
- **Builders**: Business logic for constructing sections
- **Slots**: Reusable UI components

### 2. Naming Conventions
- **No redundant prefixes**: Classes use simple names (e.g., `CreateAsideSlot` not `BlogCreateAsideSlot`)
- **Namespace context**: Module context is provided by namespace (e.g., `App\Layouts\Slot\Blog\CreateAsideSlot`)
- **Consistent patterns**: All modules follow identical structure

### 3. Module Organization
Each module (Blog, Listing) has:
- **Layout Builder**: Orchestrates section construction
- **Slot Classes**: Individual component definitions
- **Main Layout**: Entry point with configuration

## 📝 Component Types

### Builders
Located in `Builder/` - Handle layout construction logic:
- **LayoutBuilder**: Main layout orchestration
- **TableColumnsBuilder**: Shared table column definitions

**Namespace**: `App\Layouts\Builder\{Module}`

### Slots
Located in `Slot/` - Define reusable UI components:

#### Aside Slots (Drawer Panels)
- **CreateAsideSlot**: Create new item form
- **EditAsideSlot**: Edit existing item form
- **ViewAsideSlot**: View item details (read-only)

#### Content Slots
- **DetailSlot**: Detail view sections
- **MainContentSlot**: Primary content area
- **HeaderSlot**: Page header with navigation
- **FooterSlot**: Page footer

#### Interactive Slots
- **ModalSlot**: Modal dialog configurations
- **FormActivityAsideSlot**: Activity timeline (Blog only)
- **LeftSidebarSlot**: Left sidebar (Blog only)
- **RightSidebarSlot**: Right sidebar (Blog only)

**Namespace**: `App\Layouts\Slot\{Module}`

## 🔄 Data Flow

```
Controller
    ↓
Main Layout (BlogLayout.php)
    ↓
LayoutBuilder (orchestration)
    ↓
Slot Classes (components)
    ↓
Form Components / UI Elements
```

## 📖 Usage Examples

### Creating a New Module

1. **Create Directory Structure**:
   ```
   app/Layouts/Builder/YourModule/
   app/Layouts/Slot/YourModule/
   ```

2. **Create Main Layout**:
   ```php
   namespace App\Layouts;
   
   use App\Layouts\Builder\YourModule\LayoutBuilder;
   use Litepie\Layout\LayoutBuilder as LitepieLayoutBuilder;
   
   class YourModuleLayout
   {
       public static function make($masterData)
       {
           return LitepieLayoutBuilder::create('your-module', 'page')
               ->title('Your Module')
               ->section('main', fn($s) => LayoutBuilder::buildMainSection($s, $masterData))
               ->build();
       }
   }
   ```

3. **Create LayoutBuilder**:
   ```php
   namespace App\Layouts\Builder\YourModule;
   
   use App\Layouts\Slot\YourModule\CreateAsideSlot;
   
   class LayoutBuilder
   {
       public static function buildMainSection($section, $masterData)
       {
           // Build your section logic
       }
   }
   ```

4. **Create Slot Classes**:
   ```php
   namespace App\Layouts\Slot\YourModule;
   
   class CreateAsideSlot
   {
       public static function make(array $masterData = [], bool $fullscreen = false): array
       {
           // Build your component
       }
   }
   ```

## ✅ Best Practices

1. **Keep Slots Focused**: Each slot should have a single responsibility
2. **Use Array Parameters**: Make methods flexible with `array $options = []`
3. **Consistent Naming**: Follow existing patterns (CreateAsideSlot, EditAsideSlot, etc.)
4. **Document Public Methods**: Add PHPDoc comments for clarity
5. **Separate Helper Methods**: Use private methods for complex logic
6. **Follow PSR Standards**: Maintain consistent code style

## 🔍 Key Files Reference

| File | Purpose | Namespace |
|------|---------|-----------|
| `BlogLayout.php` | Blog layout entry | `App\Layouts` |
| `ListingLayout.php` | Listing layout entry | `App\Layouts` |
| `Builder/Blog/LayoutBuilder.php` | Blog builder | `App\Layouts\Builder\Blog` |
| `Builder/Listing/LayoutBuilder.php` | Listing builder | `App\Layouts\Builder\Listing` |
| `Builder/TableColumnsBuilder.php` | Shared columns | `App\Layouts\Builder` |
| `Slot/Blog/*` | Blog components | `App\Layouts\Slot\Blog` |
| `Slot/Listing/*` | Listing components | `App\Layouts\Slot\Listing` |
| `Slot/Shared/*` | Generic components | `App\Layouts\Slot\Shared` |

## 🚀 Performance Tips

1. **Lazy Loading**: Components are built on-demand
2. **Caching**: Use Laravel's cache for expensive operations
3. **Optimize Queries**: Load only necessary master data
4. **Reuse Components**: Leverage Shared slots where possible

## 🔧 Maintenance

When modifying layouts:

1. **Run Tests**: Ensure changes don't break existing functionality
2. **Clear Cache**: Run `php artisan optimize:clear`
3. **Regenerate Autoload**: Run `composer dump-autoload`
4. **Check Namespaces**: Verify all imports are correct
5. **Update Documentation**: Keep this README current

## 📚 Related Documentation

- [Litepie Layout Package](https://github.com/litepie/layout)
- [Litepie Form Package](https://github.com/litepie/form)
- [Component API Reference](../COMPONENT_API_REFERENCE.md)
