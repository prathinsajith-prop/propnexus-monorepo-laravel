<?php

namespace Litepie\Layout\Components;

use Litepie\Layout\ActionModal;

/**
 * CardComponent
 *
 * Card component for displaying content with header, media, content, and actions.
 */
class CardComponent extends BaseComponent
{
    /**
     * If true, the card will use raised styling.
     */
    protected bool $raised = false;

    /**
     * The variant of the card (e.g., 'outlined', 'elevated', 'filled').
     */
    protected ?string $variant = null;

    /**
     * The content of the component (children).
     */
    protected ?string $children = null;

    // CardHeader properties
    /**
     * The action to display in the card header.
     * Can be a string, array of actions, or null.
     */
    protected string|array|null $action = null;

    /**
     * The Avatar element to display.
     */
    protected ?string $avatar = null;

    /**
     * The content of the Card header title.
     */
    protected ?string $title = null;

    /**
     * The content of the Card header subheader.
     */
    protected ?string $subheader = null;

    /**
     * If true, subheader and title won't be wrapped by a Typography component.
     */
    protected bool $disableTypography = false;

    // CardMedia properties
    /**
     * Image to be displayed as a background image or via src.
     */
    protected ?string $image = null;

    /**
     * An alias for image property. Available only with media components.
     */
    protected ?string $src = null;

    /**
     * The component used for the media. Either a string to use a HTML element or a component.
     */
    protected ?string $component = null;

    // CardContent (just holds children, no specific props)

    // CardActions properties
    /**
     * If true, the actions do not have additional margin.
     */
    protected bool $disableSpacing = false;

    /**
     * Array of action items for CardActions.
     */
    protected array $actions = [];

    /**
     * The footer content or components.
     */
    protected mixed $footer = null;

    /**
     * Badge text to display on the card.
     */
    protected ?string $badge = null;

    /**
     * Color theme for the card.
     */
    protected ?string $color = null;

    /**
     * Form component data.
     */
    protected mixed $form = null;

    /**
     * Array of child components to be rendered in the card content.
     */
    protected array $components = [];

