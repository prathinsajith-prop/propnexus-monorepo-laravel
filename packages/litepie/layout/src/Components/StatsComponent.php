<?php

namespace Litepie\Layout\Components;

class StatsComponent extends BaseComponent
{
    protected array $metrics = []; // Array of metric configurations

    protected string $layout = 'grid'; // grid, list, inline

    protected int $statsColumns = 4;

    protected string $size = 'md'; // sm, md, lg

    protected bool $showTrend = true;

    protected bool $showChange = true;

    // Single stat properties
    protected mixed $value = null;

    protected ?string $label = null;

    protected ?string $change = null;

    protected ?string $trend = null; // up, down, neutral

    protected ?string $prefix = null;

    protected ?string $suffix = null;

    protected ?string $color = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'stats');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Add a metric configuration (structure only, no data)
     */
    public function addMetric(string $key, string $label, array $options = []): self
    {
        $this->metrics[] = [
            'key' => $key,
            'label' => $label,
            'icon' => $options['icon'] ?? null,
            'color' => $options['color'] ?? null,
            'format' => $options['format'] ?? 'number', // number, currency, percentage
            'prefix' => $options['prefix'] ?? null,
            'suffix' => $options['suffix'] ?? null,
            'show_trend' => $options['show_trend'] ?? $this->showTrend,
            'show_change' => $options['show_change'] ?? $this->showChange,
        ];

        return $this;
    }

    /**
     * Set the stat value (for single stat display)
     */
    public function value(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the stat label
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the change indicator (e.g., "+12.5%", "-3.2%")
     */
    public function change(string $change): self
    {
        $this->change = $change;

        return $this;
    }

    /**
     * Set the trend direction
     */
    public function trend(string $trend): self
    {
        $this->trend = $trend;

        return $this;
    }

    /**
     * Set value prefix (e.g., "$" for currency)
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set value suffix (e.g., "%" for percentage)
     */
    public function suffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Set the color theme
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function layout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function columns(int $columns): self
    {
        $this->statsColumns = $columns;

        return $this;
    }

    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function showTrend(bool $show = true): self
    {
        $this->showTrend = $show;

        return $this;
    }

    public function showChange(bool $show = true): self
    {
        $this->showChange = $show;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'value' => $this->value,
            'label' => $this->label,
            'change' => $this->change,
            'trend' => $this->trend,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'color' => $this->color,
            'metrics' => $this->metrics,
            'layout' => $this->layout,
            'columns' => $this->statsColumns,
            'size' => $this->size,
            'show_trend' => $this->showTrend,
            'show_change' => $this->showChange,
        ]));
    }
}
