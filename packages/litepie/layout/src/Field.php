<?php

namespace Litepie\Layout;

use Litepie\Layout\Contracts\Renderable;
use Litepie\Layout\Traits\EvaluatesConditions;

class Field implements Renderable
{
    use EvaluatesConditions;

    protected string $name;

    protected string $type = 'text';

    protected ?string $label = null;

    protected ?string $placeholder = null;

    protected ?string $description = null;

    protected mixed $default = null;

    protected bool $required = false;

    protected bool $readonly = false;

    protected bool $disabled = false;

    protected ?int $maxLength = null;

    protected ?int $minLength = null;

    protected array $options = [];

    protected array $rules = [];

    protected array $attributes = [];

    protected array $meta = [];

    protected ?int $order = null;

    protected ?string $group = null;

    protected bool $visible = true;

    // Enhanced field properties
    protected ?string $prefix = null;

    protected ?string $suffix = null;

    protected ?string $mask = null;

    protected ?string $transform = null;

    protected bool $copyable = false;

    protected bool $clickable = false;

    protected ?int $decimals = null;

    protected bool $searchable = false;

    protected ?string $dataSource = null;

    protected ?string $format = null;

    protected ?string $displayFormat = null;

    protected bool $showRelative = false;

    protected bool $showAge = false;

    protected ?string $timezone = null;

    protected ?string $currency = null;

    protected ?string $currencySymbol = null;

    protected bool $showTags = false;

    protected ?string $layout = null;

    protected ?string $color = null;

    protected ?string $icon = null;

    protected array $colorMap = [];

    protected ?string $relationship = null;

    protected ?string $relationshipModel = null;

    protected bool $showPreview = false;

    protected bool $allowCrop = false;

    protected bool $allowResize = false;

    protected array $cropRatios = [];

    protected array $resizeDimensions = [];

    protected array $presetColors = [];

    protected ?string $editor = null;

    protected ?string $syntax = null;

    protected ?string $mapProvider = null;

    protected ?string $apiKey = null;

    protected array $compositeFields = [];

    // Authorization properties
    protected array $permissions = [];

    protected array $roles = [];

    protected ?\Closure $canSeeCallback = null;

    protected ?\Closure $canEditCallback = null;

    protected bool $authorizedToSee = true;

    protected bool $authorizedToEdit = true;

    // Conditional visibility & dependencies
    protected array $visibleConditions = [];

    protected array $requiredConditions = [];

    protected array $disabledConditions = [];

    protected ?string $dependsOnField = null;

    protected ?string $dependsOnUrl = null;

    protected array $dependsOnParams = [];

    // Computed field
    protected ?\Closure $computedCallback = null;

    protected bool $isComputed = false;

    // Layout
    protected ?int $columnSpan = null;

    protected ?string $columnStart = null;

    // Help & UI
    protected ?string $help = null;

    protected ?string $tooltip = null;

    protected ?string $example = null;

    protected ?string $loadingText = null;

    protected ?string $confirmMessage = null;

    protected array $validationMessages = [];

    // Audit/History
    protected bool $trackChanges = false;

    protected bool $showHistory = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function default(mixed $value): self
    {
        $this->default = $value;

        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        if ($required && ! in_array('required', $this->rules)) {
            $this->rules[] = 'required';
        }

        return $this;
    }

    public function readonly(bool $readonly = true): self
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function maxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;
        $this->rules[] = "max:{$maxLength}";

