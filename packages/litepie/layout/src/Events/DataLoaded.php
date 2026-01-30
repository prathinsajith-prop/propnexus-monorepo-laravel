<?php

namespace Litepie\Layout\Events;

class DataLoaded extends LayoutEvent
{
    public function __construct(
        string $componentName,
        string $componentType,
        public mixed $data = null,
        public ?string $dataUrl = null
    ) {
        parent::__construct($componentName, $componentType, $data);
    }
}
