<?php

namespace App\Slots\Listing;

use App\Forms\Listing\ListingForm;
use App\Forms\Listing\ListingViewForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

class ListingDetailSlot
{
    /**
     * Build create listing detail section
     *
     * @param array $masterData Master data for form
     * @return array Detail section definition
     */
    public static function createDetail(array $masterData): array
    {
        $formComponent = ListingForm::make('create-listing-detail-form', 'POST', '/api/listing', $masterData);

        // Create main grid for form
        $mainGrid = GridSection::make('create-detail-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = SlotManager::make('header-slot');
        $centerSlot = SlotManager::make('create-detail-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Create New Listing')
                ->variant('h3')
                ->meta(['fontWeight' => 'bold'])
        );
        $headerSlot->setSection(
            HeaderSection::make('create-detail-header')
                ->setCenter($centerSlot)
                ->variant('elevated')
                ->padding('lg')
        );

        // Build footer
        $footerSlot = SlotManager::make('footer-slot');
        $footerRightSlot = SlotManager::make('create-detail-footer-right');
        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label('Cancel')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('create-btn')
                ->label('Create Listing')
                ->icon('check')
                ->variant('contained')
                ->color('primary')
                ->type('submit')
                ->data('action', 'submit')
                ->data('url', '/api/listing')
                ->data('method', 'POST')
        );

        $footerSlot->setSection(
            FooterSection::make('create-detail-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('create-listing-detail')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build edit listing detail section
     *
     * @param array $masterData Master data for form
     * @return array Detail section definition
     */
    public static function editDetail(array $masterData): array
    {
        $formComponent = ListingForm::make('edit-listing-detail-form', 'PUT', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('edit-detail-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = SlotManager::make('header-slot');
        $centerSlot = SlotManager::make('edit-detail-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Edit Listing')
                ->variant('h3')
                ->meta(['fontWeight' => 'bold'])
        );
        $headerSlot->setSection(
            HeaderSection::make('edit-detail-header')
                ->setCenter($centerSlot)
                ->variant('elevated')
                ->padding('lg')
        );

        // Build footer
        $footerSlot = SlotManager::make('footer-slot');
        $footerRightSlot = SlotManager::make('edit-detail-footer-right');
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
        );

        $footerSlot->setSection(
            FooterSection::make('edit-detail-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('edit-listing-detail')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build view listing detail section
     *
     * @param array $masterData Master data for form
     * @return array Detail section definition
     */
    public static function viewDetail(array $masterData): array
    {
        $formComponent = ListingViewForm::make('view-listing-detail-form', 'GET', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('view-detail-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = SlotManager::make('header-slot');
        $centerSlot = SlotManager::make('view-detail-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('View Listing')
                ->variant('h3')
                ->meta(['fontWeight' => 'bold'])
        );
        $headerSlot->setSection(
            HeaderSection::make('view-detail-header')
                ->setCenter($centerSlot)
                ->variant('elevated')
                ->padding('lg')
        );

        // Build footer
        $footerSlot = SlotManager::make('footer-slot');
        $footerRightSlot = SlotManager::make('view-detail-footer-right');
        $footerRightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerSlot->setSection(
            FooterSection::make('view-detail-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('view-listing-detail')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
