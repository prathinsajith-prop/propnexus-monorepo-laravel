<?php

namespace Litepie\Layout\Components;

/**
 * ModalComponent
 *
 * Modal component provides a solid foundation for creating dialogs, popovers, lightboxes, or whatever else.
 * Renders children in front of a backdrop component and manages focus, stacking, and accessibility.
 */
class ModalComponent extends BaseComponent
{
    // Core props
    protected bool $open = false;

    protected ?array $children = null; // Modal content

    // Callback handlers
    protected ?string $onClose = null; // Callback when modal should close

    // Backdrop props
    protected bool $hideBackdrop = false;

    protected ?array $backdropProps = null;

    // Keyboard interaction
    protected bool $disableEscapeKeyDown = false;

    // Focus management
    protected bool $disableAutoFocus = false;

    protected bool $disableEnforceFocus = false;

    protected bool $disableRestoreFocus = false;

    // Performance
    protected bool $keepMounted = false;

    // Portal
    protected bool $disablePortal = false;

    // Slots (for customization)
    protected ?array $slots = null;

    protected ?array $slotProps = null;

    // Accessibility
    protected ?string $ariaLabelledby = null;

    protected ?string $ariaDescribedby = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'modal');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Core Props
    // ========================================================================

    /**
     * Set modal open state
     */
    public function open(bool $open = true): self
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Set modal children content
     */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Set onClose callback
     */
    public function onClose(string $callback): self
    {
        $this->onClose = $callback;

        return $this;
    }

    // ========================================================================
    // Backdrop Props
    // ========================================================================

    /**
     * Hide the backdrop
     */
    public function hideBackdrop(bool $hide = true): self
    {
        $this->hideBackdrop = $hide;

        return $this;
    }

    /**
     * Set backdrop props
     */
    public function backdropProps(array $props): self
    {
        $this->backdropProps = $props;

        return $this;
    }

    // ========================================================================
    // Keyboard Interaction
    // ========================================================================

    /**
     * Disable closing on Escape key
     */
    public function disableEscapeKeyDown(bool $disable = true): self
    {
        $this->disableEscapeKeyDown = $disable;

        return $this;
    }

    // ========================================================================
    // Focus Management
    // ========================================================================

    /**
     * Disable auto focus when modal opens
     */
    public function disableAutoFocus(bool $disable = true): self
    {
        $this->disableAutoFocus = $disable;

        return $this;
    }

    /**
     * Disable focus trap (allow focus to escape modal)
     */
    public function disableEnforceFocus(bool $disable = true): self
    {
        $this->disableEnforceFocus = $disable;

        return $this;
    }

    /**
     * Disable restoring focus to previously focused element on close
     */
    public function disableRestoreFocus(bool $disable = true): self
    {
        $this->disableRestoreFocus = $disable;

        return $this;
    }

    // ========================================================================
    // Performance
    // ========================================================================

    /**
     * Keep modal mounted when closed (optimization for expensive content)
     */
    public function keepMounted(bool $keep = true): self
    {
        $this->keepMounted = $keep;

        return $this;
    }

    // ========================================================================
    // Portal
    // ========================================================================

    /**
     * Disable portal rendering (for SSR)
     */
    public function disablePortal(bool $disable = true): self
    {
        $this->disablePortal = $disable;

        return $this;
    }

    // ========================================================================
    // Customization (Slots)
    // ========================================================================

    /**
     * Set custom slots for modal components
     */
    public function slots(array $slots): self
    {
        $this->slots = $slots;

        return $this;
    }

    /**
     * Set props for custom slots
     */
    public function slotProps(array $props): self
    {
        $this->slotProps = $props;

        return $this;
    }

    // ========================================================================
    // Accessibility
    // ========================================================================

    /**
     * Set aria-labelledby attribute
     */
    public function ariaLabelledby(string $id): self
    {
        $this->ariaLabelledby = $id;

        return $this;
    }

    /**
     * Set aria-describedby attribute
     */
    public function ariaDescribedby(string $id): self
    {
        $this->ariaDescribedby = $id;

        return $this;
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'open' => $this->open ? true : null,
            'children' => $this->children,
            'onClose' => $this->onClose,
            'hideBackdrop' => $this->hideBackdrop ? true : null,
            'backdropProps' => $this->backdropProps,
            'disableEscapeKeyDown' => $this->disableEscapeKeyDown ? true : null,
            'disableAutoFocus' => $this->disableAutoFocus ? true : null,
            'disableEnforceFocus' => $this->disableEnforceFocus ? true : null,
            'disableRestoreFocus' => $this->disableRestoreFocus ? true : null,
            'keepMounted' => $this->keepMounted ? true : null,
            'disablePortal' => $this->disablePortal ? true : null,
            'slots' => $this->slots,
            'slotProps' => $this->slotProps,
            'aria-labelledby' => $this->ariaLabelledby,
            'aria-describedby' => $this->ariaDescribedby,
        ]));
    }
}
