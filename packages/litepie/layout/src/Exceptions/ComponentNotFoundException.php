<?php

namespace Litepie\Layout\Exceptions;

/**
 * ComponentNotFoundException
 * 
 * Thrown when attempting to create a component type that doesn't exist.
 * Provides detailed information about available types for better debugging.
 */
class ComponentNotFoundException extends LayoutException
{
    protected string $componentType;
    protected array $availableTypes;

    public function __construct(string $type, array $availableTypes = [])
    {
        $this->componentType = $type;
        $this->availableTypes = $availableTypes;

        $message = "Component type '{$type}' not found.";
        
        if (!empty($availableTypes)) {
            $suggestions = $this->findSimilar($type, $availableTypes);
            if (!empty($suggestions)) {
                $message .= " Did you mean: " . implode(', ', array_map(fn($s) => "'{$s}'", $suggestions)) . "?";
            } else {
                $message .= " Available types: " . implode(', ', array_map(fn($s) => "'{$s}'", array_slice($availableTypes, 0, 10)));
            }
        }

        parent::__construct($message);
    }

    /**
     * Find similar type names using Levenshtein distance
     */
    protected function findSimilar(string $needle, array $haystack): array
    {
        $similar = [];
        foreach ($haystack as $type) {
            $distance = levenshtein(strtolower($needle), strtolower($type));
            if ($distance <= 2) { // Allow 2 character difference
                $similar[] = $type;
            }
        }
        return array_slice($similar, 0, 3); // Max 3 suggestions
    }

    public function getComponentType(): string
    {
        return $this->componentType;
    }

    public function getAvailableTypes(): array
    {
        return $this->availableTypes;
    }
}
