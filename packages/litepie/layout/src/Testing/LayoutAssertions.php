<?php

namespace Litepie\Layout\Testing;

use PHPUnit\Framework\Assert;

class LayoutAssertions
{
    protected $layout;

    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Assert layout has a section
     */
    public function assertHasSection(string $name, ?string $message = null): self
    {
        $sections = $this->getAllSections($this->layout->toArray());
        $names = array_column($sections, 'name');

        Assert::assertContains(
            $name,
            $names,
            $message ?? "Failed asserting that layout has section '{$name}'"
        );

        return $this;
    }

    /**
     * Assert section is visible
     */
    public function assertSectionVisible(string $name, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertTrue(
            $section['visible'] ?? true,
            $message ?? "Failed asserting that section '{$name}' is visible"
        );

        return $this;
    }

    /**
     * Assert section is hidden
     */
    public function assertSectionHidden(string $name, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertFalse(
            $section['visible'] ?? true,
            $message ?? "Failed asserting that section '{$name}' is hidden"
        );

        return $this;
    }

    /**
     * Assert section has type
     */
    public function assertSectionType(string $name, string $type, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertEquals(
            $type,
            $section['type'] ?? null,
            $message ?? "Failed asserting that section '{$name}' has type '{$type}'"
        );

        return $this;
    }

    /**
     * Assert section has property
     */
    public function assertSectionHasProperty(string $name, string $property, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertArrayHasKey(
            $property,
            $section,
            $message ?? "Failed asserting that section '{$name}' has property '{$property}'"
        );

        return $this;
    }

    /**
     * Assert section property equals
     */
    public function assertSectionProperty(string $name, string $property, mixed $expected, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertEquals(
            $expected,
            $section[$property] ?? null,
            $message ?? "Failed asserting that section '{$name}' property '{$property}' equals expected value"
        );

        return $this;
    }

    /**
     * Assert section count
     */
    public function assertSectionCount(int $expected, ?string $message = null): self
    {
        $sections = $this->getAllSections($this->layout->toArray());

        Assert::assertCount(
            $expected,
            $sections,
            $message ?? "Failed asserting that layout has {$expected} sections"
        );

        return $this;
    }

    /**
     * Assert section has nested sections
     */
    public function assertHasNestedSections(string $name, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertArrayHasKey(
            'sections',
            $section,
            $message ?? "Failed asserting that section '{$name}' has nested sections"
        );

        Assert::assertNotEmpty(
            $section['sections'],
            $message ?? "Failed asserting that section '{$name}' has nested sections"
        );

        return $this;
    }

    /**
     * Assert section has data URL
     */
    public function assertSectionHasDataUrl(string $name, ?string $expectedUrl = null, ?string $message = null): self
    {
        $section = $this->findSection($name);

        Assert::assertNotNull(
            $section,
            "Section '{$name}' not found in layout"
        );

        Assert::assertArrayHasKey(
            'data_url',
            $section,
            $message ?? "Failed asserting that section '{$name}' has data URL"
        );

        if ($expectedUrl) {
            Assert::assertEquals(
                $expectedUrl,
                $section['data_url'],
                $message ?? "Failed asserting that section '{$name}' data URL matches expected"
            );
        }

        return $this;
    }

    /**
     * Find section by name
     */
    protected function findSection(string $name): ?array
    {
        $sections = $this->getAllSections($this->layout->toArray());

        foreach ($sections as $section) {
            if ($section['name'] === $name) {
                return $section;
            }
        }

        return null;
    }

    /**
     * Get all sections (including nested)
     */
    protected function getAllSections(array $data): array
    {
        $sections = [];

        if (isset($data['sections'])) {
            foreach ($data['sections'] as $section) {
                $sections[] = $section;

                if (isset($section['sections'])) {
                    $sections = array_merge(
                        $sections,
                        $this->getAllSections($section)
                    );
                }
            }
        }

        return $sections;
    }
}
