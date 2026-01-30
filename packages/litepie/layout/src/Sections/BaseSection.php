<?php

namespace Litepie\Layout\Sections;

use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Contracts\Renderable;
use Litepie\Layout\Traits\Debuggable;
use Litepie\Layout\Traits\HasVisibility;
use Litepie\Layout\Traits\HasDataSource;
use Litepie\Layout\Traits\HasEvents;
use Litepie\Layout\Traits\Responsive;
use Litepie\Layout\Traits\Translatable;
use Litepie\Layout\Traits\Validatable;
use Litepie\Layout\Traits\CreatesSections;
use Litepie\Layout\Traits\CreatesComponents;

/**
 * BaseSection
 *
 * Base class for all Section types in the new architecture:
 * Layout → Section → Section/Component
 *
 * Architecture Rules:
 * - Sections can directly contain other Sections (containers)
 * - Sections can directly contain Components (content)
 * - No intermediate "slots" layer
 * - Uses composition via traits for creating sections and components
 *
 * Examples: HeaderSection, LayoutSection, GridSection, TabsSection, AccordionSection
 *
 * For content leaf nodes, use BaseComponent instead.
 */
abstract class BaseSection implements Component, Renderable
{
    use Debuggable,
        HasVisibility,
        HasDataSource,
        HasEvents,
        Responsive,
        Translatable,
        Validatable,
        CreatesSections,
        CreatesComponents;

    protected string $name;

    protected string $type;

    protected ?int $order = null;

    protected bool $visible = true;

    protected array $meta = [];

    // Section header properties
    protected ?string $title = null;

    protected ?string $subtitle = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected array $actions = [];

    // Reference to parent builder for endSection() support
    public $parentBuilder = null;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function order(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function visible(bool $visible = true): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function hidden(): self
    {
        return $this->visible(false);
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function subtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function addAction(string $label, string $url, array $options = []): self
    {
        $this->actions[] = array_merge([
            'label' => $label,
            'url' => $url,
        ], $options);

        return $this;
    }

    public function actions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    // ========================================================================
    // Universal Add Method (Auto-detects type)
    // ========================================================================

    /**
     * Add a section or component (auto-detects type based on component's type property)
     * 
     * @param Component $item Section or Component instance to add
     * @return self For method chaining
     */
    public function add(Component $item): self
    {
        $type = $item->getType();
        $sectionTypes = ['layout', 'grid', 'row', 'tabs', 'accordion', 'wizard', 'header', 'footer', 'sidebar'];
        
        if (in_array($type, $sectionTypes)) {
            return $this->addSection($item);
        } else {
            return $this->addComponent($item);
        }
    }

    /**
     * End current section and return to parent builder
     */
    public function endSection()
    {
        return $this->parentBuilder;
    }

    /**
     * Helper method to get common properties for toArray()
     */
    protected function getCommonProperties(): array
    {
        $properties = [
            'type' => $this->type,
            'name' => $this->name,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'icon' => $this->icon,
            'data' => $this->getDataConfig(),
            'actions' => $this->actions,
            'order' => $this->order,
            'visible' => $this->visible,
            'meta' => $this->meta,
        ];

        // Add sections and components directly (no slots!)
        if ($this->hasSections() || $this->hasComponents()) {
            $properties = array_merge($properties, $this->serializeContent());
        }

        return $properties;
    }

    /**
     * Helper method to serialize sections and components for toArray()
     * Returns sections and components arrays directly
     */
    protected function serializeContent(): array
    {
        $result = [];

        // Serialize nested sections
        if (!empty($this->sections)) {
            $serializedSections = [];
            foreach ($this->sections as $section) {
                if (method_exists($section, 'toArray')) {
                    $serializedSections[] = $section->toArray();
                } else {
                    $serializedSections[] = (array) $section;
                }
            }
            $result['sections'] = $serializedSections;
        }

        // Serialize components
        if (!empty($this->components)) {
            $serializedComponents = [];
            foreach ($this->components as $component) {
                if (method_exists($component, 'toArray')) {
                    $serializedComponents[] = $component->toArray();
                } else {
                    $serializedComponents[] = (array) $component;
                }
            }
            $result['components'] = $serializedComponents;
        }

        return $result;
    }

    abstract public function toArray(): array;

    public function render(): array
    {
        return $this->toArray();
    }
}
