<?php

namespace Litepie\Layout\Traits;

/**
 * HasDataSource Trait
 *
 * Provides data source configuration for components that need to load data
 * either from frontend API calls or server-side callbacks.
 */
trait HasDataSource
{
    // Data source configuration for frontend loading
    protected ?string $dataSource = null;

    protected ?\Closure $dataSourceCallback = null;

    protected ?string $dataUrl = null;

    protected array $dataParams = [];

    protected ?string $dataTransform = null;

    protected bool $loadOnMount = true;

    protected bool $reloadOnChange = false;

    protected bool $useSharedData = false;

    protected ?string $dataKey = null;

    /**
     * Set data source endpoint for frontend loading or callback for server-side data
     * Accepts either a string URL or a Closure for server-side data generation
     */
    public function dataSource(string|\Closure $source): self
    {
        if ($source instanceof \Closure) {
            $this->dataSourceCallback = $source;
            // Execute the callback and store the result
            $this->dataSource = 'callback';
        } else {
            $this->dataSource = $source;
        }

        return $this;
    }

    /**
     * Get the data from the data source callback
     */
    public function getDataSourceData(): mixed
    {
        if ($this->dataSourceCallback) {
            return ($this->dataSourceCallback)();
        }

        return null;
    }

    /**
     * Set full data URL for frontend loading
     */
    public function dataUrl(string $url): self
    {
        $this->dataUrl = $url;

        return $this;
    }

    /**
     * Set data parameters for API call
     */
    public function dataParams(array $params): self
    {
        $this->dataParams = array_merge($this->dataParams, $params);

        return $this;
    }

    /**
     * Set data transform function name
     */
    public function dataTransform(string $transform): self
    {
        $this->dataTransform = $transform;

        return $this;
    }

    /**
     * Set whether to load data on component mount
     */
    public function loadOnMount(bool $load = true): self
    {
        $this->loadOnMount = $load;

        return $this;
    }

    /**
     * Set whether to reload when parent context changes
     */
    public function reloadOnChange(bool $reload = true): self
    {
        $this->reloadOnChange = $reload;

        return $this;
    }

    /**
     * Use data from shared/parent data source instead of separate API call
     */
    public function useSharedData(bool $shared = true, ?string $key = null): self
    {
        $this->useSharedData = $shared;
        if ($key !== null) {
            $this->dataKey = $key;
        }

        return $this;
    }

    /**
     * Set the key to extract data from shared data source
     */
    public function dataKey(string $key): self
    {
        $this->dataKey = $key;

        return $this;
    }

    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    public function getDataUrl(): ?string
    {
        return $this->dataUrl;
    }

    public function getDataParams(): array
    {
        return $this->dataParams;
    }

    /**
     * Get complete data configuration as structured array
     */
    public function getDataConfig(): array
    {
        $config = [
            'source' => $this->dataSource,
            'url' => $this->dataUrl,
            'params' => $this->dataParams,
            'transform' => $this->dataTransform,
            'load_on_mount' => $this->loadOnMount,
            'reload_on_change' => $this->reloadOnChange,
            'use_shared' => $this->useSharedData,
            'key' => $this->dataKey,
            'values' => $this->dataSourceCallback ? $this->getDataSourceData() : null,
        ];

        // Filter null values and empty arrays
        return array_filter($config, function ($value) {
            if ($value === null) {
                return false;
            }
            if (is_array($value) && empty($value)) {
                return false;
            }
            if ($value === false || $value === true) {
                return true; // Keep boolean values
            }

            return true;
        });
    }
}
