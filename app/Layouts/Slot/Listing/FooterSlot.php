<?php

namespace App\Layouts\Slot\Listing;

use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

class FooterSlot
{
    /**
     * Build aside footer with grid sections for buttons
     */
    public static function make(): SlotManager
    {
        $footerSlot = SlotManager::make('footer-slot');

        // Create left grid for help button
        $footerGrid = GridSection::make('footer-left-grid', 1)
            ->rows(1)
            ->gap('xs');

        $footerGrid->add(
            ButtonComponent::make('help-btn')
                ->label(__('layout.need_help'))
                ->variant('text')
                ->meta(['action' => 'help'])
        );

        // Create right grid for action buttons
        $footerRightGrid = GridSection::make('footer-right-grid', 2)
            ->rows(1)
            ->gap('xs');

        $footerRightRow = RowSection::make('footer-right-row')
            ->gap('xs')
            ->align('center')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('cancel-btn')
                ->label(__('layout.cancel'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightRow->add(
            ButtonComponent::make('save-btn')
                ->label(__('layout.save_changes'))
                ->variant('contained')
                ->meta(['action' => 'save'])
        );

        $footerRightGrid->add($footerRightRow);

        // Wrap grids in SlotManager
        $footerLeftSlot = SlotManager::make('footer-left-slot');
        $footerLeftSlot->setSection($footerGrid);

        $footerRightSlot = SlotManager::make('footer-right-slot');
        $footerRightSlot->setSection($footerRightGrid);

        // Create footer component with grid sections
        return $footerSlot->setSection(
            FooterSection::make('aside-footer')
                ->setLeft($footerLeftSlot)
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );
    }
}
