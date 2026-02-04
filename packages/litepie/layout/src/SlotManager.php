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
     * Order of sections/components
     *
     * @var array
     */
    protected array $order = [];

    /**
     * Automatic insertion order tracking
     *
     * @var array
     */
    protected array $insertionOrder = [];

    /**
     * Slot name/identifier
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Create a new SlotManager instance
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    /**
     * Create a new SlotManager instance
     *
     * @param string|null $name Optional slot identifier
     * @return static
     */
    public static function make(?string $name = null): static
    {
        return new static($name);
    }

    /**
     * Register a section
     *
     * @param BaseSection $section Section instance (HeaderSection, FooterSection, GridSection, etc.)
     * @return $this
     */
    public function setSection(BaseSection $section): static
    {
        $sectionArray = $section->toArray();
        $this->sections[] = $sectionArray;

        // Automatically track insertion order by section name
        if (isset($sectionArray['name'])) {
            $this->insertionOrder[] = $sectionArray['name'];
        }

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
        $componentArray = $component->toArray();
        $this->components[] = $componentArray;

        // Automatically track insertion order by component name
        if (isset($componentArray['name'])) {
            $this->insertionOrder[] = $componentArray['name'];
        }

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
     * Get priority order
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Get order of sections/components
     *
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * Get automatic insertion order
     *
     * @return array
     */
    public function getInsertionOrder(): array
    {
        return $this->insertionOrder;
    }

    /**
     * Get effective order (returns insertion order)
     *
     * @return array
     */
    public function getEffectiveOrder(): array
    {
        return $this->insertionOrder;
    }

    /**
     * Get slot name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set slot name
     *
     * @param string $name Slot identifier
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
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
     * Check if has configuration
     *
     * @return bool
     */
    public function hasConfig(): bool
    {
        return !empty($this->config);
    }

    /**
     * Check if has custom order
     *
     * @return bool
     */
    public function hasOrder(): bool
    {
        return !empty($this->order);
    }

    /**
     * Check if priority is set to custom value
     *
     * @return bool
     */
    public function isCustomPriority(): bool
    {
        return $this->priority !== self::PRIORITY_SECTION;
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

     * Reset priority to default (sections)
     *
     * @return $this
     */
    public function resetPriority(): static
    {
        $this->priority = self::PRIORITY_SECTION;
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
        $this->insertionOrder = [];
        $this->priority = self::PRIORITY_SECTION;
        return $this;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'sections' => $this->sections,
            'components' => $this->components,
            'config' => $this->config,
            'priority' => $this->priority,
            'order' => $this->insertionOrder,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        // Add metadata to enforce order preservation
        if (!empty($this->insertionOrder)) {
            $data['config']['orderEnforced'] = true;
            $data['config']['preserveOrder'] = true;
        }

        return $data;
    }
}
