<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Export\LayoutExporter;
use Litepie\Layout\Export\LayoutImporter;

trait Exportable
{
    /**
     * Export to JSON
     */
    public function toJson(bool $pretty = true): string
    {
        return (new LayoutExporter)->toJson($this, $pretty);
    }

    /**
     * Export to YAML
     */
    public function toYaml(): string
    {
        return (new LayoutExporter)->toYaml($this);
    }

    /**
     * Export with metadata
     */
    public function export(string $format = 'json'): string
    {
        $exporter = (new LayoutExporter)->format($format)->withMetadata(true);

        return match ($format) {
            'yaml' => $exporter->toYaml($this),
            'json' => $exporter->toJson($this, true),
            default => $exporter->toJson($this, true),
        };
    }

    /**
     * Import from JSON
     */
    public static function importJson(string $json): LayoutBuilder
    {
        return (new LayoutImporter)->fromJson($json);
    }

    /**
     * Import from array
     */
    public static function importArray(array $data): LayoutBuilder
    {
        return (new LayoutImporter)->fromArray($data);
    }

    /**
     * Import from YAML
     */
    public static function importYaml(string $yaml): LayoutBuilder
    {
        return (new LayoutImporter)->fromYaml($yaml);
    }
}
