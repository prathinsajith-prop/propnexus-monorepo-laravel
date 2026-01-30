<?php

namespace Litepie\Layout\Sections;

/**
 * AccordionSection
 *
 * Collapsible accordion section where each panel has its own content.
 * Panels store sections and components directly without slots.
 *
 * Example:
 *   AccordionSection::make('faq')
 *       ->panel('about', 'About Us', function($section) {
 *           $section->text('about-text')->content('...');
 *       })
 *       ->panel('contact', 'Contact', function($section) {
 *           $section->form('contact-form');
 *       });
 */
class AccordionSection extends BaseSection
{
    protected array $panels = [];
    protected bool $multiple = false;
    protected bool $collapsible = true;
    protected array $expanded = [];
    protected ?string $variant = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'accordion');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Add a panel with callback configuration
     */
    public function panel(string $id, string $label, \Closure $callback, array $options = []): self
    {
        // Store panel metadata
        $this->panels[$id] = [
            'id' => $id,
            'label' => $label,
            'icon' => $options['icon'] ?? null,
            'badge' => $options['badge'] ?? null,
            'disabled' => $options['disabled'] ?? false,
            'visible' => $options['visible'] ?? true,
            'description' => $options['description'] ?? null,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'sections' => [],
            'components' => [],
        ];

        // Set first panel as expanded if none set
        if (empty($this->expanded)) {
            $this->expanded = [$id];
        }

        // Store current sections/components counts
        $beforeSections = count($this->sections);
        $beforeComponents = count($this->components);

        // Execute callback - it will add to $this->sections and $this->components
        $callback($this);

        // Move new sections/components into panel
        $this->panels[$id]['sections'] = array_splice($this->sections, $beforeSections);
        $this->panels[$id]['components'] = array_splice($this->components, $beforeComponents);

        return $this;
    }

    /**
     * Add a panel without content (just metadata)
     */
    public function addPanel(string $id, string $label, array $options = []): self
    {
        $this->panels[$id] = [
            'id' => $id,
            'label' => $label,
            'icon' => $options['icon'] ?? null,
            'badge' => $options['badge'] ?? null,
            'disabled' => $options['disabled'] ?? false,
            'visible' => $options['visible'] ?? true,
            'description' => $options['description'] ?? null,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'sections' => [],
            'components' => [],
        ];

        if (empty($this->expanded)) {
            $this->expanded = [$id];
        }

        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function allowMultiple(bool $allow = true): self
    {
        return $this->multiple($allow);
    }

    public function collapsible(bool $collapsible = true): self
    {
        $this->collapsible = $collapsible;
        return $this;
    }

    public function expanded(string $panelId): self
    {
        $this->expanded = [$panelId];
        return $this;
    }

    public function expandedPanels(array $panelIds): self
    {
        $this->expanded = $panelIds;
        return $this;
    }

    public function variant(string $variant): self
    {
        $this->variant = $variant;
        return $this;
    }

    public function getPanels(): array
    {
        return $this->panels;
    }

    public function getPanel(string $id): ?array
    {
        return $this->panels[$id] ?? null;
    }

    public function resolveAuthorization($user = null): self
    {
        parent::resolveAuthorization($user);

        foreach ($this->panels as $id => &$panel) {
            if (!empty($panel['permissions'])) {
                $panel['authorized'] = $this->checkPermissions($user, $panel['permissions']);
            } elseif (!empty($panel['roles'])) {
                $panel['authorized'] = $this->checkRoles($user, $panel['roles']);
            } else {
                $panel['authorized'] = true;
            }

            foreach ($panel['components'] as $component) {
                if (method_exists($component, 'resolveAuthorization')) {
                    $component->resolveAuthorization($user);
                }
            }

            foreach ($panel['sections'] as $section) {
                if (method_exists($section, 'resolveAuthorization')) {
                    $section->resolveAuthorization($user);
                }
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $data = $this->getCommonProperties();
        
        $panelsOutput = [];
        foreach ($this->panels as $id => $panel) {
            $panelData = [
                'id' => $panel['id'],
                'label' => $panel['label'],
                'icon' => $panel['icon'],
                'badge' => $panel['badge'],
                'disabled' => $panel['disabled'],
                'visible' => $panel['visible'],
                'description' => $panel['description'],
                'permissions' => $panel['permissions'],
                'roles' => $panel['roles'],
                'authorized' => $panel['authorized'] ?? true,
            ];

            if (!empty($panel['sections'])) {
                $panelData['sections'] = array_map(function($section) {
                    return method_exists($section, 'toArray') ? $section->toArray() : (array)$section;
                }, $panel['sections']);
            }

            if (!empty($panel['components'])) {
                $panelData['components'] = array_map(function($component) {
                    return method_exists($component, 'toArray') ? $component->toArray() : (array)$component;
                }, $panel['components']);
            }

            $panelsOutput[] = $panelData;
        }

        return array_merge($data, [
            'panels' => $panelsOutput,
            'multiple' => $this->multiple,
            'collapsible' => $this->collapsible,
            'expanded' => $this->expanded,
            'variant' => $this->variant,
            'permissions' => $this->permissions ?? [],
            'roles' => $this->roles ?? [],
            'authorized_to_see' => $this->authorizedToSee ?? null,
        ]);
    }
}
