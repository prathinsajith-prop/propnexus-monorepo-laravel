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
 * 
 * @package App\Forms\Blog
 */
class BlogNotesForm
{
    /**
     * Create blog notes form structure
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
            ->gap('md')
            ->meta([
                'isEditable' => true,
                'dataKey' => 'data',
            ]);

        $form->textarea('note_content')
            ->label('Note Content')
            ->placeholder('Add your note here...')
            ->attribute('rows', 4)
            ->required(true)
            ->validation('required|string|max:1000')
            ->help('Maximum 1000 characters')
            ->col(12);

        $form->select('note_type')
            ->label('Note Type')
            ->options([
                ['value' => 'general', 'label' => 'General'],
                ['value' => 'important', 'label' => 'Important'],
                ['value' => 'todo', 'label' => 'To-Do'],
                ['value' => 'feedback', 'label' => 'Feedback'],
            ])
            ->placeholder('Select note type')
            ->value('general')
            ->col(6);

        $form->select('note_priority')
            ->label('Priority')
            ->options([
                ['value' => 'low', 'label' => 'Low'],
                ['value' => 'medium', 'label' => 'Medium'],
                ['value' => 'high', 'label' => 'High'],
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
