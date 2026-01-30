<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogFollowUpsForm
 * 
 * Form for scheduling follow-ups on blog posts with:
 * - Follow-up title
 * - Scheduled date and time
 * - Follow-up type (review, update, publish, other)
 * - Description
 * - Email reminder option
 * 
 * @package App\Forms\Blog
 */
class BlogFollowUpsForm
{
    /**
     * Create blog follow-ups form structure
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
            ->columns(2)
            ->gap('md');

        $form->text('followup_title')
            ->label('Follow-up Title')
            ->placeholder('e.g., Review content, Update images')
            ->required(true)
            ->validation('required|string|max:200')
            ->col(12);

        $form->datetime('followup_date')
            ->label('Follow-up Date & Time')
            ->required(true)
            ->validation('required|date|after:now')
            ->help('Schedule when to follow up')
            ->col(6);

        $form->select('followup_type')
            ->label('Type')
            ->options([
                ['value' => 'review', 'label' => 'Review'],
                ['value' => 'update', 'label' => 'Update'],
                ['value' => 'publish', 'label' => 'Publish'],
                ['value' => 'other', 'label' => 'Other'],
            ])
            ->value('review')
            ->col(6);

        $form->textarea('followup_description')
            ->label('Description')
            ->placeholder('Add details about this follow-up...')
            ->attribute('rows', 3)
            ->col(12);

        $form->checkbox('send_reminder')
            ->label('Send Email Reminder')
            ->value(true)
            ->help('Receive email notification before follow-up')
            ->col(12);

        // Add form action buttons
        $form->actions([
            // ['label' => 'Clear', 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'reset', 'type' => 'reset'],
            // ['label' => 'Schedule Follow-up', 'color' => 'primary', 'icon' => 'check', 'type' => 'submit'],
        ]);

        return $form;
    }
}
