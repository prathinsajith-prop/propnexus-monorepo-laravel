<?php

namespace Litepie\Layout\Components;

class LinkComponent extends BaseComponent
{
    protected string $href = '#';

    protected ?string $text = null;

    protected string $color = 'primary';

    protected string $underline = 'hover'; // none, hover, always

    protected ?string $variant = null; // body1, body2, button, caption, overline, etc.

    protected ?string $size = null; // small, medium, large

    protected ?string $target = null; // _blank, _self, _parent, _top

    protected ?string $rel = null; // noopener, noreferrer, nofollow

    protected ?string $download = null; // filename for download attribute

    protected ?string $prefixIcon = null;

    protected ?string $suffixIcon = null;

    protected bool $disabled = false;

    protected bool $visited = false;

    protected bool $active = false;

    protected ?string $title = null; // HTML title attribute

    protected ?string $ariaLabel = null;

    protected ?string $className = null; // CSS class names

    public function __construct(string $name)
    {
        parent::__construct($name, 'link');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function href(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function underline(string $underline): self
    {
        $this->underline = $underline;

        return $this;
    }

    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function target(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function rel(string $rel): self
    {
        $this->rel = $rel;

        return $this;
    }

    public function download(?string $filename = null): self
    {
        $this->download = $filename ?? 'download';

        return $this;
    }

    public function prefixIcon(string $icon): self
    {
        $this->prefixIcon = $icon;

        return $this;
    }

    public function suffixIcon(string $icon): self
    {
        $this->suffixIcon = $icon;

        return $this;
    }

    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function visited(bool $visited = true): self
    {
        $this->visited = $visited;

        return $this;
    }

    public function active(bool $active = true): self
    {
        $this->active = $active;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function ariaLabel(string $label): self
    {
        $this->ariaLabel = $label;

        return $this;
    }

    public function addClass(string $class): self
    {
        if ($this->className) {
            $this->className .= ' ' . $class;
        } else {
            $this->className = $class;
        }

        return $this;
    }

    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    // Convenience methods for common link patterns

    public function external(string $href, ?string $text = null): self
    {
        $this->href = $href;
        if ($text) {
            $this->text = $text;
        }
        $this->target = '_blank';
        $this->rel = 'noopener noreferrer';
        $this->suffixIcon = 'external-link';

        return $this;
    }

    public function email(string $email, ?string $subject = null): self
    {
        $href = "mailto:{$email}";
        if ($subject) {
            $href .= "?subject=" . urlencode($subject);
        }
        $this->href = $href;
        $this->prefixIcon = 'mail';

        return $this;
    }

    public function phone(string $phone): self
    {
        $this->href = "tel:" . preg_replace('/[^0-9+]/', '', $phone);
        $this->text = $this->text ?? $phone;
        $this->prefixIcon = 'phone';

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'href' => $this->href,
            'text' => $this->text,
            'color' => $this->color,
            'underline' => $this->underline,
            'variant' => $this->variant,
            'size' => $this->size,
            'target' => $this->target,
            'rel' => $this->rel,
            'download' => $this->download,
            'prefixIcon' => $this->prefixIcon,
            'suffixIcon' => $this->suffixIcon,
            'disabled' => $this->disabled ? true : null,
            'visited' => $this->visited ? true : null,
            'active' => $this->active ? true : null,
            'title' => $this->title,
            'ariaLabel' => $this->ariaLabel,
            'className' => $this->className,
        ]));
    }
}
