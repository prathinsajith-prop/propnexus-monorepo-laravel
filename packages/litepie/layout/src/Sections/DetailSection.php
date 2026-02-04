<?php

namespace Litepie\Layout\Sections;

use Litepie\Layout\SlotManager;

/**
 * DetailSection
 *
 * Detail section provides a static detail/view page layout with configurable sections.
 * Designed for displaying detailed information in a structured layout.
 * 
 * SUPPORTED SECTIONS (ONLY):
 * - header: Detail header content
 * - footer: Detail footer content
 * - main: Main detail content (center)
 * - left: Left sidebar content
 * - right: Right sidebar content
 */
class DetailSection extends BaseSection
{
    // Supported sections - NO OTHER KEYS ALLOWED
    private const SUPPORTED_SECTIONS = ['header', 'footer', 'main', 'left', 'right'];

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

    // Styling
    protected ?string $backgroundColor = null;
    protected ?string $textColor = null;
    protected ?string $borderRadius = null;
    protected ?string $padding = null;
    protected ?string $margin = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'detail');
    }

    public static function make(string $name): self
    {
        return new static($name);
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
     * Set border radius
     */
    public function borderRadius(string $radius): self
    {
        $this->borderRadius = $radius;
        return $this;
    }

    /**
     * Set padding
     */
    public function padding(string $padding): self
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * Set margin
     */
    public function margin(string $margin): self
    {
        $this->margin = $margin;
        return $this;
    }

    // ========================================================================
    // Getters
    // ========================================================================

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function getBorderRadius(): ?string
    {
        return $this->borderRadius;
    }

    public function getPadding(): ?string
    {
        return $this->padding;
    }

    public function getMargin(): ?string
    {
        return $this->margin;
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
            'backgroundColor' => $this->backgroundColor,
            'textColor' => $this->textColor,
            'borderRadius' => $this->borderRadius,
            'padding' => $this->padding,
            'margin' => $this->margin,
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
