<?php

namespace Litepie\Layout\Sections;

use Litepie\Layout\Contracts\Component;

/**
 * AsideSection
 *
 * Aside section provides side panels that slide in from edges of the screen.
 * 
 * SUPPORTED SECTIONS (ONLY):
 * - header: Aside header content
 * - footer: Aside footer content
 * - main: Main aside content (center)
 * - left: Left sidebar content
 * - right: Right sidebar content
 * 
 * Supports multiple anchor positions (left, right, top, bottom) and variants (temporary, persistent, mini, permanent).
 */
class AsideSection extends BaseSection
{
    // Supported sections - NO OTHER KEYS ALLOWED
    private const SUPPORTED_SECTIONS = ['header', 'footer', 'main', 'left', 'right'];

    // Core properties
    protected string $anchor = 'left'; // left, right, top, bottom
    protected string $variant = 'temporary'; // temporary, persistent, mini, permanent
    protected bool $open = false;
    protected ?string $width = null;
    protected ?string $height = null;
    protected ?string $miniWidth = null;

    // Backdrop properties
    protected bool $backdrop = true;
    protected ?string $backdropColor = null;
    protected bool $closeOnBackdrop = true;
    protected bool $closeOnEscape = true;

    // UI elements
    protected bool $closeButton = true;
    protected bool $defaultOpen = false;

    // Mini variant properties
    protected bool $expandOnHover = false;

    // Content sections - ONLY these 5 sections allowed
    protected array $asideSections = [
        'header' => [],
        'footer' => [],
        'main' => [],
        'left' => [],
        'right' => [],
    ];

    // Section configurations (layout, size, properties)
    protected array $sectionConfig = [
        'header' => [],
        'footer' => [],
        'main' => [],
        'left' => [],
        'right' => [],
    ];

    protected ?array $trigger = null;

    // Styling
    protected ?string $backgroundColor = null;
    protected ?string $textColor = null;
    protected ?int $elevation = null;
    protected ?string $borderRadius = null;
    protected ?string $transition = null;
    protected ?int $transitionDuration = null;

