<?php

namespace Litepie\Layout\Sections;

/**
 * GridSection
 *
 * Comprehensive CSS Grid layout section with full grid property support.
 * Directly contains sections and components (no slots).
 *
 * Example:
 *   $grid = GridSection::make('dashboard')
 *       ->columns(3)
 *       ->rows(2)
 *       ->gap('lg')
 *       ->columnGap('md')
 *       ->rowGap('sm')
 *       ->autoFlow('row')
 *       ->alignItems('center')
 *       ->justifyItems('start')
 *       ->alignContent('space-between')
 *       ->justifyContent('center')
 *       ->templateColumns('repeat(auto-fit, minmax(250px, 1fr))')
 *       ->templateRows('auto 1fr auto')
 *       ->autoColumns('1fr')
 *       ->autoRows('minmax(100px, auto)');
 *
 *   $grid->add($card1);
 *   $grid->add($card2);
 */
class GridSection extends BaseSection
{
    protected int $gridColumns;
    protected int $gridRows;
    protected ?string $gap = null;
    protected ?string $columnGap = null;
    protected ?string $rowGap = null;
    protected ?string $templateColumns = null;
    protected ?string $templateRows = null;
    protected ?string $templateAreas = null;
    protected ?string $autoFlow = null;
    protected ?string $autoColumns = null;
    protected ?string $autoRows = null;
    protected ?string $alignItems = null;
    protected ?string $justifyItems = null;
    protected ?string $alignContent = null;
    protected ?string $justifyContent = null;
    protected ?string $placeItems = null;
    protected ?string $placeContent = null;
    protected ?bool $responsive = null;
    protected ?int $minColumnWidth = null;
    protected ?array $columnSizes = null;
    protected ?int $gridColumnSpan = null;

    public function __construct(string $name, int $columns = 1, int $rows = 1)
    {
        parent::__construct($name, 'grid');
        $this->gridColumns = $columns;
        $this->gridRows = $rows;
    }

    public static function make(string $name, int $columns = 1, int $rows = 1): self
    {
        return new static($name, $columns, $rows);
    }

    /**
     * Set the number of columns
     */
    public function columns(int $columns): self
    {
        $this->gridColumns = $columns;

        return $this;
    }

    /**
     * Set the number of rows
     */
    public function rows(int $rows): self
    {
        $this->gridRows = $rows;

        return $this;
    }

