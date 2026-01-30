<?php

namespace Litepie\Layout\Export;

use Litepie\Layout\LayoutBuilder;

class LayoutImporter
{
    /**
     * Import layout from JSON
     */
    public function fromJson(string $json): LayoutBuilder
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $this->fromArray($data);
    }

    /**
     * Import layout from array
     */
    public function fromArray(array $data): LayoutBuilder
    {
        // Remove metadata if present
        unset($data['_metadata']);

        $layout = LayoutBuilder::create(
            $data['name'] ?? 'imported',
            $data['mode'] ?? 'view'
        );

        // Set shared data if present
        if (isset($data['shared_data_url'])) {
            $layout->sharedDataUrl($data['shared_data_url']);
        }

        if (isset($data['shared_data_params'])) {
            $layout->sharedDataParams($data['shared_data_params']);
        }

        // Import sections
        if (isset($data['sections']) && is_array($data['sections'])) {
            $this->importSections($layout, $data['sections']);
        }

        return $layout;
    }

    /**
     * Import layout from YAML
     */
    public function fromYaml(string $yaml): LayoutBuilder
    {
        if (! class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new \RuntimeException('symfony/yaml package is required for YAML import');
        }

        $data = \Symfony\Component\Yaml\Yaml::parse($yaml);

        return $this->fromArray($data);
    }

    /**
     * Import sections recursively
     */
    protected function importSections(LayoutBuilder $layout, array $sections): void
    {
        foreach ($sections as $sectionData) {
            $type = $sectionData['type'] ?? 'custom';
            $name = $sectionData['name'] ?? 'section_'.uniqid();

            // Call appropriate section method
            $method = $type.'Section';

            if (method_exists($layout, $method)) {
                $section = $layout->$method($name);

                // Apply properties
                $this->applySectionProperties($section, $sectionData);

                // Import nested sections if present
                if (isset($sectionData['sections']) && is_array($sectionData['sections'])) {
                    foreach ($sectionData['sections'] as $nestedData) {
                        $this->importNestedSection($section, $nestedData);
                    }
                }
            }
        }
    }

    /**
     * Import nested section
     */
    protected function importNestedSection($parentSection, array $sectionData): void
    {
        $type = $sectionData['type'] ?? 'custom';
        $method = $type.'Section';

        if (method_exists($parentSection, $method)) {
            $section = $parentSection->$method($sectionData['name'] ?? 'nested_'.uniqid());
            $this->applySectionProperties($section, $sectionData);

            if (isset($sectionData['sections'])) {
                foreach ($sectionData['sections'] as $nested) {
                    $this->importNestedSection($section, $nested);
                }
            }
        }
    }

    /**
     * Apply section properties
     */
    protected function applySectionProperties($section, array $data): void
    {
        $methodMap = [
            'title' => 'title',
            'subtitle' => 'subtitle',
            'icon' => 'icon',
            'data_url' => 'dataUrl',
            'data_key' => 'dataKey',
            'use_shared_data' => 'useSharedData',
            'load_on_mount' => 'loadOnMount',
            'order' => 'order',
            'visible' => 'visible',
        ];

        foreach ($methodMap as $key => $method) {
            if (isset($data[$key]) && method_exists($section, $method)) {
                $section->$method($data[$key]);
            }
        }

        // Apply actions
        if (isset($data['actions']) && is_array($data['actions'])) {
            foreach ($data['actions'] as $action) {
                if (method_exists($section, 'addAction')) {
                    $section->addAction(
                        $action['name'] ?? 'action',
                        $action['label'] ?? 'Action',
                        $action['options'] ?? []
                    );
                }
            }
        }
    }
}
