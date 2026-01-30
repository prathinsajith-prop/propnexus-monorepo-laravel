<?php

namespace Litepie\Layout\Components;

/**
 * ButtonGroupComponent
 *
 * Groups multiple buttons together with consistent styling.
 * Commonly used in toolbars, forms, and action panels.
 * 
 * API Properties:
 * - orientation: 'horizontal' or 'vertical' layout
 * - variant: Button variant applied to all children ('contained', 'outlined', 'text')
 * - size: Button size applied to all children ('small', 'medium', 'large')
 * - color: Button color applied to all children
 * - disabled: Disable all buttons in the group
 * - fullWidth: Make button group span full width
 * - spacing: Spacing between buttons
 * - children: Array of button configurations
 */
class ButtonGroupComponent extends BaseComponent
{
    // Orientation of button group
    protected string $orientation = 'horizontal'; // horizontal, vertical

    // Variant applied to all child buttons
    protected ?string $variant = null;

    // Size applied to all child buttons
    protected ?string $size = null;

    // Color applied to all child buttons
    protected ?string $color = null;

    // Disabled state for all buttons
    protected bool $disabled = false;

    // Full width mode
    protected bool $fullWidth = false;

    // Spacing between buttons
    protected ?string $spacing = null; // 'none', 'small', 'medium', 'large'

    // Dividers between buttons
    protected bool $dividers = false;

    // Child buttons
    protected array $children = [];

    public function __construct(string $name)
    {
        parent::__construct($name, 'button-group');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core ButtonGroup Props
    // ========================================================================

    /**
     * Set button group orientation
     */
    public function orientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * Set horizontal orientation
     */
    public function horizontal(): self
    {
        return $this->orientation('horizontal');
    }

    /**
     * Set vertical orientation
     */
    public function vertical(): self
    {
        return $this->orientation('vertical');
    }

    /**
     * Set variant for all child buttons
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set contained variant
     */
    public function contained(): self
    {
        return $this->variant('contained');
    }

    /**
     * Set outlined variant
     */
    public function outlined(): self
    {
        return $this->variant('outlined');
    }

    /**
     * Set text variant
     */
    public function text(): self
    {
        return $this->variant('text');
    }

    /**
     * Set size for all child buttons
     */
    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set small size
     */
    public function small(): self
    {
        return $this->size('small');
    }

    /**
     * Set medium size
     */
    public function medium(): self
    {
        return $this->size('medium');
    }

    /**
     * Set large size
     */
    public function large(): self
    {
        return $this->size('large');
    }

    /**
     * Set color for all child buttons
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Set primary color
     */
    public function primary(): self
    {
        return $this->color('primary');
    }

    /**
     * Set secondary color
     */
    public function secondary(): self
    {
        return $this->color('secondary');
    }

    /**
     * Set success color
     */
    public function success(): self
    {
        return $this->color('success');
    }

    /**
     * Set error color
     */
    public function error(): self
    {
        return $this->color('error');
    }

    /**
     * Set warning color
     */
    public function warning(): self
    {
        return $this->color('warning');
    }

    /**
     * Set info color
     */
    public function info(): self
    {
        return $this->color('info');
    }

    /**
     * Disable all buttons in the group
     */
    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Make button group full width
     */
    public function fullWidth(bool $fullWidth = true): self
    {
        $this->fullWidth = $fullWidth;

        return $this;
    }

    /**
     * Set spacing between buttons
     */
    public function spacing(string $spacing): self
    {
        $this->spacing = $spacing;

        return $this;
    }

    /**
     * Enable dividers between buttons
     */
    public function dividers(bool $dividers = true): self
    {
        $this->dividers = $dividers;

        return $this;
    }

    // ========================================================================
    // Children Management
    // ========================================================================

    /**
     * Add a button to the group
     * Can accept ButtonComponent instance or array configuration
     */
    public function addButton($button): self
    {
        if ($button instanceof ButtonComponent) {
            $this->children[] = $button->toArray();
        } elseif (is_array($button)) {
            $this->children[] = $button;
        }

        return $this;
    }

    /**
     * Set all buttons at once
     */
    public function children(array $buttons): self
    {
        $this->children = [];
        foreach ($buttons as $button) {
            $this->addButton($button);
        }

        return $this;
    }

    /**
     * Convenience method to add multiple buttons
     */
    public function buttons(array $buttons): self
    {
        return $this->children($buttons);
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'orientation' => $this->orientation !== 'horizontal' ? $this->orientation : null,
            'variant' => $this->variant,
            'size' => $this->size,
            'color' => $this->color,
            'disabled' => $this->disabled ?: null,
            'fullWidth' => $this->fullWidth ?: null,
            'spacing' => $this->spacing,
            'dividers' => $this->dividers ?: null,
            'children' => !empty($this->children) ? $this->children : null,
        ]));
    }
}
