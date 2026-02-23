<?php

namespace App\Layouts\Slot\Blog;

use App\Forms\Blog\BlogViewForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * BlogFormActivityAsideSlot
 *
 * Builds the Forms & Activity aside for viewing blog posts.
 * This aside includes activity history, chat, notes, and other interactive features.
 */
class FormActivityAsideSlot
{
    /**
     * Build the Forms & Activity aside for viewing a blog post
     *
     * @param  array  $masterData  The master data containing blog post information
     * @param  bool  $fullscreen  Whether to display in fullscreen mode
     * @return array The aside configuration array
     */
    public static function make(array $masterData, bool $fullscreen = false): array
    {
        // Use the dedicated BlogViewForm for read-only display
        $formComponent = BlogViewForm::make('view-blog-form-fa', $masterData, '/api/blogs/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('view-fa-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build aside using DetailSection
        return DetailSection::make($fullscreen ? 'view-blog-fa-full' : 'view-blog-fa')
            ->setHeader(
                SlotManager::make('view-fa-header-slot')
                    ->setSection(self::buildHeader())
            )
            ->setMain(
                SlotManager::make('view-fa-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter(
                SlotManager::make('view-fa-footer-slot')
                    ->setSection(self::buildFooter())
            )
            ->toArray();
    }

    /**
     * Build the header section with title and action buttons
     */
    private static function buildHeader(): HeaderSection
    {
        // Create header center slot with title and subtitle
        $centerSlot = SlotManager::make('view-fa-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.blog_post_details'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.view_complete_blog_info'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right slot with action buttons
        $rightSlot = SlotManager::make('view-fa-header-right', 4);
        $rightSlot->setComponent(
            BadgeComponent::make('status-badge')
                ->content(__('layout.published'))
                ->color('success')
                ->variant('standard')
                ->meta(['size' => 'sm'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('edit-btn')
                ->label(__('layout.edit'))
                ->icon('pen')
                ->variant('outlined')
                ->size('sm')
                ->meta(['action' => 'edit', 'type' => 'aside', 'component' => 'edit-blog-full', 'tooltip' => __('layout.tooltip_edit_blog_post')])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->label(__('layout.delete'))
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->confirm([
                    'title' => __('layout.delete_blog_post'),
                    'message' => __('layout.delete_blog_post_confirmation'),
                    'confirmLabel' => __('layout.delete'),
                    'cancelLabel' => __('layout.cancel'),
                    'action' => 'delete',
                    'dataUrl' => '/api/blogs/:id',
                    'method' => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => __('layout.tooltip_delete_blog_post'),
                    'color' => 'error',
                ])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        return HeaderSection::make('view-fa-aside-header')
            ->setLeft($centerSlot)
            ->setRight($rightSlot)
            ->variant('elevated')
            ->padding('md');
    }

    /**
     * Build the footer section with navigation and action buttons
     */
    private static function buildFooter(): FooterSection
    {
        // Create footer right slot with action buttons
        $footerRightSlot = SlotManager::make('view-fa-footer-right', 3);

        $footerRightSlot->setComponent(
            ButtonComponent::make('close-footer-btn')
                ->label(__('layout.close'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('fullscreen-btn')
                ->label(__('layout.view_fullscreen'))
                ->icon('expand')
                ->variant('contained')
                ->data('component', 'view-blog-full')
                ->data('type', 'aside')
                ->data('action', 'view')
                ->data('config', [
                    'width' => '800px',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                ])
                ->dataParams(['id' => ':id'])
                ->dataUrl('/api/blogs/:id')
                ->meta(['action' => 'view', 'type' => 'aside', 'component' => 'view-blog-full'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('more-fullscreen-btn')
                ->label(__('layout.view_more_fullscreen'))
                ->icon('expand')
                ->variant('contained')
                ->meta(['action' => 'view', 'type' => 'aside', 'component' => 'view-blog-fa-full'])
        );

        return FooterSection::make('view-fa-aside-footer')
            ->setRight($footerRightSlot)
            ->variant('elevated')
            ->padding('md');
    }
}
