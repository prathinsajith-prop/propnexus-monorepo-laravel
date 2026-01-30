<?php

namespace Litepie\Layout\Sections;

class ScrollSpySection extends BaseSection
{
    protected array $spySections = [];

    protected string $position = 'left'; // left or right

    protected bool $sticky = true;

    protected int $offset = 80; // Scroll offset for active state

    // ScrollSpy uses dynamic slots for each spy section
    // allowedSlots is empty to allow dynamic slots

    public function __construct(string $name)
    {
        parent::__construct($name, 'scrollspy');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Add a scrollspy section with components
     */
    public function addSpySection(string $id, string $label, array $components = [], array $options = []): self
    {
        $this->spySections[$id] = [
            'id' => $id,
            'label' => $label,
            'components' => $components,
            'icon' => $options['icon'] ?? null,
            'visible' => $options['visible'] ?? true,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'description' => $options['description'] ?? null,
        ];

        return $this;
    }

    /**
     * Set navigation position
     */
    public function position(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Set whether navigation is sticky
     */
    public function sticky(bool $sticky = true): self
    {
        $this->sticky = $sticky;

        return $this;
    }

    /**
     * Set scroll offset for active state detection
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get all scrollspy sections
     */
    public function getSpySections(): array
    {
        return $this->spySections;
    }

    /**
     * Get a specific scrollspy section
     */
    public function getSpySection(string $id): ?array
    {
        return $this->spySections[$id] ?? null;
    }

    /**
     * Resolve authorization for sections and their components
     */
    public function resolveAuthorization($user = null): self
    {
        parent::resolveAuthorization($user);

        foreach ($this->spySections as &$section) {
            // Check section-level permissions
            if (! empty($section['permissions'])) {
                $section['authorized'] = $this->checkPermissions($user, $section['permissions']);
            } elseif (! empty($section['roles'])) {
                $section['authorized'] = $this->checkRoles($user, $section['roles']);
            } else {
                $section['authorized'] = true;
            }

            // Resolve authorization for components in the section
            if (! empty($section['components'])) {
                foreach ($section['components'] as $component) {
                    if (method_exists($component, 'resolveAuthorization')) {
                        $component->resolveAuthorization($user);
                    }
                }
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $sections = [];
        foreach ($this->spySections as $section) {
            $sections[] = [
                'id' => $section['id'],
                'label' => $section['label'],
                'icon' => $section['icon'],
                'visible' => $section['visible'],
                'authorized' => $section['authorized'] ?? true,
                'description' => $section['description'],
                'components' => array_map(
                    fn ($comp) => method_exists($comp, 'toArray') ? $comp->toArray() : (array) $comp,
                    $section['components']
                ),
                'permissions' => $section['permissions'],
                'roles' => $section['roles'],
            ];
        }

        return [
            'type' => $this->type,
            'name' => $this->name,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon,
            'sections' => $sections,
            'position' => $this->position,
            'sticky' => $this->sticky,
            'offset' => $this->offset,
            'actions' => $this->actions,
            'order' => $this->order,
            'visible' => $this->visible,
            'permissions' => $this->permissions,
            'roles' => $this->roles,
            'authorized_to_see' => $this->authorizedToSee,
            'meta' => $this->meta,
        ];
    }
}