    /**
     * Set both column and row gap (shorthand)
     */
    public function gap(string $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * Set column gap specifically
     */
    public function columnGap(string $gap): self
    {
        $this->columnGap = $gap;

        return $this;
    }

    /**
     * Set row gap specifically
     */
    public function rowGap(string $gap): self
    {
        $this->rowGap = $gap;

        return $this;
    }

    /**
     * Set grid-template-columns (e.g., 'repeat(3, 1fr)', '200px 1fr 2fr')
     */
    public function templateColumns(string $template): self
    {
        $this->templateColumns = $template;

        return $this;
    }

    /**
     * Set grid-template-rows (e.g., 'auto 1fr auto', 'repeat(3, 100px)')
     */
    public function templateRows(string $template): self
    {
        $this->templateRows = $template;

        return $this;
    }

    /**
     * Set grid-template-areas for named grid areas
     */
    public function templateAreas(string $areas): self
    {
        $this->templateAreas = $areas;

        return $this;
    }

    /**
     * Set grid-auto-flow (row | column | dense | row dense | column dense)
     */
    public function autoFlow(string $flow): self
    {
        $this->autoFlow = $flow;

        return $this;
    }

    /**
     * Set grid-auto-columns (e.g., '1fr', 'minmax(100px, 1fr)')
     */
    public function autoColumns(string $size): self
    {
        $this->autoColumns = $size;

        return $this;
    }

    /**
     * Set grid-auto-rows (e.g., 'minmax(100px, auto)')
     */
    public function autoRows(string $size): self
    {
        $this->autoRows = $size;

        return $this;
    }

    /**
     * Set align-items (start | end | center | stretch)
     */
    public function alignItems(string $align): self
    {
        $this->alignItems = $align;

        return $this;
    }

    /**
     * Set justify-items (start | end | center | stretch)
     */
    public function justifyItems(string $justify): self
    {
        $this->justifyItems = $justify;

        return $this;
    }

    /**
     * Set align-content (start | end | center | stretch | space-between | space-around | space-evenly)
     */
    public function alignContent(string $align): self
    {
        $this->alignContent = $align;

        return $this;
    }

    /**
     * Set justify-content (start | end | center | stretch | space-between | space-around | space-evenly)
     */
    public function justifyContent(string $justify): self
    {
        $this->justifyContent = $justify;

        return $this;
    }

    /**
     * Set place-items (shorthand for align-items and justify-items)
     */
    public function placeItems(string $place): self
    {
        $this->placeItems = $place;

        return $this;
    }

    /**
     * Set place-content (shorthand for align-content and justify-content)
     */
    public function placeContent(string $place): self
    {
        $this->placeContent = $place;

        return $this;
    }

    /**
     * Enable responsive grid behavior
     */
    public function responsive(bool $responsive = true): self
    {
        $this->responsive = $responsive;

        return $this;
    }

    /**
     * Set minimum column width for responsive auto-fit/auto-fill patterns
     */
    public function minColumnWidth(int $width): self
    {
        $this->minColumnWidth = $width;

        return $this;
    }

    /**
     * Set column sizes as an array (e.g., [6, 6] or [8, 2, 2])
     * Automatically converts to templateColumns using 'fr' units
     * Also stores the array for API output
     * 
     * Usage: ->columnSizes([6, 6]) generates 'grid-template-columns: 6fr 6fr'
     *        ->columnSizes([8, 2, 2]) generates 'grid-template-columns: 8fr 2fr 2fr'
     */
    public function columnSizes(array $sizes): self
    {
        $this->columnSizes = $sizes;

        // Also set templateColumns automatically
        $template = implode(' ', array_map(fn($size) => $size . 'fr', $sizes));
        $this->templateColumns = $template;

        return $this;
    }

    /**
     * Helper: Create a responsive auto-fit grid
     * Usage: ->autoFit(250) generates: repeat(auto-fit, minmax(250px, 1fr))
     */
    public function autoFit(int $minWidth): self
    {
        $this->templateColumns = "repeat(auto-fit, minmax({$minWidth}px, 1fr))";
        $this->responsive = true;
        $this->minColumnWidth = $minWidth;
        return $this;
    }

    /**
     * Helper: Create a responsive auto-fill grid
     * Usage: ->autoFill(250) generates: repeat(auto-fill, minmax(250px, 1fr))
     */
    public function autoFill(int $minWidth): self
    {
        $this->templateColumns = "repeat(auto-fill, minmax({$minWidth}px, 1fr))";
        $this->responsive = true;
        $this->minColumnWidth = $minWidth;
        return $this;
    }

    /**
     * Set grid-column span for nested grids
     * This allows a child grid to span multiple columns in its parent grid
     * Usage: ->gridColumnSpan(8) generates: grid-column: span 8
     */
    public function gridColumnSpan(int $span): self
    {
        $this->gridColumnSpan = $span;
        return $this;
    }

    public function getColumns(): int
    {
        return $this->gridColumns;
    }

    public function getRows(): int
    {
        return $this->gridRows;
    }

    public function toArray(): array
    {
        $data = $this->getCommonProperties();

        $gridProperties = [
            'columns' => $this->gridColumns,
            'rows' => $this->gridRows,
        ];

        if ($this->gap !== null) $gridProperties['gap'] = $this->gap;
        if ($this->columnGap !== null) $gridProperties['columnGap'] = $this->columnGap;
        if ($this->rowGap !== null) $gridProperties['rowGap'] = $this->rowGap;
        if ($this->templateColumns !== null) $gridProperties['templateColumns'] = $this->templateColumns;
        if ($this->templateRows !== null) $gridProperties['templateRows'] = $this->templateRows;
        if ($this->templateAreas !== null) $gridProperties['templateAreas'] = $this->templateAreas;
        if ($this->autoFlow !== null) $gridProperties['autoFlow'] = $this->autoFlow;
        if ($this->autoColumns !== null) $gridProperties['autoColumns'] = $this->autoColumns;
        if ($this->autoRows !== null) $gridProperties['autoRows'] = $this->autoRows;
        if ($this->alignItems !== null) $gridProperties['alignItems'] = $this->alignItems;
        if ($this->justifyItems !== null) $gridProperties['justifyItems'] = $this->justifyItems;
        if ($this->alignContent !== null) $gridProperties['alignContent'] = $this->alignContent;
        if ($this->justifyContent !== null) $gridProperties['justifyContent'] = $this->justifyContent;
        if ($this->placeItems !== null) $gridProperties['placeItems'] = $this->placeItems;
        if ($this->placeContent !== null) $gridProperties['placeContent'] = $this->placeContent;
        if ($this->responsive !== null) $gridProperties['responsive'] = $this->responsive;
        if ($this->minColumnWidth !== null) $gridProperties['minColumnWidth'] = $this->minColumnWidth;
        if ($this->columnSizes !== null) $gridProperties['columnSizes'] = $this->columnSizes;
        if ($this->gridColumnSpan !== null) $gridProperties['gridColumnSpan'] = $this->gridColumnSpan;

        return array_merge($data, $gridProperties, [
            'permissions' => $this->permissions ?? [],
            'roles' => $this->roles ?? [],
            'authorized_to_see' => $this->authorizedToSee ?? null,
        ]);
    }
}
