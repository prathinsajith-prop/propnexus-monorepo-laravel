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
            ->label(__('layout.followup_title'))
            ->placeholder(__('layout.followup_placeholder'))
            ->required(true)
            ->validation('required|string|max:200')
            ->col(12);

        $form->datetime('followup_date')
            ->label(__('layout.followup_date_time'))
            ->required(true)
            ->validation('required|date|after:now')
            ->help(__('layout.schedule_followup_help'))
            ->col(6);

        $form->select('followup_type')
            ->label(__('layout.type'))
            ->options([
                ['value' => 'review', 'label' => __('layout.review')],
                ['value' => 'update', 'label' => __('layout.update')],
                ['value' => 'publish', 'label' => __('layout.publish')],
                ['value' => 'other', 'label' => __('layout.other')],
            ])
            ->value('review')
            ->col(6);

        $form->textarea('followup_description')
            ->label(__('layout.description'))
            ->placeholder(__('layout.followup_description_placeholder'))
            ->attribute('rows', 3)
            ->col(12);

        $form->checkbox('send_reminder')
            ->label(__('layout.send_email_reminder'))
            ->value(true)
            ->help(__('layout.reminder_help'))
            ->col(12);

        // Add form action buttons
        $form->actions([
            // ['label' => 'Clear', 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'reset', 'type' => 'reset'],
            // ['label' => 'Schedule Follow-up', 'color' => 'primary', 'icon' => 'check', 'type' => 'submit'],
        ]);

        return $form;
    }
}
