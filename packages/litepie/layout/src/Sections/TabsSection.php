<?php

namespace Litepie\Layout\Sections;

/**
 * TabsSection
 *
 * Tabbed interface section. Each tab stores its content directly.
 * No slots - tabs store sections and components in arrays.
 *
 * Example:
 *   TabsSection::make('user-tabs')
 *       ->tab('profile', 'Profile', function($section) {
 *           $section->form('profile-form');
 *       }, ['icon' => 'user'])
 *       ->tab('settings', 'Settings', function($section) {
 *           $section->form('settings-form');
 *       }, ['icon' => 'cog']);
 */
class TabsSection extends BaseSection
{
    protected array $tabs = [];
    protected ?string $activeTab = null;
    protected string $position = 'top'; // top, left, right, bottom
    protected bool $lazy = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'tabs');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Add a tab with callback configuration
     */
    public function tab(string $id, string $label, \Closure $callback, array $options = []): self
    {
        // Store tab metadata
        $this->tabs[$id] = [
            'id' => $id,
            'label' => $label,
            'icon' => $options['icon'] ?? null,
            'badge' => $options['badge'] ?? null,
            'disabled' => $options['disabled'] ?? false,
            'visible' => $options['visible'] ?? true,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'sections' => [],
            'components' => [],
        ];

        // Set first tab as active if none set
        if ($this->activeTab === null) {
            $this->activeTab = $id;
        }

        // Store current sections/components counts
        $beforeSections = count($this->sections);
        $beforeComponents = count($this->components);

        // Execute callback - it will add to $this->sections and $this->components
        $callback($this);

        // Move new sections/components into tab
        $this->tabs[$id]['sections'] = array_splice($this->sections, $beforeSections);
        $this->tabs[$id]['components'] = array_splice($this->components, $beforeComponents);

        return $this;
    }

    /**
     * Add a tab without content (just metadata)
     */
    public function addTab(string $id, string $label, array $options = []): self
    {
        $this->tabs[$id] = [
            'id' => $id,
            'label' => $label,
            'icon' => $options['icon'] ?? null,
            'badge' => $options['badge'] ?? null,
            'disabled' => $options['disabled'] ?? false,
            'visible' => $options['visible'] ?? true,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'sections' => [],
            'components' => [],
        ];

        if ($this->activeTab === null) {
            $this->activeTab = $id;
        }

        return $this;
    }

    public function activeTab(string $tabId): self
    {
        $this->activeTab = $tabId;
        return $this;
    }

    public function position(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function lazy(bool $lazy = true): self
    {
        $this->lazy = $lazy;
        return $this;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function getTab(string $id): ?array
    {
        return $this->tabs[$id] ?? null;
    }

    public function resolveAuthorization($user = null): self
    {
        parent::resolveAuthorization($user);

        foreach ($this->tabs as $id => &$tab) {
            if (!empty($tab['permissions'])) {
                $tab['authorized'] = $this->checkPermissions($user, $tab['permissions']);
            } elseif (!empty($tab['roles'])) {
                $tab['authorized'] = $this->checkRoles($user, $tab['roles']);
            } else {
                $tab['authorized'] = true;
            }

            foreach ($tab['components'] as $component) {
                if (method_exists($component, 'resolveAuthorization')) {
                    $component->resolveAuthorization($user);
                }
            }

            foreach ($tab['sections'] as $section) {
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
        
        $tabsOutput = [];
        foreach ($this->tabs as $id => $tab) {
            $tabData = [
                'id' => $tab['id'],
                'label' => $tab['label'],
                'icon' => $tab['icon'],
                'badge' => $tab['badge'],
                'disabled' => $tab['disabled'],
                'visible' => $tab['visible'],
                'permissions' => $tab['permissions'],
                'roles' => $tab['roles'],
                'authorized' => $tab['authorized'] ?? true,
            ];

            if (!empty($tab['sections'])) {
                $tabData['sections'] = array_map(function($section) {
                    return method_exists($section, 'toArray') ? $section->toArray() : (array)$section;
                }, $tab['sections']);
            }

            if (!empty($tab['components'])) {
                $tabData['components'] = array_map(function($component) {
                    return method_exists($component, 'toArray') ? $component->toArray() : (array)$component;
                }, $tab['components']);
            }

            $tabsOutput[] = $tabData;
        }

        return array_merge($data, [
            'tabs' => $tabsOutput,
            'activeTab' => $this->activeTab,
            'position' => $this->position,
            'lazy' => $this->lazy,
            'permissions' => $this->permissions ?? [],
            'roles' => $this->roles ?? [],
            'authorized_to_see' => $this->authorizedToSee ?? null,
        ]);
    }
}
