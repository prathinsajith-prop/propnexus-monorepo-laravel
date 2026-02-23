<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogNotesForm
 *
 * Form for adding notes to blog posts with:
 * - Note content (textarea)
 * - Note type (general, important, todo, feedback)
 * - Priority (low, medium, high)
 */
class BlogNotesForm
{
    /**
     * Create blog notes form structure
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method (POST/PUT)
     * @param  string  $action  Form action URL
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $method, $action)
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(2)
            ->gap('md')
            ->meta([
                'isEditable' => true,
                'dataKey' => 'data',
            ]);

        $form->textarea('note_content')
            ->label(__('layout.note_content'))
            ->placeholder(__('layout.add_note_placeholder'))
            ->attribute('rows', 4)
            ->required(true)
            ->validation('required|string|max:1000')
            ->help(__('layout.max_1000_chars'))
            ->col(12);

        $form->select('note_type')
            ->label(__('layout.note_type'))
            ->options([
                ['value' => 'general', 'label' => __('layout.general')],
                ['value' => 'important', 'label' => __('layout.important')],
                ['value' => 'todo', 'label' => __('layout.todo')],
                ['value' => 'feedback', 'label' => __('layout.feedback')],
            ])
            ->placeholder(__('layout.select_note_type'))
            ->value('general')
            ->col(6);

        $form->select('note_priority')
            ->label(__('layout.priority'))
            ->options([
                ['value' => 'low', 'label' => __('layout.low')],
                ['value' => 'medium', 'label' => __('layout.medium')],
                ['value' => 'high', 'label' => __('layout.high')],
            ])
            ->value('medium')
            ->col(6);

        // Add form action buttons
        $form->actions([
            // ['label' => 'Clear', 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'reset', 'type' => 'reset'],
            // ['label' => 'Add Note', 'color' => 'primary', 'icon' => 'check', 'type' => 'submit'],
            // ['label' => '', 'color' => 'primary', 'icon' => 'pen', 'type' => 'button', 'isIconButton' => true],
        ]);

        return $form;
    }
}
