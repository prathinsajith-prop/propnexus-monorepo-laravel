<?php

namespace Litepie\Layout\Events;

abstract class LayoutEvent
{
    public function __construct(
        public string $componentName,
        public string $componentType,
        public mixed $payload = null
    ) {}
}