    // Advanced
    protected ?string $parent = null; // For nested drawers
    protected ?string $maxWidth = null;
    protected ?string $maxHeight = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'aside');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core Methods
    // ========================================================================

    /**
     * Set anchor position (left, right, top, bottom)
     */
    public function anchor(string $anchor): self
    {
        $this->anchor = $anchor;
        return $this;
    }

    /**
     * Set width (for left/right anchors)
     */
    public function width(string $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set height (for top/bottom anchors)
     */
    public function height(string $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set aside variant
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * Set open state
     */
    public function open(bool $open = true): self
    {
        $this->open = $open;
        return $this;
    }

    // ========================================================================
    // Backdrop Methods
    // ========================================================================

    /**
     * Show/hide backdrop
     */
    public function backdrop(bool $show = true): self
    {
        $this->backdrop = $show;
        return $this;
    }

    /**
     * Set backdrop color
     */
    public function backdropColor(string $color): self
    {
        $this->backdropColor = $color;
        return $this;
    }

    /**
     * Close aside when clicking backdrop
     */
    public function closeOnBackdrop(bool $close = true): self
    {
        $this->closeOnBackdrop = $close;
        return $this;
    }

    /**
     * Close aside on ESC key
     */
    public function closeOnEscape(bool $close = true): self
    {
        $this->closeOnEscape = $close;
        return $this;
    }

    // ========================================================================
    // UI Elements
    // ========================================================================

    /**
     * Show/hide close button
     */
    public function closeButton(bool $show = true): self
    {
        $this->closeButton = $show;
        return $this;
    }

    /**
     * Set aside to open by default
     */
    public function defaultOpen(bool $open = true): self
    {
        $this->defaultOpen = $open;
        return $this;
    }

    // ========================================================================
    // Mini Variant Methods
    // ========================================================================

    /**
     * Set mini width (collapsed width for mini variant)
     */
    public function miniWidth(string $width): self
    {
        $this->miniWidth = $width;
        return $this;
    }

    /**
     * Expand mini aside on hover
     */
    public function expandOnHover(bool $expand = true): self
    {
        $this->expandOnHover = $expand;
        return $this;
    }

    // ========================================================================
    // Section Methods - ONLY 5 SUPPORTED SECTIONS
    // ========================================================================

    /**
     * Validate section name
     * 
     * @throws \InvalidArgumentException if section is not supported
     */
    private function validateSection(string $section): void
    {
        if (!in_array($section, self::SUPPORTED_SECTIONS)) {
            throw new \InvalidArgumentException(
                "Section '{$section}' is not supported in AsideSection. Only these sections are allowed: " .
                    implode(', ', self::SUPPORTED_SECTIONS)
            );
        }
    }

    /**
     * Set aside header section
     * 
     * @param array|\Closure $config Component data array or closure
     * @return self
     */
    public function header($config): self
    {
        if ($config instanceof \Closure) {
            $config($this);
        } elseif (is_array($config)) {
            $this->asideSections['header'] = $config;
        }
        return $this;
    }

    /**
     * Set aside footer section
     * 
     * @param array|\Closure $config Component data array or closure
     * @return self
     */
    public function footer($config): self
    {
        if ($config instanceof \Closure) {
            $config($this);
        } elseif (is_array($config)) {
            $this->asideSections['footer'] = $config;
        }
        return $this;
    }

    /**
     * Set aside main content section
     * 
     * @param array|\Closure $config Component data array or closure
     * @return self
     */
    public function main($config): self
    {
        if ($config instanceof \Closure) {
            $config($this);
        } elseif (is_array($config)) {
            $this->asideSections['main'] = $config;
        }
        return $this;
    }

    /**
     * Set aside left section content
     * 
     * @param array|\Closure $config Component data array or closure
     * @return self
     */
    public function left($config): self
    {
        if ($config instanceof \Closure) {
            $config($this);
        } elseif (is_array($config)) {
            $this->asideSections['left'] = $config;
        }
        return $this;
    }

    /**
     * Set aside right section content
     * 
     * @param array|\Closure $config Component data array or closure
     * @return self
     */
    public function right($config): self
    {
        if ($config instanceof \Closure) {
            $config($this);
        } elseif (is_array($config)) {
            $this->asideSections['right'] = $config;
        }
        return $this;
    }

    // ========================================================================
    // Component Management Methods
    // ========================================================================

    /**
     * Add a component to a specific section
     * 
     * @param string $section Section name (header, footer, main, left, right)
     * @param mixed $component Component instance (with toArray()) or array
     * @return self
     * @throws \InvalidArgumentException if section is not supported
     */
    public function addToSection(string $section, $component): self
    {
        $this->validateSection($section);

        if (is_object($component) && method_exists($component, 'toArray')) {
            $this->asideSections[$section][] = $component->toArray();
        } elseif (is_array($component)) {
            $this->asideSections[$section][] = $component;
        }

        return $this;
    }

    /**
     * Add a component to the header section
     */
    public function addHeaderComponent($component): self
    {
        return $this->addToSection('header', $component);
    }

    /**
     * Add a component to the footer section
     */
    public function addFooterComponent($component): self
    {
        return $this->addToSection('footer', $component);
    }

    /**
     * Add a component to the main section
     */
    public function addMainComponent($component): self
    {
        return $this->addToSection('main', $component);
    }

    /**
     * Add a component to the left section
     */
    public function addLeftComponent($component): self
    {
        return $this->addToSection('left', $component);
    }

    /**
     * Add a component to the right section
     */
    public function addRightComponent($component): self
    {
        return $this->addToSection('right', $component);
    }

    /**
     * Add multiple components to a section
     * 
     * @param string $section Section name
     * @param array $components Array of component instances or arrays
     * @return self
     */
    public function addComponentsToSection(string $section, array $components): self
    {
        $this->validateSection($section);

        foreach ($components as $component) {
            $this->addToSection($section, $component);
        }

        return $this;
    }

    /**
     * Clear all components from a section
     * 
     * @param string $section Section name
     * @return self
     */
    public function clearSection(string $section): self
    {
        $this->validateSection($section);
        $this->asideSections[$section] = [];
        return $this;
    }

    /**
     * Get components from a specific section
     * 
     * @param string $section Section name
     * @return array
     */
    public function getSectionComponents(string $section): array
    {
        $this->validateSection($section);
        return $this->asideSections[$section];
    }

    /**
     * Check if a section has components
     * 
     * @param string $section Section name
     * @return bool
     */
    public function hasSectionComponents(string $section): bool
    {
        $this->validateSection($section);
        return !empty($this->asideSections[$section]);
    }

    /**
     * Alias for addMainComponent() for consistency
     */
    public function addComponent($component): self
    {
        return $this->addMainComponent($component);
    }

    // ========================================================================
    // Section Configuration Methods
    // ========================================================================

    /**
     * Configure a section's layout and properties
     * 
     * @param string $section Section name (header, footer, main, left, right)
     * @param array $config Configuration array with properties like:
     *   - width: Section width (e.g., '300px', '25%', '3fr')
     *   - height: Section height (e.g., '100px', 'auto')
     *   - minWidth: Minimum width
     *   - maxWidth: Maximum width
     *   - minHeight: Minimum height
     *   - maxHeight: Maximum height
     *   - gridColumnSpan: Grid column span (for grid layouts)
     *   - gridRowSpan: Grid row span
     *   - order: Flex/grid order
     *   - flex: Flex grow/shrink/basis
     *   - padding: Section padding
     *   - margin: Section margin
     *   - backgroundColor: Section background color
     *   - borderRadius: Section border radius
     *   - overflow: Overflow behavior ('auto', 'hidden', 'scroll')
     *   - sticky: Whether section is sticky
     *   - position: CSS position ('relative', 'absolute', 'fixed', 'sticky')
     *   - zIndex: Z-index for stacking
     *   - className: Additional CSS classes
     *   - style: Custom inline styles
     * @return self
     * @throws \InvalidArgumentException if section is not supported
     * 
     * @example
     * $aside->config('left', [
     *     'width' => '300px',
     *     'gridColumnSpan' => 3,
     *     'overflow' => 'auto',
     *     'sticky' => true
     * ]);
     */
    public function config(string $section, array $config): self
    {
        $this->validateSection($section);
        $this->sectionConfig[$section] = array_merge(
            $this->sectionConfig[$section] ?? [],
            $config
        );
        return $this;
    }

    /**
     * Configure multiple sections at once
     * 
     * @param array $configs Associative array with section names as keys and config arrays as values
     * @return self
     * 
     * @example
     * $aside->configSections([
     *     'left' => ['width' => '250px', 'gridColumnSpan' => 3],
     *     'main' => ['width' => '100%', 'gridColumnSpan' => 6],
     *     'right' => ['width' => '250px', 'gridColumnSpan' => 3]
     * ]);
     */
    public function configSections(array $configs): self
    {
        foreach ($configs as $section => $config) {
            $this->config($section, $config);
        }
        return $this;
    }

    /**
     * Get configuration for a specific section
     * 
     * @param string $section Section name
     * @return array
     */
    public function getSectionConfig(string $section): array
    {
        $this->validateSection($section);
        return $this->sectionConfig[$section] ?? [];
    }

    /**
     * Get all section configurations
     * 
     * @return array
     */
    public function getAllSectionConfigs(): array
    {
        return $this->sectionConfig;
    }

    /**
     * Clear configuration for a specific section
     * 
     * @param string $section Section name
     * @return self
     */
    public function clearSectionConfig(string $section): self
    {
        $this->validateSection($section);
        $this->sectionConfig[$section] = [];
        return $this;
    }

    // ========================================================================
    // Trigger
    // ========================================================================

    /**
     * Set trigger button/element
     */
    public function trigger(array $config): self
    {
        $this->trigger = $config;
        return $this;
    }

    public function getTrigger(): ?array
    {
        return $this->trigger;
    }

    // ========================================================================
    // Styling Methods
    // ========================================================================

    /**
     * Set background color
     */
    public function backgroundColor(string $color): self
    {
        $this->backgroundColor = $color;
        return $this;
    }

    /**
     * Set text color
     */
    public function textColor(string $color): self
    {
        $this->textColor = $color;
        return $this;
    }

    /**
     * Set shadow elevation (0-24)
     */
    public function elevation(int $level): self
    {
        $this->elevation = $level;
        return $this;
    }

    /**
     * Set border radius
     */
    public function borderRadius(string $radius): self
    {
        $this->borderRadius = $radius;
        return $this;
    }

    /**
     * Set transition animation type
     */
    public function transition(string $type): self
    {
        $this->transition = $type;
        return $this;
    }

    /**
     * Set transition duration in milliseconds
     */
    public function transitionDuration(int $ms): self
    {
        $this->transitionDuration = $ms;
        return $this;
    }

    // ========================================================================
    // Advanced Methods
    // ========================================================================

    /**
     * Set parent aside for nested asides
     */
    public function parent(string $asideName): self
    {
        $this->parent = $asideName;
        return $this;
    }

    /**
     * Set maximum width
     */
    public function maxWidth(string $width): self
    {
        $this->maxWidth = $width;
        return $this;
    }

    /**
     * Set maximum height
     */
    public function maxHeight(string $height): self
    {
        $this->maxHeight = $height;
        return $this;
    }

    // ========================================================================
    // Preset Methods
    // ========================================================================

    /**
     * Create a basic navigation aside
     */
    public function navigationMenu(): self
    {
        return $this
            ->anchor('left')
            ->width('280px')
            ->variant('temporary')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->closeOnEscape(true);
    }

    /**
     * Create a filter panel aside
     */
    public function filterPanel(): self
    {
        return $this
            ->anchor('right')
            ->width('360px')
            ->variant('temporary')
            ->backdrop(true)
            ->closeOnBackdrop(true);
    }

    /**
     * Create a mini app navigation aside
     */
    public function miniNav(): self
    {
        return $this
            ->anchor('left')
            ->variant('mini')
            ->width('280px')
            ->miniWidth('72px')
            ->expandOnHover(true)
            ->defaultOpen(true)
            ->backdrop(false);
    }

    /**
     * Create a persistent sidebar
     */
    public function persistentSidebar(): self
    {
        return $this
            ->anchor('left')
            ->width('280px')
            ->variant('persistent')
            ->defaultOpen(true)
            ->backdrop(false);
    }

    /**
     * Create a mobile bottom sheet
     */
    public function bottomSheet(): self
    {
        return $this
            ->anchor('bottom')
            ->height('50vh')
            ->variant('temporary')
            ->backdrop(true)
            ->closeOnBackdrop(true);
    }

    // ========================================================================
    // Getters
    // ========================================================================

    public function getAnchor(): string
    {
        return $this->anchor;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function getMiniWidth(): ?string
    {
        return $this->miniWidth;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function hasBackdrop(): bool
    {
        return $this->backdrop;
    }

    public function getBackdropColor(): ?string
    {
        return $this->backdropColor;
    }

    public function shouldCloseOnBackdrop(): bool
    {
        return $this->closeOnBackdrop;
    }

    public function shouldCloseOnEscape(): bool
    {
        return $this->closeOnEscape;
    }

    public function hasCloseButton(): bool
    {
        return $this->closeButton;
    }

    public function isDefaultOpen(): bool
    {
        return $this->defaultOpen;
    }

    public function shouldExpandOnHover(): bool
    {
        return $this->expandOnHover;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * Get all aside sections
     */
    public function getAsideSections(): array
    {
        return $this->asideSections;
    }

    // ========================================================================
    // Array Conversion
    // ========================================================================

    public function toArray(): array
    {
        $result = array_merge($this->getCommonProperties(), [
            'anchor' => $this->anchor,
            'variant' => $this->variant,
            'width' => $this->width,
            'height' => $this->height,
            'miniWidth' => $this->miniWidth,
            'open' => $this->open,
            'defaultOpen' => $this->defaultOpen,
            'backdrop' => $this->backdrop,
            'backdropColor' => $this->backdropColor,
            'closeOnBackdrop' => $this->closeOnBackdrop,
            'closeOnEscape' => $this->closeOnEscape,
            'closeButton' => $this->closeButton,
            'expandOnHover' => $this->expandOnHover,
            'backgroundColor' => $this->backgroundColor,
            'textColor' => $this->textColor,
            'elevation' => $this->elevation,
            'borderRadius' => $this->borderRadius,
            'transition' => $this->transition,
            'transitionDuration' => $this->transitionDuration,
            'parent' => $this->parent,
            'maxWidth' => $this->maxWidth,
            'maxHeight' => $this->maxHeight,
            'trigger' => $this->trigger,
        ]);

        // Add sections (only non-empty sections)
        $sections = [];
        foreach (self::SUPPORTED_SECTIONS as $sectionName) {
            if (!empty($this->asideSections[$sectionName])) {
                $sections[$sectionName] = $this->asideSections[$sectionName];
            }
        }

        if (!empty($sections)) {
            $result['sections'] = $sections;
        }

        // Add section configurations as separate top-level key
        $sectionConfig = [];
        foreach (self::SUPPORTED_SECTIONS as $sectionName) {
            if (!empty($this->sectionConfig[$sectionName])) {
                $sectionConfig[$sectionName] = $this->sectionConfig[$sectionName];
            }
        }

        if (!empty($sectionConfig)) {
            $result['sectionConfig'] = $sectionConfig;
        }

        return $this->filterNullValues($result);
    }

    /**
     * Filter out null values from array
     */
    protected function filterNullValues(array $array): array
    {
        return array_filter($array, fn($value) => $value !== null);
    }
}
