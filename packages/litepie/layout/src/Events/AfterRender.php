<?php

namespace Litepie\Layout\Events;

class AfterRender extends LayoutEvent
{
    public function __construct(
        string $componentName,
        string $componentType,
        public array $rendered = []
    ) {
        parent::__construct($componentName, $componentType, $rendered);
    }
}
