<?php

namespace Litepie\Layout;

use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Sections\BaseSection;

/**
 * SlotManager
 * 
 * Manages sections and components with strict type validation.
 * Stores everything in a single internal configuration/conversation array.
 * 
 * Responsibilities:
 * - Register sections with validation
 * - Register components with validation
 * - Store configuration data
 * - Provide clean getter/setter methods
 * 
 * Does NOT handle rendering or UI behavior.
 * 
 * @package Litepie\Layout
 */
class SlotManager
{
    public const PRIORITY_SECTION = 'sections';
    public const PRIORITY_COMPONENT = 'components';

    /**
     * Section storage
     *
     * @var array
     */
    protected array $sections = [];

    /**
     * Components storage
     *
     * @var array
     */
    protected array $components = [];

    /**
     * Configuration storage
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Priority order: 'section' or 'component'
     *
     * @var string
     */
    protected string $priority = self::PRIORITY_SECTION;

    /**
     * Create a new SlotManager instance
     */
    public function __construct() {}

    /**
     * Create a new SlotManager instance
     *
     * @return static
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Register a section
     *
     * @param BaseSection $section Section instance (HeaderSection, FooterSection, GridSection, etc.)
     * @return $this
     */
    public function setSection(BaseSection $section): static
    {
        $this->sections[] = $section->toArray();

        return $this;
    }

    /**
     * Register a component
     *
     * @param Component $component Component instance
     * @return $this
     */
    public function setComponent(Component $component): static
    {
        $this->components[] = $component->toArray();

        return $this;
    }

    /**
     * Set configuration
     *
     * @param array $config Configuration array
     * @return $this
     */
    public function setConfig(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set priority order
     *
     * @param string $priority Use SlotManager::PRIORITY_SECTION or SlotManager::PRIORITY_COMPONENT
     * @return $this
     */
    public function setPriority(string $priority): static
    {
        $this->priority = in_array($priority, [self::PRIORITY_SECTION, self::PRIORITY_COMPONENT])
            ? $priority
            : self::PRIORITY_SECTION;

        return $this;
    }

    /**
     * Get registered section
     *
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Get registered components
     *
     * @return array
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Check if has sections
     *
     * @return bool
     */
    public function hasSections(): bool
    {
        return !empty($this->sections);
    }

    /**
     * Check if has components
     *
     * @return bool
     */
    public function hasComponents(): bool
    {
        return !empty($this->components);
    }

    /**
     * Clear sections
     *
     * @return $this
     */
    public function clearSections(): static
    {
        $this->sections = [];
        return $this;
    }

    /**
     * Clear components
     *
     * @return $this
     */
    public function clearComponents(): static
    {
        $this->components = [];
        return $this;
    }

    /**
     * Clear everything
     *
     * @return $this
     */
    public function clear(): static
    {
        $this->sections = [];
        $this->components = [];
        $this->config = [];
        return $this;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'sections' => $this->sections,
            'components' => $this->components,
            'config' => $this->config,
            'priority' => $this->priority,
        ];
    }
}
