<?php

namespace App\Forms\ProductProperty;

use Litepie\Layout\Components\FormComponent;

/**
 * ProductPropertyDuplicateForm
 *
 * Form for duplicating a property with:
 * - Ref (text input — new reference number)
 * - Title (text input — new property title)
 */
class ProductPropertyDuplicateForm
{
    /**
     * Create the duplicate form structure.
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method
     * @param  string  $action  Form action URL
     */
    public static function make(
        string $formId = 'duplicate-property-form',
        string $method = 'POST',
        string $action = '/api/product-property/:id/duplicate'
    ): FormComponent {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(1)
            ->gap('md')
            ->title(__('layout.duplicate'))
            ->meta(['dataKey' => 'data'])
            ->dataParams(['id' => ':eid']);

        $form->text('ref')
            ->label(__('product_property.column_ref'))
            ->placeholder(__('layout.duplicate_ref_placeholder'))
            ->required(true)
            ->validation('required|string|max:100')
            ->col(12);

        $form->text('title')
            ->label(__('product_property.property_title'))
            ->placeholder(__('layout.duplicate_title_placeholder'))
            ->required(true)
            ->validation('required|string|max:255')
            ->col(12);

        $form->actions([
            ['label' => __('layout.cancel'), 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close', 'type' => 'button'],
            ['label' => __('layout.duplicate'), 'color' => 'primary', 'icon' => 'duplicate', 'type' => 'submit'],
        ]);

        return $form;
    }
}
