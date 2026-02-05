<?php

namespace App\Layouts\Slot\Blog;

use App\Forms\Blog\BlogForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * CreateAsideSlot
 * 
 * **Purpose**: Builds the drawer panel (aside) for creating a new blog post.
 * 
 * **Component Structure**:
 * - Header: Title, subtitle, close button
 * - Main: Blog creation form (BlogForm)
 * - Footer: Cancel and create action buttons
 * 
 * **Features**:
 * - Supports fullscreen mode
 * - Separated header/footer builders for clean code
 * - Consistent button configuration
 * - Flexible parameter handling with defaults
 * 
 * **Architecture Pattern**:
 * This follows the standard aside slot pattern used across all modules:
 * 1. make() - Main builder method
 * 2. buildHeader() - Private method for header construction
 * 3. buildFooter() - Private method for footer construction
 * 
 * @package App\Layouts\Slot\Blog
 * @see \App\Layouts\Slot\Listing\CreateAsideSlot Listing equivalent
 */
class CreateAsideSlot
{
    /**
     * Build create blog aside
     *
     * @param array $masterData Master data for form
     * @param bool $fullscreen Whether to display fullscreen
     * @return array
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = BlogForm::make('create-blog-form', 'POST', '/api/blogs', $masterData);

        // Create main grid for form
        $mainGrid = GridSection::make('create-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = self::buildHeader();

        // Build footer
        $footerSlot = self::buildFooter();

        $aside = DetailSection::make('create-blog')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('create-main-slot')
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
        // Create header center grid
        $centerSlot = SlotManager::make('create-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Create New Blog Post')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content('Add a new blog post to your collection')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid
        $rightSlot = SlotManager::make('create-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('create-aside-header')
                ->setCenter($centerSlot)
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
        $footerRightSlot = SlotManager::make('create-footer-right');
        $footerRightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
        ]);
        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('create-btn')
                ->label('Create Post')
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/blogs', 'method' => 'POST', 'color' => 'primary'])
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('create-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
