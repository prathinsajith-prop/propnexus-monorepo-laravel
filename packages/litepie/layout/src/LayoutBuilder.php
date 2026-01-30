<?php

namespace Litepie\Layout;

use Litepie\Layout\Components\CustomComponent;
use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Sections\LayoutSection;
use Litepie\Layout\Traits\Cacheable;
use Litepie\Layout\Traits\Debuggable;
use Litepie\Layout\Traits\Exportable;
use Litepie\Layout\Traits\HandlesComputedFields;
use Litepie\Layout\Traits\Testable;

use Litepie\Layout\Contracts\Renderable;

/**
 * LayoutBuilder
 *
 * Root-level container in the 4-level architecture:
 * Layout → Section → Slot → Component
 *
 * LayoutBuilder can contain Sections (e.g., HeaderSection, GridSection, etc.)
 * Sections contain Slots (named areas like 'left', 'right', 'body', 'items')
 * Slots contain Components or nested Sections
 *
 * SUPPORTED TOP-LEVEL SECTIONS (ONLY):
 * - header: Page header content
 * - footer: Page footer content  
 * - main: Main page content
 * - search: Search functionality
 * - actions: Action buttons/controls
 *
 * Note: addComponent() method name is maintained for backward compatibility
 * but primarily adds Sections. For pure components, wrap them in a Section.
 */
class LayoutBuilder implements Renderable
{
    use Cacheable, Debuggable, Exportable, HandlesComputedFields, Testable;

    /**
     * Supported top-level sections
     * Only these 5 sections are allowed in the layout
     */
    private const SUPPORTED_SECTIONS = ['header', 'footer', 'main', 'search', 'actions'];

    protected string $name;

    protected string $mode;

    protected array $sections = [];

    protected ?string $sharedDataUrl = null; // Single API endpoint for all components

    protected array $sharedDataParams = [];

    protected array $meta = [];

    protected array $beforeRenderCallbacks = [];

    protected array $afterRenderCallbacks = [];

    protected $authUser = null;

    public function __construct(string $name, string $mode)
    {
        $this->name = $name;
        $this->mode = $mode;
    }

    /**
     * Set the layout title
     */
    public function title(string $title): self
    {
        $this->meta['title'] = $title;

        return $this;
    }

    /**
     * Set the layout type
     */
    public function type(string $type): self
    {
        $this->meta['type'] = $type;

        return $this;
    }

    /**
     * Set shared data for all components
     */
    public function setSharedData(array $data): self
    {
        $this->sharedDataParams = array_merge($this->sharedDataParams, $data);

        return $this;
    }

    /**
     * Set layout metadata
     */
    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    /**
     * Alias for cacheTtl() - set cache TTL in seconds
     */
    public function ttl(int $seconds): self
    {
        return $this->cacheTtl($seconds);
    }

    /**
     * Alias for cacheKey() - set custom cache key
     */
    public function key(string $key): self
    {
        return $this->cacheKey($key);
    }

    /**
     * Alias for cacheInvalidateOn() - add cache invalidation tags
     */
    public function tags(string|array $tags): self
    {
        return $this->cacheInvalidateOn($tags);
    }

