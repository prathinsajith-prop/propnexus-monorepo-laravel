<?php

namespace App\Layouts\Slot\Listing;

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
     */
    public static function make(): SlotManager
    {
        $headerSlot = SlotManager::make('header-slot');

        // Create grid section for center with 3 columns
        $leftGrid = GridSection::make('header-center-grid', 3)
            ->rows(1)
            ->gap('md');

        // Column 2: Title and Subtitle (create a nested grid for vertical stacking)
        $titleGrid = GridSection::make('title-section', 1)
            ->rows(2)
            ->gap('xs');

        $titleGrid->add(
            TextComponent::make('title')
                ->content(__('layout.property_listing'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $titleGrid->add(
            TextComponent::make('subtitle')
                ->content(__('layout.manage_your_listings'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $leftGrid->add($titleGrid);

        // Create right grid for action buttons
        $rightGrid = GridSection::make('header-right-grid', 1)
            ->rows(1);

        // Create right section for action buttons
        $rightRow = RowSection::make('header-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $rightRow->add(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'edit', 'tooltip' => __('layout.edit')])
        );

        $rightRow->add(
            ButtonComponent::make('share-btn')
                ->icon('share')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'share', 'tooltip' => __('layout.share')])
        );

        $rightRow->add(
            ButtonComponent::make('print-btn')
                ->icon('printer')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'print', 'tooltip' => __('layout.print')])
        );

        $rightRow->add(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->isIconButton(true)
                ->variant('text')
                ->confirm([
                    'title' => __('layout.delete_listing'),
                    'message' => __('layout.delete_listing_confirmation'),
                    'confirmLabel' => __('layout.delete'),
                    'cancelLabel' => __('layout.cancel'),
                    'confirmColor' => 'danger',
                    'icon' => 'binempty',
                    'iconColor' => 'danger',
                ])
                ->data('action', 'delete')
                ->data('method', 'DELETE')
                ->dataUrl('/api/listing/:id')
                ->meta([
                    'tooltip' => __('layout.delete_listing'),
                    'color' => 'danger',
                ])
        );

        $rightRow->add(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->isIconButton(true)
                ->variant('text')
                ->meta(['action' => 'close', 'tooltip' => __('layout.close')])
        );

        $rightGrid->add($rightRow);

        // Wrap grids in SlotManager
        $leftSlot = SlotManager::make('header-center-slot');
        $leftSlot->setSection($leftGrid);

        $rightSlot = SlotManager::make('header-right-slot');
        $rightSlot->setSection($rightGrid);

        // Create header component with row in right section
        return $headerSlot->setSection(
            HeaderSection::make('aside-header')
                ->setLeft($leftSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );
    }
}
