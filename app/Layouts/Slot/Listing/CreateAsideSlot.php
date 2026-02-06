<?php

namespace App\Layouts\Slot\Listing;

use App\Forms\Listing\ListingForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * Create Aside Slot
 * 
 * Builds the aside for creating a new listing
 */
class CreateAsideSlot
{
    /**
     * Build create listing aside
     *
     * @param array $masterData Master data for form
     * @param bool $fullscreen Whether to display fullscreen
     * @return array
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = ListingForm::make('create-listing-form', 'POST', '/api/listing', $masterData);

        // Create main grid for form
        $mainGrid = GridSection::make('create-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = self::buildHeader();

        // Build footer
        $footerSlot = self::buildFooter();

        $aside = DetailSection::make('create-listing')
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
        $centerSlot = SlotManager::make('create-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'center',
                'items' => 'start',
                'gridColumnSpan' => 6,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.create_new_listing'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.add_new_property_description'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid
        $rightSlot = SlotManager::make('create-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

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
            'gridColumnSpan' => 12,
        ]);
        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label(__('layout.cancel'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('create-btn')
                ->label(__('layout.create_listing'))
                ->icon('check')
                ->variant('contained')
                ->meta(['action' => 'submit', 'dataUrl' => '/api/listing', 'method' => 'POST', 'color' => 'primary'])
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
