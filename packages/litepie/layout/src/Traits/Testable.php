<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Testing\LayoutAssertions;

trait Testable
{
    /**
     * Get test assertions helper
     */
    public function assertions(): LayoutAssertions
    {
        return new LayoutAssertions($this);
    }

    /**
     * Create test snapshot
     */
    public function snapshot(): array
    {
        return [
            'layout' => $this->toArray(),
            'timestamp' => now()->toIso8601String(),
            'sections_count' => $this->getSectionsCount(),
        ];
    }

    /**
     * Get sections count (including nested)
     */
    protected function getSectionsCount(): int
    {
        $count = count($this->sections);

        foreach ($this->sections as $section) {
            if (method_exists($section, 'getSectionsCount')) {
                $count += $section->getSectionsCount();
            }
        }

        return $count;
    }
}
