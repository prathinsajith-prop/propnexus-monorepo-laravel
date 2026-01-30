<?php

namespace Litepie\Layout;

use Litepie\Layout\Contracts\Renderable;
use Litepie\Layout\Contracts\Component;

class Subsection implements Component
{
    protected string $name;

    protected ?string $type = 'subsection';

    protected ?string $label = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected array $formFields = []; // Litepie/Form field instances

    protected array $components = []; // Layout components

    protected ?int $order = null;

    protected bool $collapsible = false;

    protected bool $collapsed = false;

    protected bool $visible = true;

    protected array $meta = [];

    protected array $actions = [];

    protected array $modals = [];

    protected ?Section $parent = null;

    public mixed $parentBuilder = null;

    // Authorization properties
    protected array $permissions = [];

    protected array $roles = [];

    protected ?\Closure $canSeeCallback = null;

    protected bool $authorizedToSee = true;

    // Column layout
    protected int $columns = 1;

    protected string $gap = 'md'; // xs, sm, md, lg, xl

    // Conditional visibility
    protected array $visibleConditions = [];

    public function __construct(string $name, ?Section $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Get the component type
     */
    public function getType(): string
    {
        return $this->type ?? 'subsection';
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Add a Litepie/Form field instance to this subsection
     *
     * @param  \Litepie\Form\Field  $field
     */
    public function addFormField($field): self
    {
        if (method_exists($field, 'getName')) {
            $this->formFields[$field->getName()] = $field;
        } else {
            $this->formFields[] = $field;
        }

        return $this;
    }

    /**
     * Add multiple Litepie/Form fields
     */
    public function addFormFields(array $fields): self
    {
        foreach ($fields as $field) {
            $this->addFormField($field);
        }

        return $this;
    }

    public function order(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function collapsible(bool $collapsible = true): self
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): self
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    public function visible(bool $visible = true): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function hidden(): self
    {
        return $this->visible(false);
    }

    // Column layout methods

    /**
     * Set number of columns for the subsection layout
     */
    public function columns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set gap between columns
     */
    public function gap(string $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * Get number of columns
     */
    public function getColumns(): int
    {
        return $this->columns;
    }

    /**
     * Get gap between columns
     */
    public function getGap(): string
    {
        return $this->gap;
    }

    // Conditional visibility for subsection

    /**
     * Show subsection only when another field has a specific value
     */
    public function visibleWhen(string $field, string $operator, mixed $value = null): self
    {
        $this->visibleConditions[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Get visible conditions
     */
    public function getVisibleConditions(): array
    {
        return $this->visibleConditions;
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function action(string $label, string $url, array $options = []): self
    {
        $this->actions[] = array_merge([
            'label' => $label,
            'url' => $url,
            'type' => 'button',
            'class' => 'btn btn-secondary',
            'method' => 'GET',
        ], $options);

        return $this;
    }

    public function addAction(array $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function modal(string $id): \Litepie\Layout\ActionModal
    {
        $modal = new \Litepie\Layout\ActionModal($id);
        $this->modals[$id] = $modal;

        return $modal;
    }

    public function addModal(\Litepie\Layout\ActionModal $modal): self
    {
        $this->modals[$modal->getId()] = $modal;

        return $this;
    }

    public function getModals(): array
    {
        return $this->modals;
    }

    public function getModal(string $id): ?\Litepie\Layout\ActionModal
    {
        return $this->modals[$id] ?? null;
    }

    // Authorization methods

    public function permissions(array|string $permissions): self
    {
        $this->permissions = is_array($permissions) ? $permissions : [$permissions];

        return $this;
    }

    public function roles(array|string $roles): self
    {
        $this->roles = is_array($roles) ? $roles : [$roles];

        return $this;
    }

    public function canSee(\Closure $callback): self
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    public function visibleForPermissions(array|string $permissions): self
    {
        return $this->permissions($permissions);
    }

    public function visibleForRoles(array|string $roles): self
    {
        return $this->roles($roles);
    }

    public function resolveAuthorization($user = null): self
    {
        if ($this->canSeeCallback !== null) {
            $this->authorizedToSee = call_user_func($this->canSeeCallback, $user);
        }

        if (! empty($this->permissions) && $user !== null) {
            $this->authorizedToSee = $this->checkPermissions($user, $this->permissions);
        }

        if (! empty($this->roles) && $user !== null) {
            $this->authorizedToSee = $this->checkRoles($user, $this->roles);
        }

        // Resolve authorization for form fields
        foreach ($this->formFields as $field) {
            if (method_exists($field, 'resolveAuthorization')) {
                $field->resolveAuthorization($user);
            }
        }

        return $this;
    }

    protected function checkPermissions($user, array $permissions): bool
    {
        if (method_exists($user, 'hasAnyPermission')) {
            return $user->hasAnyPermission($permissions);
        }
        if (method_exists($user, 'can')) {
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function checkRoles($user, array $roles): bool
    {
        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($roles);
        }
        if (method_exists($user, 'hasRole')) {
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return true;
                }
            }
        }
        if (isset($user->role)) {
            return in_array($user->role, $roles);
        }

        return false;
    }

    public function isAuthorizedToSee(): bool
    {
        return $this->authorizedToSee;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * End field chaining and return to subsection level
     */
    public function endField(): self
    {
        return $this;
    }

    public function endSubsection(): ?Section
    {
        return $this->parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get all Litepie/Form fields in this subsection
     */
    public function getFormFields(): array
    {
        return $this->formFields;
    }

    /**
     * Get a specific Litepie/Form field by name
     *
     * @return mixed|null
     */
    public function getFormField(string $name)
    {
        return $this->formFields[$name] ?? null;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function isCollapsible(): bool
    {
        return $this->collapsible;
    }

    public function isCollapsed(): bool
    {
        return $this->collapsed;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    // ========================================================================
    // Component creation methods (similar to Slot)
    // ========================================================================

    /**
     * Add a component to this subsection
     */
    public function add($component): self
    {
        if (is_object($component) && property_exists($component, 'parentBuilder')) {
            $component->parentBuilder = $this;
        }
        $this->components[] = $component;

        return $this;
    }

    /**
     * Create and add a component by type
     */
    public function component(string $type, string $name)
    {
        // Convert kebab-case to PascalCase
        $className = str_replace('-', '', ucwords($type, '-'));

        // Try Components namespace
        $componentClass = 'Litepie\\Layout\\Components\\' . $className . 'Component';
        if (class_exists($componentClass)) {
            $component = $componentClass::make($name);
            $this->add($component);
            return $component;
        }

        return $this->custom($type, $name);
    }

    /**
     * Create and add a TextComponent
     */
    public function text(string $name)
    {
        return $this->component('text', $name);
    }

    /**
     * Create and add a CardComponent
     */
    public function card(string $name)
    {
        return $this->component('card', $name);
    }

    /**
     * Create and add a FormComponent
     */
    public function form(string $name)
    {
        return $this->component('form', $name);
    }

    /**
     * Create and add a DividerComponent
     */
    public function divider(string $name)
    {
        return $this->component('divider', $name);
    }

    /**
     * Create and add an AvatarComponent
     */
    public function avatar(string $name)
    {
        return $this->component('avatar', $name);
    }

    /**
     * Create and add a BadgeComponent
     */
    public function badge(string $name)
    {
        return $this->component('badge', $name);
    }

    /**
     * Create and add an AlertComponent
     */
    public function alert(string $name)
    {
        return $this->component('alert', $name);
    }


    /**
     * Create and add a StepperComponent
     */
    public function stepper(string $name)
    {
        return $this->component('stepper', $name);
    }

    /**
     * Create and add a TableComponent
     */
    public function table(string $name)
    {
        return $this->component('table', $name);
    }

    /**
     * Create and add a ListComponent
     */
    public function list(string $name)
    {
        return $this->component('list', $name);
    }

    /**
     * Create and add a nested Subsection
     */
    public function subsection(string $name, ?\Closure $callback = null): Subsection
    {
        $subsection = new Subsection($name, null);
        $this->add($subsection);

        if ($callback) {
            $callback($subsection);
        }

        return $subsection;
    }

    /**
     * Create and add a MediaComponent with video type
     */
    public function videoPlayer(string $name)
    {
        $component = $this->component('media', $name);
        $component->video(); // Set media type to video

        return $component;
    }

    /**
     * Create and add a CustomComponent
     */
    public function custom(string $type, string $name)
    {
        $class = 'Litepie\\Layout\\Components\\CustomComponent';
        if (class_exists($class)) {
            $component = $class::make($name, $type);
            $this->add($component);
            return $component;
        }

        throw new \RuntimeException("CustomComponent class not found and cannot create component of type '{$type}'");
    }

    /**
     * Get all components in this subsection
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    public function toArray(): array
    {
        $array = [
            'name' => $this->name,
            'type' => $this->getType(),
            'label' => $this->label,
            'description' => $this->description,
            'icon' => $this->icon,
            'fields' => array_map(fn($field) => method_exists($field, 'toArray') ? $field->toArray() : (array) $field, $this->formFields),
            'components' => array_map(fn($comp) => method_exists($comp, 'toArray') ? $comp->toArray() : (array) $comp, $this->components),
            'actions' => $this->actions,
            'modals' => array_map(fn($modal) => $modal->toArray(), $this->modals),
            'order' => $this->order,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'visible' => $this->visible,
            'meta' => $this->meta,
            'permissions' => $this->permissions,
            'roles' => $this->roles,
            'authorized_to_see' => $this->authorizedToSee,
        ];

        // Column layout
        if ($this->columns > 1) {
            $array['columns'] = $this->columns;
            $array['gap'] = $this->gap;
        }

        // Conditional visibility
        if (! empty($this->visibleConditions)) {
            $array['visible_conditions'] = $this->visibleConditions;
        }

        return $array;
    }

    public function render(): array
    {
        return $this->toArray();
    }
}
