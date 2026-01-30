<?php

namespace Litepie\Layout\Components;

class TimelineComponent extends BaseComponent
{
    protected string $orientation = 'vertical'; // vertical, horizontal

    protected string $position = 'left'; // left, right, center, alternate

    protected bool $showDates = true;

    protected bool $showIcons = true;

    protected string $dateFormat = 'relative'; // relative, absolute, custom

    protected array $events = []; // Event configurations

    public function __construct(string $name)
    {
        parent::__construct($name, 'timeline');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function orientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function vertical(): self
    {
        return $this->orientation('vertical');
    }

    public function horizontal(): self
    {
        return $this->orientation('horizontal');
    }

    public function position(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function alternate(): self
    {
        return $this->position('alternate');
    }

    public function showDates(bool $show = true): self
    {
        $this->showDates = $show;

        return $this;
    }

    public function showIcons(bool $show = true): self
    {
        $this->showIcons = $show;

        return $this;
    }

    public function dateFormat(string $format): self
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * Add event configuration (structure only)
     * Supports both patterns:
     * - addEvent('event1', ['icon' => 'check', 'color' => 'green'])
     * - addEvent(['key' => 'event1', 'icon' => 'check', 'color' => 'green'])
     */
    public function addEvent(string|array $keyOrEvent, array $options = []): self
    {
        if (is_array($keyOrEvent)) {
            // Array pattern: all data in first parameter
            $this->events[] = [
                'key' => $keyOrEvent['key'] ?? null,
                'title' => $keyOrEvent['title'] ?? null,
                'description' => $keyOrEvent['description'] ?? null,
                'date' => $keyOrEvent['date'] ?? null,
                'icon' => $keyOrEvent['icon'] ?? null,
                'color' => $keyOrEvent['color'] ?? null,
                'variant' => $keyOrEvent['variant'] ?? 'default',
            ];
        } else {
            // Individual parameters pattern
            $this->events[] = [
                'key' => $keyOrEvent,
                'title' => $options['title'] ?? null,
                'description' => $options['description'] ?? null,
                'date' => $options['date'] ?? null,
                'icon' => $options['icon'] ?? null,
                'color' => $options['color'] ?? null,
                'variant' => $options['variant'] ?? 'default',
            ];
        }

        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'orientation' => $this->orientation,
            'position' => $this->position,
            'show_dates' => $this->showDates,
            'show_icons' => $this->showIcons,
            'date_format' => $this->dateFormat,
            'events' => $this->events,
        ]));
    }
}
