<?php

namespace Litepie\Layout\Components;

/**
 * BadgeComponent
 *
 * Badge component that generates a small badge to the top-right of its children.
 * Supports numerical badges, dot badges, colors, visibility control, and positioning.
 */
class BadgeComponent extends BaseComponent
{
    // Core badge props
    protected string|int|null $badgeContent = null;

    protected ?array $children = null; // Content to wrap (can be component config)

    protected string $color = 'default'; // default, primary, secondary, error, info, success, warning

    protected string $variant = 'standard'; // standard, dot

    // Positioning
    protected string $vertical = 'top'; // top, bottom

    protected string $horizontal = 'right'; // left, right

    // Overlap
    protected string $overlap = 'rectangular'; // rectangular, circular

    // Visibility control
    protected bool $invisible = false;

    protected bool $showZero = false;

    // Maximum value
    protected ?int $max = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'badge');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core Props
    // ========================================================================

    /**
     * Set badge content (number or text)
     */
    public function badgeContent(string|int $content): self
    {
        $this->badgeContent = $content;

        return $this;
    }

    /**
     * Alias for badgeContent()
     */
    public function content(string|int $content): self
    {
        return $this->badgeContent($content);
    }

    /**
     * Set children (wrapped content)
     */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Set color
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function primary(): self
    {
        return $this->color('primary');
    }

    public function secondary(): self
    {
        return $this->color('secondary');
    }

    public function error(): self
    {
        return $this->color('error');
    }

    public function info(): self
    {
        return $this->color('info');
    }

    public function success(): self
    {
        return $this->color('success');
    }

    public function warning(): self
    {
        return $this->color('warning');
    }

    /**
     * Set variant
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set dot variant (small dot notification)
     */
    public function dot(): self
    {
        return $this->variant('dot');
    }

    // ========================================================================
    // Positioning (anchorOrigin)
    // ========================================================================

    /**
     * Set anchor origin (positioning)
     */
    public function anchorOrigin(string $vertical, string $horizontal): self
    {
        $this->vertical = $vertical;
        $this->horizontal = $horizontal;

        return $this;
    }

    /**
     * Position badge at top-right (default)
     */
    public function topRight(): self
    {
        return $this->anchorOrigin('top', 'right');
    }

    /**
     * Position badge at top-left
     */
    public function topLeft(): self
    {
        return $this->anchorOrigin('top', 'left');
    }

    /**
     * Position badge at bottom-right
     */
    public function bottomRight(): self
    {
        return $this->anchorOrigin('bottom', 'right');
    }

    /**
     * Position badge at bottom-left
     */
    public function bottomLeft(): self
    {
        return $this->anchorOrigin('bottom', 'left');
    }

    // ========================================================================
    // Overlap
    // ========================================================================

    /**
     * Set overlap mode (affects positioning relative to wrapped element)
     */
    public function overlap(string $overlap): self
    {
        $this->overlap = $overlap;

        return $this;
    }

    public function rectangular(): self
    {
        return $this->overlap('rectangular');
    }

    public function circular(): self
    {
        return $this->overlap('circular');
    }

    // ========================================================================
    // Visibility Control
    // ========================================================================

    /**
     * Set badge visibility
     */
    public function invisible(bool $invisible = true): self
    {
        $this->invisible = $invisible;

        return $this;
    }

    /**
     * Show badge even when badgeContent is 0
     */
    public function showZero(bool $showZero = true): self
    {
        $this->showZero = $showZero;

        return $this;
    }

    // ========================================================================
    // Maximum Value
    // ========================================================================

    /**
     * Set maximum value to display (shows "max+" when exceeded)
     */
    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'badgeContent' => $this->badgeContent,
            'children' => $this->children,
            'color' => $this->color !== 'default' ? $this->color : null,
            'variant' => $this->variant !== 'standard' ? $this->variant : null,
            'anchorOrigin' => [
                'vertical' => $this->vertical,
                'horizontal' => $this->horizontal,
            ],
            'overlap' => $this->overlap !== 'rectangular' ? $this->overlap : null,
            'invisible' => $this->invisible ? true : null,
            'showZero' => $this->showZero ? true : null,
            'max' => $this->max,
        ]));
    }
}
