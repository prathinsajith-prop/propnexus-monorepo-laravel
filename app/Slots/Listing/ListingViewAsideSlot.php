<?php

namespace App\Slots\Listing;

use App\Forms\Listing\ListingViewForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

class ListingViewAsideSlot
{
    /**
     * Build view listing aside
     *
     * @param array $masterData Master data for form
     * @param bool $fullscreen Whether to use fullscreen mode
     * @return array Aside definition
     */
    public static function make(array $masterData, bool $fullscreen = false): array
    {
        $formComponent = ListingViewForm::make('view-listing-form', 'GET', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('view-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header center slot
        $centerSlot = SlotManager::make('view-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('View Listing')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content('Property listing details')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right slot
        $rightSlot = SlotManager::make('view-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->isIconButton(true)
                ->variant('text')
                ->data('type', 'aside')
                ->data('component', 'edit-listing')
                ->data('action', 'open')
                ->meta(['tooltip' => 'Edit Listing'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->isIconButton(true)
                ->variant('text')
                ->color('danger')
                ->data('type', 'modal')
                ->data('component', 'delete-listing-modal')
                ->data('action', 'open')
                ->meta(['tooltip' => 'Delete Listing'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->isIconButton(true)
                ->variant('text')
                ->meta(['action' => 'close', 'tooltip' => 'Close'])
        );

        // Create footer right slot
        $footerRightSlot = SlotManager::make('view-footer-right');
        $footerRightSlot->setComponent(
            ButtonComponent::make('close-footer-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('view-aside-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('view-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make($fullscreen ? 'view-listing-full' : 'view-listing')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('view-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
