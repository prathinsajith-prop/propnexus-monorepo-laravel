<?php

namespace Litepie\Layout\Traits;

use Illuminate\Support\Facades\Event;
use Litepie\Layout\Events\AfterRender;
use Litepie\Layout\Events\BeforeRender;
use Litepie\Layout\Events\DataError;
use Litepie\Layout\Events\DataLoaded;

trait HasEvents
{
    protected array $beforeRenderCallbacks = [];

    protected array $afterRenderCallbacks = [];

    protected array $dataLoadedCallbacks = [];

    protected array $dataErrorCallbacks = [];

    protected bool $eventsEnabled = true;

    /**
     * Register before render callback
     */
    public function onBeforeRender(callable $callback): self
    {
        $this->beforeRenderCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register after render callback
     */
    public function onAfterRender(callable $callback): self
    {
        $this->afterRenderCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register data loaded callback
     */
    public function onDataLoaded(callable $callback): self
    {
        $this->dataLoadedCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register data error callback
     */
    public function onDataError(callable $callback): self
    {
        $this->dataErrorCallbacks[] = $callback;

        return $this;
    }

    /**
     * Enable/disable events
     */
    public function withEvents(bool $enabled = true): self
    {
        $this->eventsEnabled = $enabled;

        return $this;
    }

    /**
     * Fire before render event
     */
    protected function fireBeforeRender(array $data = []): array
    {
        if (! $this->eventsEnabled) {
            return $data;
        }

        // Fire Laravel event
        Event::dispatch(new BeforeRender($this->name, $this->type, $data));

        // Execute callbacks
        foreach ($this->beforeRenderCallbacks as $callback) {
            $result = $callback($data, $this);
            if (is_array($result)) {
                $data = $result;
            }
        }

        return $data;
    }

    /**
     * Fire after render event
     */
    protected function fireAfterRender(array $rendered): array
    {
        if (! $this->eventsEnabled) {
            return $rendered;
        }

        // Fire Laravel event
        Event::dispatch(new AfterRender($this->name, $this->type, $rendered));

        // Execute callbacks
        foreach ($this->afterRenderCallbacks as $callback) {
            $result = $callback($rendered, $this);
            if (is_array($result)) {
                $rendered = $result;
            }
        }

        return $rendered;
    }

    /**
     * Fire data loaded event
     */
    protected function fireDataLoaded(mixed $data, ?string $url = null): mixed
    {
        if (! $this->eventsEnabled) {
            return $data;
        }

        // Fire Laravel event
        Event::dispatch(new DataLoaded($this->name, $this->type, $data, $url));

        // Execute callbacks
        foreach ($this->dataLoadedCallbacks as $callback) {
            $result = $callback($data, $this);
            if ($result !== null) {
                $data = $result;
            }
        }

        return $data;
    }

    /**
     * Fire data error event
     */
    protected function fireDataError(\Throwable $exception, ?string $url = null): void
    {
        if (! $this->eventsEnabled) {
            return;
        }

        // Fire Laravel event
        Event::dispatch(new DataError($this->name, $this->type, $exception, $url));

        // Execute callbacks
        foreach ($this->dataErrorCallbacks as $callback) {
            $callback($exception, $this);
        }
    }
}
