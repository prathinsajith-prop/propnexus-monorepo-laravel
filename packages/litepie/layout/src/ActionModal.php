<?php

namespace Litepie\Layout;

class ActionModal
{
    protected string $id;

    protected string $title;

    protected ?string $description = null;

    protected array $formFields = []; // Litepie/Form field instances

    protected string $submitLabel = 'Submit';

    protected string $cancelLabel = 'Cancel';

    protected string $submitClass = 'btn btn-primary';

    protected array $meta = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function make(string $id): self
    {
        return new static($id);
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

    /**
     * Add a Litepie/Form field instance to this modal
     *
     * @param  mixed  $field  Litepie\Form\Field instance
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

    public function submitLabel(string $label): self
    {
        $this->submitLabel = $label;

        return $this;
    }

    public function cancelLabel(string $label): self
    {
        $this->cancelLabel = $label;

        return $this;
    }

    public function submitClass(string $class): self
    {
        $this->submitClass = $class;

        return $this;
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFormFields(): array
    {
        return $this->formFields;
    }

    public function getSubmitLabel(): string
    {
        return $this->submitLabel;
    }

    public function getCancelLabel(): string
    {
        return $this->cancelLabel;
    }

    public function getSubmitClass(): string
    {
        return $this->submitClass;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'fields' => array_map(fn ($field) => method_exists($field, 'toArray') ? $field->toArray() : (array) $field, $this->formFields),
            'submit_label' => $this->submitLabel,
            'cancel_label' => $this->cancelLabel,
            'submit_class' => $this->submitClass,
            'meta' => $this->meta,
        ];
    }
}
