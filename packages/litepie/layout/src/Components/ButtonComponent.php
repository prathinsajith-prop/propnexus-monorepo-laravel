<?php

namespace Litepie\Layout\Components;

class ButtonComponent extends BaseComponent
{
    protected ?string $label = null;

    protected string $variant = 'contained'; // contained, outlined, text, elevated, tonal

    protected string $color = 'primary'; // primary, secondary, success, error, warning, info

    protected string $size = 'medium'; // small, medium, large

    protected string $type = 'button'; // button, submit, reset

    protected ?string $href = null; // Makes button act as a link

    protected ?string $target = null; // _blank, _self, _parent, _top

    protected bool $disabled = false;

    protected bool $fullWidth = false;

    protected bool $loading = false;

    protected ?string $icon = null; // Icon name/class

    protected ?string $iconPosition = 'left'; // left, right

    protected ?string $prefixIcon = null;

    protected ?string $suffixIcon = null;

    protected ?string $tooltip = null;

    protected ?string $ariaLabel = null;

    protected ?string $className = null;

    protected ?string $onClick = null; // JavaScript click handler

    protected array $dataAttributes = []; // data-* attributes

    protected ?string $action = null; // Action handler (view, edit, delete, etc.)

    protected bool $isIconButton = false; // Flag to identify icon-only buttons


    // Dropdown properties
    protected bool $hasDropdown = false;

    protected ?array $dropdownConfig = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'button');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Set button label/text
     */
    public function label(?string $label = null): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set button variant
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set button color
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Set button size
     */
    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set button type
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Make button act as a link
     */
    public function href(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Alias for href() - more semantic for buttons
     */
    public function link(string $href): self
    {
        return $this->href($href);
    }

    /**
     * Set link target
     */
    public function target(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Open link in new tab
     */
    public function newTab(): self
    {
        $this->target = '_blank';

        return $this;
    }

    /**
     * Disable the button
     */
    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Make button full width
     */
    public function fullWidth(bool $fullWidth = true): self
    {
        $this->fullWidth = $fullWidth;

        return $this;
    }

    /**
     * Show loading state
     */
    public function loading(bool $loading = true): self
    {
        $this->loading = $loading;

        return $this;
    }

    /**
     * Set icon
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set icon position
     */
    public function iconPosition(string $position): self
    {
        $this->iconPosition = $position;

        return $this;
    }

    /**
     * Set prefix icon
     */
    public function prefixIcon(string $icon): self
    {
        $this->prefixIcon = $icon;

        return $this;
    }

    /**
     * Set suffix icon
     */
    public function suffixIcon(string $icon): self
    {
        $this->suffixIcon = $icon;

        return $this;
    }

    /**
     * Set tooltip
     */
    public function tooltip(string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * Set ARIA label
     */
    public function ariaLabel(string $label): self
    {
        $this->ariaLabel = $label;

        return $this;
    }

    /**
     * Set CSS class name
     */
    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Set onClick handler
     */
    public function onClick(string $handler): self
    {
        $this->onClick = $handler;

        return $this;
    }

    /**
     * Set button action (view, edit, delete, etc.)
     */
    public function action(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Mark button as icon-only button
     */
    public function isIconButton(bool $isIconButton = false): self
    {
        $this->isIconButton = $isIconButton;

        return $this;
    }

    /**
     * Add data attribute
     */
    public function data(string $key, $value): self
    {
        $this->dataAttributes[$key] = $value;

        return $this;
    }

    /**
     * Add custom HTML attribute (generic method for any attribute)
     * This is an alias for data() method for consistency with other components
     */
    public function attribute(string $key, $value): self
    {
        // If it's a data attribute, use the dataAttributes array
        if (strpos($key, 'data-') === 0) {
            $dataKey = substr($key, 5); // Remove 'data-' prefix
            $this->dataAttributes[$dataKey] = $value;
        } else {
            // For other attributes, store them in dataAttributes with the key as-is
            // Frontend can handle mapping these to appropriate HTML attributes
            $this->dataAttributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Variant shortcuts
     */
    public function contained(): self
    {
        return $this->variant('contained');
    }

    public function outlined(): self
    {
        return $this->variant('outlined');
    }

    public function text(): self
    {
        return $this->variant('text');
    }

    public function elevated(): self
    {
        return $this->variant('elevated');
    }

    public function tonal(): self
    {
        return $this->variant('tonal');
    }

    /**
     * Color shortcuts
     */
    public function primary(): self
    {
        return $this->color('primary');
    }

    public function secondary(): self
    {
        return $this->color('secondary');
    }

    public function success(): self
    {
        return $this->color('success');
    }

    public function error(): self
    {
        return $this->color('error');
    }

    public function warning(): self
    {
        return $this->color('warning');
    }

    public function info(): self
    {
        return $this->color('info');
    }

    /**
     * Size shortcuts
     */
    public function small(): self
    {
        return $this->size('small');
    }

    public function medium(): self
    {
        return $this->size('medium');
    }

    public function large(): self
    {
        return $this->size('large');
    }

    /**
     * Type shortcuts
     */
    public function submit(): self
    {
        return $this->type('submit');
    }

    public function reset(): self
    {
        return $this->type('reset');
    }

    /**
     * Set dropdown configuration
     */
    public function dropdown(array $config): self
    {
        $this->hasDropdown = true;
        $this->dropdownConfig = $config;

        // Also set data attributes for frontend detection
        if (isset($config['id'])) {
            $this->data('dropdown', $config['id']);
        }
        if (isset($config['placement'])) {
            $this->data('dropdown-placement', $config['placement']);
        }
        if (isset($config['closeOnClick'])) {
            $this->data('dropdown-close-on-click', $config['closeOnClick'] ? 'true' : 'false');
        }

        return $this;
    }

    /**
     * Check if button has dropdown
     */
    public function hasDropdown(): bool
    {
        return $this->hasDropdown;
    }

    /**
     * Get dropdown configuration
     */
    public function getDropdownConfig(): ?array
    {
        return $this->dropdownConfig;
    }

    /**
     * Set button group (meta property for frontend rendering hint)
     */
    public function group(string $group): self
    {
        return $this->meta(['group' => $group]);
    }

    /**
     * Set button group position (meta property for frontend rendering hint)
     */
    public function groupPosition(string $position): self
    {
        return $this->meta(['groupPosition' => $position]);
    }

    /**
     * Convert to array for JSON output
     */
    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'label' => $this->label,
            'variant' => $this->variant,
            'color' => $this->color,
            'size' => $this->size,
            'type' => $this->type,
            'href' => $this->href,
            'target' => $this->target,
            'disabled' => $this->disabled,
            'fullWidth' => $this->fullWidth,
            'loading' => $this->loading,
            'icon' => $this->icon,
            'iconPosition' => $this->iconPosition,
            'prefixIcon' => $this->prefixIcon,
            'suffixIcon' => $this->suffixIcon,
            'tooltip' => $this->tooltip,
            'ariaLabel' => $this->ariaLabel,
            'className' => $this->className,
            'onClick' => $this->onClick,
            'action' => $this->action,
            'isIconButton' => $this->isIconButton,
            'dataAttributes' => $this->dataAttributes,
            'hasDropdown' => $this->hasDropdown,
            'dropdownConfig' => $this->dropdownConfig,
        ]));
    }
}
