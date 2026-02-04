<?php

namespace App\Slots\Shared;

use App\Forms\Blog\BlogForm;
use Litepie\Layout\Components\ModalComponent;

/**
 * Shared Modal Slot Builder
 * 
 * Provides reusable modal configurations for various actions
 */
class ModalSlot
{
    /**
     * Build create blog modal
     *
     * @param array $masterData Master data for form
     * @return array
     */
    public static function createBlog(array $masterData = []): array
    {
        $formComponent = BlogForm::make('create-blog-form-modal', 'POST', '/api/blogs', $masterData);

        $modalChildren = [
            [
                'type' => 'header',
                'title' => 'Create New Blog Post',
                'icon' => 'documentfull',
            ],
            [
                'type' => 'footer',
                'buttonGroup' => [
                    'buttons' => [
                        ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                        ['label' => 'Create Post', 'type' => 'submit', 'color' => 'primary', 'icon' => 'check', 'dataUrl' => '/api/blogs', 'method' => 'POST'],
                    ],
                ],
            ],
            $formComponent,
        ];

        $modal = ModalComponent::make('create-blog-modal')
            ->ariaLabelledby('create-blog-modal-title');

        $modalArray = $modal->toArray();
        $modalArray['children'] = $modalChildren;

        return $modalArray;
    }

    /**
     * Build delete confirmation modal
     *
     * @param string $entityName Name of entity being deleted
     * @param string $itemName Optional specific item name
     * @return array
     */
    public static function deleteConfirmation(
        string $entityName = 'item',
        ?string $itemName = null
    ): array {
        $title = "Delete " . ucfirst($entityName);
        $message = $itemName
            ? "Are you sure you want to delete '{$itemName}'? This action cannot be undone."
            : "Are you sure you want to delete this {$entityName}? This action cannot be undone.";

        $modalChildren = [
            [
                'type' => 'header',
                'title' => $title,
                'icon' => 'binempty',
                'color' => 'danger',
            ],
            [
                'type' => 'body',
                'content' => $message,
            ],
            [
                'type' => 'footer',
                'buttonGroup' => [
                    'buttons' => [
                        ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                        ['label' => 'Delete', 'color' => 'danger', 'icon' => 'binempty', 'dataUrl' => '/api/blogs/:id', 'method' => 'DELETE'],
                    ],
                ],
            ],
        ];

        $modal = ModalComponent::make('delete-modal')
            ->ariaLabelledby('delete-modal-title')
            ->ariaDescribedby('delete-modal-description');

        $modalArray = $modal->toArray();
        $modalArray['children'] = $modalChildren;

        return $modalArray;
    }

    /**
     * Build generic confirmation modal
     *
     * @param string $title Modal title
     * @param string $message Confirmation message
     * @param string $confirmLabel Confirm button label
     * @param string $confirmAction Action to trigger on confirm
     * @param string $confirmColor Button color
     * @return array
     */
    public static function confirmation(
        string $title,
        string $message,
        string $confirmLabel = 'Confirm',
        string $confirmAction = 'confirm',
        string $confirmColor = 'primary'
    ): array {
        $modalChildren = [
            [
                'type' => 'header',
                'title' => $title,
            ],
            [
                'type' => 'body',
                'content' => $message,
            ],
            [
                'type' => 'footer',
                'buttonGroup' => [
                    'buttons' => [
                        ['label' => 'Cancel', 'variant' => 'outlined', 'action' => 'close'],
                        ['label' => $confirmLabel, 'color' => $confirmColor, 'action' => $confirmAction],
                    ],
                ],
            ],
        ];

        $modal = ModalComponent::make('confirmation-modal')
            ->ariaLabelledby('confirmation-modal-title')
            ->ariaDescribedby('confirmation-modal-description');

        $modalArray = $modal->toArray();
        $modalArray['children'] = $modalChildren;

        return $modalArray;
    }
}
