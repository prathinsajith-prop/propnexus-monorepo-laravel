<?php

namespace Litepie\Layout\Sections;

/**
 * WizardSection
 *
 * Multi-step wizard section. Each step stores its content directly.
 * No slots - steps store sections and components in arrays.
 *
 * Example:
 *   WizardSection::make('checkout')
 *       ->step('info', 'Your Info', function($section) {
 *           $section->form('info-form');
 *       })
 *       ->step('payment', 'Payment', function($section) {
 *           $section->form('payment-form');
 *       }, ['icon' => 'credit-card'])
 *       ->step('review', 'Review', function($section) {
 *           $section->card('review-card');
 *       }, ['optional' => true]);
 */
class WizardSection extends BaseSection
{
    protected array $steps = [];
    protected int $currentStep = 0;
    protected bool $linear = true;
    protected bool $showStepNumbers = true;
    protected string $orientation = 'horizontal'; // horizontal, vertical
    protected bool $validateOnNext = true;

    public function __construct(string $name)
    {
        parent::__construct($name, 'wizard');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Add a step with callback configuration
     */
    public function step(string $key, string $label, \Closure $callback, array $options = []): self
    {
        // Store step metadata
        $this->steps[$key] = [
            'key' => $key,
            'label' => $label,
            'description' => $options['description'] ?? null,
            'icon' => $options['icon'] ?? null,
            'optional' => $options['optional'] ?? false,
            'validation' => $options['validation'] ?? null,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'sections' => [],
            'components' => [],
        ];

        // Store current sections/components counts
        $beforeSections = count($this->sections);
        $beforeComponents = count($this->components);

        // Execute callback - it will add to $this->sections and $this->components
        $callback($this);

        // Move new sections/components into step
        $this->steps[$key]['sections'] = array_splice($this->sections, $beforeSections);
        $this->steps[$key]['components'] = array_splice($this->components, $beforeComponents);

        return $this;
    }

    /**
     * Add a step without content (just metadata)
     */
    public function addStep(string $key, string $label, array $options = []): self
    {
        $this->steps[$key] = [
            'key' => $key,
            'label' => $label,
            'description' => $options['description'] ?? null,
            'icon' => $options['icon'] ?? null,
            'optional' => $options['optional'] ?? false,
            'validation' => $options['validation'] ?? null,
            'permissions' => $options['permissions'] ?? [],
            'roles' => $options['roles'] ?? [],
            'sections' => [],
            'components' => [],
        ];

        return $this;
    }

    public function currentStep(string|int $step): self
    {
        if (is_string($step)) {
            // Find the step index by key
            $keys = array_keys($this->steps);
            $index = array_search($step, $keys);
            $this->currentStep = $index !== false ? $index : 0;
        } else {
            $this->currentStep = $step;
        }

        return $this;
    }

    public function linear(bool $linear = true): self
    {
        $this->linear = $linear;
        return $this;
    }

    public function showStepNumbers(bool $show = true): self
    {
        $this->showStepNumbers = $show;
        return $this;
    }

    public function orientation(string $orientation): self
    {
        $this->orientation = $orientation;
        return $this;
    }

    public function validateOnNext(bool $validate = true): self
    {
        $this->validateOnNext = $validate;
        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getStep(string $key): ?array
    {
        return $this->steps[$key] ?? null;
    }

    public function getCurrentStep(): int
    {
        return $this->currentStep;
    }

    public function resolveAuthorization($user = null): self
    {
        parent::resolveAuthorization($user);

        foreach ($this->steps as $key => &$step) {
            if (!empty($step['permissions'])) {
                $step['authorized'] = $this->checkPermissions($user, $step['permissions']);
            } elseif (!empty($step['roles'])) {
                $step['authorized'] = $this->checkRoles($user, $step['roles']);
            } else {
                $step['authorized'] = true;
            }

            foreach ($step['components'] as $component) {
                if (method_exists($component, 'resolveAuthorization')) {
                    $component->resolveAuthorization($user);
                }
            }

            foreach ($step['sections'] as $section) {
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
        
        $stepsOutput = [];
        foreach ($this->steps as $key => $step) {
            $stepData = [
                'key' => $step['key'],
                'label' => $step['label'],
                'description' => $step['description'],
                'icon' => $step['icon'],
                'optional' => $step['optional'],
                'validation' => $step['validation'],
                'permissions' => $step['permissions'],
                'roles' => $step['roles'],
                'authorized' => $step['authorized'] ?? true,
            ];

            if (!empty($step['sections'])) {
                $stepData['sections'] = array_map(function($section) {
                    return method_exists($section, 'toArray') ? $section->toArray() : (array)$section;
                }, $step['sections']);
            }

            if (!empty($step['components'])) {
                $stepData['components'] = array_map(function($component) {
                    return method_exists($component, 'toArray') ? $component->toArray() : (array)$component;
                }, $step['components']);
            }

            $stepsOutput[] = $stepData;
        }

        return array_merge($data, [
            'steps' => $stepsOutput,
            'currentStep' => $this->currentStep,
            'linear' => $this->linear,
            'showStepNumbers' => $this->showStepNumbers,
            'orientation' => $this->orientation,
            'validateOnNext' => $this->validateOnNext,
            'permissions' => $this->permissions ?? [],
            'roles' => $this->roles ?? [],
            'authorized_to_see' => $this->authorizedToSee ?? null,
        ]);
    }
}
