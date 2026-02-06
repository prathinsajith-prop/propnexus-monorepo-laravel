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
            ->label(__('layout.message'))
            ->placeholder(__('layout.type_message_placeholder'))
            ->attribute('rows', 3)
            ->required(true)
            ->validation('required|string|max:500')
            ->help(__('layout.max_500_chars'))
            ->col(12);

        $form->checkbox('internal_only')
            ->label(__('layout.internal_only'))
            ->value(false)
            ->help(__('layout.internal_only_help'))
            ->col(12);

        // Add form action buttons
        $form->actions([
            ['label' => __('layout.clear'), 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'reset', 'type' => 'reset'],
            ['label' => __('layout.send_message'), 'color' => 'primary', 'icon' => 'send', 'type' => 'submit'],
        ]);

        return $form;
    }
}
