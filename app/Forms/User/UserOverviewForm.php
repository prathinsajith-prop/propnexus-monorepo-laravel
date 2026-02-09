<?php

namespace App\Forms\User;

use Litepie\Layout\Components\FormComponent;

/**
 * UserOverviewForm
 * 
 * Read-only overview form for displaying user profile details in a 3-column grid.
 * Used in cards and overview sections.
 * 
 * @package App\Forms\User
 */
class UserOverviewForm
{
    /**
     * Create user overview form structure
     *
     * @param string $formId Form identifier
     * @param string|null $dataUrl URL to fetch data from
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make(string $formId = 'user-overview-form', ?string $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->columns(3)
            ->gap('md')
            ->meta(['readOnly' => true]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl);
        }

        // Row 1: Name (full width)
        $form->text('name')
            ->label(__('layout.full_name'))
            ->disabled(true)
            ->width(12);

        // Row 2: Email, Phone, Role
        $form->text('email')
            ->label(__('layout.email'))
            ->disabled(true)
            ->width(4);

        $form->text('phone')
            ->label(__('layout.phone'))
            ->disabled(true)
            ->width(4);

        $form->text('role')
            ->label(__('layout.role'))
            ->disabled(true)
            ->width(4);

        // Row 3: Department, Location, Status
        $form->text('department')
            ->label(__('layout.department'))
            ->disabled(true)
            ->width(4);

        $form->text('location')
            ->label(__('layout.location'))
            ->disabled(true)
            ->width(4);

        $form->text('status')
            ->label(__('layout.status'))
            ->disabled(true)
            ->width(4);

        // Row 4: Joined Date, Last Login
        $form->text('created_at')
            ->label(__('layout.joined_date'))
            ->disabled(true)
            ->width(6);

        $form->text('last_login_at')
            ->label(__('layout.last_login'))
            ->disabled(true)
            ->width(6);

        // Row 5: Bio (full width)
        $form->textarea('bio')
            ->label(__('layout.bio'))
            ->disabled(true)
            ->width(12)
            ->rows(2);

        return $form;
    }

    /**
     * Create a compact version with fewer fields
     *
     * @param string $formId Form identifier
     * @param string|null $dataUrl URL to fetch data from
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function makeCompact(string $formId = 'user-overview-compact', ?string $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->columns(2)
            ->gap('sm')
            ->meta(['readOnly' => true]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl);
        }

        $form->text('name')
            ->label(__('layout.name'))
            ->disabled(true)
            ->width(12);

        $form->text('email')
            ->label(__('layout.email'))
            ->disabled(true)
            ->width(6);

        $form->text('role')
            ->label(__('layout.role'))
            ->disabled(true)
            ->width(6);

        $form->text('status')
            ->label(__('layout.status'))
            ->disabled(true)
            ->width(12);

        return $form;
    }
}
