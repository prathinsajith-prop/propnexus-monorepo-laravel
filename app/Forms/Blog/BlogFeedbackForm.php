<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogFeedbackForm
 *
 * Form for collecting feedback with:
 * - Feedback message (textarea)
 * - Agree to terms (checkbox)
 */
class BlogFeedbackForm
{
    /**
     * Create blog feedback form structure
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method (POST/PUT)
     * @param  string  $action  Form action URL
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make(string $formId = 'feedback-form', string $method = 'POST', string $action = '/api/blogs/:id/feedback')
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(1)
            ->gap('md')
            ->title(__('layout.blog_feedback'))
            ->meta([
                'isEditable' => true,
                'dataKey' => 'data',
            ]);

        $form->textarea('feedback_message')
            ->label(__('layout.feedback_message'))
            ->placeholder(__('layout.share_feedback_placeholder'))
            ->attribute('rows', 6)
            ->required(true)
            ->validation('required|string|max:2000')
            ->help(__('layout.feedback_help'))
            ->col(12);

        $form->checkbox('agree_terms')
            ->label(__('layout.agree_terms'))
            ->required(true)
            ->validation('required|accepted')
            ->help(__('layout.agree_terms_help'))
            ->col(12);

        $form->checkbox('subscribe_updates')
            ->label(__('layout.subscribe_updates'))
            ->value(false)
            ->help(__('layout.subscribe_updates_help'))
            ->col(12);

        // Add form action buttons
        $form->actions([
            ['label' => __('layout.cancel'), 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close', 'type' => 'button'],
            ['label' => __('layout.submit_feedback'), 'color' => 'primary', 'icon' => 'sendright', 'type' => 'submit'],
        ]);

        return $form;
    }
}
