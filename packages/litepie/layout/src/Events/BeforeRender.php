<?php

namespace Litepie\Layout\Events;

class BeforeRender extends LayoutEvent
{
    public function __construct(
        string $componentName,
        string $componentType,
        public array $data = []
    ) {
        parent::__construct($componentName, $componentType, $data);
    }
}
