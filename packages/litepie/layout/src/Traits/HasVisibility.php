<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Conditional\ExpressionEvaluator;

/**
 * HasVisibility Trait
 *
 * Provides conditional visibility logic and authorization control.
 * Controls when components are shown/hidden based on conditions or permissions.
 */
trait HasVisibility
{
    protected array $showWhenConditions = [];

    protected array $hideWhenConditions = [];

    protected array $enableWhenConditions = [];

    protected string $conditionLogic = 'AND'; // AND or OR

    protected bool $enabled = true;

    // Authorization
    protected array $permissions = [];

    protected array $roles = [];

    protected ?\Closure $canSeeCallback = null;

    protected bool $authorizedToSee = true;

    /**
     * Show component when condition is met
     *
     * Usage:
     * ->showWhen('user.role', '==', 'admin')
     * ->showWhen('user.role == admin') // string expression
     * ->showWhen(['field' => 'user.role', 'operator' => '==', 'value' => 'admin']) // array
     */
    public function showWhen(string|array $field, ?string $operator = null, mixed $value = null): self
    {
        $this->showWhenConditions[] = $this->normalizeCondition($field, $operator, $value);

        return $this;
    }

    /**
     * Hide component when condition is met
     */
    public function hideWhen(string|array $field, ?string $operator = null, mixed $value = null): self
    {
        $this->hideWhenConditions[] = $this->normalizeCondition($field, $operator, $value);

        return $this;
    }

    /**
     * Enable component when condition is met
     */
    public function enableWhen(string|array $field, ?string $operator = null, mixed $value = null): self
    {
        $this->enableWhenConditions[] = $this->normalizeCondition($field, $operator, $value);

        return $this;
    }

    /**
     * Set condition logic (AND/OR)
     */
    public function conditionLogic(string $logic): self
    {
        $this->conditionLogic = strtoupper($logic);

        return $this;
    }

    /**
     * Set required permissions
     */
    public function permissions(array|string $permissions): self
    {
        $this->permissions = is_array($permissions) ? $permissions : [$permissions];

        return $this;
    }

    /**
     * Set required roles
     */
    public function roles(array|string $roles): self
    {
        $this->roles = is_array($roles) ? $roles : [$roles];

        return $this;
    }

    /**
     * Set custom authorization callback
     */
    public function canSee(\Closure $callback): self
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    /**
     * Resolve authorization for given user
     */
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

        return $this;
    }

    /**
     * Check if user has required permissions
     */
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

    /**
     * Check if user has required roles
     */
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

    /**
     * Check if component is authorized to be seen
     */
    public function isAuthorizedToSee(): bool
    {
        return $this->authorizedToSee;
    }

    /**
     * Get required permissions
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Get required roles
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Evaluate all conditions against context
     */
    public function evaluateConditions(array $context): void
    {
        $evaluator = new ExpressionEvaluator;

        // Evaluate show conditions
        if (! empty($this->showWhenConditions)) {
            $this->visible = $evaluator->evaluateMultiple(
                $this->showWhenConditions,
                $context,
                $this->conditionLogic
            );
        }

        // Evaluate hide conditions
        if (! empty($this->hideWhenConditions)) {
            $shouldHide = $evaluator->evaluateMultiple(
                $this->hideWhenConditions,
                $context,
                $this->conditionLogic
            );

            if ($shouldHide) {
                $this->visible = false;
            }
        }

        // Evaluate enable conditions
        if (! empty($this->enableWhenConditions)) {
            $this->enabled = $evaluator->evaluateMultiple(
                $this->enableWhenConditions,
                $context,
                $this->conditionLogic
            );
        }
    }

    /**
     * Normalize condition to array format
     */
    protected function normalizeCondition(string|array $field, ?string $operator = null, mixed $value = null): array
    {
        // If already an array, return as-is
        if (is_array($field)) {
            return $field;
        }

        // If it's a string expression, parse it
        if ($operator === null && $value === null) {
            $evaluator = new ExpressionEvaluator;
            $parsed = $evaluator->parseExpression($field);

            if ($parsed) {
                return $parsed;
            }
        }

        // Otherwise, build from parameters
        return [
            'field' => $field,
            'operator' => $operator ?? '==',
            'value' => $value,
        ];
    }

    /**
     * Add conditional logic to array output
     */
    protected function addConditionalToArray(array $array): array
    {
        if (! empty($this->showWhenConditions)) {
            $array['show_when'] = $this->showWhenConditions;
        }

        if (! empty($this->hideWhenConditions)) {
            $array['hide_when'] = $this->hideWhenConditions;
        }

        if (! empty($this->enableWhenConditions)) {
            $array['enable_when'] = $this->enableWhenConditions;
        }

        if (! empty($this->showWhenConditions) || ! empty($this->hideWhenConditions) || ! empty($this->enableWhenConditions)) {
            $array['condition_logic'] = $this->conditionLogic;
        }

        $array['enabled'] = $this->enabled;

        return $array;
    }

    /**
     * Check if component is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
