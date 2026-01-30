<?php

namespace Litepie\Layout\Contracts;

interface Renderable
{
    public function toArray(): array;

    public function render(): array;
}
