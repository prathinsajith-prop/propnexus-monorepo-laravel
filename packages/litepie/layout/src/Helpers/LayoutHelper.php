<?php

namespace Litepie\Layout\Helpers;

use Litepie\Layout\Adapters\LayoutFormAdapter;

/**
 * Helper class for working with Layouts and Litepie/Form integration
 *
 * This class provides utility methods for layout manipulation and
 * delegates form-specific operations to the LayoutFormAdapter
 */
class LayoutHelper
{
    /**
     * Generate validation rules from a layout's form fields
     *
     * @deprecated Use LayoutFormAdapter::extractValidationRules() instead
     */
    public static function extractValidationRules($layout): array
    {
        $fields = $layout->getAllFormFields();

        return LayoutFormAdapter::extractValidationRules($fields);
    }

    /**
     * Generate field attributes array for a Litepie/Form field
     *
     * @deprecated Use LayoutFormAdapter::extractFieldAttributes() instead
     */
    public static function extractFieldAttributes($field): array
    {
        return LayoutFormAdapter::extractFieldAttributes($field);
    }

    /**
     * Generate HTML attributes string
     */
    public static function attributesToString(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $parts[] = $key;
                }
            } else {
                $parts[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Filter visible items (sections, subsections, etc.)
     */
    public static function filterVisible(array $items): array
    {
        return array_filter($items, function ($item) {
            return method_exists($item, 'isVisible') && $item->isVisible();
        });
    }

    /**
     * Filter authorized items (sections, subsections, etc.)
     */
    public static function filterAuthorized(array $items): array
    {
        return array_filter($items, function ($item) {
            return method_exists($item, 'isAuthorizedToSee') && $item->isAuthorizedToSee();
        });
    }

    /**
     * Generate a form data array with defaults from form fields
     *
     * @deprecated Use LayoutFormAdapter::generateDefaultData() instead
     */
    public static function generateDefaultData($layout): array
    {
        $fields = $layout->getAllFormFields();

        return LayoutFormAdapter::generateDefaultData($fields);
    }

    /**
     * Validate data against layout's form field rules
     */
    public static function validate($layout, array $data, $validator = null): array
    {
        $rules = self::extractValidationRules($layout);

        if ($validator) {
            return $validator->make($data, $rules)->validate();
        }

        return $rules;
    }

    /**
     * Convert layout to JSON
     */
    public static function toJson($layout, int $options = 0): string
    {
        return json_encode($layout->toArray(), $options);
    }

    /**
     * Get form field names as array
     */
    public static function getFieldNames($layout): array
    {
        return array_map(
            fn ($field) => method_exists($field, 'getName') ? $field->getName() : null,
            array_filter($layout->getAllFormFields())
        );
    }

    /**
     * Check if layout has a form field with given name
     */
    public static function hasField($layout, string $fieldName): bool
    {
        return $layout->getFormFieldByName($fieldName) !== null;
    }

    /**
     * Sort sections by order property
     */
    public static function sortSectionsByOrder(array $sections): array
    {
        usort($sections, function ($a, $b) {
            $orderA = method_exists($a, 'getOrder') ? ($a->getOrder() ?? PHP_INT_MAX) : PHP_INT_MAX;
            $orderB = method_exists($b, 'getOrder') ? ($b->getOrder() ?? PHP_INT_MAX) : PHP_INT_MAX;

            return $orderA <=> $orderB;
        });

        return $sections;
    }

    /**
     * Sort subsections by order property
     */
    public static function sortSubsectionsByOrder(array $subsections): array
    {
        usort($subsections, function ($a, $b) {
            $orderA = method_exists($a, 'getOrder') ? ($a->getOrder() ?? PHP_INT_MAX) : PHP_INT_MAX;
            $orderB = method_exists($b, 'getOrder') ? ($b->getOrder() ?? PHP_INT_MAX) : PHP_INT_MAX;

            return $orderA <=> $orderB;
        });

        return $subsections;
    }
}
