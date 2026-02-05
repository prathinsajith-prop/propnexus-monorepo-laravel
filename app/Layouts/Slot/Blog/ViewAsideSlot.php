<?php

namespace App\Layouts\Slot\Blog;

use App\Forms\Blog\BlogForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * View Aside Slot
 * 
 * Builds the aside for viewing a blog post
 */
class ViewAsideSlot
{
    /**
     * Build view blog aside
     *
     * @param array $masterData Master data for form
     * @param bool $fullscreen Whether to display fullscreen
     * @return array
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = BlogForm::make('view-blog-form', 'PUT', '/api/blogs/:id', $masterData, '/api/blogs/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('view-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = self::buildHeader();

        // Build footer
        $footerSlot = self::buildFooter();

        $aside = DetailSection::make('view-blog')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('view-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();

        // Add fullscreen configuration if needed
        if ($fullscreen && is_array($aside)) {
            $aside['width'] = '100vw';
            $aside['height'] = '100vh';
        }

        return $aside;
    }

    /**
     * Build header slot
     *
     * @return SlotManager
     */
    private static function buildHeader(): SlotManager
    {
        // Create header center/left grid
        $centerSlot = SlotManager::make('view-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Blog Post Details')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content('View complete blog post information')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid
        $rightSlot = SlotManager::make('view-header-right');
        $rightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
        ]);
        $rightSlot->setComponent(
            BadgeComponent::make('status-badge')
                ->content('Published')
                ->color('success')
                ->variant('standard')
                ->meta(['size' => 'sm'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('edit-btn')
                ->label('Edit')
                ->icon('pen')
                ->variant('outlined')
                ->size('sm')
                ->meta(['action' => 'edit', 'type' => 'aside', 'component' => 'edit-blog-full', 'tooltip' => 'Edit Blog Post'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->label('Delete')
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->confirm([
                    'title' => 'Delete Blog Post',
                    'message' => 'Are you sure you want to delete this blog post? This action cannot be undone.',
                    'confirmLabel' => 'Delete',
                    'cancelLabel' => 'Cancel',
                    'action' => 'delete',
                    'dataUrl' => '/api/blogs/:id',
                    'method' => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => 'Delete Blog Post',
                    'color' => 'error',
                ])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('view-header-slot');
        $headerSlot->setSection(
            HeaderSection::make('view-aside-header')
                ->setLeft($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $headerSlot;
    }

    /**
     * Build footer slot
     *
     * @return SlotManager
     */
    private static function buildFooter(): SlotManager
    {
        // Create footer right grid
        $footerRightSlot = SlotManager::make('view-footer-right');
        $footerRightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
        ]);
        $footerRightSlot->setComponent(
            ButtonComponent::make('close-footer-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('fullscreen-btn')
                ->label('View Fullscreen')
                ->icon('expand')
                ->variant('contained')
                ->meta(['action' => 'view', 'type' => 'aside', 'component' => 'view-blog-full'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('forms-activity-btn')
                ->label('Forms & Activity')
                ->icon('dashboard')
                ->variant('contained')
                ->meta(['action' => 'view', 'type' => 'aside', 'component' => 'view-blog-forms-full', 'tooltip' => 'Open Forms & Activity Dashboard', 'color' => 'success'])
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('view-footer-slot');
        $footerSlot->setSection(
            FooterSection::make('view-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
