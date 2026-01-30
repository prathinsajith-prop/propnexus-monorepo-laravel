<?php

namespace Litepie\Layout\Components;

class FilterComponent extends BaseComponent
{
    protected string $filterType = 'standard'; // standard, advanced, sidebar, inline, drawer
    
    protected array $filters = [];
    
    protected ?string $searchField = null;
    
    protected bool $searchable = true;
    
    protected ?string $searchPlaceholder = 'Search...';
    
    protected bool $showClearAll = true;
    
    protected bool $showApplyButton = true;
    
    protected bool $showResetButton = true;
    
    protected string $applyButtonText = 'Apply Filters';
    
    protected string $resetButtonText = 'Reset';
    
    protected string $clearAllText = 'Clear All';
    
    protected bool $collapsible = false;
    
    protected bool $collapsed = false;
    
    protected ?string $submitAction = null;
    
    protected string $submitMethod = 'GET';
    
    protected bool $liveFilter = false;
    
    protected int $liveFilterDelay = 300; // milliseconds
    
    protected bool $showActiveCount = true;
    
    protected string $position = 'top'; // top, left, right
    
    protected string $width = 'medium'; // small, medium, large, full
    
    protected bool $sticky = false;
    
    protected array $presets = [];
    
    protected ?string $activePreset = null;
    
    protected bool $rememberFilters = false;
    
    protected ?string $storageKey = null;
    
    protected array $quickFilters = [];