    /**
     * Register a callback to run before rendering
     */
    public function beforeRender(\Closure $callback): self
    {
        $this->beforeRenderCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback to run after rendering
     */
    public function afterRender(\Closure $callback): self
    {
        $this->afterRenderCallbacks[] = $callback;

        return $this;
    }

    /**
     * Set the user for authorization resolution
     */
    public function resolveAuthorization($user): self
    {
        $this->authUser = $user;

        return $this;
    }

    /**
     * Set a shared data URL for all components to use
     */
    public function sharedDataUrl(string $url): self
    {
        $this->sharedDataUrl = $url;

        return $this;
    }

    /**
     * Set shared data parameters
     */
    public function sharedDataParams(array $params): self
    {
        $this->sharedDataParams = array_merge($this->sharedDataParams, $params);

        return $this;
    }

    public static function create(string $module, string $context): self
    {
        return new static($module, $context);
    }

    /**
     * Add a component to the layout
     */
    public function addComponent(Component $component): self
    {
        if (method_exists($component, 'getName')) {
            $this->sections[$component->getName()] = $component;
        } else {
            $this->sections[] = $component;
        }

        return $this;
    }

    // ========================================================================
    // Supported Layout Sections
    // Only these 5 sections are allowed: header, footer, main, search, actions
    // ========================================================================

    /**
     * Add header section
     */
    public function header(\Closure $callback): self
    {
        return $this->section('header', $callback);
    }

    /**
     * Add footer section
     */
    public function footer(\Closure $callback): self
    {
        return $this->section('footer', $callback);
    }

    /**
     * Add main section
     */
    public function main(\Closure $callback): self
    {
        return $this->section('main', $callback);
    }

    /**
     * Add search section
     */
    public function search(\Closure $callback): self
    {
        return $this->section('search', $callback);
    }

    /**
     * Add actions section
     */
    public function actions(\Closure $callback): self
    {
        return $this->section('actions', $callback);
    }

    /**
     * Create and add a Section (container component like Grid, Row, Column, Tabs, etc.)
     * Sections are containers that have slots which hold other components or sections
     *
     * Usage:
     *   $layout->section('grid', 'main-grid')->columns(3)
     *   $layout->section('header', function($section) { ... })
     *
     * @param  string  $typeOrName  Section type (grid, row, column, tabs, etc.) or section name when using callback
     * @param  string|\Closure|null  $nameOrCallback  Section name or configuration callback
     */
    public function section(string $typeOrName, string|\Closure|null $nameOrCallback = null): self|Component
    {
        // Pattern 1: section('name', function($section) {...}) - LayoutSection with callback
        if ($nameOrCallback instanceof \Closure) {
            $sectionName = $typeOrName;
            $callback = $nameOrCallback;

            // Validate section name
            if (!in_array($sectionName, self::SUPPORTED_SECTIONS)) {
                throw new \InvalidArgumentException(
                    "Section '{$sectionName}' is not supported. Only these sections are allowed: " .
                        implode(', ', self::SUPPORTED_SECTIONS)
                );
            }

            // Create a LayoutSection (container for other components)
            $layoutSection = LayoutSection::make($sectionName);
            $layoutSection->parentBuilder = $this;

            // Execute the callback with the layout section itself (no slots!)
            $callback($layoutSection);

            $this->addComponent($layoutSection);

            return $this;
        }

        // Pattern 2: section('type', 'name') - Create specific section type
        $type = $typeOrName;
        $name = $nameOrCallback;

        // Convert kebab-case to PascalCase (e.g., 'avatar-group' => 'AvatarGroup')
        $className = str_replace('-', '', ucwords($type, '-'));

        // Try Section suffix (containers: Header, Layout, Grid, Tabs, Accordion, Row, Column)
        $sectionClass = 'Litepie\\Layout\\Sections\\' . $className . 'Section';
        if (class_exists($sectionClass)) {
            $section = $sectionClass::make($name);
            $section->parentBuilder = $this;
            $this->addComponent($section);

            return $section;
        }

        // If section type not found, throw exception
        throw new \InvalidArgumentException("Section type '{$type}' not found. Available section types: grid, row, column, tabs, accordion, layout, etc.");
    }

    /**
     * Create and add a Component (content component like Card, Button, Table, Form, etc.)
     * Components are content items that don't have slots - they render actual UI elements
     *
     * Usage:
     *   $layout->component('card', 'user-card')->title('User Profile')
     *   $layout->component('button', 'submit-btn')->label('Submit')
     *
     * @param  string  $type  Component type (card, button, table, form, text, etc.)
     * @param  string  $name  Component name/identifier
     */
    public function component(string $type, string $name): Component
    {
        // Convert kebab-case to PascalCase (e.g., 'avatar-group' => 'AvatarGroup')
        $className = str_replace('-', '', ucwords($type, '-'));

        // Try Component suffix (content: Form, Card, Table, List, Button, Text, etc.)
        $componentClass = 'Litepie\\Layout\\Components\\' . $className . 'Component';
        if (class_exists($componentClass)) {
            $component = $componentClass::make($name);
            $component->parentBuilder = $this;
            $this->addComponent($component);

            return $component;
        }

        // Fallback to CustomComponent for unknown types
        $component = \Litepie\Layout\Components\CustomComponent::make($name, $type);
        $component->parentBuilder = $this;
        $this->addComponent($component);

        return $component;
    }

    /**
     * Legacy support: Create a Section (old structure)
     *
     * @deprecated Use section($type, $name) instead
     */
    public function legacySection(string $name): Section
    {
        $section = new Section($name, $this);
        $this->sections[$name] = $section;

        return $section;
    }

    /**
     * Legacy support: Add a Section
     *
     * @deprecated Use addComponent() instead
     */
    public function addSection(Component $section): self
    {
        $this->sections[$section->getName()] = $section;

        return $this;
    }

    public function getModule(): string
    {
        return $this->name;
    }

    public function getContext(): string
    {
        return $this->mode;
    }

    /**
     * Get all components
     */
    public function getComponents(): array
    {
        return $this->sections;
    }

    /**
     * Legacy support: Get sections
     *
     * @deprecated Use getComponents() instead
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Get component by name
     */
    public function getComponent(string $name): ?Component
    {
        return $this->sections[$name] ?? null;
    }

    /**
     * Legacy support: Get section
     *
     * @deprecated Use getComponent() instead
     */
    public function getSection(string $name): mixed
    {
        return $this->getComponent($name);
    }

    public function build(): Layout
    {
        $layout = new Layout($this->name, $this->mode, $this->sections, $this->sharedDataUrl, $this->sharedDataParams);
        if (! empty($this->meta)) {
            $layout->meta($this->meta);
        }

        return $layout;
    }

    public function render(): array
    {
        return $this->build()->render();
    }

    public function toArray(): array
    {
        return [
            'module' => $this->name,
            'context' => $this->mode,
            'shared_data_url' => $this->sharedDataUrl,
            'shared_data_params' => $this->sharedDataParams,
            'meta' => $this->meta,
            'sections' => array_map(
                fn($section) => method_exists($section, 'toArray') ? $section->toArray() : (array) $section,
                $this->sections
            ),
        ];
    }
}
