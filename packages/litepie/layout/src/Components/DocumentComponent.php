<?php

namespace Litepie\Layout\Components;

class DocumentComponent extends BaseComponent
{
    protected string $documentType = 'upload'; // upload, list, viewer

    protected array $allowedTypes = []; // e.g., ['pdf', 'doc', 'docx', 'txt']

    protected ?int $maxSize = null; // In MB

    protected ?int $maxFiles = null; // Maximum number of files

    protected bool $multiple = false;

    protected bool $dragDrop = true;

    protected string $uploadUrl = '';

    protected string $listUrl = '';

    protected string $deleteUrl = '';

    protected string $downloadUrl = '';

    // Display options
    protected string $displayMode = 'table'; // table, grid, list

    protected bool $showPreview = true;

    protected bool $showSize = true;

    protected bool $showDate = true;

    protected bool $showActions = true;

    protected array $columns = []; // Custom columns for table view

    protected array $filters = []; // Filter options (by type, date, size)

    protected bool $searchable = true;

    protected bool $sortable = true;

    protected array $items = []; // Document configurations

    public function __construct(string $name)
    {
        parent::__construct($name, 'document');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function documentType(string $type): self
    {
        $this->documentType = $type;

        return $this;
    }

    public function upload(): self
    {
        return $this->documentType('upload');
    }

    public function list(): self
    {
        return $this->documentType('list');
    }

    public function viewer(): self
    {
        return $this->documentType('viewer');
    }

    public function allowedTypes(array $types): self
    {
        $this->allowedTypes = $types;

        return $this;
    }

    public function maxSize(int $size): self
    {
        $this->maxSize = $size;

        return $this;
    }

    public function maxFiles(int $max): self
    {
        $this->maxFiles = $max;

        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function dragDrop(bool $dragDrop = true): self
    {
        $this->dragDrop = $dragDrop;

        return $this;
    }

    public function uploadUrl(string $url): self
    {
        $this->uploadUrl = $url;

        return $this;
    }

    public function listUrl(string $url): self
    {
        $this->listUrl = $url;

        return $this;
    }

    public function deleteUrl(string $url): self
    {
        $this->deleteUrl = $url;

        return $this;
    }

    public function downloadUrl(string $url): self
    {
        $this->downloadUrl = $url;

        return $this;
    }

    public function displayMode(string $mode): self
    {
        $this->displayMode = $mode;

        return $this;
    }

    public function table(): self
    {
        return $this->displayMode('table');
    }

    public function grid(): self
    {
        return $this->displayMode('grid');
    }

    public function listMode(): self
    {
        return $this->displayMode('list');
    }

    public function showPreview(bool $show = true): self
    {
        $this->showPreview = $show;

        return $this;
    }

    public function showSize(bool $show = true): self
    {
        $this->showSize = $show;

        return $this;
    }

    public function showDate(bool $show = true): self
    {
        $this->showDate = $show;

        return $this;
    }

    public function showActions(bool $show = true): self
    {
        $this->showActions = $show;

        return $this;
    }

    public function columns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function addColumn(string $key, string $label, array $options = []): self
    {
        $this->columns[] = [
            'key' => $key,
            'label' => $label,
            'sortable' => $options['sortable'] ?? true,
            'width' => $options['width'] ?? null,
            'align' => $options['align'] ?? 'left',
        ];

        return $this;
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function addFilter(string $key, string $label, array $options = []): self
    {
        $this->filters[] = [
            'key' => $key,
            'label' => $label,
            'type' => $options['type'] ?? 'select',
            'options' => $options['options'] ?? [],
        ];

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

    /**
     * Add document item configuration
     */
    public function addItem(string $key, array $options = []): self
    {
        $this->items[] = [
            'key' => $key,
            'name' => $options['name'] ?? null,
            'type' => $options['type'] ?? null,
            'size' => $options['size'] ?? null,
            'url' => $options['url'] ?? null,
            'thumbnail' => $options['thumbnail'] ?? null,
            'uploaded_at' => $options['uploaded_at'] ?? null,
            'uploaded_by' => $options['uploaded_by'] ?? null,
            'description' => $options['description'] ?? null,
        ];

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'document_type' => $this->documentType,
            'allowed_types' => $this->allowedTypes,
            'max_size' => $this->maxSize,
            'max_files' => $this->maxFiles,
            'multiple' => $this->multiple,
            'drag_drop' => $this->dragDrop,
            'upload_url' => $this->uploadUrl,
            'list_url' => $this->listUrl,
            'delete_url' => $this->deleteUrl,
            'download_url' => $this->downloadUrl,
            'display_mode' => $this->displayMode,
            'show_preview' => $this->showPreview,
            'show_size' => $this->showSize,
            'show_date' => $this->showDate,
            'show_actions' => $this->showActions,
            'columns' => $this->columns,
            'filters' => $this->filters,
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'items' => $this->items,
        ]));
    }
}
