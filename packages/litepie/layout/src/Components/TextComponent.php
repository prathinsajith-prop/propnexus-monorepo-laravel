<?php

namespace Litepie\Layout\Components;

class TextComponent extends BaseComponent
{
    protected ?string $content = null;

    protected string $size = 'md';

    protected string $align = 'left';

    protected ?string $color = null;

    protected ?string $variant = null;

    protected bool $gutterBottom = false;

    protected string|int|null $weight = null;

    protected ?string $style = null;

    protected ?string $decoration = null;

    protected ?string $format = null; // text, markdown, html

    protected ?string $spacing = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'text');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function align(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function gutterBottom(bool $gutterBottom = true): self
    {
        $this->gutterBottom = $gutterBottom;

        return $this;
    }

    public function weight(string|int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function style(string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function italic(): self
    {
        return $this->style('italic');
    }

    public function decoration(string $decoration): self
    {
        $this->decoration = $decoration;

        return $this;
    }

    public function underline(): self
    {
        return $this->decoration('underline');
    }

    public function lineThrough(): self
    {
        return $this->decoration('line-through');
    }

    public function overline(): self
    {
        return $this->decoration('overline');
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function spacing(string $spacing): self
    {
        $this->spacing = $spacing;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'content' => $this->content,
            'size' => $this->size,
            'align' => $this->align,
            'color' => $this->color,
            'variant' => $this->variant,
            'weight' => $this->weight,
            'style' => $this->style,
            'decoration' => $this->decoration,
            'gutterBottom' => $this->gutterBottom ? true : null,
            'format' => $this->format,
            'spacing' => $this->spacing,
        ]));
    }
}
