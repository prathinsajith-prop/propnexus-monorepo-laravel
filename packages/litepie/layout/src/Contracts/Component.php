<?php

namespace Litepie\Layout\Contracts;

interface Component extends Renderable
{
    /**
     * Get the component type
     */
    public function getType(): string;

    /**
     * Get the component name
     */
    public function getName(): string;

    /**
     * Set component order
     */
    public function order(int $order): self;

    /**
     * Get component order
     */
    public function getOrder(): ?int;

    /**
     * Set component visibility
     */
    public function visible(bool $visible = true): self;

    /**
     * Check if component is visible
     */
    public function isVisible(): bool;

    /**
     * Set authorization permissions
     */
    public function permissions(array|string $permissions): self;

    /**
     * Set authorization roles
     */
    public function roles(array|string $roles): self;

    /**
     * Resolve authorization for user
     */
    public function resolveAuthorization($user = null): self;

    /**
     * Check if authorized to see
     */
    public function isAuthorizedToSee(): bool;
}
