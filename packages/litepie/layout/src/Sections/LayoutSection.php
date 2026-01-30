<?php

namespace Litepie\Layout\Sections;

/**
 * LayoutSection
 *
 * A section representing a complete page layout.
 * Common use case: Master layouts, dashboard layouts, application shells
 *
 * No predefined slots - all content added via sections/components.
 * Use meta data or dedicated properties to organize content areas.
 */
class LayoutSection extends BaseSection
{
    protected string $variant = 'default'; // default, sidebar-left, sidebar-right, full-width

    protected bool $hasSidebar = true;

    protected string $sidebarPosition = 'left'; // left, right

    protected ?int $sidebarWidth = null;

    protected bool $stickyHeader = false;

    protected bool $stickyFooter = false;

    protected ?string $containerWidth = null; // sm, md, lg, xl, full

    public function __construct(string $name)
    {
        parent::__construct($name, 'layout');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Set layout variant
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Enable or disable sidebar
     */
    public function sidebar(bool $enabled = true): self
    {
        $this->hasSidebar = $enabled;

        return $this;
    }

    /**
     * Set sidebar to left
     */
    public function sidebarLeft(): self
    {
        $this->sidebarPosition = 'left';
        $this->hasSidebar = true;

        return $this;
    }

    /**
     * Set sidebar to right
     */
    public function sidebarRight(): self
    {
        $this->sidebarPosition = 'right';
        $this->hasSidebar = true;

        return $this;
    }

    /**
     * Set sidebar width
     */
    public function sidebarWidth(int $width): self
    {
        $this->sidebarWidth = $width;

        return $this;
    }

    /**
     * Make header sticky
     */
    public function stickyHeader(bool $sticky = true): self
    {
        $this->stickyHeader = $sticky;

        return $this;
    }

    /**
     * Make footer sticky
     */
    public function stickyFooter(bool $sticky = true): self
    {
        $this->stickyFooter = $sticky;

        return $this;
    }

    /**
     * Set container width
     */
    public function containerWidth(string $width): self
    {
        $this->containerWidth = $width;

        return $this;
    }

    /**
     * Full width layout
     */
    public function fullWidth(): self
    {
        return $this->containerWidth('full');
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), [
            'variant' => $this->variant,
            'has_sidebar' => $this->hasSidebar,
            'sidebar_position' => $this->sidebarPosition,
            'sidebar_width' => $this->sidebarWidth,
            'sticky_header' => $this->stickyHeader,
            'sticky_footer' => $this->stickyFooter,
            'container_width' => $this->containerWidth,
        ]);
    }
}
