<?php

namespace Litepie\Layout;

use Litepie\Layout\Components\FormComponent;
use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Contracts\Renderable;
use Litepie\Layout\Sections\AccordionSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\ScrollSpySection;
use Litepie\Layout\Sections\TabsSection;
use Litepie\Layout\Traits\Cacheable;
use Litepie\Layout\Traits\Debuggable;
use Litepie\Layout\Traits\Exportable;
use Litepie\Layout\Traits\Testable;

/**
 * Layout
 *
 * Root-level container in the 4-level architecture:
 * Layout → Section → Slot → Component
 *
 * Layout contains Sections (BaseSection subclasses)
 * Sections contain Slots (named content areas)
 * Slots contain Components or nested Sections
 *
 * This class also maintains backward compatibility with legacy Section/Subsection structure.
 */
class Layout implements Renderable
{
    use Cacheable, Debuggable, Exportable, Testable;

    protected string $module;

    protected string $context;

    protected array $sections = [];

    protected array $meta = [];

    protected ?string $sharedDataUrl = null;

    protected array $sharedDataParams = [];

    public function __construct(string $module, string $context, array $sections = [], ?string $sharedDataUrl = null, array $sharedDataParams = [])
    {
        $this->module = $module;
        $this->context = $context;
        $this->sharedDataUrl = $sharedDataUrl;
        $this->sharedDataParams = $sharedDataParams;
        $this->sections = $sections;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function getSection(string $name): ?Section
    {
        return $this->sections[$name] ?? null;
    }

    public function getSubsection(string $sectionName, string $subsectionName): ?Subsection
    {
        $section = $this->getSection($sectionName);

        return $section?->getSubsection($subsectionName);
    }

    /**
     * Add a section to the layout
     */
    public function addSection(Component $section): self
    {
        if (method_exists($section, 'getName')) {
            $this->sections[$section->getName()] = $section;
        } else {
            $this->sections[] = $section;
        }

        return $this;
    }

    /**
     * Get a specific form field from a subsection
     *
     * @return mixed|null
     */
    public function getFormField(string $sectionName, string $subsectionName, string $fieldName)
    {
        $subsection = $this->getSubsection($sectionName, $subsectionName);

        return $subsection?->getFormField($fieldName);
    }

    /**
     * Get all Litepie/Form fields from all sections
     */
    public function getAllFormFields(): array
    {
        $fields = [];
        $this->collectFormFieldsRecursive($this->sections, $fields);

        return $fields;
    }

    /**
     * Helper to recursively collect form fields from sections
     */
    protected function collectFormFieldsRecursive(array $sections, array &$fields): void
    {
        foreach ($sections as $section) {
            // If it's a FormComponent, get its form fields
            if ($section instanceof FormComponent) {
                foreach ($section->getFormFields() as $field) {
                    $fields[] = $field;
                }
            }

            // Recurse into any section's nested sections (infinite nesting support)
            if (method_exists($section, 'getSections') && method_exists($section, 'hasSections')) {
                if ($section->hasSections()) {
                    $this->collectFormFieldsRecursive($section->getSections(), $fields);
                }
            }

            // If section has slots, recurse into each slot's components
            if (method_exists($section, 'getSlots')) {
                foreach ($section->getSlots() as $slot) {
                    if (method_exists($slot, 'getComponents')) {
                        $this->collectFormFieldsRecursive($slot->getComponents(), $fields);
                    }
                }
            }

            // Support legacy Section/Subsection structure
            if ($section instanceof Section) {
                foreach ($section->getSubsections() as $subsection) {
                    foreach ($subsection->getFormFields() as $field) {
                        $fields[] = $field;
                    }
                }
            }
        }
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Resolve authorization for all sections, subsections, and fields
     */
    public function resolveAuthorization($user = null): self
    {
        $this->resolveSectionAuthorization($this->sections, $user);

        return $this;
    }

    /**
     * Helper to recursively resolve authorization for sections
     */
    protected function resolveSectionAuthorization(array $sections, $user): void
    {
        foreach ($sections as $section) {
            if (method_exists($section, 'resolveAuthorization')) {
                $section->resolveAuthorization($user);
            }

            // Recurse into nested sections
            if ($section instanceof GridSection && method_exists($section, 'getSections')) {
                $this->resolveSectionAuthorization($section->getSections(), $user);
            }

            // Recurse into nested sections through slots
            if (method_exists($section, 'getSlots')) {
                foreach ($section->getSlots() as $slot) {
                    if (method_exists($slot, 'getComponents')) {
                        $this->resolveSectionAuthorization($slot->getComponents(), $user);
                    }
                }
            }

            // Support legacy Section/Subsection structure
            if ($section instanceof Section) {
                foreach ($section->getSubsections() as $subsection) {
                    if (method_exists($subsection, 'resolveAuthorization')) {
                        $subsection->resolveAuthorization($user);
                    }
                }
            }
        }
    }

    /**
     * Get only authorized sections
     */
    public function getAuthorizedSections(): array
    {
        return array_filter(
            $this->sections,
            fn ($section) => ! method_exists($section, 'isAuthorizedToSee') || $section->isAuthorizedToSee()
        );
    }

    public function toArray(): array
    {
        return [
            'module' => $this->module,
            'context' => $this->context,
            'shared_data_url' => $this->sharedDataUrl,
            'shared_data_params' => $this->sharedDataParams,
            'sections' => array_map(
                fn ($section) => method_exists($section, 'toArray') ? $section->toArray() : (array) $section,
                $this->sections
            ),
            'meta' => $this->meta,
        ];
    }

    /**
     * Convert to array with only authorized sections
     */
    public function toAuthorizedArray(): array
    {
        $sections = [];
        foreach ($this->getAuthorizedSections() as $key => $section) {
            if (method_exists($section, 'toArray')) {
                $sectionArray = $section->toArray();
                $sections[$key] = $this->filterAuthorizedRecursive($sectionArray);
            } else {
                $sections[$key] = (array) $section;
            }
        }

        return [
            'module' => $this->module,
            'context' => $this->context,
            'shared_data_url' => $this->sharedDataUrl,
            'shared_data_params' => $this->sharedDataParams,
            'sections' => $sections,
            'meta' => $this->meta,
        ];
    }

    /**
     * Helper to recursively filter authorized items from section arrays
     */
    protected function filterAuthorizedRecursive(array $data): array
    {
        // Filter tabs
        if (isset($data['tabs']) && is_array($data['tabs'])) {
            $data['tabs'] = array_filter($data['tabs'], fn ($tab) => $tab['authorized'] ?? true);
            foreach ($data['tabs'] as &$tab) {
                if (isset($tab['sections']) && is_array($tab['sections'])) {
                    $tab['sections'] = array_map(
                        fn ($section) => $this->filterAuthorizedRecursive($section),
                        array_filter($tab['sections'], fn ($section) => $section['authorized_to_see'] ?? true)
                    );
                }
            }
        }

        // Filter accordion items
        if (isset($data['items']) && is_array($data['items'])) {
            $data['items'] = array_filter($data['items'], fn ($item) => $item['authorized'] ?? true);
            foreach ($data['items'] as &$item) {
                if (isset($item['sections']) && is_array($item['sections'])) {
                    $item['sections'] = array_map(
                        fn ($section) => $this->filterAuthorizedRecursive($section),
                        array_filter($item['sections'], fn ($section) => $section['authorized_to_see'] ?? true)
                    );
                }
            }
        }

        // Filter nested sections
        if (isset($data['sections']) && is_array($data['sections'])) {
            $data['sections'] = array_filter($data['sections'], fn ($section) => $section['authorized'] ?? true);
            foreach ($data['sections'] as &$section) {
                if (is_array($section)) {
                    $section = $this->filterAuthorizedRecursive($section);
                }
            }
        }

        // Filter subsections (legacy support)
        if (isset($data['subsections']) && is_array($data['subsections'])) {
            $data['subsections'] = array_filter(
                $data['subsections'],
                fn ($sub) => $sub['authorized_to_see'] ?? true
            );

            // Filter authorized fields within subsections
            foreach ($data['subsections'] as &$subsection) {
                if (! empty($subsection['fields'])) {
                    $subsection['fields'] = array_filter(
                        $subsection['fields'],
                        fn ($field) => $field['authorized_to_see'] ?? true
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Get layout for a specific user with authorization resolved
     */
    public function forUser($user): self
    {
        return $this->resolveAuthorization($user);
    }

    /**
     * Get a Litepie/Form field by name from anywhere in the layout
     *
     * @return mixed|null
     */
    public function getFormFieldByName(string $name)
    {
        foreach ($this->getAllFormFields() as $field) {
            if (method_exists($field, 'getName') && $field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }

    public function render(): array
    {
        return $this->toArray();
    }
}
