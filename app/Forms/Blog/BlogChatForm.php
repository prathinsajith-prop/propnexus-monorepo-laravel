<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogChatForm
 * 
 * Form for adding chat messages to blog posts with:
 * - Message content (textarea)
 * - Internal only flag
 * 
 * @package App\Forms\Blog
 */
class BlogChatForm
{
    /**
     * Create blog chat form structure
     *
     * @param string $formId Form identifier
     * @param string $method HTTP method (POST/PUT)
     * @param string $action Form action URL
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $method, $action)
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(1)
            ->gap('sm');

        $form->textarea('message')
            ->label('Message')
            ->placeholder('Type your message here...')
            ->attribute('rows', 3)
            ->required(true)
            ->validation('required|string|max:500')
            ->help('Maximum 500 characters')
            ->col(12);

        $form->checkbox('internal_only')
            ->label('Internal Only')
            ->value(false)
            ->help('Only team members can see this message')
            ->col(12);

        // Add form action buttons
        $form->actions([
            ['label' => 'Clear', 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'reset', 'type' => 'reset'],
            ['label' => 'Send Message', 'color' => 'primary', 'icon' => 'send', 'type' => 'submit'],
        ]);

        return $form;
    }
}
