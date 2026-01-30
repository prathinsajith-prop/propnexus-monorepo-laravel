<?php

namespace Litepie\Layout\Sections;

use Litepie\Layout\SlotManager;

/**
 * DetailSection
 *
 * Detail section provides detailed view panels that slide in from edges of the screen.
 * Similar to AsideSection but specifically designed for detail/view layouts.
 * 
 * SUPPORTED SECTIONS (ONLY):
 * - header: Detail header content
 * - footer: Detail footer content
 * - main: Main detail content (center)
 * - left: Left sidebar content
 * - right: Right sidebar content
 * 
 * Supports multiple anchor positions (left, right, top, bottom) and variants (temporary, persistent, mini, permanent).
 */
class DetailSection extends BaseSection
{
    // Supported sections - NO OTHER KEYS ALLOWED
    private const SUPPORTED_SECTIONS = ['header', 'footer', 'main', 'left', 'right'];

    // Core properties
    protected string $anchor = 'right'; // left, right, top, bottom
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
    protected array $detailSections = [
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
    protected ?string $parent = null; // For nested details
    protected ?string $maxWidth = null;
    protected ?string $maxHeight = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'detail');
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
     * Set detail variant
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
     * Close detail when clicking backdrop
     */
    public function closeOnBackdrop(bool $close = true): self
    {
        $this->closeOnBackdrop = $close;
        return $this;
    }

    /**
     * Close detail on ESC key
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
     * Set detail to open by default
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
     * Expand mini detail on hover
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
                "Section '{$section}' is not supported in DetailSection. Only these sections are allowed: " .
                    implode(', ', self::SUPPORTED_SECTIONS)
            );
        }
    }

    /**
     * Set detail header section
     * Only accepts SlotManager instances
     * 
     * @param SlotManager $slotManager SlotManager instance
     * @return self
     */
    public function setHeader(SlotManager $slotManager): self
    {
        $this->detailSections['header'] = $slotManager->toArray();
        return $this;
    }

    /**
     * Set detail footer section
     * Only accepts SlotManager instances
     * 
     * @param SlotManager $slotManager SlotManager instance
     * @return self
     */
    public function setFooter(SlotManager $slotManager): self
    {
        $this->detailSections['footer'] = $slotManager->toArray();
        return $this;
    }

    /**
     * Set detail main content section
     * Only accepts SlotManager instances
     * 
     * @param SlotManager $slotManager SlotManager instance
     * @return self
     */
    public function setMain(SlotManager $slotManager): self
    {
        $this->detailSections['main'] = $slotManager->toArray();
        return $this;
    }

    /**
     * Set detail left section content
     * Only accepts SlotManager instances
     * 
     * @param SlotManager $slotManager SlotManager instance
     * @return self
     */
    public function setLeft(SlotManager $slotManager): self
    {
        $this->detailSections['left'] = $slotManager->toArray();
        return $this;
    }

    /**
     * Set detail right section content
     * Only accepts SlotManager instances
     * 
     * @param SlotManager $slotManager SlotManager instance
     * @return self
     */
    public function setRight(SlotManager $slotManager): self
    {
        $this->detailSections['right'] = $slotManager->toArray();
        return $this;
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
     * $detail->config('left', [
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
     * $detail->configSections([
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
     * Set parent detail for nested details
     */
    public function parent(string $detailName): self
    {
        $this->parent = $detailName;
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
     * Create a detail view panel
     */
    public function detailView(): self
    {
        return $this
            ->anchor('right')
            ->width('600px')
            ->variant('temporary')
            ->backdrop(true)
            ->closeOnBackdrop(true)
            ->closeOnEscape(true);
    }

    /**
     * Create a fullscreen detail view
     */
    public function fullscreenDetail(): self
    {
        return $this
            ->anchor('right')
            ->width('100vw')
            ->height('100vh')
            ->variant('temporary')
            ->backdrop(false)
            ->closeOnBackdrop(false);
    }

    /**
     * Create a large detail panel
     */
    public function largeDetail(): self
    {
        return $this
            ->anchor('right')
            ->width('900px')
            ->variant('temporary')
            ->backdrop(true)
            ->closeOnBackdrop(true);
    }

    /**
     * Create a persistent detail sidebar
     */
    public function persistentDetail(): self
    {
        return $this
            ->anchor('right')
            ->width('400px')
            ->variant('persistent')
            ->defaultOpen(true)
            ->backdrop(false);
    }

    /**
     * Create a mobile bottom detail sheet
     */
    public function bottomDetail(): self
    {
        return $this
            ->anchor('bottom')
            ->height('70vh')
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
     * Get all detail sections
     */
    public function getDetailSections(): array
    {
        return $this->detailSections;
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
        $sections = array_filter($this->detailSections, fn($section) => !empty($section));
        if (!empty($sections)) {
            $result['sections'] = $sections;
        }

        // Add section configurations (only non-empty configs)
        $sectionConfig = array_filter($this->sectionConfig, fn($config) => !empty($config));
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
