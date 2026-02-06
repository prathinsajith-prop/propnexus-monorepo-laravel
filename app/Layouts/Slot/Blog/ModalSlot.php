<?php

namespace App\Layouts\Slot\Blog;

use App\Forms\Blog\BlogForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

/**
 * Blog Modal Slot Builder
 * 
 * Blog-specific modal configurations using DetailSection structure.
 * Follows the same clean architecture pattern as aside slots.
 * 
 * @package App\Layouts\Slot\Blog
 */
class ModalSlot
{
    /**
     * Build create blog modal
     *
     * @param array $options [
     *   'masterData' => array,  // Master data for form
     *   'apiUrl' => string,     // API endpoint (default: /api/blogs)
     *   'method' => string,     // HTTP method (default: POST)
     * ]
     * @return array Modal definition
     */
    public static function createBlog(array $options = []): array
    {
        $defaults = [
            'masterData' => [],
            'apiUrl' => '/api/blogs',
            'method' => 'POST',
        ];

        $config = array_merge($defaults, $options);

        $formComponent = BlogForm::make('create-blog-form-modal', $config['method'], $config['apiUrl'], $config['masterData']);

        // Create main grid for form
        $mainGrid = GridSection::make('create-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $centerSlot = SlotManager::make('create-modal-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'start',
                'items' => 'start',
                'gridColumnSpan' => 6,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Create New Blog Post')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $rightSlot = SlotManager::make('create-modal-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('create-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Build footer with action buttons
        $footerRightSlot = SlotManager::make('create-modal-footer-right')->setSection(
            RowSection::make('create-modal-footer-row')
                ->gap('xs')
                ->align('right')
                ->justify('end')
                ->add(
                    ButtonComponent::make('cancel-btn')
                        ->label('Cancel')
                        ->variant('outlined')
                        ->meta(['action' => 'close'])
                )
                ->add(
                    ButtonComponent::make('create-btn')
                        ->label('Create Post')
                        ->icon('check')
                        ->variant('contained')
                        ->meta([
                            'action' => 'submit',
                            'dataUrl' => $config['apiUrl'],
                            'method' => $config['method'],
                            'color' => 'primary'
                        ])
                )
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('create-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('create-blog-modal')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('create-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build delete blog confirmation modal
     *
     * @param array $options [
     *   'itemName' => string,    // Optional specific blog title
     *   'apiUrl' => string,      // API endpoint (default: /api/blogs/:id)
     *   'method' => string,      // HTTP method (default: DELETE)
     * ]
     * @return array Modal definition
     */
    public static function deleteBlog(array $options = []): array
    {
        $defaults = [
            'itemName' => null,
            'apiUrl' => '/api/blogs/:id',
            'method' => 'DELETE',
        ];

        $config = array_merge($defaults, $options);

        $title = "Delete Blog Post";
        $message = $config['itemName']
            ? "Are you sure you want to delete '{$config['itemName']}'? This action cannot be undone."
            : "Are you sure you want to delete this blog post? This action cannot be undone.";

        // Build header
        $centerSlot = SlotManager::make('delete-modal-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content($title)
                ->variant('h4')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-red-600'])
        );

        $rightSlot = SlotManager::make('delete-modal-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('delete-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Build main content
        $mainGrid = GridSection::make('delete-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add(
            TextComponent::make('message')
                ->content($message)
                ->variant('body1')
                ->meta(['color' => 'text-gray-700'])
        );

        // Build footer with action buttons
        $footerRightSlot = SlotManager::make('delete-modal-footer-right')->setSection(
            RowSection::make('delete-modal-footer-row')
                ->gap('xs')
                ->align('right')
                ->justify('end')
                ->add(
                    ButtonComponent::make('cancel-btn')
                        ->label('Cancel')
                        ->variant('outlined')
                        ->meta(['action' => 'close'])
                )
                ->add(
                    ButtonComponent::make('delete-btn')
                        ->label('Delete')
                        ->icon('binempty')
                        ->variant('contained')
                        ->meta([
                            'action' => 'submit',
                            'dataUrl' => $config['apiUrl'],
                            'method' => $config['method'],
                            'color' => 'danger'
                        ])
                )
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('delete-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('delete-blog-modal')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('delete-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build publish blog confirmation modal
     *
     * @param array $options [
     *   'itemName' => string,    // Blog title (required)
     *   'apiUrl' => string,      // API endpoint (default: /api/blogs/:id/publish)
     *   'method' => string,      // HTTP method (default: POST)
     * ]
     * @return array Modal definition
     */
    public static function publishBlog(array $options = []): array
    {
        $defaults = [
            'itemName' => 'this post',
            'apiUrl' => '/api/blogs/:id/publish',
            'method' => 'POST',
        ];

        $config = array_merge($defaults, $options);

        $title = "Publish Blog Post";
        $message = "Are you sure you want to publish '{$config['itemName']}'? It will be visible to all readers.";

        // Build header
        $centerSlot = SlotManager::make('publish-modal-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content($title)
                ->variant('h4')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-green-600'])
        );

        $rightSlot = SlotManager::make('publish-modal-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('publish-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Build main content
        $mainGrid = GridSection::make('publish-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add(
            TextComponent::make('message')
                ->content($message)
                ->variant('body1')
                ->meta(['color' => 'text-gray-700'])
        );

        // Build footer with action buttons
        $footerRightSlot = SlotManager::make('publish-modal-footer-right')->setSection(
            RowSection::make('publish-modal-footer-row')
                ->gap('xs')
                ->align('right')
                ->justify('end')
                ->add(
                    ButtonComponent::make('cancel-btn')
                        ->label('Cancel')
                        ->variant('outlined')
                        ->meta(['action' => 'close'])
                )
                ->add(
                    ButtonComponent::make('publish-btn')
                        ->label('Publish')
                        ->icon('checkmark')
                        ->variant('contained')
                        ->meta([
                            'action' => 'submit',
                            'dataUrl' => $config['apiUrl'],
                            'method' => $config['method'],
                            'color' => 'success'
                        ])
                )
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('publish-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('publish-blog-modal')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('publish-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
