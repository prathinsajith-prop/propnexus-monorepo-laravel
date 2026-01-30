<?php

namespace Litepie\Layout\Traits;

trait EvaluatesConditions
{
    /**
     * Evaluate a single condition against the given data
     */
    public function evaluateCondition(array $condition, array $data): bool
    {
        $field = $condition['field'];
        $operator = $condition['operator'];
        $expectedValue = $condition['value'];
        $actualValue = $data[$field] ?? null;

        return match ($operator) {
            '=' => $actualValue == $expectedValue,
            '===' => $actualValue === $expectedValue,
            '!=' => $actualValue != $expectedValue,
            '!==' => $actualValue !== $expectedValue,
            '>' => $actualValue > $expectedValue,
            '<' => $actualValue < $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            'in' => is_array($expectedValue) && in_array($actualValue, $expectedValue),
            'not_in' => is_array($expectedValue) && ! in_array($actualValue, $expectedValue),
            'contains' => is_string($actualValue) && str_contains($actualValue, $expectedValue),
            'not_contains' => is_string($actualValue) && ! str_contains($actualValue, $expectedValue),
            'starts_with' => is_string($actualValue) && str_starts_with($actualValue, $expectedValue),
            'ends_with' => is_string($actualValue) && str_ends_with($actualValue, $expectedValue),
            'empty' => empty($actualValue),
            'not_empty' => ! empty($actualValue),
            'null' => $actualValue === null,
            'not_null' => $actualValue !== null,
            'true' => $actualValue === true || $actualValue === '1' || $actualValue === 1,
            'false' => $actualValue === false || $actualValue === '0' || $actualValue === 0,
            'regex' => is_string($actualValue) && preg_match($expectedValue, $actualValue),
            default => false,
        };
    }

    /**
     * Evaluate multiple conditions with AND logic
     */
    public function evaluateConditions(array $conditions, array $data, string $logic = 'and'): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $result = $this->evaluateCondition($condition, $data);

            if ($logic === 'and' && ! $result) {
                return false;
            }

            if ($logic === 'or' && $result) {
                return true;
            }
        }

        return $logic === 'and';
    }

    /**
     * Check if field should be visible based on conditions and data
     */
    public function shouldBeVisible(array $data): bool
    {
        if (! method_exists($this, 'getVisibleConditions')) {
            return true;
        }

        return $this->evaluateConditions($this->getVisibleConditions(), $data);
    }

    /**
     * Check if field should be required based on conditions and data
     */
    public function shouldBeRequired(array $data): bool
    {
        if (! method_exists($this, 'getRequiredConditions')) {
            return method_exists($this, 'isRequired') ? $this->isRequired() : false;
        }

        $conditions = $this->getRequiredConditions();

        if (empty($conditions)) {
            return method_exists($this, 'isRequired') ? $this->isRequired() : false;
        }

        return $this->evaluateConditions($conditions, $data);
    }

    /**
     * Check if field should be disabled based on conditions and data
     */
    public function shouldBeDisabled(array $data): bool
    {
        if (! method_exists($this, 'getDisabledConditions')) {
            return method_exists($this, 'isDisabled') ? $this->isDisabled() : false;
        }

        $conditions = $this->getDisabledConditions();

        if (empty($conditions)) {
            return method_exists($this, 'isDisabled') ? $this->isDisabled() : false;
        }

        return $this->evaluateConditions($conditions, $data);
    }
}
