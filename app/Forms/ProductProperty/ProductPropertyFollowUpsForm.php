<?php

namespace App\Forms\ProductProperty;

use Litepie\Layout\Components\FormComponent;

/**
 * ProductPropertyFollowUpsForm
 *
 * Form for scheduling follow-ups on a product property with:
 * - Follow-up title
 * - Scheduled date and time
 * - Follow-up type (call, meeting, viewing, offer, other)
 * - Description / notes
 * - Email reminder option
 *
 * @package App\Forms\ProductProperty
 */
class ProductPropertyFollowUpsForm
{
    /**
     * Create the product property follow-up form structure.
     *
     * @param string $formId  Form identifier
     * @param string $method  HTTP method (POST/PUT)
     * @param string $action  Form action URL (e.g. /api/product-property/:id/followups)
     * @return FormComponent
     */
    public static function make(string $formId, string $method, string $action): FormComponent
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
                ['value' => 'call',    'label' => __('layout.call')],
                ['value' => 'meeting', 'label' => __('layout.meeting')],
                ['value' => 'viewing', 'label' => __('layout.viewing')],
                ['value' => 'offer',   'label' => __('layout.offer')],
                ['value' => 'other',   'label' => __('layout.other')],
            ])
            ->value('call')
            ->col(6);

        $form->textarea('description')
            ->label(__('layout.description'))
            ->placeholder(__('layout.followup_description_placeholder'))
            ->attribute('rows', 3)
            ->col(12);

        $form->checkbox('send_reminder')
            ->label(__('layout.send_email_reminder'))
            ->value(true)
            ->help(__('layout.reminder_help'))
            ->col(12);

        $form->actions([]);

        return $form;
    }
}
