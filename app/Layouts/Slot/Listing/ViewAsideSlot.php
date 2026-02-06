<?php

namespace App\Layouts\Slot\Listing;

use App\Forms\Listing\ListingViewForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * View Aside Slot
 * 
 * Builds the aside for viewing a listing
 */
class ViewAsideSlot
{
    /**
     * Build view listing aside
     *
     * @param array $masterData Master data for form
     * @param bool $fullscreen Whether to display fullscreen
     * @return array
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = ListingViewForm::make('view-listing-form', 'GET', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create main grid for form
        $mainGrid = GridSection::make('view-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Build header
        $headerSlot = self::buildHeader();

        // Build footer
        $footerSlot = self::buildFooter();

        $aside = DetailSection::make('view-listing')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('view-main-slot')
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
        // Create header center/left grid
        $centerSlot = SlotManager::make('view-header-center');
        $centerSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'column',
            'gap' => '1',
            'justify' => 'center',
            'items' => 'start',
            'gridColumnSpan' => 6,
        ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content('Listing Details')
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content('View complete property listing information')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid
        $rightSlot = SlotManager::make('view-header-right');
        $rightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
            'gridColumnSpan' => 6,
        ]);

        $rightSlot->setComponent(
            BadgeComponent::make('status-badge')
                ->content('Active')
                ->color('success')
                ->variant('standard')
                ->meta(['size' => 'sm'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->data('component', 'edit-listing-full')
                ->data('type', 'aside')
                ->data('action', 'edit')
                ->data('config', [
                    'width' => '800px',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                ])
                ->dataParams(['id' => ':id'])
                ->dataUrl('/api/listing/:id')
                ->meta([
                    'action' => 'edit',
                    'type' => 'aside',
                    'component' => 'edit-listing-full',
                    'tooltip' => 'Edit Listing'
                ])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->confirm([
                    'title' => 'Delete Listing',
                    'message' => 'Are you sure you want to delete this listing? This action cannot be undone.',
                    'confirmLabel' => 'Delete',
                    'cancelLabel' => 'Cancel',
                    'action' => 'delete',
                    'dataUrl' => '/api/listing/:id',
                    'method' => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => 'Delete Listing',
                    'color' => 'error',
                ])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('more-btn')
                ->icon('ellipsisVertical')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->dropdown([
                    'id' => 'more-options',
                    'placement' => 'bottom-end',
                    'offset' => [0, 8],
                    'closeOnClick' => true,
                    'closeOnEscape' => true,
                    'items' => [
                        [
                            'id' => 'share',
                            'label' => 'Share',
                            'icon' => 'share',
                            'action' => 'share',
                            'type' => 'button',
                        ],
                        [
                            'id' => 'print',
                            'label' => 'Print',
                            'icon' => 'printer',
                            'action' => 'print',
                            'type' => 'button',
                        ],
                        [
                            'type' => 'divider',
                        ],
                        [
                            'id' => 'duplicate',
                            'label' => 'Duplicate',
                            'icon' => 'copy',
                            'action' => 'duplicate',
                            'type' => 'button',
                        ],
                        [
                            'id' => 'archive',
                            'label' => 'Archive',
                            'icon' => 'archive',
                            'action' => 'archive',
                            'type' => 'button',
                        ],
                        [
                            'type' => 'divider',
                        ],
                        [
                            'id' => 'export',
                            'label' => 'Export',
                            'icon' => 'downloadcloud',
                            'action' => 'export',
                            'type' => 'button',
                        ],
                    ],
                ])
                ->meta(['tooltip' => 'More options'])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->isIconButton(true)
                ->meta(['action' => 'close'])
        );

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('view-header-slot');
        $headerSlot->setSection(
            HeaderSection::make('view-aside-header')
                ->setLeft($centerSlot)
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
        $footerRightSlot = SlotManager::make('view-footer-right');
        $footerRightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
            'gridColumnSpan' => 12,
        ]);
        $footerRightSlot->setComponent(
            ButtonComponent::make('close-footer-btn')
                ->label('Close')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('fullscreen-btn')
                ->label('View Fullscreen')
                ->icon('expand')
                ->variant('contained')
                ->data('component', 'view-listing-full')
                ->data('type', 'aside')
                ->data('action', 'view')
                ->data('config', [
                    'width' => '100vw',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                    'componentType' => 'aside',
                ])
                ->dataParams(['id' => ':id'])
                ->dataUrl('/api/listing/:id')
                ->meta([
                    'action' => 'view',
                    'type' => 'aside',
                    'component' => 'view-listing-full',
                ])
        );

        $footerRightSlot->setComponent(
            ButtonComponent::make('print-details-btn')
                ->label('Print Details')
                ->icon('printer')
                ->variant('contained')
                ->meta(['action' => 'print', 'tooltip' => 'Print Listing Details', 'color' => 'success'])
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('view-footer-slot');
        $footerSlot->setSection(
            FooterSection::make('view-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
