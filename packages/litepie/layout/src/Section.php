<?php

namespace Litepie\Layout;

use Litepie\Layout\Contracts\Renderable;

class Section implements Renderable
{
    protected string $name;

    protected ?string $label = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected array $subsections = [];

    protected ?int $order = null;

    protected bool $visible = true;

    protected array $meta = [];

    protected array $actions = [];

    protected array $modals = [];

    protected ?LayoutBuilder $builder = null;

    // Authorization properties
    protected array $permissions = [];

    protected array $roles = [];

    protected ?\Closure $canSeeCallback = null;

    protected bool $authorizedToSee = true;

    // Column layout
    protected int $columns = 1;

    protected string $gap = 'md';

    // Conditional visibility
    protected array $visibleConditions = [];

    public function __construct(string $name, ?LayoutBuilder $builder = null)
    {
        $this->name = $name;
        $this->builder = $builder;
    }

    public static function make(string $name): self
    {
        return new static($name);
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

    public function subsection(string $name): Subsection
    {
        $subsection = new Subsection($name, $this);
        $this->subsections[$name] = $subsection;

        return $subsection;
    }

    public function addSubsection(Subsection $subsection): self
    {
        $this->subsections[$subsection->getName()] = $subsection;

        return $this;
    }

    public function order(int $order): self
    {
        $this->order = $order;

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
     * Set number of columns for subsections layout
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

    // Conditional visibility for section

    /**
     * Show section only when another field has a specific value
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
            'class' => 'btn btn-primary',
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

        // Resolve authorization for subsections
        foreach ($this->subsections as $subsection) {
            if (method_exists($subsection, 'resolveAuthorization')) {
                $subsection->resolveAuthorization($user);
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

    public function endSection(): ?LayoutBuilder
    {
        return $this->builder;
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

    public function getSubsections(): array
    {
        return $this->subsections;
    }

    public function getSubsection(string $name): ?Subsection
    {
        return $this->subsections[$name] ?? null;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function toArray(): array
    {
        $array = [
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'icon' => $this->icon,
            'subsections' => array_map(fn ($subsection) => $subsection->toArray(), $this->subsections),
            'actions' => $this->actions,
            'modals' => array_map(fn ($modal) => $modal->toArray(), $this->modals),
            'order' => $this->order,
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
