<?php

namespace Litepie\Layout\Components;

/**
 * AvatarComponent
 *
 * Avatar component for displaying user avatars.
 * Supports image avatars, letter/text avatars, icon avatars, variants, and fallbacks.
 */
class AvatarComponent extends BaseComponent
{
    // Avatar source
    protected ?string $src = null;

    protected ?string $srcSet = null;

    protected ?string $alt = null;

    // Children - can be text/letters or icon name
    protected ?string $children = null; // For letter avatars (e.g., "H", "OP", or icon name)

    // Variants
    protected string $variant = 'circular'; // circular, rounded, square

    // Dimensions
    protected ?int $width = null;

    protected ?int $height = null;

    // Styling
    protected ?string $bgColor = null;

    protected ?string $color = null;

    // Image props
    protected ?array $imgProps = null;

    // Sizes
    protected ?string $sizes = null;

    // Fallback configuration
    protected bool $useFallback = true;

    public function __construct(string $name)
    {
        parent::__construct($name, 'avatar');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core Avatar Props
    // ========================================================================

    /**
     * Set image source URL
     */
    public function src(string $src): self
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Set responsive image sources
     */
    public function srcSet(string $srcSet): self
    {
        $this->srcSet = $srcSet;

        return $this;
    }

    /**
     * Set alt text for image
     */
    public function alt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Set children content (letter/text for letter avatars, or icon name)
     */
    public function children(string $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Set variant shape
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set circular variant (default)
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
    // Size Methods
    // ========================================================================

    /**
     * Set avatar width
     */
    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set avatar height
     */
    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Set both width and height
     */
    public function size(int $size): self
    {
        $this->width = $size;
        $this->height = $size;

        return $this;
    }

    /**
     * Set sizes attribute for responsive images
     */
    public function sizes(string $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    // ========================================================================
    // Styling Methods
    // ========================================================================

    /**
     * Set background color
     */
    public function bgColor(string $color): self
    {
        $this->bgColor = $color;

        return $this;
    }

    /**
     * Set text/icon color
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    // ========================================================================
    // Image Props
    // ========================================================================

    /**
     * Set img element props
     */
    public function imgProps(array $props): self
    {
        $this->imgProps = $props;

        return $this;
    }

    // ========================================================================
    // Fallback Control
    // ========================================================================

    /**
     * Enable/disable fallback behavior
     */
    public function useFallback(bool $use = true): self
    {
        $this->useFallback = $use;

        return $this;
    }

    // ========================================================================
    // Helper Methods
    // ========================================================================

    /**
     * Create letter avatar from initials
     */
    public function initials(string $name): self
    {
        $parts = explode(' ', trim($name));
        $initials = '';

        if (count($parts) >= 2) {
            $initials = strtoupper(substr($parts[0], 0, 1).substr($parts[count($parts) - 1], 0, 1));
        } elseif (count($parts) === 1) {
            $initials = strtoupper(substr($parts[0], 0, 2));
        }

        $this->children = $initials;

        return $this;
    }

    /**
     * Create icon avatar
     */
    public function icon(string $icon): self
    {
        $this->children = $icon;

        return $this;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'src' => $this->src,
            'srcSet' => $this->srcSet,
            'alt' => $this->alt,
            'children' => $this->children,
            'variant' => $this->variant !== 'circular' ? $this->variant : null,
            'width' => $this->width,
            'height' => $this->height,
            'sizes' => $this->sizes,
            'bgColor' => $this->bgColor,
            'color' => $this->color,
            'imgProps' => $this->imgProps,
            'useFallback' => !$this->useFallback ? false : null,
        ]));
    }
}