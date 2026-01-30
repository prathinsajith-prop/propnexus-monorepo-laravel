<?php

namespace Litepie\Layout\Exceptions;

/**
 * SectionNotFoundException
 * 
 * Thrown when attempting to create a section type that doesn't exist.
 */
class SectionNotFoundException extends LayoutException
{
    protected string $sectionType;
    protected array $availableTypes;

    public function __construct(string $type, array $availableTypes = [])
    {
        $this->sectionType = $type;
        $this->availableTypes = $availableTypes;

        $message = "Section type '{$type}' not found.";
        
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

    protected function findSimilar(string $needle, array $haystack): array
    {
        $similar = [];
        foreach ($haystack as $type) {
            $distance = levenshtein(strtolower($needle), strtolower($type));
            if ($distance <= 2) {
                $similar[] = $type;
            }
        }
        return array_slice($similar, 0, 3);
    }

    public function getSectionType(): string
    {
        return $this->sectionType;
    }

    public function getAvailableTypes(): array
    {
        return $this->availableTypes;
    }
}
