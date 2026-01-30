<?php

namespace Litepie\Layout\Components;

/**
 * StepperComponent
 *
 * Stepper component for displaying progress through a sequence of steps.
 * Supports horizontal/vertical orientation, linear/non-linear navigation,
 * alternative labels, and mobile variants.
 *
 * @package Litepie\Layout\Components
 */
class StepperComponent extends BaseComponent
{
    protected string $orientation = 'horizontal'; // horizontal, vertical
    protected bool $linear = true;
    protected bool $alternativeLabel = false;
    protected int $activeStep = 0;
    protected array $steps = [];
    
    // Mobile stepper specific
    protected bool $mobile = false;
    protected string $mobileVariant = 'dots'; // dots, text, progress
    protected int $totalSteps = 0;
    protected string $position = 'bottom'; // bottom, static
    
    // Step connector
    protected bool $connector = true;
    protected ?string $connectorColor = null;
    
    // Navigation
    protected ?array $backButton = null;
    protected ?array $nextButton = null;
    protected bool $showButtons = true;
    
    // Content
    protected ?string $content = null;
    protected bool $expandContent = true;
    
    // Styling
    protected ?string $activeColor = null;
    protected ?string $completedColor = null;
    protected ?string $inactiveColor = null;
    protected ?string $iconSize = null;
    protected ?string $spacing = null;

    public function __construct(string $name, string $type = 'stepper')
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Static factory method to create a new instance
     */
    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core Configuration
    // ========================================================================

    /**
     * Set stepper orientation
     */
    public function orientation(string $orientation): self
    {
        $this->orientation = $orientation;
        return $this;
    }

    /**
     * Set linear mode (sequential steps)
     */
    public function linear(bool $linear = true): self
    {
        $this->linear = $linear;
        return $this;
    }

    /**
     * Enable alternative label layout
     */
    public function alternativeLabel(bool $alternativeLabel = true): self
    {
        $this->alternativeLabel = $alternativeLabel;
        return $this;
    }

    /**
     * Set active step index
     */
    public function activeStep(int $step): self
    {
        $this->activeStep = $step;
        return $this;
    }

    // ========================================================================
    // Steps Configuration
    // ========================================================================

