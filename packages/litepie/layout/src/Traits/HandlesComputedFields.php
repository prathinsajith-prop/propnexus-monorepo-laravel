<?php

namespace Litepie\Layout\Traits;

trait HandlesComputedFields
{
    /**
     * Process all computed fields in a layout with the given data
     */
    public function processComputedFields(array $layout, array $data): array
    {
        return $this->processComputedFieldsRecursive($layout, $data);
    }

    /**
     * Recursively process computed fields
     */
    protected function processComputedFieldsRecursive(array $structure, array &$data): array
    {
        // Process sections
        if (isset($structure['sections'])) {
            foreach ($structure['sections'] as &$section) {
                $section = $this->processComputedFieldsRecursive($section, $data);
            }
        }

        // Process subsections
        if (isset($structure['subsections'])) {
            foreach ($structure['subsections'] as &$subsection) {
                $subsection = $this->processComputedFieldsRecursive($subsection, $data);
            }
        }

        // Process fields
        if (isset($structure['fields'])) {
            foreach ($structure['fields'] as &$field) {
                if (isset($field['is_computed']) && $field['is_computed']) {
                    // Mark as computed and add computed value
                    $field['computed_value'] = $data[$field['name']] ?? null;
                }
            }
        }

        return $structure;
    }

    /**
     * Calculate all computed values from fields in a layout builder
     */
    public function calculateComputedValues(array $data): array
    {
        $computedValues = [];
        $fields = $this->getAllComputedFields();

        // Sort fields by dependency order (simple topological sort)
        $sortedFields = $this->sortFieldsByDependency($fields, $data);

        foreach ($sortedFields as $field) {
            $computedValues[$field->getName()] = $field->computeValue(
                array_merge($data, $computedValues)
            );
        }

        return $computedValues;
    }

    /**
     * Get all computed fields from the layout
     */
    protected function getAllComputedFields(): array
    {
        $computedFields = [];

        if (! method_exists($this, 'getSections')) {
            return $computedFields;
        }

        foreach ($this->getSections() as $section) {
            foreach ($section->getSubsections() as $subsection) {
                foreach ($subsection->getFields() as $field) {
                    if ($field->isComputed()) {
                        $computedFields[] = $field;
                    }
                }
            }
        }

        return $computedFields;
    }

    /**
     * Sort fields by their dependencies (basic implementation)
     * In a more complex scenario, you'd want proper topological sorting
     */
    protected function sortFieldsByDependency(array $fields, array $data): array
    {
        // For now, just return as-is
        // A production implementation would analyze dependencies
        // and sort fields so dependent fields are computed after their dependencies
        return $fields;
    }
}
