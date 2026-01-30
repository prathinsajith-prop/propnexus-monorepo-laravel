<?php

namespace Litepie\Layout\Components;

/**
 * ListComponent
 *
 * Lists are continuous, vertical indexes of text or images.
 * Composed of items containing primary and supplemental actions, represented by icons and text.
 */
class ListComponent extends BaseComponent
{
    // List props
    protected bool $dense = false;

    protected bool $disablePadding = false;

    protected ?string $subheader = null;

    protected ?array $subheaderProps = null;

    protected ?string $variant = null;

    // Children - array of list items
    protected array $children = [];

    public function __construct(string $name)
    {
        parent::__construct($name, 'list');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core List Props
    // ========================================================================

    /**
     * Enable dense spacing
     */
    public function dense(bool $dense = true): self
    {
        $this->dense = $dense;

        return $this;
    }

    /**
     * Disable padding
     */
    public function disablePadding(bool $disable = true): self
    {
        $this->disablePadding = $disable;

        return $this;
    }

    /**
     * Set subheader text
     */
    public function subheader(string $text, array $props = []): self
    {
        $this->subheader = $text;
        $this->subheaderProps = $props;

        return $this;
    }

    /**
     * Set list variant/style
     * 
     * @param string $variant Variant type: 'ordered', 'unordered', 'none'
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set children items
     */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Add multiple simple text items from an array of strings or arrays
     * 
     * @param array $items Array of strings or item configurations
     * @return self
     */
    public function items(array $items): self
    {
        if (!is_array($items)) {
            return $this;
        }

        foreach ($items as $item) {
            if (is_string($item)) {
                $this->addTextItem($item);
            } elseif (is_array($item)) {
                $this->addItem($item);
            }
        }

        return $this;
    }

    // ========================================================================
    // List Item Builder
    // ========================================================================

    /**
     * Add a list item
     * 
     * @param array $config Item configuration with keys:
     *   - primary: Primary text (string)
     *   - secondary: Secondary text (string)
     *   - button: Make item clickable (bool)
     *   - href: Link URL (string)
     *   - selected: Selected state (bool)
     *   - disabled: Disabled state (bool)
     *   - divider: Show divider after item (bool)
     *   - disableGutters: Disable gutters (bool)
     *   - disablePadding: Disable padding (bool)
     *   - alignItems: Alignment (flex-start, center)
     *   - inset: Inset item (bool)
     *   - icon: Leading icon config (string or array)
     *   - avatar: Leading avatar config (array with src, alt, etc)
     *   - secondaryAction: Secondary action config (button/icon button)
     *   - nested: Nested list items (array)
     */
    public function addItem(array $config): self
    {
        $item = [];

        // Text content
        if (isset($config['primary'])) {
            $item['primary'] = $config['primary'];
        }
        if (isset($config['secondary'])) {
            $item['secondary'] = $config['secondary'];
        }

        // Button/interaction props
        if (isset($config['button'])) {
            $item['button'] = $config['button'];
        }
        if (isset($config['href'])) {
            $item['href'] = $config['href'];
        }
        if (isset($config['selected'])) {
            $item['selected'] = $config['selected'];
        }
        if (isset($config['disabled'])) {
            $item['disabled'] = $config['disabled'];
        }

        // Layout props
        if (isset($config['divider'])) {
            $item['divider'] = $config['divider'];
        }
        if (isset($config['disableGutters'])) {
            $item['disableGutters'] = $config['disableGutters'];
        }
        if (isset($config['disablePadding'])) {
            $item['disablePadding'] = $config['disablePadding'];
        }
        if (isset($config['alignItems'])) {
            $item['alignItems'] = $config['alignItems'];
        }
        if (isset($config['inset'])) {
            $item['inset'] = $config['inset'];
        }

        // Content elements
        if (isset($config['icon'])) {
            $item['icon'] = $config['icon'];
        }
        if (isset($config['avatar'])) {
            $item['avatar'] = $config['avatar'];
        }
        if (isset($config['secondaryAction'])) {
            $item['secondaryAction'] = $config['secondaryAction'];
        }

        // Nested items
        if (isset($config['nested'])) {
            $item['nested'] = $config['nested'];
        }

        $this->children[] = $item;

        return $this;
    }

    /**
     * Add a simple text item
     */
    public function addTextItem(string $primary, ?string $secondary = null): self
    {
        return $this->addItem([
            'primary' => $primary,
            'secondary' => $secondary,
        ]);
    }

    /**
     * Add a button item (clickable)
     */
    public function addButtonItem(string $primary, string $href, array $options = []): self
    {
        return $this->addItem(array_merge([
            'primary' => $primary,
            'button' => true,
            'href' => $href,
        ], $options));
    }

    /**
     * Add an item with icon
     */
    public function addIconItem(string $icon, string $primary, ?string $secondary = null, array $options = []): self
    {
        return $this->addItem(array_merge([
            'icon' => $icon,
            'primary' => $primary,
            'secondary' => $secondary,
        ], $options));
    }

    /**
     * Add an item with avatar
     */
    public function addAvatarItem(array $avatar, string $primary, ?string $secondary = null, array $options = []): self
    {
        return $this->addItem(array_merge([
            'avatar' => $avatar,
            'primary' => $primary,
            'secondary' => $secondary,
        ], $options));
    }

    /**
     * Add a divider
     */
    public function addDivider(array $props = []): self
    {
        $this->children[] = [
            'type' => 'divider',
            'props' => $props,
        ];

        return $this;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'dense' => $this->dense ? true : null,
            'disablePadding' => $this->disablePadding ? true : null,
            'subheader' => $this->subheader,
            'subheaderProps' => $this->subheaderProps,
            'variant' => $this->variant,
            'children' => $this->children,
        ]));
    }
}
