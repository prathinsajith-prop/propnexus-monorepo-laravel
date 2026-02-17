<?php

namespace Litepie\Layout\Components;

class FormGroupComponent extends BaseComponent
{
    protected array $fields = [];

    protected ?string $key = null;

    protected ?string $title = null;

    protected ?string $description = null;

    protected ?string $icon = null;

    protected bool $collapsible = false;

    protected bool $collapsed = false;

    protected ?string $variant = 'bordered'; // bordered, card, plain

    protected int $columns = 1;

    protected string $gap = 'md';

    protected array $columnWidths = [];

    protected ?int $gridRow = null;
    protected ?int $gridColumn = null;
    protected ?int $gridRowEnd = null;
    protected ?int $gridColumnEnd = null;
    protected ?int $columnSpan = null;
    protected bool $isEditable = true;
    protected bool $isCreate = false;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Form group name cannot be empty. A unique identifier is required.');
        }
        parent::__construct($name, 'formGroup');
    }

    public static function make(string $name): self
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Form group name cannot be empty. A unique identifier is required.');
        }
        return new static($name);
    }

    public function title(string $title): self
    {
        $this->title = $title;

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

    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function columns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function gap(string $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * Set column widths for fields within the group
     * 
     * @param array $widths Array of width values (e.g., ['1fr', '2fr', '1fr'], ['33%', '33%', '33%'], ['300px', '200px', '400px'])
     * @return self
     */
    public function columnWidths(array $widths): self
    {
        $this->columnWidths = $widths;

        return $this;
    }

    /**
     * Set grid row and column position for the group
     * 
     * @param int $row Row number (1-based)
     * @param int $column Column number (1-based)
     * @return self
     */
    public function position(int $row, int $column): self
    {
        $this->gridRow = $row;
        $this->gridColumn = $column;

        return $this;
    }

    /**
     * Set grid row (CSS Grid row-start)
     * 
     * @param int $row Row number (1-based)
     * @return self
     */
    public function gridRow(int $row): self
    {
        $this->gridRow = $row;

        return $this;
    }

    /**
     * Set grid column (CSS Grid column-start)
     * 
     * @param int $column Column number (1-based)
     * @return self
     */
    public function gridColumn(int $column): self
    {
        $this->gridColumn = $column;

        return $this;
    }

    /**
     * Set grid row end (CSS Grid row-end)
     * 
     * @param int $rowEnd Row end number
     * @return self
     */
    public function gridRowEnd(int $rowEnd): self
    {
        $this->gridRowEnd = $rowEnd;

        return $this;
    }

    /**
     * Set grid column end (CSS Grid column-end)
     * 
     * @param int $columnEnd Column end number
     * @return self
     */
    public function gridColumnEnd(int $columnEnd): self
    {
        $this->gridColumnEnd = $columnEnd;

        return $this;
    }

    /**
     * Set how many columns this group should span
     * 
     * @param int $span Number of columns to span (1, 2, 3, etc.)
     * @return self
     */
    public function columnSpan(int $span): self
    {
        $this->columnSpan = $span;

        return $this;
    }

    /**
     * Set the unique key for this form group
     * Format: form.{formName}.{groupName}
     * 
     * @param string $key The unique key identifier
     * @return self
     */
    public function setKey(string $key): self
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Form group key cannot be empty.');
        }
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key for this form group
     * 
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Create a field inside this group and return it for chaining
     * Note: Fields in groups are stored as indexed array to preserve order
     * 
     * @param string $type Field type
     * @param string $name Field name
     * @return mixed Field instance for chaining
     */
    protected function createField(string $type, string $name)
    {
        $field = \Litepie\Form\Field::make($type, $name);
        $this->fields[] = $field; // Use indexed array to preserve order
        return $field;
    }

    /**
     * Add an existing field to this group
     */
    public function addField($field): self
    {
        // Always add to indexed array to preserve order
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add multiple fields to this group
     */
    public function addFields(array $fields): self
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    // ========================================================================
    // Field Creation Methods - Returns Field for Chaining
    // ========================================================================

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

    public function richtext(string $name)
    {
        return $this->createField('richtext', $name);
    }

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

    public function toggle(string $name)
    {
        return $this->createField('toggle', $name);
    }

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

    public function dateRange(string $name)
    {
        return $this->createField('daterange', $name);
    }

    public function file(string $name)
    {
        return $this->createField('file', $name);
    }

    public function image(string $name)
    {
        return $this->createField('image', $name);
    }

    public function color(string $name)
    {
        return $this->createField('color', $name);
    }

    public function range(string $name)
    {
        return $this->createField('range', $name);
    }

    public function rating(string $name)
    {
        return $this->createField('rating', $name);
    }

    public function tags(string $name)
    {
        return $this->createField('tags', $name);
    }

    public function hiddenField(string $name)
    {
        return $this->createField('hidden', $name);
    }

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

    /**
     * Get all fields in this group
     * 
     * @return array Indexed array of field objects
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get a specific field by its name
     * 
     * @param string $name Field name to search for
     * @return mixed|null Field object if found, null otherwise
     */
    public function getField(string $name)
    {
        foreach ($this->fields as $field) {
            // Handle field objects with getName() method
            if (is_object($field) && method_exists($field, 'getName') && $field->getName() === $name) {
                return $field;
            }

            // Handle array-based fields
            if (is_array($field) && ($field['name'] ?? null) === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Check if a field with the given name exists in this group
     * 
     * @param string $name Field name to check
     * @return bool True if field exists, false otherwise
     */
    public function hasField(string $name): bool
    {
        return $this->getField($name) !== null;
    }

    /**
     * Set whether the group is editable
     *
     * @param bool $editable
     * @return self
     */
    public function editable(bool $editable = true): self
    {
        $this->isEditable = $editable;
        return $this;
    }

    /**
     * Method create.
     *
     * @param bool $isCreate Whether the form group is in create mode
     *
     * @return self
     */
    public function create(bool $isCreate = true): self
    {
        $this->isCreate = $isCreate;
        return $this;
    }

    public function toArray(): array
    {
        // Ensure key is always set with proper format
        if (empty($this->key)) {
            throw new \RuntimeException(
                "Form group '{$this->name}' is missing required key property. "
                    . "Key must follow pattern: form.{{formName}}.{{groupName}}"
            );
        }

        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'variant' => $this->variant,
            'columns' => $this->columns,
            'gap' => $this->gap,
            'columnWidths' => !empty($this->columnWidths) ? $this->columnWidths : null,
            'gridPosition' => $this->getGridPosition(),
            'isEditable' => $this->isEditable,
            'isCreate' => $this->isCreate,
            'fields' => array_map(
                fn($field) => (is_object($field) && method_exists($field, 'toArray'))
                    ? $field->toArray()
                    : (array) $field,
                $this->fields
            ),
        ]));
    }

    /**
     * Get grid position information for the group
     * 
     * @return array|null
     */
    protected function getGridPosition(): ?array
    {
        if ($this->gridRow === null && $this->gridColumn === null) {
            return null;
        }

        return $this->filterNullValues([
            'row' => $this->gridRow,
            'column' => $this->gridColumn,
            'rowEnd' => $this->gridRowEnd,
            'columnEnd' => $this->gridColumnEnd,
            'columnSpan' => $this->columnSpan,
        ]);
    }
}