    /**
     * Set steps array
     */
    public function steps(array $steps): self
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * Add a single step
     */
    public function addStep(array $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    // ========================================================================
    // Mobile Stepper Configuration
    // ========================================================================

    /**
     * Enable mobile stepper
     */
    public function mobile(bool $mobile = true): self
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Set mobile variant
     */
    public function mobileVariant(string $variant): self
    {
        $this->mobileVariant = $variant;
        return $this;
    }

    /**
     * Set total steps for mobile stepper
     */
    public function totalSteps(int $total): self
    {
        $this->totalSteps = $total;
        return $this;
    }

    /**
     * Set mobile stepper position
     */
    public function position(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    // ========================================================================
    // Connector Configuration
    // ========================================================================

    /**
     * Show/hide step connector
     */
    public function connector(bool $connector = true): self
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * Set connector color
     */
    public function connectorColor(string $color): self
    {
        $this->connectorColor = $color;
        return $this;
    }

    // ========================================================================
    // Navigation Configuration
    // ========================================================================

    /**
     * Set back button configuration
     */
    public function backButton(array $config): self
    {
        $this->backButton = $config;
        return $this;
    }

    /**
     * Set next button configuration
     */
    public function nextButton(array $config): self
    {
        $this->nextButton = $config;
        return $this;
    }

    /**
     * Show/hide navigation buttons
     */
    public function showButtons(bool $show = true): self
    {
        $this->showButtons = $show;
        return $this;
    }

    // ========================================================================
    // Content Configuration
    // ========================================================================

    /**
     * Set step content
     */
    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Enable/disable content expansion
     */
    public function expandContent(bool $expand = true): self
    {
        $this->expandContent = $expand;
        return $this;
    }

    // ========================================================================
    // Styling Methods
    // ========================================================================

    /**
     * Set active step color
     */
    public function activeColor(string $color): self
    {
        $this->activeColor = $color;
        return $this;
    }

    /**
     * Set completed step color
     */
    public function completedColor(string $color): self
    {
        $this->completedColor = $color;
        return $this;
    }

    /**
     * Set inactive step color
     */
    public function inactiveColor(string $color): self
    {
        $this->inactiveColor = $color;
        return $this;
    }

    /**
     * Set icon size
     */
    public function iconSize(string $size): self
    {
        $this->iconSize = $size;
        return $this;
    }

    /**
     * Set spacing between steps
     */
    public function spacing(string $spacing): self
    {
        $this->spacing = $spacing;
        return $this;
    }

    // ========================================================================
    // Preset Methods
    // ========================================================================

    /**
     * Create a simple horizontal linear stepper
     */
    public function horizontalLinear(array $steps, int $activeStep = 0): self
    {
        return $this->orientation('horizontal')
            ->linear(true)
            ->steps($steps)
            ->activeStep($activeStep)
            ->showButtons(true);
    }

    /**
     * Create a non-linear stepper (allows jumping between steps)
     */
    public function nonLinear(array $steps, int $activeStep = 0): self
    {
        return $this->orientation('horizontal')
            ->linear(false)
            ->steps($steps)
            ->activeStep($activeStep)
            ->showButtons(true);
    }

    /**
     * Create a vertical stepper
     */
    public function vertical(array $steps, int $activeStep = 0): self
    {
        return $this->orientation('vertical')
            ->linear(true)
            ->steps($steps)
            ->activeStep($activeStep)
            ->expandContent(true);
    }

    /**
     * Create a stepper with alternative labels (below icons)
     */
    public function withAlternativeLabels(array $steps, int $activeStep = 0): self
    {
        return $this->orientation('horizontal')
            ->alternativeLabel(true)
            ->steps($steps)
            ->activeStep($activeStep)
            ->showButtons(true);
    }

    /**
     * Create a mobile stepper with dots
     */
    public function mobileDots(int $activeStep = 0, int $totalSteps = 6): self
    {
        return $this->mobile(true)
            ->mobileVariant('dots')
            ->activeStep($activeStep)
            ->totalSteps($totalSteps)
            ->position('bottom');
    }

    /**
     * Create a mobile stepper with text counter
     */
    public function mobileText(int $activeStep = 0, int $totalSteps = 6): self
    {
        return $this->mobile(true)
            ->mobileVariant('text')
            ->activeStep($activeStep)
            ->totalSteps($totalSteps)
            ->position('static');
    }

    /**
     * Create a mobile stepper with progress bar
     */
    public function mobileProgress(int $activeStep = 0, int $totalSteps = 6): self
    {
        return $this->mobile(true)
            ->mobileVariant('progress')
            ->activeStep($activeStep)
            ->totalSteps($totalSteps)
            ->position('static');
    }

    // ========================================================================
    // Array Conversion
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'orientation' => $this->orientation,
            'linear' => $this->linear ? true : null,
            'alternativeLabel' => $this->alternativeLabel ? true : null,
            'activeStep' => $this->activeStep,
            'steps' => $this->steps,
            'mobile' => $this->mobile ? true : null,
            'mobileVariant' => $this->mobile ? $this->mobileVariant : null,
            'totalSteps' => $this->mobile && $this->totalSteps > 0 ? $this->totalSteps : null,
            'position' => $this->mobile ? $this->position : null,
            'connector' => $this->connector ? true : null,
            'connectorColor' => $this->connectorColor,
            'backButton' => $this->backButton,
            'nextButton' => $this->nextButton,
            'showButtons' => $this->showButtons ? true : null,
            'content' => $this->content,
            'expandContent' => $this->expandContent ? true : null,
            'activeColor' => $this->activeColor,
            'completedColor' => $this->completedColor,
            'inactiveColor' => $this->inactiveColor,
            'iconSize' => $this->iconSize,
            'spacing' => $this->spacing,
        ]));
    }
}
