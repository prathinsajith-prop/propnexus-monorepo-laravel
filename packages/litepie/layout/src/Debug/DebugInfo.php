<?php

namespace Litepie\Layout\Debug;

class DebugInfo
{
    protected array $data = [];

    protected float $startTime;

    protected array $queries = [];

    protected array $events = [];

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Add debug information
     */
    public function add(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Record query execution
     */
    public function recordQuery(string $url, float $duration, ?int $statusCode = null): self
    {
        $this->queries[] = [
            'url' => $url,
            'duration' => $duration,
            'status_code' => $statusCode,
            'timestamp' => microtime(true),
        ];

        return $this;
    }

    /**
     * Record event
     */
    public function recordEvent(string $event, array $data = []): self
    {
        $this->events[] = [
            'event' => $event,
            'data' => $data,
            'timestamp' => microtime(true),
        ];

        return $this;
    }

    /**
     * Get execution time
     */
    public function getExecutionTime(): float
    {
        return round((microtime(true) - $this->startTime) * 1000, 2); // ms
    }

    /**
     * Get all debug data
     */
    public function toArray(): array
    {
        return [
            'execution_time' => $this->getExecutionTime(),
            'queries' => $this->queries,
            'query_count' => count($this->queries),
            'total_query_time' => array_sum(array_column($this->queries, 'duration')),
            'events' => $this->events,
            'event_count' => count($this->events),
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2), // MB
            'peak_memory' => round(memory_get_peak_usage() / 1024 / 1024, 2), // MB
            'data' => $this->data,
        ];
    }

    /**
     * Get summary
     */
    public function getSummary(): array
    {
        return [
            'execution_time' => $this->getExecutionTime().'ms',
            'queries' => count($this->queries),
            'events' => count($this->events),
            'memory' => round(memory_get_usage() / 1024 / 1024, 2).'MB',
        ];
    }
}
