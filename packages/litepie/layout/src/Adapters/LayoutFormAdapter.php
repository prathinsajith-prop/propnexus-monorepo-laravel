<?php

namespace Litepie\Layout\Adapters;

use Litepie\Layout\Section;
use Litepie\Layout\Subsection;

/**
 * Adapter to bridge Layout structure with Litepie/Form fields
 *
 * This adapter allows Layout package to work seamlessly with
 * Litepie/Form field instances while maintaining the layout
 * structure organization (sections → subsections → fields)
 */
class LayoutFormAdapter
{
    /**
     * Create a Litepie/Form field and add it to a subsection
     *
     * @return \Litepie\Form\Field
     */
    public static function addField(Subsection $subsection, string $type, string $name)
    {
        $field = \Litepie\Form\Field::make($type, $name);
        $subsection->addFormField($field);

        return $field;
    }

    /**
     * Extract all Litepie/Form fields from a section
     */
    public static function getFieldsFromSection(Section $section): array
    {
        $fields = [];
        foreach ($section->getSubsections() as $subsection) {
            $fields = array_merge($fields, $subsection->getFormFields());
        }

        return $fields;
    }

    /**
     * Extract validation rules from Litepie/Form fields
     *
     * @param  array  $fields  Array of Litepie\Form\Field instances
     */
    public static function extractValidationRules(array $fields): array
    {
        $rules = [];
        foreach ($fields as $field) {
            if (method_exists($field, 'getRules')) {
                $fieldRules = $field->getRules();
                if (! empty($fieldRules)) {
                    $rules[$field->getName()] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    /**
     * Extract field attributes for rendering
     *
     * @param  mixed  $field  Litepie\Form\Field instance
     */
    public static function extractFieldAttributes($field): array
    {
        $attributes = [];

        if (method_exists($field, 'getAttributes')) {
            $attributes = $field->getAttributes();
        }

        return $attributes;
    }

    /**
     * Generate default data from fields
     *
     * @param  array  $fields  Array of Litepie\Form\Field instances
     */
    public static function generateDefaultData(array $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            if (method_exists($field, 'getDefault')) {
                $default = $field->getDefault();
                if ($default !== null) {
                    $data[$field->getName()] = $default;
                }
            }
        }

        return $data;
    }

    /**
     * Convert Layout structure to Form-compatible array
     *
     * @param  mixed  $layout
     */
    public static function toFormArray($layout): array
    {
        if (method_exists($layout, 'toArray')) {
            return $layout->toArray();
        }

        return [];
    }
}
