<?php

namespace App\Slots\Blog;

use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

class BlogFooterSlot
{
    /**
     * Build aside footer with grid sections for buttons
     *
     * @return SlotManager
     */
    public static function make(): SlotManager
    {
        $footerSlot = SlotManager::make('footer-slot');

        // Create left grid for help button
        $footerLeftGrid = SlotManager::make('footer-left-grid', 1);

        $footerLeftGrid->setSection(
            RowSection::make('footer-left-row')
                ->gap('xs')
                ->align('left')
                ->justify('start')
                ->add(
                    ButtonComponent::make('help-btn')
                        ->label('Need Help?')
                        ->variant('text')
                        ->meta(['action' => 'help'])
                )
        )->setConfig([
            'colSpan' => '6',
        ]);

        // Create right grid for action buttons
        $footerRightGrid = SlotManager::make('footer-right-grid', 1);

        $footerRightRow = RowSection::make('footer-right-row')
            ->gap('xs')
            ->align('right')
            ->justify('end');

        $footerRightRow->add(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightRow->add(
            ButtonComponent::make('save-btn')
                ->label('Save Changes')
                ->variant('contained')
                ->meta(['action' => 'save'])
        );

        $footerRightGrid->setSection($footerRightRow)->setConfig([
            'colSpan' => '6',
        ]);

        // Create footer component with grid sections
        return $footerSlot->setSection(
            FooterSection::make('aside-footer')
                ->setLeft($footerLeftGrid)
                ->setRight($footerRightGrid)
                ->variant('elevated')
                ->padding('md')
        );
    }
}
