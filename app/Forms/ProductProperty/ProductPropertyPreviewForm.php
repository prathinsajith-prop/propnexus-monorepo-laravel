<?php

namespace App\Forms\ProductProperty;

use App\Enums\PreviewType;
use Litepie\Layout\Components\FormComponent;

/**
 * ProductPropertyPreviewForm
 *
 * Form for generating a property preview link with:
 * - Preview type (select dropdown)
 * - Price (optional override with AED suffix)
 */
class ProductPropertyPreviewForm
{
    /**
     * Create the preview form structure.
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method
     * @param  string  $action  Form action URL
     */
    public static function make(
        string $formId = 'preview-property-form',
        string $method = 'POST',
        string $action = '/api/product-property/:id/preview'
    ): FormComponent {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(2)
            ->gap('md')
            ->title(__('layout.preview'))
            ->meta(['dataKey' => 'data'])
            ->dataParams(['id' => ':eid']);

        $form->select('preview_type')
            ->label(__('layout.preview_type'))
            ->options(PreviewType::getMasterdata())
            ->value(PreviewType::WithMyDetails->value)
            ->required(true)
            ->validation('required|string')
            ->col(6);

        $form->number('price')
            ->label(__('layout.price'))
            ->placeholder('500000000')
            ->suffix('AED')
            ->col(6);

        $form->actions([
            ['label' => __('layout.cancel'), 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close', 'type' => 'button'],
            ['label' => __('layout.submit'), 'color' => 'primary', 'type' => 'submit'],
        ]);

        return $form;
    }
}
