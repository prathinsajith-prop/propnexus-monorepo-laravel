<?php

namespace Litepie\Layout\Components;

class TableComponent extends BaseComponent
{
    protected array $tableColumns = [];

    protected array $tableData = [];

    protected bool $searchable = false;

    protected bool $sortable = false;

    protected bool $filterable = false;

    protected bool $selectable = false;

    protected bool $paginated = false;

    protected ?int $perPage = null;

    protected ?string $sortColumn = null;

    protected string $sortDirection = 'asc';

    protected bool $hoverable = false;

    protected bool $striped = false;

    protected bool $rowClickable = false;

    protected string $displayMode = 'table'; // table, list, grid, cards

    protected ?array $rowActionsConfig = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'table');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Add a column to the table
     */
    public function addColumn(string $key, string $label, array $options = []): self
    {
        $this->tableColumns[] = array_merge([
            'key' => $key,
            'label' => $label,
        ], $options);

        return $this;
    }

    public function columns(array $columns): self
    {
        $this->tableColumns = $columns;

        return $this;
    }

    public function data(array $data): self
    {
        $this->tableData = $data;

        return $this;
    }

    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function filterable(bool $filterable = true): self
    {
        $this->filterable = $filterable;

        return $this;
    }

    public function selectable(bool $selectable = true): self
    {
        $this->selectable = $selectable;

        return $this;
    }

    public function hoverable(bool $hoverable = true): self
    {
        $this->hoverable = $hoverable;

        return $this;
    }

    public function striped(bool $striped = true): self
    {
        $this->striped = $striped;

        return $this;
    }

    public function rowClickable(bool $rowClickable = true): self
    {
        $this->rowClickable = $rowClickable;

        return $this;
    }

    public function paginated(bool $paginated = true): self
    {
        $this->paginated = $paginated;

        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Alias for perPage() - enables pagination and sets per page count
     */
    public function paginate(int $perPage): self
    {
        $this->paginated = true;
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Alias for paginate() - enables pagination and sets per page count
     */
    public function pagination(int $perPage): self
    {
        return $this->paginate($perPage);
    }

    public function defaultSort(string $column, string $direction = 'asc'): self
    {
        $this->sortColumn = $column;
        $this->sortDirection = $direction;

        return $this;
    }

    /**
     * Set display mode for the table/data
     */
    public function displayMode(string $mode): self
    {
        $this->displayMode = $mode;

        return $this;
    }

    /**
     * Display data as table (default)
     */
    public function asTable(): self
    {
        return $this->displayMode('table');
    }

    /**
     * Display data as list view
     */
    public function asList(): self
    {
        return $this->displayMode('list');
    }

    /**
     * Display data as grid view
     */
    public function asGrid(): self
    {
        return $this->displayMode('grid');
    }

    /**
     * Display data as cards view
     */
    public function asCards(): self
    {
        return $this->displayMode('cards');
    }

    /**
     * Configure row click actions
     * 
     * @param string $type Action type ('aside', 'modal')
     * @param string $component Component name to open
     * @param string|null $dataUrl Optional data URL with placeholders like '/api/blogs/:id'
     * @return self
     */
    public function rowActions(string $type, string $component, ?string $dataUrl = null, ?array $config = null): self
    {
        $this->rowActionsConfig = [
            'type' => $type,
            'component' => $component,
            'config' => $config
        ];

        if ($dataUrl !== null) {
            $this->rowActionsConfig['dataUrl'] = $dataUrl;
        }

        return $this;
    }

    /**
     * Enable row clickability and configure row actions
     * This is a convenience method that combines rowClickable with rowActions
     * 
     * @param string $type Action type ('aside', 'modal')
     * @param string $component Component name to open
     * @param string|null $dataUrl Optional data URL with placeholders like '/api/blogs/:id'
     * @return self
     */
    public function clickableRows(string $type, string $component, ?string $dataUrl = null, ?array $config = null): self
    {
        $this->rowClickable = true;
        return $this->rowActions($type, $component, $dataUrl, $config);
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'columns' => $this->tableColumns,
            'data' => $this->tableData,
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'filterable' => $this->filterable,
            'selectable' => $this->selectable,
            'hoverable' => $this->hoverable,
            'striped' => $this->striped,
            'rowClickable' => $this->rowClickable,
            'paginated' => $this->paginated,
            'perPage' => $this->perPage,
            'sortColumn' => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
            'displayMode' => $this->displayMode !== 'table' ? $this->displayMode : null,
            'rowActions' => $this->rowActionsConfig,
        ]));
    }
}
