<?php

namespace Litepie\Layout\Components;

/**
 * BreadcrumbComponent
 *
 * Breadcrumbs component that helps visualize a page's location within a site's hierarchical structure.
 * Allows navigation up to any of the ancestors.
 */
class BreadcrumbComponent extends BaseComponent
{
    // Children - array of breadcrumb items
    protected array $children = [];

    // Separator between breadcrumb items
    protected string|array|null $separator = '/'; // Can be string or icon config

    // Collapsing configuration
    protected ?int $maxItems = null; // Maximum number of items to display

    protected int $itemsBeforeCollapse = 1; // Number of items before collapse

    protected int $itemsAfterCollapse = 1; // Number of items after collapse

    protected string $expandText = '...'; // Text for expand indicator

    public function __construct(string $name)
    {
        parent::__construct($name, 'breadcrumb');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core Props
    // ========================================================================

    /**
     * Add a breadcrumb item (Link or Typography)
     */
    public function addItem(string $label, ?string $href = null, array $options = []): self
    {
        $item = [
            'label' => $label,
            'href' => $href,
        ];

        // Optional properties
        if (isset($options['icon'])) {
            $item['icon'] = $options['icon'];
        }
        if (isset($options['underline'])) {
            $item['underline'] = $options['underline'];
        }
        if (isset($options['color'])) {
            $item['color'] = $options['color'];
        }

        $this->children[] = $item;

        return $this;
    }

    /**
     * Set all breadcrumb items at once
     */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Alias for children() - set all breadcrumb items at once
     */
    public function items(array $items): self
    {
        return $this->children($items);
    }

    /**
     * Set separator between items
     */
    public function separator(string|array $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Set color for all breadcrumb links
     */
    public function color(string $color): self
    {
        $this->meta['color'] = $color;

        return $this;
    }

    // ========================================================================
    // Separator Helpers
    // ========================================================================

    /**
     * Use chevron separator (›)
     */
    public function chevron(): self
    {
        return $this->separator('›');
    }

    /**
     * Use slash separator (/)
     */
    public function slash(): self
    {
        return $this->separator('/');
    }

    /**
     * Use dash separator (-)
     */
    public function dash(): self
    {
        return $this->separator('-');
    }

    /**
     * Use icon separator
     */
    public function iconSeparator(string $icon, array $options = []): self
    {
        $this->separator = array_merge(['icon' => $icon], $options);

        return $this;
    }

    // ========================================================================
    // Collapsing Configuration
    // ========================================================================

    /**
     * Set maximum number of items to display
     */
    public function maxItems(int $max): self
    {
        $this->maxItems = $max;

        return $this;
    }

    /**
     * Set number of items to display before collapse indicator
     */
    public function itemsBeforeCollapse(int $count): self
    {
        $this->itemsBeforeCollapse = $count;

        return $this;
    }

    /**
     * Set number of items to display after collapse indicator
     */
    public function itemsAfterCollapse(int $count): self
    {
        $this->itemsAfterCollapse = $count;

        return $this;
    }

    /**
     * Set text for expand indicator
     */
    public function expandText(string $text): self
    {
        $this->expandText = $text;

        return $this;
    }

    // ========================================================================
    // Convenience Methods
    // ========================================================================

    /**
     * Get all breadcrumb items
     */
    public function getItems(): array
    {
        return $this->children;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'children' => $this->children,
            'separator' => $this->separator !== '/' ? $this->separator : null,
            'maxItems' => $this->maxItems,
            'itemsBeforeCollapse' => $this->itemsBeforeCollapse !== 1 ? $this->itemsBeforeCollapse : null,
            'itemsAfterCollapse' => $this->itemsAfterCollapse !== 1 ? $this->itemsAfterCollapse : null,
            'expandText' => $this->expandText !== '...' ? $this->expandText : null,
        ]));
    }
}
