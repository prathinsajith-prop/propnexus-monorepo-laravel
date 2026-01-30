<?php

namespace Litepie\Layout\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LayoutValidator
{
    protected array $rules = [];

    protected array $messages = [];

    protected bool $strict = false;

    /**
     * Create validator instance
     */
    public function __construct(bool $strict = false)
    {
        $this->strict = $strict;
        $this->defineDefaultRules();
    }

    /**
     * Define default validation rules
     */
    protected function defineDefaultRules(): void
    {
        $this->rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:form,text,card,table,stats,grid,tabs,accordion,scrollspy,wizard,list,timeline,alert,modal,chart,media,comment,badge,custom',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'visible' => 'nullable|boolean',
            'dataUrl' => 'nullable|string|url',
            'dataKey' => 'nullable|string|max:255',
            'loadOnMount' => 'nullable|boolean',
            'useSharedData' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'roles' => 'nullable|array',
        ];

        $this->messages = [
            'name.required' => 'Component name is required',
            'type.required' => 'Component type is required',
            'type.in' => 'Invalid component type',
            'dataUrl.url' => 'Data URL must be a valid URL',
        ];
    }

    /**
     * Validate component configuration
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, $this->rules, $this->messages);

        if ($validator->fails()) {
            if ($this->strict) {
                throw new ValidationException($validator);
            }

            return [
                'valid' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valid' => true,
            'data' => $validator->validated(),
        ];
    }

    /**
     * Validate component data (from API)
     */
    public function validateData(array $data, array $rules = []): array
    {
        if (empty($rules)) {
            return ['valid' => true, 'data' => $data];
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valid' => true,
            'data' => $validator->validated(),
        ];
    }

    /**
     * Add custom validation rule
     */
    public function addRule(string $field, string|array $rule, ?string $message = null): self
    {
        $this->rules[$field] = $rule;

        if ($message) {
            $this->messages["{$field}.required"] = $message;
        }

        return $this;
    }

    /**
     * Set strict mode
     */
    public function strict(bool $strict = true): self
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * Validate section schema
     */
    public function validateSchema(array $section): bool
    {
        $requiredKeys = ['name', 'type'];

        foreach ($requiredKeys as $key) {
            if (! isset($section[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate nested sections
     */
    public function validateNested(array $sections): array
    {
        $errors = [];

        foreach ($sections as $index => $section) {
            $result = $this->validate($section);

            if (! $result['valid']) {
                $errors["section.{$index}"] = $result['errors'];
            }

            // Recursively validate nested sections
            if (isset($section['sections']) && is_array($section['sections'])) {
                $nestedErrors = $this->validateNested($section['sections']);
                if (! empty($nestedErrors)) {
                    $errors["section.{$index}.sections"] = $nestedErrors;
                }
            }
        }

        return $errors;
    }
}
