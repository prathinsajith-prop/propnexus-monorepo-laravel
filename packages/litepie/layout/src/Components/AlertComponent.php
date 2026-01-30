<?php

namespace Litepie\Layout\Components;

class AlertComponent extends BaseComponent
{
    protected string $variant = 'info'; // info, success, warning, error, default

    protected ?string $message = null;

    protected ?string $content = null;

    protected ?string $title = null;

    protected bool $dismissible = false;

    protected bool $bordered = false;

    protected bool $filled = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'alert');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function type(string $type): self
    {
        return $this->variant($type);
    }

    public function info(): self
    {
        return $this->variant('info');
    }

    public function success(): self
    {
        return $this->variant('success');
    }

    public function warning(): self
    {
        return $this->variant('warning');
    }

    public function error(): self
    {
        return $this->variant('error');
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    public function bordered(bool $bordered = true): self
    {
        $this->bordered = $bordered;

        return $this;
    }

    public function filled(bool $filled = true): self
    {
        $this->filled = $filled;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'variant' => $this->variant,
            'message' => $this->message,
            'title' => $this->title,
            'content' => $this->content,
            'dismissible' => $this->dismissible,
            'bordered' => $this->bordered,
            'filled' => $this->filled,
        ]));
    }
}
