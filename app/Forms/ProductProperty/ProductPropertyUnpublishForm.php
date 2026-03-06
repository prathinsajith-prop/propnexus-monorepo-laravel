<?php

namespace App\Forms\ProductProperty;

use App\Enums\UnpublishReason;
use Litepie\Layout\Components\FormComponent;

/**
 * ProductPropertyUnpublishForm
 *
 * Form for unpublishing a property with:
 * - Reason (select dropdown)
 * - Description (textarea)
 */
class ProductPropertyUnpublishForm
{
    /**
     * Create the unpublish form structure.
     *
     * @param  string  $formId  Form identifier
     * @param  string  $method  HTTP method
     * @param  string  $action  Form action URL
     */
    public static function make(
        string $formId = 'unpublish-property-form',
        string $method = 'POST',
        string $action = '/api/product-property/:id/unpublish'
    ): FormComponent {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(1)
            ->gap('md')
            ->title(__('layout.unpublish'))
            ->meta(['dataKey' => 'data'])
            ->dataParams(['id' => ':eid']);

        $form->select('reason')
            ->label(__('layout.unpublish_reason'))
            ->options(UnpublishReason::getMasterdata())
            ->required(true)
            ->validation('required|string')
            ->col(12);

        $form->textarea('description')
            ->label(__('layout.description'))
            ->placeholder(__('layout.unpublish_description_placeholder'))
            ->attribute('rows', 5)
            ->col(12);

        $form->actions([
            ['label' => __('layout.cancel'), 'variant' => 'outlined', 'color' => 'secondary', 'action' => 'close', 'type' => 'button'],
            ['label' => __('layout.submit'), 'color' => 'primary', 'type' => 'submit'],
        ]);

        return $form;
    }
}
