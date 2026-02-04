<?php

namespace Litepie\Layout\Sections;

use Litepie\Layout\SlotManager;

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

    // Visual properties
    protected ?string $variant = null; // default, elevated, bordered, transparent
    protected ?string $padding = 'md'; // none, sm, md, lg, xl

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
     * @param SlotManager $content Slot manager instance
     */
    public function setLeft(SlotManager $content): self
    {
        $this->leftSection = $content->toArray();
        return $this;
    }

    /**
     * Set center section content
     * 
     * @param SlotManager $content Slot manager instance
     */
    public function setCenter(SlotManager $content): self
    {
        $this->centerSection = $content->toArray();
        return $this;
    }

    /**
     * Set right section content
     * 
     * @param SlotManager $content Slot manager instance
     */
    public function setRight(SlotManager $content): self
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
     * Set padding
     * 
     * @param string $padding none|sm|md|lg|xl
     */
    public function padding(string $padding): self
    {
        $this->padding = $padding;
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
        $result = [
            'name' => $this->name,
            'type' => $this->type,
        ];

        // Add variant and padding if set
        if ($this->variant !== null) {
            $result['variant'] = $this->variant;
        }
        if ($this->padding !== null) {
            $result['padding'] = $this->padding;
        }

        // Add sections (only non-empty)
        $sections = array_filter($this->getSections(), fn($section) => !empty($section));
        if (!empty($sections)) {
            $result['sections'] = $sections;
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
}
