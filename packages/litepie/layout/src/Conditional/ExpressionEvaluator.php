<?php

namespace Litepie\Layout\Conditional;

class ExpressionEvaluator
{
    /**
     * Evaluate a conditional expression
     *
     * Supported operators: ==, !=, ===, !==, >, <, >=, <=, in, not_in, contains, starts_with, ends_with
     */
    public function evaluate(string $field, string $operator, mixed $value, array $context): bool
    {
        $fieldValue = $this->getFieldValue($field, $context);

        return match ($operator) {
            '==' => $fieldValue == $value,
            '!=' => $fieldValue != $value,
            '===' => $fieldValue === $value,
            '!==' => $fieldValue !== $value,
            '>' => $fieldValue > $value,
            '<' => $fieldValue < $value,
            '>=' => $fieldValue >= $value,
            '<=' => $fieldValue <= $value,
            'in' => is_array($value) && in_array($fieldValue, $value),
            'not_in' => is_array($value) && ! in_array($fieldValue, $value),
            'contains' => is_string($fieldValue) && str_contains($fieldValue, $value),
            'starts_with' => is_string($fieldValue) && str_starts_with($fieldValue, $value),
            'ends_with' => is_string($fieldValue) && str_ends_with($fieldValue, $value),
            'empty' => empty($fieldValue),
            'not_empty' => ! empty($fieldValue),
            'null' => $fieldValue === null,
            'not_null' => $fieldValue !== null,
            default => false,
        };
    }

    /**
     * Evaluate multiple conditions with AND/OR logic
     */
    public function evaluateMultiple(array $conditions, array $context, string $logic = 'AND'): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $results = [];

        foreach ($conditions as $condition) {
            if (is_array($condition) && isset($condition['field'], $condition['operator'])) {
                $results[] = $this->evaluate(
                    $condition['field'],
                    $condition['operator'],
                    $condition['value'] ?? null,
                    $context
                );
            }
        }

        return $logic === 'OR'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    /**
     * Get field value from context using dot notation
     */
    protected function getFieldValue(string $field, array $context): mixed
    {
        if (! str_contains($field, '.')) {
            return $context[$field] ?? null;
        }

        $keys = explode('.', $field);
        $value = $context;

        foreach ($keys as $key) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Parse string expression to condition array
     * Example: "user.role == admin" => ['field' => 'user.role', 'operator' => '==', 'value' => 'admin']
     */
    public function parseExpression(string $expression): ?array
    {
        $operators = ['===', '!==', '==', '!=', '>=', '<=', '>', '<'];

        foreach ($operators as $operator) {
            if (str_contains($expression, $operator)) {
                [$field, $value] = array_map('trim', explode($operator, $expression, 2));

                // Remove quotes from value if present
                $value = trim($value, '\'"');

                // Convert string boolean/null to actual type
                if ($value === 'true') {
                    $value = true;
                } elseif ($value === 'false') {
                    $value = false;
                } elseif ($value === 'null') {
                    $value = null;
                } elseif (is_numeric($value)) {
                    $value = $value + 0;
                }

                return [
                    'field' => $field,
                    'operator' => $operator,
                    'value' => $value,
                ];
            }
        }

        return null;
    }
}
