<?php

namespace Litepie\Layout\Export;

class LayoutExporter
{
    protected string $format = 'json';

    protected bool $pretty = true;

    protected bool $includeMetadata = true;

    /**
     * Export layout to JSON
     */
    public function toJson($layout, bool $pretty = true): string
    {
        $data = $this->prepareExport($layout);

        $flags = JSON_THROW_ON_ERROR;
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        }

        return json_encode($data, $flags);
    }

    /**
     * Export layout to array
     */
    public function toArray($layout): array
    {
        return $this->prepareExport($layout);
    }

    /**
     * Export layout to YAML (requires symfony/yaml)
     */
    public function toYaml($layout): string
    {
        if (! class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new \RuntimeException('symfony/yaml package is required for YAML export');
        }

        $data = $this->prepareExport($layout);

        return \Symfony\Component\Yaml\Yaml::dump($data, 10, 2);
    }

    /**
     * Prepare export data
     */
    protected function prepareExport($layout): array
    {
        $data = is_array($layout) ? $layout : $layout->toArray();

        if ($this->includeMetadata) {
            $data['_metadata'] = [
                'exported_at' => now()->toIso8601String(),
                'version' => '3.0',
                'format' => $this->format,
            ];
        }

        return $data;
    }

    /**
     * Set format
     */
    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Include metadata
     */
    public function withMetadata(bool $include = true): self
    {
        $this->includeMetadata = $include;

        return $this;
    }
}
