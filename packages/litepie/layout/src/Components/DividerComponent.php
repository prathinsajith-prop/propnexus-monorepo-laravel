<?php

namespace Litepie\Layout\Components;

use Litepie\Layout\Enums\DividerVariant;

/**
 * DividerComponent
 *
 * Based on MUI Divider API
 * @see https://mui.com/material-ui/api/divider/
 */
class DividerComponent extends BaseComponent
{
    /**
     * Absolutely position the element.
     */
    protected bool $absolute = false;

    /**
     * The content of the component (text/label).
     */
    protected ?string $children = null;

    /**
     * If true, a vertical divider will have the correct height when used in flex container.
     */
    protected bool $flexItem = false;

    /**
     * If true, the divider will have a lighter color.
     */
    protected bool $light = false;

    /**
     * The component orientation.
     * @var string 'horizontal'|'vertical'
     */
    protected string $orientation = 'horizontal';

    /**
     * The text alignment.
     * @var string 'center'|'left'|'right'
     */
    protected string $textAlign = 'center';

    /**
     * The variant to use.
     * @var string 'fullWidth'|'inset'|'middle'
     */
    protected string $variant = 'fullWidth';

    /**
     * The spacing/margin around the divider.
     */
    protected string|int|null $spacing = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'divider');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // MUI Divider API Methods
    // ========================================================================

    /**
     * Absolutely position the element.
     */
    public function absolute(bool $absolute = true): self
    {
        $this->absolute = $absolute;

        return $this;
    }

    /**
     * Set the content of the component (children/label).
     */
    public function children(string $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * If true, a vertical divider will have the correct height when used in flex container.
     */
    public function flexItem(bool $flexItem = true): self
    {
        $this->flexItem = $flexItem;

        return $this;
    }

    /**
     * If true, the divider will have a lighter color.
     */
    public function light(bool $light = true): self
    {
        $this->light = $light;

        return $this;
    }

    /**
     * Set the component orientation.
     */
    public function orientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * Set orientation to horizontal.
     */
    public function horizontal(): self
    {
        return $this->orientation('horizontal');
    }

    /**
     * Set orientation to vertical.
     */
    public function vertical(): self
    {
        return $this->orientation('vertical');
    }

    /**
     * Set the text alignment.
     */
    public function textAlign(string $textAlign): self
    {
        $this->textAlign = $textAlign;

        return $this;
    }

    /**
     * Set the variant to use.
     */
    public function variant(string|DividerVariant $variant): self
    {
        $this->variant = $variant instanceof DividerVariant ? $variant->value : $variant;

        return $this;
    }

    /**
     * Set the spacing around the divider.
     */
    public function spacing(string|int $spacing): self
    {
        $this->spacing = $spacing;

        return $this;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'absolute' => $this->absolute,
            'children' => $this->children,
            'flexItem' => $this->flexItem,
            'light' => $this->light,
            'orientation' => $this->orientation,
            'textAlign' => $this->textAlign,
            'variant' => $this->variant,
            'spacing' => $this->spacing,
        ]));
    }
}
