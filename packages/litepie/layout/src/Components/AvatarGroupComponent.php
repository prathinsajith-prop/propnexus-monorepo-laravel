<?php

namespace Litepie\Layout\Components;

/**
 * AvatarGroupComponent
 *
 * Displays a collection of avatars stacked together.
 * 
 * API Properties:
 * - max: Maximum number of avatars to display before showing "+x"
 * - spacing: Spacing between avatars ('small', 'medium', or number in px)
 * - total: Total number of avatars (for showing "+x" count)
 * - variant: Shape variant inherited by children ('circular', 'rounded', 'square')
 * - children: Array of avatar configurations
 */
class AvatarGroupComponent extends BaseComponent
{
    // Maximum avatars to show (MUI: max)
    protected ?int $max = null;

    // Spacing between avatars (MUI: spacing - 'small', 'medium', or number)
    protected string|int|null $spacing = 'medium';

    // Total count for overflow calculation (MUI: total)
    protected ?int $total = null;

    // Variant applied to all child avatars (MUI: variant)
    protected ?string $variant = null;

    // Child avatars
    protected array $children = [];

    public function __construct(string $name)
    {
        parent::__construct($name, 'avatar-group');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core AvatarGroup Props (MUI Compatible)
    // ========================================================================

    /**
     * Set maximum number of avatars to display
     */
    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Set spacing between avatars
     */
    public function spacing(string|int $spacing): self
    {
        $this->spacing = $spacing;

        return $this;
    }

    /**
     * Set total count of avatars (used for overflow display)
     */
    public function total(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Set variant shape for all child avatars
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set circular variant
     */
    public function circular(): self
    {
        return $this->variant('circular');
    }

    /**
     * Set rounded variant
     */
    public function rounded(): self
    {
        return $this->variant('rounded');
    }

    /**
     * Set square variant
     */
    public function square(): self
    {
        return $this->variant('square');
    }

    // ========================================================================
    // Children Management
    // ========================================================================

    /**
     * Add an avatar to the group
     */
    public function addAvatar(array $avatar): self
    {
        $this->children[] = $avatar;

        return $this;
    }

    /**
     * Set all avatars at once
     */
    public function children(array $avatars): self
    {
        $this->children = $avatars;

        return $this;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'max' => $this->max,
            'spacing' => $this->spacing !== 'medium' ? $this->spacing : null,
            'total' => $this->total,
            'variant' => $this->variant,
            'children' => !empty($this->children) ? $this->children : null,
        ]));
    }
}
