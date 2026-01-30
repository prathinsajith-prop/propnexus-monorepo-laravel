<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Registry\TypeRegistry;
use Litepie\Layout\Exceptions\SectionNotFoundException;

/**
 * CreatesSections Trait
 *
 * Provides ability to create and add nested section instances.
 * Sections are containers (Grid, Row, Layout, Tabs, etc.)
 * 
 * This trait should be used by container classes that can hold nested sections.
 */
trait CreatesSections
{
    /**
     * Storage for nested sections
     * @var Component[]
     */
    protected array $sections = [];

    /**
     * Create and add a Section
     * 
     * @param string $type Section type (grid, row, tabs, accordion, etc.)
     * @param string $name Unique identifier
     * @return Component The created section instance
     * @throws SectionNotFoundException If section type doesn't exist
     */
    public function section(string $type, string $name): Component
    {
        $className = $this->resolveSectionClassName($type);
        
        if (!$className) {
            throw new SectionNotFoundException($type, TypeRegistry::getAllSectionTypes());
        }

        $section = $className::make($name);
        $section->parentBuilder = $this;
        $this->sections[] = $section;
        
        return $section;
    }

    /**
     * Add an existing section instance
     */
    public function addSection(Component $section): self
    {
        $section->parentBuilder = $this;
        $this->sections[] = $section;

        return $this;
    }

    /**
     * Get all nested sections
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Check if has nested sections
     */
    public function hasSections(): bool
    {
        return !empty($this->sections);
    }

    /**
     * Resolve section class name from type using TypeRegistry (O(1) lookup)
     * 
     * @param string $type Section type identifier
     * @return string|null Full class name or null if not found
     */
    protected function resolveSectionClassName(string $type): ?string
    {
        return TypeRegistry::getSection($type);
    }

    // ========================================================================
    // Convenience Methods - Common Sections
    // ========================================================================

    public function grid(string $name, int $columns = 1, int $rows = 1): Component
    {
        $className = $this->resolveSectionClassName('grid');
        $section = $className::make($name, $columns, $rows);
        $section->parentBuilder = $this;
        $this->sections[] = $section;
        return $section;
    }

    public function row(string $name): Component
    {
        return $this->section('row', $name);
    }

    public function layout(string $name): Component
    {
        return $this->section('layout', $name);
    }

    public function tabs(string $name): Component
    {
        return $this->section('tabs', $name);
    }

    public function accordion(string $name): Component
    {
        return $this->section('accordion', $name);
    }

    public function wizard(string $name): Component
    {
        return $this->section('wizard', $name);
    }

    public function header(string $name): Component
    {
        return $this->section('header', $name);
    }

    public function subsection(string $name): \Litepie\Layout\Subsection
    {
        $subsection = \Litepie\Layout\Subsection::make($name);
        $subsection->parentBuilder = $this;
        $this->sections[] = $subsection;
        
        return $subsection;
    }
}
