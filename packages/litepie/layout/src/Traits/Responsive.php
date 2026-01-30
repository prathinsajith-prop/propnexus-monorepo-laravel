<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\Responsive\DeviceDetector;

trait Responsive
{
    protected array $responsiveColumns = [];

    protected array $responsiveVisibility = [];

    protected array $responsiveOrder = [];

    protected array $deviceConfig = [];

    protected ?string $targetDevice = null;

    /**
     * Set columns per breakpoint
     *
     * Usage:
     * ->responsiveColumns(['xs' => 1, 'sm' => 2, 'md' => 3, 'lg' => 4])
     */
    public function responsiveColumns(array $columns): self
    {
        $this->responsiveColumns = $columns;

        return $this;
    }

    /**
     * Set visibility per breakpoint
     *
     * Usage:
     * ->visibleOn(['md', 'lg', 'xl'])
     * ->hiddenOn(['xs', 'sm'])
     */
    public function visibleOn(array|string $breakpoints): self
    {
        $breakpoints = is_array($breakpoints) ? $breakpoints : [$breakpoints];

        foreach ($breakpoints as $breakpoint) {
            $this->responsiveVisibility[$breakpoint] = true;
        }

        return $this;
    }

    /**
     * Hide on specific breakpoints
     */
    public function hiddenOn(array|string $breakpoints): self
    {
        $breakpoints = is_array($breakpoints) ? $breakpoints : [$breakpoints];

        foreach ($breakpoints as $breakpoint) {
            $this->responsiveVisibility[$breakpoint] = false;
        }

        return $this;
    }

    /**
     * Set order per breakpoint
     *
     * Usage:
     * ->responsiveOrder(['xs' => 2, 'md' => 1])
     */
    public function responsiveOrder(array $order): self
    {
        $this->responsiveOrder = $order;

        return $this;
    }

    /**
     * Set device-specific configuration
     *
     * Usage:
     * ->setDeviceConfig('mobile', ['columns' => 1, 'order' => 2])
     * ->setDeviceConfig('tablet', ['columns' => 2, 'hidden' => false])
     */
    public function setDeviceConfig(string $device, array $config): self
    {
        $this->deviceConfig[$device] = $config;

        return $this;
    }

    /**
     * Get device-specific configuration
     */
    public function getDeviceConfig(string $device): array
    {
        return $this->deviceConfig[$device] ?? [];
    }

    /**
     * Target specific device
     */
    public function forDevice(string $device): self
    {
        $this->targetDevice = $device;

        return $this;
    }

    /**
     * Show only on mobile
     */
    public function mobileOnly(): self
    {
        return $this->visibleOn(['xs', 'sm']);
    }

    /**
     * Show only on tablet
     */
    public function tabletOnly(): self
    {
        return $this->visibleOn(['md']);
    }

    /**
     * Show only on desktop
     */
    public function desktopOnly(): self
    {
        return $this->visibleOn(['lg', 'xl', '2xl']);
    }

    /**
     * Hide on mobile
     */
    public function hiddenMobile(): self
    {
        return $this->hiddenOn(['xs', 'sm']);
    }

    /**
     * Hide on tablet
     */
    public function hiddenTablet(): self
    {
        return $this->hiddenOn(['md']);
    }

    /**
     * Hide on desktop
     */
    public function hiddenDesktop(): self
    {
        return $this->hiddenOn(['lg', 'xl', '2xl']);
    }

    /**
     * Check if visible on current device
     */
    public function isVisibleOnDevice(?string $device = null): bool
    {
        if (empty($this->responsiveVisibility)) {
            return true;
        }

        $device = $device ?? $this->detectDevice();
        $breakpoint = $this->getBreakpointForDevice($device);

        return $this->responsiveVisibility[$breakpoint] ?? true;
    }

    /**
     * Detect current device
     */
    protected function detectDevice(): string
    {
        return (new DeviceDetector)->getDeviceType();
    }

    /**
     * Get breakpoint for device
     */
    protected function getBreakpointForDevice(string $device): string
    {
        return match ($device) {
            'mobile' => 'xs',
            'tablet' => 'md',
            'desktop' => 'xl',
            default => 'md',
        };
    }

    /**
     * Add responsive properties to array
     */
    protected function addResponsiveToArray(array $array): array
    {
        if (! empty($this->responsiveColumns)) {
            $array['responsive_columns'] = $this->responsiveColumns;
        }

        if (! empty($this->responsiveVisibility)) {
            $array['responsive_visibility'] = $this->responsiveVisibility;
        }

        if (! empty($this->responsiveOrder)) {
            $array['responsive_order'] = $this->responsiveOrder;
        }

        if (! empty($this->deviceConfig)) {
            $array['device_config'] = $this->deviceConfig;
        }

        if ($this->targetDevice) {
            $array['target_device'] = $this->targetDevice;
        }

        return $array;
    }

    /**
     * Get columns for current breakpoint
     */
    public function getColumnsForBreakpoint(string $breakpoint): ?int
    {
        return $this->responsiveColumns[$breakpoint] ?? $this->columns ?? null;
    }
}
