<?php

namespace Litepie\Layout\Components;

class ChartComponent extends BaseComponent
{
    protected string $chartType = 'line'; // line, bar, pie, doughnut, area, radar, scatter, bubble

    protected array $series = []; // Series configurations

    protected array $labels = []; // Chart labels/categories

    protected array $datasets = []; // Chart datasets

    protected ?string $dataMethod = 'GET'; // HTTP method for data fetching

    protected ?int $refreshInterval = null; // Auto-refresh interval in milliseconds

    protected bool $loadOnInit = true; // Load data on component initialization

    protected array $chartOptions = [];

    protected ?int $height = null;

    protected bool $responsive = true;

    protected bool $animated = true;

    protected ?string $library = null; // chart.js, apexcharts, recharts, etc.

    public function __construct(string $name)
    {
        parent::__construct($name, 'chart');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function chartType(string $type): self
    {
        $this->chartType = $type;

        return $this;
    }

    public function type(string $type): self
    {
        return $this->chartType($type);
    }

    public function line(): self
    {
        return $this->chartType('line');
    }

    public function bar(): self
    {
        return $this->chartType('bar');
    }

    public function pie(): self
    {
        return $this->chartType('pie');
    }

    public function doughnut(): self
    {
        return $this->chartType('doughnut');
    }

    public function area(): self
    {
        return $this->chartType('area');
    }

    public function radar(): self
    {
        return $this->chartType('radar');
    }

    public function scatter(): self
    {
        return $this->chartType('scatter');
    }

    public function bubble(): self
    {
        return $this->chartType('bubble');
    }

    public function donut(): self
    {
        return $this->chartType('doughnut');
    }

    public function gauge(): self
    {
        return $this->chartType('gauge');
    }

    public function heatmap(): self
    {
        return $this->chartType('heatmap');
    }

    public function funnel(): self
    {
        return $this->chartType('funnel');
    }

    public function polarArea(): self
    {
        return $this->chartType('polarArea');
    }

    /**
     * Chart-specific properties (stored in options)
     */
    public function value($value): self
    {
        $this->chartOptions['value'] = $value;
        return $this;
    }

    public function min($min): self
    {
        $this->chartOptions['min'] = $min;
        return $this;
    }

    public function max($max): self
    {
        $this->chartOptions['max'] = $max;
        return $this;
    }

    public function zones(array $zones): self
    {
        $this->chartOptions['zones'] = $zones;
        return $this;
    }

    public function circumference($degrees): self
    {
        $this->chartOptions['circumference'] = $degrees;
        return $this;
    }

    public function rotation($degrees): self
    {
        $this->chartOptions['rotation'] = $degrees;
        return $this;
    }

    public function xLabels(array $labels): self
    {
        $this->chartOptions['xLabels'] = $labels;
        return $this;
    }

    public function yLabels(array $labels): self
    {
        $this->chartOptions['yLabels'] = $labels;
        return $this;
    }

    public function colorScale(array $colors): self
    {
        $this->chartOptions['colorScale'] = $colors;
        return $this;
    }

    public function colors(array $colors): self
    {
        $this->chartOptions['colors'] = $colors;
        return $this;
    }

    public function color(string $color): self
    {
        $this->chartOptions['color'] = $color;
        return $this;
    }

    public function inverted(bool $inverted = true): self
    {
        $this->chartOptions['inverted'] = $inverted;
        return $this;
    }

    public function label(string $label): self
    {
        $this->chartOptions['label'] = $label;
        return $this;
    }

    /**
     * Add series configuration
     */
    public function addSeries(string $key, string $label, array $options = []): self
    {
        $this->series[] = [
            'key' => $key,
            'label' => $label,
            'color' => $options['color'] ?? null,
            'type' => $options['type'] ?? $this->chartType,
        ];

        return $this;
    }

    /**
     * Set chart labels/categories
     */
    public function labels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Set chart datasets
     */
    public function datasets(array $datasets): self
    {
        $this->datasets = $datasets;

        return $this;
    }

    /**
     * Alias for datasets
     */
    public function data(array $data): self
    {
        return $this->datasets($data);
    }

    /**
     * Set data source URL for dynamic data fetching (chart-specific)
     * This extends the parent dataSource by adding chart-specific configuration
     */
    public function chartDataSource(string $url): self
    {
        $this->dataSource($url); // Call parent trait method
        
        return $this;
    }

    /**
     * Alias for chartDataSource
     */
    public function source(string $url): self
    {
        return $this->chartDataSource($url);
    }

    /**
     * Alias for chartDataSource
     */
    public function url(string $url): self
    {
        return $this->chartDataSource($url);
    }

    /**
     * Set HTTP method for data fetching
     */
    public function dataMethod(string $method): self
    {
        $this->dataMethod = strtoupper($method);

        return $this;
    }

    /**
     * Set auto-refresh interval in milliseconds
     */
    public function refreshInterval(int $milliseconds): self
    {
        $this->refreshInterval = $milliseconds;

        return $this;
    }

    /**
     * Alias for refreshInterval - set in seconds
     */
    public function refreshEvery(int $seconds): self
    {
        return $this->refreshInterval($seconds * 1000);
    }

    /**
     * Enable/disable auto-refresh
     */
    public function autoRefresh(int $seconds): self
    {
        return $this->refreshEvery($seconds);
    }

    /**
     * Set whether to load data on initialization
     */
    public function loadOnInit(bool $load = true): self
    {
        $this->loadOnInit = $load;

        return $this;
    }

    /**
     * Alias for chartOptions
     */
    public function options(array $options): self
    {
        return $this->chartOptions($options);
    }

    public function chartOptions(array $options): self
    {
        $this->chartOptions = array_merge($this->chartOptions, $options);

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function responsive(bool $responsive = true): self
    {
        $this->responsive = $responsive;

        return $this;
    }

    public function animated(bool $animated = true): self
    {
        $this->animated = $animated;

        return $this;
    }

    public function library(string $library): self
    {
        $this->library = $library;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'chart_type' => $this->chartType,
            'labels' => $this->labels,
            'datasets' => $this->datasets,
            'series' => $this->series,
            'data_source' => $this->dataSource,
            'data_method' => $this->dataMethod,
            'data_params' => $this->dataParams,
            'refresh_interval' => $this->refreshInterval,
            'load_on_init' => $this->loadOnInit,
            'chart_options' => $this->chartOptions,
            'height' => $this->height,
            'responsive' => $this->responsive,
            'animated' => $this->animated,
            'library' => $this->library,
        ]));
    }
}
