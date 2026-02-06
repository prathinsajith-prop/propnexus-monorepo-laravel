<?php

namespace App\Layouts\Slot\Blog;

use App\Forms\Blog\BlogFeedbackForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

class HeaderSlot
{
    /**
     * Build aside header with grid layout in center section
     *
     * @return SlotManager
     */
    public static function make(): SlotManager
    {
        $headerSlot = SlotManager::make('header-slot');

        // Create grid section for center with 3 columns
        $leftSlot = SlotManager::make('header-left');

        // Column 2: Title and Subtitle (create a nested grid for vertical stacking)
        $titleGrid = GridSection::make('title-section', 1)
            ->rows(2)
            ->gap('xs');

        $titleGrid->add(
            TextComponent::make('title')
                ->content('Blog Management')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $titleGrid->add(
            TextComponent::make('subtitle')
                ->content('Forms & Activity Dashboard')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $leftSlot->setSection($titleGrid)->setConfig([
            'gridColumnSpan' => '8',
            'layout' => 'flex',
            'items' => 'center',
            'justify' => 'start',
        ]);

        // Create right slot for action buttons
        $rightSlot = SlotManager::make('header-right');

        // Create action buttons row
        $actionsRow = RowSection::make('header-actions-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $actionsRow->add(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'edit', 'tooltip' => 'Edit'])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('share-btn')
                ->icon('share')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'share', 'tooltip' => 'Share'])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('print-btn')
                ->icon('printer')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'print', 'tooltip' => 'Print'])
        );

        $actionsRow->add(
            ButtonComponent::make('feedback-btn')
                ->icon('message')
                ->variant('text')
                ->form(BlogFeedbackForm::make('blog-feedback-form', 'POST', '/api/blogs/:id/feedback')->toArray())
                ->meta(['tooltip' => 'Submit Feedback'])
        );

        $actionsRow->add(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->isIconButton(true)
                ->variant('text')
                ->confirm([
                    'title' => 'Delete Blog Post',
                    'message' => 'Are you sure you want to delete this blog post? This action cannot be undone.',
                    'confirmLabel' => 'Delete',
                    'cancelLabel' => 'Cancel',
                    'confirmColor' => 'danger',
                    'icon' => 'binempty',
                    'iconColor' => 'danger',
                ])
                ->data('action', 'delete')
                ->data('url', '/api/blogs/:id')
                ->data('method', 'DELETE')
                ->meta([
                    'tooltip' => 'Delete Blog Post',
                    'color' => 'danger',
                ])
        );

        $actionsRow->add(
            ButtonComponent::make('more-btn')
                ->icon('morehorizontal')
                ->variant('text')
                ->dropdown([
                    'id' => 'header-more-options',
                    'placement' => 'bottom-end',
                    'offset' => [0, 8],
                    'closeOnClick' => true,
                    'items' => self::getHeaderMoreOptionsDropdownItems(),
                ])
                ->meta(['tooltip' => 'More options'])
        );

        $actionsRow->add(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->isIconButton(true)
                ->variant('text')
                ->meta(['action' => 'close', 'tooltip' => 'Close'])
        );

        $rightSlot->setSection($actionsRow)
            ->setPriority(SlotManager::PRIORITY_COMPONENT)
            ->setConfig([
                'gridColumnSpan' => '4',
                'layout' => 'flex',
                'items' => 'center',
                'justify' => 'end',
            ]);

        // Create header section with left and right slots
        return $headerSlot->setSection(
            HeaderSection::make('blog-header')
                ->setLeft($leftSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );
    }

    /**
     * Get dropdown items for header "More Options" button
     *
     * @return array
     */
    private static function getHeaderMoreOptionsDropdownItems(): array
    {
        return [
            self::buildDropdownButton('settings', 'Settings', 'settings', 'open-settings'),
            self::buildDropdownButton('notifications', 'Notifications', 'bell', 'open-notifications'),
            self::buildDropdownButton('help', 'Help & Support', 'help', 'open-help'),
            ['type' => 'divider'],
            self::buildDropdownButton('export', 'Export Data', 'downloadcloud', 'export-data'),
            self::buildDropdownButton('import', 'Import Data', 'uploadcloud', 'import-data'),
            ['type' => 'divider'],
            self::buildDropdownButton('fullscreen', 'Toggle Fullscreen', 'expand', 'toggle-fullscreen'),
        ];
    }

    /**
     * Build dropdown button
     */
    private static function buildDropdownButton(string $id, string $label, string $icon, string $action): array
    {
        return ButtonComponent::make($id)
            ->label($label)
            ->icon($icon)
            ->meta(['action' => $action])
            ->toArray();
    }
}
