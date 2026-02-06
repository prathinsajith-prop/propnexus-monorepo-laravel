<?php

namespace App\Layouts\Slot\Blog;

use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

class FooterSlot
{
    /**
     * Build aside footer with sections for buttons
     *
     * @return SlotManager
     */
    public static function make(): SlotManager
    {
        $footerSlot = SlotManager::make('footer-slot');

        // Create left section for help button
        $leftSlot = SlotManager::make('footer-left');
        $leftSlot->setSection(
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
            'gridColumnSpan' => '9',
        ]);

        // Create right section for action buttons
        $rightSlot = SlotManager::make('footer-right');
        $rightSlot->setSection(
            RowSection::make('footer-right-row')
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
                    ButtonComponent::make('save-btn')
                        ->label('Save Changes')
                        ->variant('contained')
                        ->meta(['action' => 'save'])
                )
        )->setConfig([
            'gridColumnSpan' => '3',
        ]);

        // Create footer section with left and right slots
        $footerSlot->setSection(
            FooterSection::make('blog-footer')
                ->setLeft($leftSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
