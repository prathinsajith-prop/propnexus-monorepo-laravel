<?php

namespace Litepie\Layout\Components;

/**
 * PageHeader Component
 * 
 * A composite component that combines breadcrumb navigation and page title
 * in a cohesive, reusable header layout.
 * 
 * @example
 * $section->pageHeader('main-header')
 *     ->title('Sales Orders')
 *     ->subtitle('Manage all your sales orders in one place')
 *     ->breadcrumbs([
 *         ['label' => 'Dashboard', 'link' => '/'],
 *         ['label' => 'Sales', 'link' => '/sales'],
 *         ['label' => 'Orders', 'active' => true]
 *     ])
 *     ->align('left')
 *     ->spacing('md');
 */
class PageHeaderComponent extends BaseComponent
{
    protected ?string $pageTitle = null;
    
    protected ?string $subtitle = null;
    
    protected array $breadcrumbs = [];
    
    protected string $align = 'left'; // left, center, right
    
    protected string $spacing = 'md'; // sm, md, lg, xl
    
    protected ?string $icon = null;
    
    protected bool $showBreadcrumb = true;
    
    protected bool $showTitle = true;
    
    protected ?string $titleVariant = 'h1';
    
    protected ?string $titleSize = '2xl';
    
    protected ?string $titleWeight = 'bold';
    
    protected bool $titleGutterBottom = true;

    public function __construct(string $name)
    {
        parent::__construct($name, 'pageHeader');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Set the page title
     */
    public function title(string $title): self
    {
        $this->pageTitle = $title;
        return $this;
    }

    /**
     * Set the subtitle
     */
    public function subtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * Set breadcrumb items
     * 
     * @param array $items Array of breadcrumb items: [['label' => 'Home', 'link' => '/', 'icon' => 'LiHome'], ...]
     */
    public function breadcrumbs(array $items): self
    {
        $this->breadcrumbs = $items;
        return $this;
    }

    /**
     * Add a single breadcrumb item
     */
    public function addBreadcrumb(string $label, ?string $link = null, ?string $icon = null, bool $active = false): self
    {
        $item = ['label' => $label];
        
        if ($link !== null) {
            $item['link'] = $link;
        }
        
        if ($icon !== null) {
            $item['icon'] = $icon;
        }
        
        if ($active) {
            $item['active'] = true;
        }
        
        $this->breadcrumbs[] = $item;
        return $this;
    }

    /**
     * Set alignment: left, center, right
     */
    public function align(string $align): self
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Set spacing: sm, md, lg, xl
     */
    public function spacing(string $spacing): self
    {
        $this->spacing = $spacing;
        return $this;
    }

    /**
     * Set icon for the title
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Show or hide breadcrumb
     */
    public function showBreadcrumb(bool $show = true): self
    {
        $this->showBreadcrumb = $show;
        return $this;
    }

    /**
     * Show or hide title
     */
    public function showTitle(bool $show = true): self
    {
        $this->showTitle = $show;
        return $this;
    }

    /**
     * Set title variant: h1, h2, h3, h4, h5, h6
     */
    public function titleVariant(string $variant): self
    {
        $this->titleVariant = $variant;
        return $this;
    }

    /**
     * Set title size: xs, sm, base, lg, xl, 2xl, 3xl, 4xl
     */
    public function titleSize(string $size): self
    {
        $this->titleSize = $size;
        return $this;
    }

    /**
     * Set title weight: light, normal, medium, semibold, bold
     */
    public function titleWeight(string $weight): self
    {
        $this->titleWeight = $weight;
        return $this;
    }

    /**
     * Set whether title has bottom margin
     */
    public function titleGutterBottom(bool $gutter = true): self
    {
        $this->titleGutterBottom = $gutter;
        return $this;
    }

    /**
     * Convert component to array for JSON serialization
     */
    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'pageTitle' => $this->pageTitle,
            'subtitle' => $this->subtitle,
            'breadcrumbs' => $this->breadcrumbs,
            'align' => $this->align,
            'spacing' => $this->spacing,
            'icon' => $this->icon,
            'showBreadcrumb' => $this->showBreadcrumb ? true : null,
            'showTitle' => $this->showTitle ? true : null,
            'titleVariant' => $this->titleVariant,
            'titleSize' => $this->titleSize,
            'titleWeight' => $this->titleWeight,
            'titleGutterBottom' => $this->titleGutterBottom ? true : null,
        ]));
    }
}
