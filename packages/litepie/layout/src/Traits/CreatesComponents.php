<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Contracts\Component;
use Litepie\Layout\Registry\TypeRegistry;
use Litepie\Layout\Exceptions\ComponentNotFoundException;

/**
 * CreatesComponents Trait
 *
 * Provides ability to create and add component instances.
 * Components are leaf nodes (Card, Button, Text, Table, etc.)
 * 
 * This trait should be used by container classes (Sections) that can hold components.
 */
trait CreatesComponents
{
    /**
     * Storage for components
     * @var Component[]
     */
    protected array $components = [];

    /**
     * Create and add a Component
     * 
     * @param string $type Component type (card, button, text, filter, etc.)
     * @param string $name Unique identifier
     * @return Component The created component instance
     * @throws ComponentNotFoundException If component type doesn't exist
     */
    public function component(string $type, string $name): Component
    {
        $className = $this->resolveComponentClassName($type);

        if (!$className) {
            throw new ComponentNotFoundException($type, TypeRegistry::getAllComponentTypes());
        }

        $component = $className::make($name);
        $component->parentBuilder = $this;
        $this->components[] = $component;

        return $component;
    }

    /**
     * Add an existing component instance
     */
    public function addComponent(Component $component): self
    {
        $component->parentBuilder = $this;
        $this->components[] = $component;

        return $this;
    }

    /**
     * Get all components
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Check if has components
     */
    public function hasComponents(): bool
    {
        return !empty($this->components);
    }

    /**
     * Resolve component class name from type using TypeRegistry (O(1) lookup)
     * 
     * @param string $type Component type identifier
     * @return string|null Full class name or null if not found
     */
    protected function resolveComponentClassName(string $type): ?string
    {
        return TypeRegistry::getComponent($type);
    }

    // ========================================================================
    // Convenience Methods - Common Components
    // ========================================================================

    public function form(string $name): Component
    {
        return $this->component('form', $name);
    }

    public function filter(string $name): Component
    {
        return $this->component('filter', $name);
    }

    public function text(string $name): Component
    {
        return $this->component('text', $name);
    }

    public function card(string $name): Component
    {
        return $this->component('card', $name);
    }

    public function alert(string $name): Component
    {
        return $this->component('alert', $name);
    }

    public function table(string $name): Component
    {
        return $this->component('table', $name);
    }

    public function button(string $name): Component
    {
        return $this->component('button', $name);
    }

    public function buttonGroup(string $name): Component
    {
        return $this->component('button-group', $name);
    }

    public function list(string $name): Component
    {
        return $this->component('list', $name);
    }

    public function chart(string $name): Component
    {
        return $this->component('chart', $name);
    }

    public function stats(string $name): Component
    {
        return $this->component('stats', $name);
    }

    public function breadcrumb(string $name): Component
    {
        return $this->component('breadcrumb', $name);
    }

    public function avatar(string $name): Component
    {
        return $this->component('avatar', $name);
    }

    public function avatarGroup(string $name): Component
    {
        return $this->component('avatar-group', $name);
    }

    public function badge(string $name): Component
    {
        return $this->component('badge', $name);
    }

    public function image(string $name): Component
    {
        return $this->component('image', $name);
    }

    public function video(string $name): Component
    {
        return $this->component('video', $name);
    }

    public function media(string $name): Component
    {
        return $this->component('media', $name);
    }

    public function videoPlayer(string $name): Component
    {
        $component = $this->component('media', $name);
        // Set the media type to 'video' automatically
        // @phpstan-ignore-next-line - MediaComponent has video() method
        $component->video();
        return $component;
    }

    public function link(string $name): Component
    {
        return $this->component('link', $name);
    }

    public function divider(string $name): Component
    {
        return $this->component('divider', $name);
    }

    public function spacer(string $name): Component
    {
        return $this->component('spacer', $name);
    }

    public function timeline(string $name): Component
    {
        return $this->component('timeline', $name);
    }

    public function comment(string $name): Component
    {
        return $this->component('comment', $name);
    }

    public function code(string $name): Component
    {
        return $this->component('code', $name);
    }

    public function drawer(string $name): Component
    {
        return $this->component('drawer', $name);
    }

    public function document(string $name): Component
    {
        return $this->component('document', $name);
    }

    public function modal(string $name): Component
    {
        return $this->component('modal', $name);
    }

    public function stepper(string $name): Component
    {
        return $this->component('stepper', $name);
    }

    public function pageHeader(string $name): Component
    {
        return $this->component('pageHeader', $name);
    }
}
