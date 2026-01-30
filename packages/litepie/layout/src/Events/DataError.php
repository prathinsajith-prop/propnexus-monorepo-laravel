<?php

namespace Litepie\Layout\Events;

class DataError extends LayoutEvent
{
    public function __construct(
        string $componentName,
        string $componentType,
        public \Throwable $exception,
        public ?string $dataUrl = null
    ) {
        parent::__construct($componentName, $componentType, $exception);
    }
}
