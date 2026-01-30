<?php

namespace Litepie\Layout\Components;

use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Contracts\Renderable;
use Litepie\Layout\Traits\Debuggable;
use Litepie\Layout\Traits\HasVisibility;
use Litepie\Layout\Traits\HasDataSource;
use Litepie\Layout\Traits\HasEvents;
use Litepie\Layout\Traits\Responsive;
use Litepie\Layout\Traits\Translatable;
use Litepie\Layout\Traits\Validatable;

/**
 * BaseComponent
 *
 * Base class for simple content components without section slots.
 * Use this for components that render actual content like forms, cards, tables, lists, alerts, etc.
 *
 * Components are leaf nodes - they cannot contain other sections or components.
 * Examples: FormComponent, CardComponent, TableComponent, ListComponent, AlertComponent
 *
 * For container components that have named section slots, use BaseSection instead.
 */
abstract class BaseComponent implements Component, Renderable
{
    use Debuggable,
        HasVisibility,
        HasDataSource,
        HasEvents,
        Responsive,
        Translatable,
        Validatable;

    protected string $name;

    protected string $type;

    protected ?int $order = null;

    protected bool $visible = true;

    protected array $meta = [];

    // Grid layout properties
    protected ?int $gridColumnSpan = null;

    // Section header properties
    protected ?string $title = null;

    protected ?string $subtitle = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected array $actions = [];

    // Components are leaf nodes and shouldn't contain sections
    // This property exists for backward compatibility
    protected array $sections = [];

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

    /**
     * Set grid column span (how many columns this component should occupy)
     * Used in grid layouts to define component width
     */
    public function gridColumnSpan(int $span): self
    {
        $this->gridColumnSpan = $span;

        return $this;
    }

    /**
     * Alias for gridColumnSpan for brevity
     */
    public function colSpan(int $span): self
    {
        return $this->gridColumnSpan($span);
    }

    /**
     * Get grid column span
     */
    public function getGridColumnSpan(): ?int
    {
        return $this->gridColumnSpan;
    }

    /**
     * End section and return to parent builder
     * Allows chaining: ->slot('body')->chart()->endSection()->slot('footer')
     *
     * If parentBuilder is a Slot, returns its parent (the section/layout)
     * Otherwise returns parentBuilder directly
     */
    public function endSection()
    {
        if ($this->parentBuilder instanceof \Litepie\Layout\Slot) {
            return $this->parentBuilder->getParent();
        }

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

        // Add grid layout properties if set
        if ($this->gridColumnSpan !== null) {
            $properties['gridColumnSpan'] = $this->gridColumnSpan;
            $properties['size'] = $this->gridColumnSpan; // Alias for easier frontend access
        }

        return $this->filterNullValues($properties);
    }

    /**
     * Filter out null values and empty arrays from properties
     */
    protected function filterNullValues(array $properties): array
    {
        return array_filter($properties, function ($value, $key) {
            if ($value === null) {
                return false;
            }
            // Keep meta array even if empty - it's important for configuration
            if ($key === 'meta') {
                return true;
            }
            if (is_array($value) && empty($value)) {
                return false;
            }
            if ($value === false || $value === true) {
                return true; // Keep boolean values
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);
    }

    abstract public function toArray(): array;

    public function render(): array
    {
        return $this->toArray();
    }
}