    public function __construct(string $name)
    {
        parent::__construct($name, 'card');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ========================================================================
    // Card API Methods
    // ========================================================================

    /**
     * If true, the card will use raised styling.
     */
    public function raised(bool $raised = true): self
    {
        $this->raised = $raised;

        return $this;
    }

    /**
     * Set the variant of the card (e.g., 'outlined', 'elevated', 'filled').
     */
    public function variant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Set the content of the component (children).
     */
    public function children(string $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Alias for children() method. Set the content of the card.
     */
    public function content(string $content): self
    {
        return $this->children($content);
    }

    /**
     * Add a form component as the card's content.
     * Automatically handles form conversion from objects or arrays.
     * 
     * @param mixed $formComponent Form component (object with toArray() or array)
     * @param array $options Optional configuration:
     *   - 'actions': Array of action items for card footer
     *   - 'footer': Footer content
     * @return self
     */
    public function addForm(mixed $formComponent, array $options = []): self
    {
        // Convert form to array if it's an object with toArray() method
        $formContent = is_object($formComponent) && method_exists($formComponent, 'toArray')
            ? $formComponent->toArray()
            : $formComponent;

        // Set the form data
        $this->form = $formContent;

        // Add actions if provided
        if (isset($options['actions']) && is_array($options['actions'])) {
            $this->actions($options['actions']);
        }

        // Add footer if provided
        if (isset($options['footer'])) {
            $this->footer($options['footer']);
        }

        return $this;
    }

    // ========================================================================
    // CardHeader API Methods
    // ========================================================================

    /**
     * Set the action to display in the card header.
     * Can accept a string or array of action items.
     */
    public function action(string|array $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Add a single action to the card header.
     * Use this to build multiple header actions.
     */
    public function addHeaderAction(string $label, string $url, array $options = []): self
    {
        if (!is_array($this->action)) {
            $this->action = [];
        }

        $this->action[] = array_merge([
            'type' => 'button',
            'label' => $label,
            'url' => $url,
        ], $options);

        return $this;
    }

    /**
     * Add a ButtonComponent instance as a card header action.
     * Accepts a fully configured ButtonComponent and appends it to header actions.
     *
     * @param ButtonComponent $button Configured ButtonComponent instance
     * @return self
     *
     * Example:
     * ->addHeaderButton(
     *     ButtonComponent::make('add-btn')
     *         ->icon('plus')
     *         ->variant('outlined')
     *         ->size('sm')
     *         ->isIconButton(true)
     *         ->data('component', 'create-xyz')
     *         ->data('type', 'modal')
     *         ->meta(['tooltip' => 'Add'])
     * )
     */
    public function addHeaderButton(ButtonComponent $button): self
    {
        if (!is_array($this->action)) {
            $this->action = [];
        }

        $this->action[] = $button->toArray();

        return $this;
    }

    /**
     * Add a dropdown menu to the card header.
     * 
     * @param string $label The dropdown button label
     * @param array $items Array of menu items. Each item can have:
     *                     - 'label': Menu item text
     *                     - 'url': Action URL
     *                     - 'icon': Optional icon
     *                     - 'confirmation': Confirmation dialog config
     *                     - 'modal': Modal dialog config
     * @param array $options Additional options (icon, variant, etc.)
     * 
     * Example:
     * ->addHeaderDropdown('Actions', [
     *     ['label' => 'Edit', 'url' => '/edit', 'icon' => 'edit'],
     *     ['label' => 'Delete', 'url' => '/delete', 'confirmation' => [
     *         'title' => 'Confirm Delete',
     *         'message' => 'Are you sure?'
     *     ]],
     *     ['label' => 'Share', 'url' => '/share', 'modal' => [
     *         'title' => 'Share Item',
     *         'fields' => [...]
     *     ]]
     * ])
     */
    public function addHeaderDropdown(string $label, array $items, array $options = []): self
    {
        if (!is_array($this->action)) {
            $this->action = [];
        }

        $this->action[] = array_merge([
            'type' => 'dropdown',
            'label' => $label,
            'items' => $items,
        ], $options);

        return $this;
    }

    /**
     * Set the Avatar element to display.
     */
    public function avatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Set the title content.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the subheader content.
     */
    public function subheader(string $subheader): self
    {
        $this->subheader = $subheader;

        return $this;
    }

    /**
     * If true, subheader and title won't be wrapped by a Typography component.
     */
    public function disableTypography(bool $disableTypography = true): self
    {
        $this->disableTypography = $disableTypography;

        return $this;
    }

    // ========================================================================
    // CardMedia API Methods
    // ========================================================================

    /**
     * Set image to be displayed as a background image.
     */
    public function image(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Set the src (alias for image property).
     */
    public function src(string $src): self
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Set the component used for the media.
     */
    public function component(string $component): self
    {
        $this->component = $component;

        return $this;
    }

    // ========================================================================
    // CardActions API Methods
    // ========================================================================

    /**
     * If true, the actions do not have additional margin.
     */
    public function disableSpacing(bool $disableSpacing = true): self
    {
        $this->disableSpacing = $disableSpacing;

        return $this;
    }

    /**
     * Add an action item to CardActions.
     */
    public function addAction(string $label, string $actionUrl, array $options = []): self
    {
        $this->actions[] = array_merge([
            'label' => $label,
            'action' => $actionUrl,
        ], $options);

        return $this;
    }

    /**
     * Set all actions at once.
     */
    public function actions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Set the footer content or components.
     * Can accept a string, array, or closure that receives a slot/container.
     */
    public function footer(mixed $footer): self
    {
        if ($footer instanceof \Closure) {
            // If closure, we'll need to create a container/slot for it
            // For now, store the closure to be executed during rendering
            $this->footer = $footer;
        } else {
            $this->footer = $footer;
        }

        return $this;
    }

    /**
     * Set a badge text to display on the card.
     */
    public function badge(string $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Set the color theme for the card (e.g., 'primary', 'success', 'warning', 'error').
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    // ========================================================================
    // Component Management
    // ========================================================================

    /**
     * Add a component to the card's content area.
     * Accepts any component instance (Button, Text, List, Table, Form, etc.)
     * 
     * @param mixed $component Component instance (must have toArray() method)
     * @return self
     * 
     * Example:
     * $card->addComponent($section->text('description')->content('Card description'))
     *      ->addComponent($section->button('action')->label('Click me'));
     */
    public function addComponent($component): self
    {
        if (is_object($component) && method_exists($component, 'toArray')) {
            $this->components[] = $component->toArray();
        } elseif (is_array($component)) {
            $this->components[] = $component;
        }

        return $this;
    }

    /**
     * Set multiple components at once.
     * Replaces any existing components.
     * 
     * @param array $components Array of component instances or arrays
     * @return self
     */
    public function components(array $components): self
    {
        $this->components = [];

        foreach ($components as $component) {
            $this->addComponent($component);
        }

        return $this;
    }

    /**
     * Get all components added to the card.
     * 
     * @return array
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Alias for addComponent() - universal add method.
     * Maintains consistency with Section API.
     * 
     * @param mixed $component Component instance
     * @return self
     */
    public function add($component): self
    {
        return $this->addComponent($component);
    }

    // ========================================================================
    // Serialization
    // ========================================================================

    public function toArray(): array
    {
        $data = array_merge($this->getCommonProperties(), [
            'raised' => $this->raised,
            'variant' => $this->variant,
            'badge' => $this->badge,
            'color' => $this->color,
            'children' => $this->children,
        ]);

        // Add header section if any header properties are set
        $header = $this->filterNullValues([
            'actions' => $this->serializeActions($this->action),
            'avatar' => $this->avatar,
            'title' => $this->title,
            'subheader' => $this->subheader,
            'disableTypography' => $this->disableTypography ?: null,
        ]);
        if (!empty($header)) {
            $data['header'] = $header;
        }

        // Add media section if any media properties are set
        $media = $this->filterNullValues([
            'image' => $this->image,
            'src' => $this->src,
            'component' => $this->component,
        ]);
        if (!empty($media)) {
            $data['media'] = $media;
        }

        // Add actions section if any action properties are set
        if (!empty($this->actions) || $this->disableSpacing) {
            $data['actions'] = $this->filterNullValues([
                'disableSpacing' => $this->disableSpacing ?: null,
                'items' => $this->serializeActionItems($this->actions),
            ]);
        }

        // Add footer section if set
        if ($this->footer !== null) {
            if ($this->footer instanceof \Closure) {
                // Footer closures should be handled by the rendering layer
                $data['footer'] = ['type' => 'closure'];
            } else {
                $data['footer'] = $this->footer;
            }
        }

        // Add form section if set
        if ($this->form !== null) {
            $data['form'] = $this->form;
        }

        // Add components section if any components are set
        if (!empty($this->components)) {
            $data['components'] = $this->components;
        }

        return $data;
    }

    /**
     * Serialize action field to handle ActionModal objects
     */
    protected function serializeActions(string|array|null $action): string|array|null
    {
        if (!is_array($action)) {
            return $action;
        }

        return array_map(function ($item) {
            if (is_string($item)) {
                return $item;
            }

            // Handle dropdown items with modals
            if (isset($item['items']) && is_array($item['items'])) {
                $item['items'] = array_map(function ($dropdownItem) {
                    if (is_string($dropdownItem)) {
                        return $dropdownItem;
                    }

                    // Convert ActionModal to array
                    if (isset($dropdownItem['modal']) && $dropdownItem['modal'] instanceof ActionModal) {
                        $dropdownItem['modal'] = $dropdownItem['modal']->toArray();
                    }

                    return $dropdownItem;
                }, $item['items']);
            }

            // Handle direct modal on action
            if (isset($item['modal']) && $item['modal'] instanceof ActionModal) {
                $item['modal'] = $item['modal']->toArray();
            }

            return $item;
        }, $action);
    }

    /**
     * Serialize action items array to handle ActionModal objects
     */
    protected function serializeActionItems(array $actions): array
    {
        return array_map(function ($action) {
            if (isset($action['modal']) && $action['modal'] instanceof ActionModal) {
                $action['modal'] = $action['modal']->toArray();
            }
            return $action;
        }, $actions);
    }
}
