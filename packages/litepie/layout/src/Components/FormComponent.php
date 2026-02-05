<?php

namespace Litepie\Layout\Components;

/**
 * FormComponent - Form builder with field and group management
 * 
 * Provides a fluent API for building forms with fields and groups.
 * Supports both traditional fields and organized field groups.
 */
class FormComponent extends BaseComponent
{
    protected array $formFields = [];
    protected ?string $label = null;
    protected ?string $action = null;
    protected string $method = 'POST';
    protected ?string $enctype = null;
    protected array $validation = [];
    protected int $formColumns = 1;
    protected string $gap = 'md';
    protected array $columnWidths = [];
    protected bool $collapsible = false;
    protected bool $collapsed = false;
    protected ?array $layoutConfig = null;
    protected ?string $layoutMap = null;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Form name cannot be empty. A unique identifier is required.');
        }
        parent::__construct($name, 'form');
    }

    public static function make(string $name): self
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Form name cannot be empty. A unique identifier is required.');
        }
        return new static($name);
    }

    // ========================================================================
    // Form Configuration Methods
    // ========================================================================

    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function method(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function enctype(string $enctype): self
    {
        $this->enctype = $enctype;
        return $this;
    }

    public function validationRules(array $rules): self
    {
        $this->validation = array_merge($this->validation, $rules);
        return $this;
    }

    public function columns(int $columns): self
    {
        $this->formColumns = $columns;
        return $this;
    }

    public function gap(string $gap): self
    {
        $this->gap = $gap;
        return $this;
    }

    /**
     * Set column widths for form layout
     * 
     * @param array $widths Array of width values (e.g., ['1fr', '2fr'], ['40%', '60%'], ['300px', '400px'])
     * @return self
     */
    public function columnWidths(array $widths): self
    {
        $this->columnWidths = $widths;
        return $this;
    }

    /**
     * Define a layout template for groups using a 2D array
     * 
     * @param array $template 2D array where each row contains group names
     * Example: [['group1', 'group2'], ['group3', null]]
     * @return self
     */
    public function layoutConfig(array $layoutConfig): self
    {
        $this->layoutConfig = $layoutConfig;

        return $this;
    }

    /**
     * Set a visual ASCII map of the layout (for documentation)
     * 
     * @param string|null $map ASCII art representation of the layout
     * @return self
     */
    public function layoutMap(?string $map = null): self
    {
        $this->layoutMap = $map;
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

    // ========================================================================
    // Form Group Creation
    // ========================================================================

    /**
     * Create a form group (fieldset) to organize related fields
     * Automatically sets the group key in format: form.{formName}.{groupName}
     * 
     * @param string $name Group name/identifier (must be unique within the form)
     * @return FormGroupComponent
     * @throws \InvalidArgumentException if group name is empty or already exists
     */
    public function group(string $name): FormGroupComponent
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Group name cannot be empty.');
        }

        if (isset($this->formFields[$name])) {
            throw new \InvalidArgumentException(
                "Form group '{$name}' already exists in form '{$this->name}'. Group names must be unique."
            );
        }

        $group = FormGroupComponent::make($name);
        // Set the key in format: form.{formName}.{groupName}
        $group->setKey("form.{$this->name}.{$name}");
        $this->formFields[$name] = $group;
        return $group;
    }

    // ========================================================================
    // Field Management Methods
    // ========================================================================

    /**
     * Add a field with options - backward compatible method
     * 
     * @param string $name Field name
     * @param string $type Field type
     * @param string $label Field label
     * @param array $options Field options
     * @return self
     */
    public function addField(string $name, string $type, string $label, array $options = []): self
    {
        $field = \Litepie\Form\Field::make($type, $name)->label($label);

        foreach ($options as $key => $value) {
            if (method_exists($field, $key)) {
                $field->$key($value);
            } else {
                $field->attribute($key, $value);
            }
        }

        $this->formFields[$name] = $field;
        return $this;
    }

    /**
     * Add a button field to the form (convenience method)
     * Usage: addButton('submit', 'Submit Form', 'submit', ['variant' => 'primary'])
     * 
     * @deprecated Use addField() with type 'button', 'submit', or 'reset' instead
     */
    public function addButton(string $name, string $label, string|array $type = 'button', array $options = []): self
    {
        // Handle case where $type is an array (options passed as 3rd parameter)
        if (is_array($type)) {
            $options = $type;
            $type = $options['type'] ?? 'button';
        }

        // Map type to correct field type
        $fieldType = match ($type) {
            'submit' => 'submit',
            'reset' => 'reset',
            default => 'button',
        };

        // Use addField instead - buttons are just fields in the Form package
        return $this->addField($name, $fieldType, $label, $options);
    }

    /**
     * Add an existing form field object
     * 
     * @param mixed $field Field object or array
     * @return self
     */
    public function addFormField($field): self
    {
        if (is_object($field) && method_exists($field, 'getName')) {
            $this->formFields[$field->getName()] = $field;
        } elseif (is_array($field) && isset($field['name'])) {
            $this->formFields[$field['name']] = $field;
        } else {
            $this->formFields[] = $field;
        }
        return $this;
    }

    /**
     * Add multiple form fields at once
     * 
     * @param array $fields Array of field objects
     * @return self
     */
    public function addFormFields(array $fields): self
    {
        foreach ($fields as $field) {
            $this->addFormField($field);
        }
        return $this;
    }

    /**
     * Get all form fields
     * 
     * @return array
     */
    public function getFormFields(): array
    {
        return $this->formFields;
    }

    /**
     * Get a specific form field by name
     * 
     * @param string $name Field name
     * @return mixed
     */
    public function getFormField(string $name)
    {
        return $this->formFields[$name] ?? null;
    }

    // ========================================================================
    // Field Creation Convenience Methods
    // ========================================================================

    /**
     * Create and register a field
     * 
     * @param string $type Field type
     * @param string $name Field name (must be unique)
     * @return mixed Field instance for chaining
     * @throws \InvalidArgumentException if field name already exists
     */
    protected function createField(string $type, string $name)
    {
        if (isset($this->formFields[$name])) {
            throw new \InvalidArgumentException(
                "Field '{$name}' already exists in form '{$this->name}'. Field names must be unique."
            );
        }

        $field = \Litepie\Form\Field::make($type, $name);
        $this->formFields[$name] = $field;
        return $field;
    }

    // Text Input Fields
    public function text(string $name)
    {
        return $this->createField('text', $name);
    }
    public function email(string $name)
    {
        return $this->createField('email', $name);
    }
    public function password(string $name)
    {
        return $this->createField('password', $name);
    }
    public function number(string $name)
    {
        return $this->createField('number', $name);
    }
    public function tel(string $name)
    {
        return $this->createField('tel', $name);
    }
    public function url(string $name)
    {
        return $this->createField('url', $name);
    }
    public function textarea(string $name)
    {
        return $this->createField('textarea', $name);
    }

    // Selection Fields
    public function select(string $name)
    {
        return $this->createField('select', $name);
    }
    public function multiselect(string $name)
    {
        return $this->createField('multiselect', $name);
    }
    public function checkbox(string $name)
    {
        return $this->createField('checkbox', $name);
    }
    public function radio(string $name)
    {
        return $this->createField('radio', $name);
    }
    public function switch(string $name)
    {
        return $this->createField('switch', $name);
    }

    // Date/Time Fields
    public function date(string $name)
    {
        return $this->createField('date', $name);
    }
    public function time(string $name)
    {
        return $this->createField('time', $name);
    }
    public function datetime(string $name)
    {
        return $this->createField('datetime', $name);
    }

    // File Fields
    public function file(string $name)
    {
        return $this->createField('file', $name);
    }

    // Special Fields
    public function range(string $name)
    {
        return $this->createField('range', $name);
    }
    public function slider(string $name)
    {
        return $this->createField('range', $name);
    }
    public function rating(string $name)
    {
        return $this->createField('rating', $name);
    }
    public function color(string $name)
    {
        return $this->createField('color', $name);
    }
    public function hiddenField(string $name)
    {
        return $this->createField('hidden', $name);
    }

    // Button Fields
    public function submit(string $name)
    {
        return $this->createField('submit', $name);
    }
    public function button(string $name)
    {
        return $this->createField('button', $name);
    }
    public function reset(string $name)
    {
        return $this->createField('reset', $name);
    }

    // ========================================================================
    // Output Generation
    // ========================================================================

    /**
     * Convert form to array representation
     * Separates groups and standalone fields intelligently
     * 
     * @return array
     */
    public function toArray(): array
    {
        [$groups, $fields] = $this->separateGroupsAndFields();

        $result = array_merge(
            $this->getCommonProperties(),
            $this->filterNullValues($this->getFormProperties())
        );

        return $this->addFieldsToResult($result, $groups, $fields);
    }

    /**
     * Separate form fields into groups and regular fields
     * Preserves the order as defined, returns indexed arrays
     * 
     * @return array [groups, fields]
     */
    protected function separateGroupsAndFields(): array
    {
        $groups = [];
        $fields = [];

        foreach ($this->formFields as $name => $field) {
            if ($field instanceof FormGroupComponent) {
                // Add group as indexed array item (preserves order)
                $groups[] = $field->toArray();
            } else {
                // Add field as indexed array item (preserves order)
                $fieldArray = is_object($field) && method_exists($field, 'toArray')
                    ? $field->toArray()
                    : (array) $field;
                $fields[] = $fieldArray;
            }
        }

        return [$groups, $fields];
    }

    /**
     * Get form properties for output
     * 
     * @return array
     */
    protected function getFormProperties(): array
    {
        return [
            'label' => $this->label,
            'action' => $this->action,
            'method' => $this->method,
            'enctype' => $this->enctype,
            'validation' => $this->validation,
            'columns' => $this->formColumns,
            'gap' => $this->gap,
            'columnWidths' => !empty($this->columnWidths) ? $this->columnWidths : null,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'layoutConfig' => $this->layoutConfig,
            'layoutMap' => $this->layoutMap,
        ];
    }

    /**
     * Add fields and groups to result array
     * 
     * @param array $result Base result array
     * @param array $groups Groups array
     * @param array $fields Fields array
     * @return array
     */
    protected function addFieldsToResult(array $result, array $groups, array $fields): array
    {
        if (!empty($groups)) {
            $result['groups'] = $groups;
            if (!empty($fields)) {
                $result['fields'] = $fields;
            }
        } elseif (!empty($fields)) {
            $result['fields'] = $fields;
        }

        return $result;
    }
}
