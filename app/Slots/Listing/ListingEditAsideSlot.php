<?php

namespace App\Slots\Listing;

use App\Forms\Listing\ListingForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

class ListingEditAsideSlot
{
    /**
     * Build edit listing aside
     *
     * @param array $masterData Master data for form
     * @param bool $fullscreen Whether to use fullscreen mode
     * @return array Aside definition
     */
    public static function make(array $masterData, bool $fullscreen = false): array
    {
        $formComponent = ListingForm::make('edit-listing-form', 'PUT', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('edit-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Create header center slot
        $centerSlot = SlotManager::make('edit-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Edit Listing')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content('Update property listing details')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right slot
        $rightSlot = SlotManager::make('edit-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->isIconButton(true)
                ->variant('text')
                ->meta(['action' => 'close', 'tooltip' => 'Close'])
        );

        // Create footer right slot
        $footerRightSlot = SlotManager::make('edit-footer-right');
        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('save-btn')
                ->label('Save Changes')
                ->icon('check')
                ->variant('contained')
                ->color('primary')
                ->type('submit')
                ->data('action', 'submit')
                ->data('url', '/api/listing/:id')
                ->data('method', 'PUT')
                ->meta(['tooltip' => 'Save listing changes'])
        );

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('edit-aside-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('edit-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make($fullscreen ? 'edit-listing-full' : 'edit-listing')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('edit-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
