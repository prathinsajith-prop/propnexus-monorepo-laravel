<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Debug\DebugInfo;

trait Debuggable
{
    protected ?DebugInfo $debugInfo = null;

    protected bool $debugEnabled = false;

    /**
     * Enable debug mode
     */
    public function debug(bool $enabled = true): self
    {
        $this->debugEnabled = $enabled;

        if ($enabled && $this->debugInfo === null) {
            $this->debugInfo = new DebugInfo;
        }

        return $this;
    }

    /**
     * Get debug info instance
     */
    protected function getDebugInfo(): ?DebugInfo
    {
        return $this->debugInfo;
    }

    /**
     * Add debug information
     */
    protected function addDebugInfo(string $key, mixed $value): void
    {
        if ($this->debugEnabled && $this->debugInfo) {
            $this->debugInfo->add($key, $value);
        }
    }

    /**
     * Record query for debugging
     */
    protected function recordDebugQuery(string $url, float $duration, ?int $statusCode = null): void
    {
        if ($this->debugEnabled && $this->debugInfo) {
            $this->debugInfo->recordQuery($url, $duration, $statusCode);
        }
    }

    /**
     * Record event for debugging
     */
    protected function recordDebugEvent(string $event, array $data = []): void
    {
        if ($this->debugEnabled && $this->debugInfo) {
            $this->debugInfo->recordEvent($event, $data);
        }
    }

    /**
     * Get debug output
     */
    public function getDebugOutput(): ?array
    {
        if (! $this->debugEnabled || ! $this->debugInfo) {
            return null;
        }

        return $this->debugInfo->toArray();
    }

    /**
     * Add debug info to array output
     */
    protected function addDebugToArray(array $array): array
    {
        if ($this->debugEnabled && $this->debugInfo) {
            $array['_debug'] = $this->debugInfo->toArray();
        }

        return $array;
    }
}
