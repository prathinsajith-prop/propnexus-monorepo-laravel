<?php

namespace App\Slots\Blog;

use App\Forms\Blog\BlogForm;
use App\Forms\Blog\BlogViewForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\SlotManager;

/**
 * BlogDetailSlot
 *
 * Builds detail sections for blog posts (create, edit, view).
 * These are used for modal/detail views separate from the main aside views.
 */
class BlogDetailSlot
{
    /**
     * Build create blog detail section
     *
     * @param array $masterData The master data array
     * @return array The detail section configuration array
     */
    public static function createDetail(array $masterData): array
    {
        $headerGrid = GridSection::make('create-header');
        $headerGrid->add(
            TextComponent::make('title')
                ->content('Create New Blog Post')
                ->variant('h4')
                ->meta(['subtitle' => 'Add a new blog post to your collection'])
        );
        $headerGrid->add(
            ButtonComponent::make('close-btn')
                ->icon('LiX')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $mainGrid = GridSection::make('create-main');
        $mainGrid->add(
            BlogForm::make('create-blog-form', 'POST', '/api/blogs', $masterData)
        );

        $footerGrid = GridSection::make('create-footer');
        $footerGrid->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerGrid->add(
            ButtonComponent::make('create-btn')
                ->label('Create Post')
                ->variant('contained')
                ->color('primary')
                ->icon('check')
                ->meta([
                    'action' => 'submit',
                    'dataUrl' => '/api/blogs',
                    'method' => 'POST',
                ])
        );

        return DetailSection::make('create-blog-detail')
            ->setHeader(SlotManager::make()->setSection($headerGrid))
            ->setMain(SlotManager::make()->setSection($mainGrid))
            ->setFooter(SlotManager::make()->setSection($footerGrid))
            ->toArray();
    }

    /**
     * Build edit blog detail section
     *
     * @param array $masterData The master data array
     * @return array The detail section configuration array
     */
    public static function editDetail(array $masterData): array
    {
        $headerGrid = GridSection::make('edit-header');
        $headerGrid->add(
            TextComponent::make('title')
                ->content('Edit Blog Post')
                ->variant('h4')
                ->meta(['subtitle' => 'Update your blog post'])
        );
        $headerGrid->add(
            ButtonComponent::make('close-btn')
                ->icon('LiX')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $mainGrid = GridSection::make('edit-main');
        $mainGrid->add(
            BlogForm::make('edit-blog-form', 'PUT', '/api/blogs/:id', $masterData, '/api/blogs/:id')
        );

        $footerGrid = GridSection::make('edit-footer');
        $footerGrid->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerGrid->add(
            ButtonComponent::make('update-btn')
                ->label('Update Post')
                ->variant('contained')
                ->color('success')
                ->icon('check')
                ->meta([
                    'action' => 'submit',
                    'dataUrl' => '/api/blogs/:id',
                    'method' => 'PUT',
                ])
        );

        return DetailSection::make('edit-blog-detail')
            ->setHeader(SlotManager::make()->setSection($headerGrid))
            ->setMain(SlotManager::make()->setSection($mainGrid))
            ->setFooter(SlotManager::make()->setSection($footerGrid))
            ->toArray();
    }

    /**
     * Build view blog detail section
     *
     * @param array $masterData The master data array
     * @return array The detail section configuration array
     */
    public static function viewDetail(array $masterData): array
    {
        $headerGrid = GridSection::make('view-header');
        $headerGrid->add(
            TextComponent::make('title')
                ->content('Blog Post Details')
                ->variant('h4')
                ->meta(['subtitle' => 'View and manage blog post'])
        );
        $headerGrid->add(
            ButtonComponent::make('close-btn')
                ->icon('LiX')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $mainGrid = GridSection::make('view-main');
        $mainGrid->add(
            BlogViewForm::make('view-blog-form', $masterData, '/api/blogs/:id')
        );

        $footerGrid = GridSection::make('view-footer');
        $footerGrid->add(
            ButtonComponent::make('edit-btn')
                ->label('Edit Post')
                ->variant('contained')
                ->color('primary')
                ->icon('pen')
                ->meta([
                    'action' => 'open-detail',
                    'component' => 'edit-blog-detail',
                ])
        );
        $footerGrid->add(
            ButtonComponent::make('close-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        return DetailSection::make('view-blog-detail')
            ->setHeader(SlotManager::make()->setSection($headerGrid))
            ->setMain(SlotManager::make()->setSection($mainGrid))
            ->setFooter(SlotManager::make()->setSection($footerGrid))
            ->toArray();
    }
}
