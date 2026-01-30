<?php

namespace Litepie\Layout\Sections;

use Litepie\Layout\Sections\GridSection;

/**
 * FooterSection
 *
 * A flexible footer component with three sections: left, center, and right.
 * Can be used in modals, drawers, layouts, and any other container.
 * 
 * Sections:
 * - left: Left-aligned content (typically secondary actions, links)
 * - center: Center-aligned content (typically copyright, info text)
 * - right: Right-aligned content (typically primary actions, buttons)
 * 
 * @example
 * FooterSection::make('modal-footer')
 *     ->left([
 *         LinkComponent::make('help')->text('Need help?')->href('/help')
 *     ])
 *     ->center([
 *         TextComponent::make('copyright')->text('© 2026 Your Company')->variant('caption')
 *     ])
 *     ->right([
 *         ButtonComponent::make('cancel-btn')->text('Cancel')->variant('outlined'),
 *         ButtonComponent::make('save-btn')->text('Save Changes')->variant('contained')
 *     ])
 *     ->variant('elevated')
 *     ->padding('md');
 */
class FooterSection extends BaseSection
{
    private const SUPPORTED_SECTIONS = ['left', 'center', 'right'];

    // Content sections
    protected array $leftSection = [];
    protected array $centerSection = [];
    protected array $rightSection = [];

    // Section configuration (layout properties for each section)
    protected array $sectionConfig = [];

    // Visual properties
    protected ?string $variant = null; // default, elevated, bordered, transparent
    protected ?string $padding = 'md'; // none, sm, md, lg, xl
    protected ?string $height = null; // sm, md, lg, auto
    protected ?string $backgroundColor = null;
    protected ?string $textColor = null;
    protected bool $sticky = false;
    protected bool $bordered = false;
    protected bool $shadow = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'footer');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Section Content Methods
    // ========================================================================

    /**
     * Validate that a section is supported
     * 
     * @param string $section Section name to validate
     * @throws \InvalidArgumentException if section is not supported
     */
    private function validateSection(string $section): void
    {
        if (!in_array($section, self::SUPPORTED_SECTIONS)) {
            throw new \InvalidArgumentException(
                "Section '{$section}' is not supported in FooterSection. Only these sections are allowed: " .
                    implode(', ', self::SUPPORTED_SECTIONS)
            );
        }
    }

    /**
     * Set left section content
     * 
     * @param GridSection $content Grid section instance
     */
    public function setLeft(GridSection $content): self
    {
        $this->leftSection = $content->toArray();
        return $this;
    }

    /**
     * Set center section content
     * 
     * @param GridSection $content Grid section instance
     */
    public function setCenter(GridSection $content): self
    {
        $this->centerSection = $content->toArray();
        return $this;
    }

    /**
     * Set right section content
     * 
     * @param GridSection $content Grid section instance
     */
    public function setRight(GridSection $content): self
    {
        $this->rightSection = $content->toArray();
        return $this;
    }

    // ========================================================================
    // Visual Style Methods
    // ========================================================================

    /**
     * Set footer variant
     * 
     * @param string $variant default|elevated|bordered|transparent
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * Set elevated variant (with shadow)
     */
    public function elevated(): self
    {
        return $this->variant('elevated');
    }

    /**
     * Set bordered variant (top border)
     */
    public function withBorder(): self
    {
        $this->bordered = true;
        return $this;
    }

    /**
     * Set transparent variant
     */
    public function transparent(): self
    {
        return $this->variant('transparent');
    }

    /**
     * Set padding
     * 
     * @param string $padding none|sm|md|lg|xl
     */
    public function padding(string $padding): self
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * Set height
     * 
     * @param string $height sm|md|lg|auto
     */
    public function height(string $height): self
    {
        $this->height = $height;
        return $this;
    }

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
     * Make footer sticky
     */
    public function sticky(bool $sticky = true): self
    {
        $this->sticky = $sticky;
        return $this;
    }

    /**
     * Add shadow
     */
    public function shadow(bool $shadow = true): self
    {
        $this->shadow = $shadow;
        return $this;
    }

    // ========================================================================
    // Section Configuration Methods
    // ========================================================================

    /**
     * Configure a specific section with layout properties
     * 
     * @param string $section Section name (left, center, right)
     * @param array $config Configuration properties (width, height, gridColumnSpan, etc.)
     * @return self
     */
    public function config(string $section, array $config): self
    {
        $this->validateSection($section);
        $this->sectionConfig[$section] = $config;
        return $this;
    }

    /**
     * Configure multiple sections at once
     * 
     * @param array $configs Associative array of section => config pairs
     * @return self
     * 
     * @example
     * ->configSections([
     *     'left' => ['width' => '200px', 'gridColumnSpan' => 2],
     *     'center' => ['flex' => '1 1 auto', 'gridColumnSpan' => 8],
     *     'right' => ['width' => '200px', 'gridColumnSpan' => 2]
     * ])
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
     */
    public function getSectionConfig(string $section): ?array
    {
        return $this->sectionConfig[$section] ?? null;
    }

    /**
     * Get all section configurations
     */
    public function getAllSectionConfigs(): array
    {
        return $this->sectionConfig;
    }

    /**
     * Clear configuration for a specific section
     */
    public function clearSectionConfig(string $section): self
    {
        unset($this->sectionConfig[$section]);
        return $this;
    }

    // ========================================================================
    // Helper Methods
    // ========================================================================

    /**
     * Get all sections
     */
    public function getSections(): array
    {
        return [
            'left' => $this->leftSection,
            'center' => $this->centerSection,
            'right' => $this->rightSection,
        ];
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    /**
     * Convert component to array for JSON serialization
     */
    public function toArray(): array
    {
        $sections = array_filter($this->getSections(), fn($section) => !empty($section));

        $result = array_merge(parent::getCommonProperties(), $this->filterNullValues([
            'variant' => $this->variant,
            'padding' => $this->padding,
            'height' => $this->height,
            'backgroundColor' => $this->backgroundColor,
            'textColor' => $this->textColor,
            'sticky' => $this->sticky,
            'bordered' => $this->bordered,
            'shadow' => $this->shadow,
        ]));

        // Only add sections if there's content
        if (!empty($sections)) {
            $result['sections'] = $sections;
        }

        // Only add sectionConfig for sections that have content
        if (!empty($this->sectionConfig)) {
            $filteredSectionConfig = array_filter(
                $this->sectionConfig,
                fn($key) => isset($sections[$key]),
                ARRAY_FILTER_USE_KEY
            );

            if (!empty($filteredSectionConfig)) {
                $result['sectionConfig'] = $filteredSectionConfig;
            }
        }

        return $result;
    }

    /**
     * Render the component to array
     */
    public function render(): array
    {
        return $this->toArray();
    }

    /**
     * Filter out null values from array
     */
    protected function filterNullValues(array $array): array
    {
        return array_filter($array, fn($value) => $value !== null);
    }
}
