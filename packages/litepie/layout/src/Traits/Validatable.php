<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Validation\LayoutValidator;

trait Validatable
{
    protected ?LayoutValidator $validator = null;

    protected bool $validationEnabled = false;

    protected array $validationRules = [];

    protected bool $strictValidation = false;

    /**
     * Enable validation
     */
    public function validate(bool $enabled = true, bool $strict = false): self
    {
        $this->validationEnabled = $enabled;
        $this->strictValidation = $strict;

        return $this;
    }

    /**
     * Add validation rules for data
     */
    public function validateData(array $rules): self
    {
        $this->validationRules = array_merge($this->validationRules, $rules);

        return $this;
    }

    /**
     * Get validator instance
     */
    protected function getValidator(): LayoutValidator
    {
        if ($this->validator === null) {
            $this->validator = new LayoutValidator($this->strictValidation);
        }

        return $this->validator;
    }

    /**
     * Validate component configuration
     */
    protected function validateConfiguration(): array
    {
        if (! $this->validationEnabled) {
            return ['valid' => true];
        }

        return $this->getValidator()->validate($this->toArray());
    }

    /**
     * Validate component data
     */
    protected function validateComponentData(array $data): array
    {
        if (! $this->validationEnabled || empty($this->validationRules)) {
            return ['valid' => true, 'data' => $data];
        }

        return $this->getValidator()->validateData($data, $this->validationRules);
    }
}