    public function __construct(string $name)
    {
        parent::__construct($name, 'filter');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Set filter type (standard, advanced, sidebar, inline, drawer)
     */
    public function filterType(string $type): self
    {
        $this->filterType = $type;
        return $this;
    }

    public function standard(): self
    {
        return $this->filterType('standard');
    }

    public function advanced(): self
    {
        return $this->filterType('advanced');
    }

    public function sidebar(): self
    {
        return $this->filterType('sidebar');
    }

    public function inline(): self
    {
        return $this->filterType('inline');
    }

    public function drawer(): self
    {
        return $this->filterType('drawer');
    }

    /**
     * Add a filter field
     * 
     * @param string $name Field name
     * @param string $type Filter type (select, multiselect, checkbox, radio, range, date, daterange, text, number, toggle, chips)
     * @param string $label Field label
     * @param array $options Filter options
     */
    public function addFilter(string $name, string $type, string $label, array $options = []): self
    {
        $this->filters[$name] = array_merge([
            'name' => $name,
            'type' => $type,
            'label' => $label,
        ], $options);

        return $this;
    }

    /**
     * Add select filter
     */
    public function addSelectFilter(string $name, string $label, array $options, array $config = []): self
    {
        return $this->addFilter($name, 'select', $label, array_merge([
            'options' => $options,
            'placeholder' => 'Select ' . $label,
        ], $config));
    }

    /**
     * Add multiselect filter
     */
    public function addMultiSelectFilter(string $name, string $label, array $options, array $config = []): self
    {
        return $this->addFilter($name, 'multiselect', $label, array_merge([
            'options' => $options,
            'placeholder' => 'Select ' . $label,
        ], $config));
    }

    /**
     * Add checkbox group filter
     */
    public function addCheckboxFilter(string $name, string $label, array $options, array $config = []): self
    {
        return $this->addFilter($name, 'checkbox', $label, array_merge([
            'options' => $options,
        ], $config));
    }

    /**
     * Add radio filter
     */
    public function addRadioFilter(string $name, string $label, array $options, array $config = []): self
    {
        return $this->addFilter($name, 'radio', $label, array_merge([
            'options' => $options,
        ], $config));
    }

    /**
     * Add range filter (for numbers)
     */
    public function addRangeFilter(string $name, string $label, int $min, int $max, array $config = []): self
    {
        return $this->addFilter($name, 'range', $label, array_merge([
            'min' => $min,
            'max' => $max,
            'step' => 1,
        ], $config));
    }

    /**
     * Add price range filter
     */
    public function addPriceRangeFilter(string $name, string $label, int $min, int $max, array $config = []): self
    {
        return $this->addFilter($name, 'range', $label, array_merge([
            'min' => $min,
            'max' => $max,
            'step' => 1,
            'prefix' => '$',
            'format' => 'currency',
        ], $config));
    }

    /**
     * Add date filter
     */
    public function addDateFilter(string $name, string $label, array $config = []): self
    {
        return $this->addFilter($name, 'date', $label, array_merge([
            'placeholder' => 'Select date',
        ], $config));
    }

    /**
     * Add date range filter
     */
    public function addDateRangeFilter(string $name, string $label, array $config = []): self
    {
        return $this->addFilter($name, 'daterange', $label, array_merge([
            'placeholder' => 'Select date range',
        ], $config));
    }

    /**
     * Add text search filter
     */
    public function addTextFilter(string $name, string $label, array $config = []): self
    {
        return $this->addFilter($name, 'text', $label, array_merge([
            'placeholder' => 'Enter ' . strtolower($label),
        ], $config));
    }

    /**
     * Add number filter
     */
    public function addNumberFilter(string $name, string $label, array $config = []): self
    {
        return $this->addFilter($name, 'number', $label, array_merge([
            'placeholder' => 'Enter ' . strtolower($label),
        ], $config));
    }

    /**
     * Add toggle/switch filter
     */
    public function addToggleFilter(string $name, string $label, array $config = []): self
    {
        return $this->addFilter($name, 'toggle', $label, $config);
    }

    /**
     * Add chips/tag filter
     */
    public function addChipsFilter(string $name, string $label, array $options, array $config = []): self
    {
        return $this->addFilter($name, 'chips', $label, array_merge([
            'options' => $options,
        ], $config));
    }

    /**
     * Add autocomplete filter
     */
    public function addAutocompleteFilter(string $name, string $label, array $options, array $config = []): self
    {
        return $this->addFilter($name, 'autocomplete', $label, array_merge([
            'options' => $options,
            'placeholder' => 'Type to search...',
        ], $config));
    }

    /**
     * Add rating filter
     */
    public function addRatingFilter(string $name, string $label, array $config = []): self
    {
        return $this->addFilter($name, 'rating', $label, array_merge([
            'max' => 5,
            'allowHalf' => false,
        ], $config));
    }

    /**
     * Add quick filter (simplified filter that appears in quick filter bar)
     * Quick filters provide fast, simple filtering options that appear above the main advanced filters
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param string $type Filter type (select, daterange, range, etc.)
     * @param array $options Filter options or configuration
     */
    public function addQuickFilter(string $name, string $label, string $type, array $options = []): self
    {
        // Handle different option formats based on type
        $filterConfig = [
            'name' => $name,
            'type' => $type,
            'label' => $label,
        ];

        // If options is a numeric array and type is select, treat as options list
        if ($type === 'select' && isset($options[0]) && is_array($options[0])) {
            $filterConfig['options'] = $options;
            $filterConfig['placeholder'] = 'Select ' . $label;
        } else {
            // Otherwise merge options as configuration
            $filterConfig = array_merge($filterConfig, $options);
        }

        $this->quickFilters[$name] = $filterConfig;

        return $this;
    }

    /**
     * Add operators to a specific filter
     * Operators define how the filter value should be compared (is, is_not, in, not_in, greater_than, less_than, between, etc.)
     * 
     * @param string $filterName The name of the filter to add operators to
     * @param array $operators Array of operators: [['value' => 'is', 'label' => 'Is'], ...]
     */
    public function addOperators(string $filterName, array $operators): self
    {
        if (isset($this->filters[$filterName])) {
            $this->filters[$filterName]['operators'] = $operators;
        }
        return $this;
    }

    /**
     * Add quick options to a specific filter
     * Quick options provide pre-configured filter values with operators for common use cases
     * 
     * @param string $filterName The name of the filter to add quick options to
     * @param array $quickOptions Array of quick options: [['label' => 'High Priority', 'value' => 'high', 'operator' => 'is'], ...]
     */
    public function addQuickOptions(string $filterName, array $quickOptions): self
    {
        if (isset($this->filters[$filterName])) {
            $this->filters[$filterName]['quickOptions'] = $quickOptions;
        }
        return $this;
    }

    /**
     * Set the default operator for a filter
     * 
     * @param string $filterName The name of the filter
     * @param string $operator The default operator value
     */
    public function setDefaultOperator(string $filterName, string $operator): self
    {
        if (isset($this->filters[$filterName])) {
            $this->filters[$filterName]['defaultOperator'] = $operator;
        }
        return $this;
    }

    /**
     * Set all filters at once
     */
    public function filters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Enable/disable search field
     */
    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * Set search field name
     */
    public function searchField(string $field): self
    {
        $this->searchField = $field;
        return $this;
    }

    /**
     * Set search placeholder
     */
    public function searchPlaceholder(string $placeholder): self
    {
        $this->searchPlaceholder = $placeholder;
        return $this;
    }

    /**
     * Show/hide clear all button
     */
    public function showClearAll(bool $show = true): self
    {
        $this->showClearAll = $show;
        return $this;
    }

    /**
     * Show/hide apply button
     */
    public function showApplyButton(bool $show = true): self
    {
        $this->showApplyButton = $show;
        return $this;
    }

    /**
     * Show/hide reset button
     */
    public function showResetButton(bool $show = true): self
    {
        $this->showResetButton = $show;
        return $this;
    }

    /**
     * Set button texts
     */
    public function applyButtonText(string $text): self
    {
        $this->applyButtonText = $text;
        return $this;
    }

    public function resetButtonText(string $text): self
    {
        $this->resetButtonText = $text;
        return $this;
    }

    public function clearAllText(string $text): self
    {
        $this->clearAllText = $text;
        return $this;
    }

    /**
     * Make the filter collapsible
     */
    public function collapsible(bool $collapsible = true): self
    {
        $this->collapsible = $collapsible;
        return $this;
    }

    /**
     * Set collapsed state
     */
    public function collapsed(bool $collapsed = true): self
    {
        $this->collapsed = $collapsed;
        return $this;
    }

    /**
     * Set submit action URL
     */
    public function submitAction(string $action): self
    {
        $this->submitAction = $action;
        return $this;
    }

    /**
     * Set submit method
     */
    public function submitMethod(string $method): self
    {
        $this->submitMethod = strtoupper($method);
        return $this;
    }

    /**
     * Enable live filtering (no submit button, filters on change)
     */
    public function liveFilter(bool $live = true, int $delay = 300): self
    {
        $this->liveFilter = $live;
        $this->liveFilterDelay = $delay;
        return $this;
    }

    /**
     * Show active filter count
     */
    public function showActiveCount(bool $show = true): self
    {
        $this->showActiveCount = $show;
        return $this;
    }

    /**
     * Set filter position (top, left, right)
     */
    public function position(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Set filter width (small, medium, large, full)
     */
    public function width(string $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Make filter sticky
     */
    public function sticky(bool $sticky = true): self
    {
        $this->sticky = $sticky;
        return $this;
    }

    /**
     * Add filter preset
     */
    public function addPreset(string $key, string $label, array $filters): self
    {
        $this->presets[$key] = [
            'label' => $label,
            'filters' => $filters,
        ];
        return $this;
    }

    /**
     * Set all presets
     */
    public function presets(array $presets): self
    {
        $this->presets = $presets;
        return $this;
    }

    /**
     * Set active preset
     */
    public function activePreset(string $preset): self
    {
        $this->activePreset = $preset;
        return $this;
    }

    /**
     * Remember filters in local storage
     */
    public function rememberFilters(bool $remember = true, ?string $storageKey = null): self
    {
        $this->rememberFilters = $remember;
        $this->storageKey = $storageKey ?? $this->name;
        return $this;
    }

    /**
     * Get all filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get a specific filter
     */
    public function getFilter(string $name): ?array
    {
        return $this->filters[$name] ?? null;
    }

    /**
     * Convert to array for JSON output
     */
    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'filterType' => $this->filterType,
            'filters' => $this->filters,
            'quickFilters' => $this->quickFilters,
            'searchField' => $this->searchField,
            'searchable' => $this->searchable,
            'searchPlaceholder' => $this->searchPlaceholder,
            'showClearAll' => $this->showClearAll,
            'showApplyButton' => $this->showApplyButton,
            'showResetButton' => $this->showResetButton,
            'applyButtonText' => $this->applyButtonText,
            'resetButtonText' => $this->resetButtonText,
            'clearAllText' => $this->clearAllText,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'submitAction' => $this->submitAction,
            'submitMethod' => $this->submitMethod,
            'liveFilter' => $this->liveFilter,
            'liveFilterDelay' => $this->liveFilterDelay,
            'showActiveCount' => $this->showActiveCount,
            'position' => $this->position,
            'width' => $this->width,
            'sticky' => $this->sticky,
            'presets' => $this->presets,
            'activePreset' => $this->activePreset,
            'rememberFilters' => $this->rememberFilters,
            'storageKey' => $this->storageKey,
        ]));
    }
}
