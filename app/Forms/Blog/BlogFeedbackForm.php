<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogFeedbackForm
 * 
 * Form for collecting feedback with:
 * - Feedback message (textarea)
 * - Agree to terms (checkbox)
 * 
 * @package App\Forms\Blog
 */
class BlogFeedbackForm
{
    /**
     * Create blog feedback form structure
     *
     * @param string $formId Form identifier
     * @param string $method HTTP method (POST/PUT)
     * @param string $action Form action URL
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make(string $formId = 'feedback-form', string $method = 'POST', string $action = '/api/blogs/:id/feedback')
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(1)
            ->gap('md')
            ->title('Blog Feedback')
            ->meta([
                'isEditable' => true,
                'dataKey' => 'data',
            ]);

        $form->textarea('feedback_message')
            ->label('Feedback Message')
            ->placeholder('Share your thoughts, suggestions, or report any issues...')
            ->attribute('rows', 6)
            ->required(true)
            ->validation('required|string|max:2000')
            ->help('Please provide detailed feedback (max 2000 characters)')
            ->col(12);

        $form->checkbox('agree_terms')
            ->label('I agree to the terms and conditions')
            ->required(true)
            ->validation('required|accepted')
            ->help('You must agree to the terms to submit feedback')
            ->col(12);

        $form->checkbox('subscribe_updates')
            ->label('Subscribe to updates about this feedback')
            ->value(false)
            ->help('Get notified when there are responses to your feedback')
            ->col(12);

        // Add form action buttons
        $form->actions([
            ['label' => 'Cancel', 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close', 'type' => 'button'],
            ['label' => 'Submit Feedback', 'color' => 'primary', 'icon' => 'send', 'type' => 'submit'],
        ]);

        return $form;
    }
}
