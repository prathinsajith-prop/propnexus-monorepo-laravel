<?php

namespace App\Layouts\Slot\ProductProperty;

use App\Forms\ProductProperty\ProductPropertyForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * Edit Aside Slot — Product Property
 *
 * Builds the aside drawer for editing an existing product property.
 */
class EditAsideSlot
{
    /**
     * Build edit product property aside.
     *
     * @param  array  $masterData  Master data for form dropdowns
     * @param  bool  $fullscreen  Whether to display fullscreen
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = ProductPropertyForm::make(
            'edit-property-form',
            'PUT',
            '/api/product-property/:id',
            $masterData,
            '/api/product-property/:id'
        )
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id']);

        $mainGrid = GridSection::make('edit-property-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        $aside = DetailSection::make('edit-property')
            ->setHeader(self::buildHeader())
            ->setMain(
                SlotManager::make('edit-property-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter(self::buildFooter())
            ->toArray();

        if ($fullscreen && is_array($aside)) {
            $aside['width'] = '100vw';
            $aside['height'] = '100vh';
        }

        return $aside;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private static function buildHeader(): SlotManager
    {
        $centerSlot = SlotManager::make('edit-property-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'center',
                'items' => 'start',
                'gridColumnSpan' => 8,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.edit_property'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.update_property_details'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $rightSlot = SlotManager::make('edit-property-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 4,
            ]);

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('edit-property-aside-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $headerSlot;
    }

    private static function buildFooter(): SlotManager
    {
        $footerRightSlot = SlotManager::make('edit-property-footer-right')
            ->setConfig([
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
            ButtonComponent::make('update-btn')
                ->label(__('layout.update_property'))
                ->icon('check')
                ->variant('contained')
                ->type('submit')
                ->color('primary')
                ->data('method', 'PUT')
                ->dataUrl('/api/product-property/:id')
                ->dataParams(['id' => ':id'])
                ->meta(['action' => 'submit'])
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('edit-property-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