        return $this;
    }

    public function minLength(int $minLength): self
    {
        $this->minLength = $minLength;
        $this->rules[] = "min:{$minLength}";

        return $this;
    }

    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function rules(array|string $rules): self
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    public function attributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function attribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function order(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function group(string $group): self
    {
        $this->group = $group;

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

    // Enhanced field methods
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function mask(string $mask): self
    {
        $this->mask = $mask;

        return $this;
    }

    public function transform(string $transform): self
    {
        $this->transform = $transform;

        return $this;
    }

    public function copyable(bool $copyable = true): self
    {
        $this->copyable = $copyable;

        return $this;
    }

    public function clickable(bool $clickable = true): self
    {
        $this->clickable = $clickable;

        return $this;
    }

    public function decimals(int $decimals): self
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function dataSource(string $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function displayFormat(string $displayFormat): self
    {
        $this->displayFormat = $displayFormat;

        return $this;
    }

    public function showRelative(bool $showRelative = true): self
    {
        $this->showRelative = $showRelative;

        return $this;
    }

    public function showAge(bool $showAge = true): self
    {
        $this->showAge = $showAge;

        return $this;
    }

    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function currency(string $currency, ?string $symbol = null): self
    {
        $this->currency = $currency;
        $this->currencySymbol = $symbol;

        return $this;
    }

    public function showTags(bool $showTags = true): self
    {
        $this->showTags = $showTags;

        return $this;
    }

    public function layout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function colorMap(array $colorMap): self
    {
        $this->colorMap = $colorMap;

        return $this;
    }

    public function relationship(string $type, string $model): self
    {
        $this->relationship = $type;
        $this->relationshipModel = $model;

        return $this;
    }

    public function showPreview(bool $showPreview = true): self
    {
        $this->showPreview = $showPreview;

        return $this;
    }

    public function allowCrop(bool $allowCrop = true, array $ratios = []): self
    {
        $this->allowCrop = $allowCrop;
        $this->cropRatios = $ratios;

        return $this;
    }

    public function allowResize(bool $allowResize = true, array $dimensions = []): self
    {
        $this->allowResize = $allowResize;
        $this->resizeDimensions = $dimensions;

        return $this;
    }

    public function presetColors(array $colors): self
    {
        $this->presetColors = $colors;

        return $this;
    }

    public function editor(string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function syntax(string $syntax): self
    {
        $this->syntax = $syntax;

        return $this;
    }

    public function mapProvider(string $provider, ?string $apiKey = null): self
    {
        $this->mapProvider = $provider;
        $this->apiKey = $apiKey;

        return $this;
    }

    public function compositeFields(array $fields): self
    {
        $this->compositeFields = $fields;

        return $this;
    }

    public function addCompositeField(Field $field): self
    {
        $this->compositeFields[] = $field;

        return $this;
    }

    // ==========================================
    // Conditional Visibility & Dependencies
    // ==========================================

    /**
     * Show field only when another field has a specific value
     *
     * @param  string  $field  The field name to check
     * @param  string  $operator  Comparison operator: =, !=, >, <, >=, <=, in, not_in, contains, empty, not_empty
     * @param  mixed  $value  The value to compare against
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
     * Hide field when another field has a specific value
     */
    public function hiddenWhen(string $field, string $operator, mixed $value = null): self
    {
        $this->visibleConditions[] = [
            'field' => $field,
            'operator' => $this->invertOperator($operator),
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Make field required when another field has a specific value
     */
    public function requiredWhen(string $field, string $operator, mixed $value = null): self
    {
        $this->requiredConditions[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Make field optional when another field has a specific value
     */
    public function optionalWhen(string $field, string $operator, mixed $value = null): self
    {
        $this->requiredConditions[] = [
            'field' => $field,
            'operator' => $this->invertOperator($operator),
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Disable field when another field has a specific value
     */
    public function disabledWhen(string $field, string $operator, mixed $value = null): self
    {
        $this->disabledConditions[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Enable field when another field has a specific value
     */
    public function enabledWhen(string $field, string $operator, mixed $value = null): self
    {
        $this->disabledConditions[] = [
            'field' => $field,
            'operator' => $this->invertOperator($operator),
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Invert comparison operator
     */
    protected function invertOperator(string $operator): string
    {
        return match ($operator) {
            '=' => '!=',
            '!=' => '=',
            '>' => '<=',
            '<' => '>=',
            '>=' => '<',
            '<=' => '>',
            'in' => 'not_in',
            'not_in' => 'in',
            'empty' => 'not_empty',
            'not_empty' => 'empty',
            'contains' => 'not_contains',
            'not_contains' => 'contains',
            default => $operator,
        };
    }

    /**
     * Field depends on another field for loading options (cascading dropdown)
     */
    public function dependsOn(string $field, ?string $url = null, array $params = []): self
    {
        $this->dependsOnField = $field;
        $this->dependsOnUrl = $url;
        $this->dependsOnParams = $params;

        return $this;
    }

    /**
     * Get visible conditions
     */
    public function getVisibleConditions(): array
    {
        return $this->visibleConditions;
    }

    /**
     * Get required conditions
     */
    public function getRequiredConditions(): array
    {
        return $this->requiredConditions;
    }

    /**
     * Get disabled conditions
     */
    public function getDisabledConditions(): array
    {
        return $this->disabledConditions;
    }

    // ==========================================
    // Computed/Derived Fields
    // ==========================================

    /**
     * Make this a computed field that derives its value from other fields
     */
    public function computed(\Closure $callback): self
    {
        $this->computedCallback = $callback;
        $this->isComputed = true;
        $this->readonly = true; // Computed fields are readonly

        return $this;
    }

    /**
     * Check if field is computed
     */
    public function isComputed(): bool
    {
        return $this->isComputed;
    }

    /**
     * Compute the value based on provided data
     */
    public function computeValue(array $data): mixed
    {
        if ($this->computedCallback === null) {
            return null;
        }

        return call_user_func($this->computedCallback, $data);
    }

    // ==========================================
    // Column Layout
    // ==========================================

    /**
     * Set how many columns this field spans in a grid layout
     */
    public function columnSpan(int $span): self
    {
        $this->columnSpan = $span;

        return $this;
    }

    /**
     * Shorthand for full width
     */
    public function fullWidth(): self
    {
        return $this->columnSpan(12);
    }

    /**
     * Shorthand for half width
     */
    public function halfWidth(): self
    {
        return $this->columnSpan(6);
    }

    /**
     * Shorthand for third width
     */
    public function thirdWidth(): self
    {
        return $this->columnSpan(4);
    }

    /**
     * Shorthand for quarter width
     */
    public function quarterWidth(): self
    {
        return $this->columnSpan(3);
    }

    /**
     * Start this field in a specific column
     */
    public function startColumn(int|string $column): self
    {
        $this->columnStart = (string) $column;

        return $this;
    }

    // ==========================================
    // Help Text & UI Enhancements
    // ==========================================

    /**
     * Add help text below the field
     */
    public function help(string $text): self
    {
        $this->help = $text;

        return $this;
    }

    /**
     * Add tooltip that shows on hover
     */
    public function tooltip(string $text): self
    {
        $this->tooltip = $text;

        return $this;
    }

    /**
     * Add example value to show as hint
     */
    public function example(string $example): self
    {
        $this->example = $example;

        return $this;
    }

    /**
     * Set loading text for async operations
     */
    public function loadingText(string $text): self
    {
        $this->loadingText = $text;

        return $this;
    }

    /**
     * Require confirmation before changing this field
     */
    public function confirmChange(string $message): self
    {
        $this->confirmMessage = $message;

        return $this;
    }

    /**
     * Set custom validation message for a specific rule
     */
    public function validationMessage(string $rule, string $message): self
    {
        $this->validationMessages[$rule] = $message;

        return $this;
    }

    /**
     * Set multiple validation messages
     */
    public function validationMessages(array $messages): self
    {
        $this->validationMessages = array_merge($this->validationMessages, $messages);

        return $this;
    }

    // ==========================================
    // Audit/History Tracking
    // ==========================================

    /**
     * Enable change tracking for this field
     */
    public function trackChanges(bool $track = true): self
    {
        $this->trackChanges = $track;

        return $this;
    }

    /**
     * Show history of changes for this field
     */
    public function showHistory(bool $show = true): self
    {
        $this->showHistory = $show;

        return $this;
    }

    /**
     * Check if tracking is enabled
     */
    public function isTrackingChanges(): bool
    {
        return $this->trackChanges;
    }

    /**
     * Check if history is shown
     */
    public function isShowingHistory(): bool
    {
        return $this->showHistory;
    }

    // ==========================================
    // Getters for new properties
    // ==========================================

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    public function getExample(): ?string
    {
        return $this->example;
    }

    public function getLoadingText(): ?string
    {
        return $this->loadingText;
    }

    public function getConfirmMessage(): ?string
    {
        return $this->confirmMessage;
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    public function getColumnSpan(): ?int
    {
        return $this->columnSpan;
    }

    public function getColumnStart(): ?string
    {
        return $this->columnStart;
    }

    public function getDependsOn(): ?string
    {
        return $this->dependsOnField;
    }

    public function getDependsOnUrl(): ?string
    {
        return $this->dependsOnUrl;
    }

    public function getDependsOnParams(): array
    {
        return $this->dependsOnParams;
    }

    // Authorization methods

    /**
     * Set required permissions to see this field
     */
    public function permissions(array|string $permissions): self
    {
        $this->permissions = is_array($permissions) ? $permissions : [$permissions];

        return $this;
    }

    /**
     * Set required roles to see this field
     */
    public function roles(array|string $roles): self
    {
        $this->roles = is_array($roles) ? $roles : [$roles];

        return $this;
    }

    /**
     * Custom callback to determine if user can see this field
     */
    public function canSee(\Closure $callback): self
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    /**
     * Custom callback to determine if user can edit this field
     */
    public function canEdit(\Closure $callback): self
    {
        $this->canEditCallback = $callback;

        return $this;
    }

    /**
     * Show field only for specific permissions
     */
    public function visibleForPermissions(array|string $permissions): self
    {
        return $this->permissions($permissions);
    }

    /**
     * Show field only for specific roles
     */
    public function visibleForRoles(array|string $roles): self
    {
        return $this->roles($roles);
    }

    /**
     * Make field editable only for specific permissions
     */
    public function editableForPermissions(array|string $permissions): self
    {
        $this->meta['editable_permissions'] = is_array($permissions) ? $permissions : [$permissions];

        return $this;
    }

    /**
     * Make field editable only for specific roles
     */
    public function editableForRoles(array|string $roles): self
    {
        $this->meta['editable_roles'] = is_array($roles) ? $roles : [$roles];

        return $this;
    }

    /**
     * Hide field for guests (unauthenticated users)
     */
    public function hideForGuests(): self
    {
        $this->meta['hide_for_guests'] = true;

        return $this;
    }

    /**
     * Show field only for authenticated users
     */
    public function onlyForAuthenticated(): self
    {
        return $this->hideForGuests();
    }

    /**
     * Show field only for the owner of the resource
     */
    public function onlyForOwner(string $ownerField = 'user_id'): self
    {
        $this->meta['only_for_owner'] = true;
        $this->meta['owner_field'] = $ownerField;

        return $this;
    }

    /**
     * Resolve authorization for a given user
     */
    public function resolveAuthorization($user = null): self
    {
        // Check canSee callback
        if ($this->canSeeCallback !== null) {
            $this->authorizedToSee = call_user_func($this->canSeeCallback, $user);
        }

        // Check canEdit callback
        if ($this->canEditCallback !== null) {
            $this->authorizedToEdit = call_user_func($this->canEditCallback, $user);
        }

        // Check permissions (requires Laravel's Gate or similar)
        if (! empty($this->permissions) && $user !== null) {
            $this->authorizedToSee = $this->checkPermissions($user, $this->permissions);
        }

        // Check roles
        if (! empty($this->roles) && $user !== null) {
            $this->authorizedToSee = $this->checkRoles($user, $this->roles);
        }

        // Hide for guests
        if (($this->meta['hide_for_guests'] ?? false) && $user === null) {
            $this->authorizedToSee = false;
        }

        // If not authorized to see, also not authorized to edit
        if (! $this->authorizedToSee) {
            $this->authorizedToEdit = false;
        }

        // Check editable permissions
        if (! empty($this->meta['editable_permissions'] ?? []) && $user !== null) {
            $this->authorizedToEdit = $this->checkPermissions($user, $this->meta['editable_permissions']);
        }

        // Check editable roles
        if (! empty($this->meta['editable_roles'] ?? []) && $user !== null) {
            $this->authorizedToEdit = $this->checkRoles($user, $this->meta['editable_roles']);
        }

        // If not authorized to edit, make readonly
        if (! $this->authorizedToEdit && $this->authorizedToSee) {
            $this->readonly = true;
        }

        return $this;
    }

    /**
     * Check if user has any of the given permissions
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
     * Check if user has any of the given roles
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
        // Fallback: check role property or relationship
        if (isset($user->role)) {
            return in_array($user->role, $roles);
        }
        if (method_exists($user, 'roles') && is_iterable($user->roles)) {
            foreach ($user->roles as $userRole) {
                $roleName = is_object($userRole) ? ($userRole->name ?? $userRole->slug ?? '') : $userRole;
                if (in_array($roleName, $roles)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if authorized to see
     */
    public function isAuthorizedToSee(): bool
    {
        return $this->authorizedToSee;
    }

    /**
     * Check if authorized to edit
     */
    public function isAuthorizedToEdit(): bool
    {
        return $this->authorizedToEdit;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getRules(): array
    {
        return array_unique($this->rules);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function toArray(): array
    {
        $array = [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'description' => $this->description,
            'default' => $this->default,
            'required' => $this->required,
            'readonly' => $this->readonly,
            'disabled' => $this->disabled,
            'max_length' => $this->maxLength,
            'min_length' => $this->minLength,
            'options' => $this->options,
            'rules' => $this->getRules(),
            'attributes' => $this->attributes,
            'meta' => $this->meta,
            'order' => $this->order,
            'group' => $this->group,
            'visible' => $this->visible,
        ];

        // Add enhanced properties if set
        if ($this->prefix !== null) {
            $array['prefix'] = $this->prefix;
        }
        if ($this->suffix !== null) {
            $array['suffix'] = $this->suffix;
        }
        if ($this->mask !== null) {
            $array['mask'] = $this->mask;
        }
        if ($this->transform !== null) {
            $array['transform'] = $this->transform;
        }
        if ($this->copyable) {
            $array['copyable'] = $this->copyable;
        }
        if ($this->clickable) {
            $array['clickable'] = $this->clickable;
        }
        if ($this->decimals !== null) {
            $array['decimals'] = $this->decimals;
        }
        if ($this->searchable) {
            $array['searchable'] = $this->searchable;
        }
        if ($this->dataSource !== null) {
            $array['data_source'] = $this->dataSource;
        }
        if ($this->format !== null) {
            $array['format'] = $this->format;
        }
        if ($this->displayFormat !== null) {
            $array['display_format'] = $this->displayFormat;
        }
        if ($this->showRelative) {
            $array['show_relative'] = $this->showRelative;
        }
        if ($this->showAge) {
            $array['show_age'] = $this->showAge;
        }
        if ($this->timezone !== null) {
            $array['timezone'] = $this->timezone;
        }
        if ($this->currency !== null) {
            $array['currency'] = $this->currency;
        }
        if ($this->currencySymbol !== null) {
            $array['currency_symbol'] = $this->currencySymbol;
        }
        if ($this->showTags) {
            $array['show_tags'] = $this->showTags;
        }
        if ($this->layout !== null) {
            $array['layout'] = $this->layout;
        }
        if ($this->color !== null) {
            $array['color'] = $this->color;
        }
        if ($this->icon !== null) {
            $array['icon'] = $this->icon;
        }
        if (! empty($this->colorMap)) {
            $array['color_map'] = $this->colorMap;
        }
        if ($this->relationship !== null) {
            $array['relationship'] = $this->relationship;
            $array['relationship_model'] = $this->relationshipModel;
        }
        if ($this->showPreview) {
            $array['show_preview'] = $this->showPreview;
        }
        if ($this->allowCrop) {
            $array['allow_crop'] = $this->allowCrop;
            $array['crop_ratios'] = $this->cropRatios;
        }
        if ($this->allowResize) {
            $array['allow_resize'] = $this->allowResize;
            $array['resize_dimensions'] = $this->resizeDimensions;
        }
        if (! empty($this->presetColors)) {
            $array['preset_colors'] = $this->presetColors;
        }
        if ($this->editor !== null) {
            $array['editor'] = $this->editor;
        }
        if ($this->syntax !== null) {
            $array['syntax'] = $this->syntax;
        }
        if ($this->mapProvider !== null) {
            $array['map_provider'] = $this->mapProvider;
            $array['api_key'] = $this->apiKey;
        }
        if (! empty($this->compositeFields)) {
            $array['composite_fields'] = array_map(
                fn ($field) => $field instanceof Field ? $field->toArray() : $field,
                $this->compositeFields
            );
        }

        // Add conditional visibility & dependencies
        if (! empty($this->visibleConditions)) {
            $array['visible_conditions'] = $this->visibleConditions;
        }
        if (! empty($this->requiredConditions)) {
            $array['required_conditions'] = $this->requiredConditions;
        }
        if (! empty($this->disabledConditions)) {
            $array['disabled_conditions'] = $this->disabledConditions;
        }

        // Add field dependencies
        if ($this->dependsOnField !== null) {
            $array['depends_on'] = $this->dependsOnField;
            if ($this->dependsOnUrl !== null) {
                $array['depends_on_url'] = $this->dependsOnUrl;
            }
            if (! empty($this->dependsOnParams)) {
                $array['depends_on_params'] = $this->dependsOnParams;
            }
        }

        // Add computed field info
        if ($this->isComputed) {
            $array['is_computed'] = true;
        }

        // Add column layout
        if ($this->columnSpan !== null) {
            $array['column_span'] = $this->columnSpan;
        }
        if ($this->columnStart !== null) {
            $array['column_start'] = $this->columnStart;
        }

        // Add help & UI enhancements
        if ($this->help !== null) {
            $array['help'] = $this->help;
        }
        if ($this->tooltip !== null) {
            $array['tooltip'] = $this->tooltip;
        }
        if ($this->example !== null) {
            $array['example'] = $this->example;
        }
        if ($this->loadingText !== null) {
            $array['loading_text'] = $this->loadingText;
        }
        if ($this->confirmMessage !== null) {
            $array['confirm_message'] = $this->confirmMessage;
        }
        if (! empty($this->validationMessages)) {
            $array['validation_messages'] = $this->validationMessages;
        }

        // Add audit/history tracking
        if ($this->trackChanges) {
            $array['track_changes'] = true;
        }
        if ($this->showHistory) {
            $array['show_history'] = true;
        }

        // Add authorization properties
        if (! empty($this->permissions)) {
            $array['permissions'] = $this->permissions;
        }
        if (! empty($this->roles)) {
            $array['roles'] = $this->roles;
        }
        $array['authorized_to_see'] = $this->authorizedToSee;
        $array['authorized_to_edit'] = $this->authorizedToEdit;

        return $array;
    }

    public function render(): array
    {
        return $this->toArray();
    }
}
