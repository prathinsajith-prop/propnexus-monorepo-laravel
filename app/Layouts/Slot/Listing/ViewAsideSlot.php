<?php

namespace App\Layouts\Slot\Listing;

use App\Enums\ListingStatus;
use App\Forms\Listing\ListingOverviewForm;
use App\Forms\Listing\ListingViewForm;
use App\Support\ImageHelper;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\CardComponent;
use Litepie\Layout\Components\MediaComponent;
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
     * @param  array  $masterData  Master data for form
     * @param  bool  $fullscreen  Whether to display fullscreen
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = ListingViewForm::make('view-listing-form', 'GET', '/api/listing/:id', $masterData, '/api/listing/:id');

        // Create overview form using the dedicated form class
        // $overviewForm = ListingOverviewForm::make('property-overview-form', '/api/listing/:id')->gridColumnSpan(4);

        $overview = CardComponent::make('property-overview-card')
            ->addComponent(
                MediaComponent::make('property-image')
                    ->gallery()  // Set type to gallery
                    ->grid()     // Layout: grid, masonry, or carousel
                    ->columns(1) // 1 images per row
                    ->lightbox(true)
                    ->captions(true)
                    ->aspectRatio('1:1')
                    ->width('300')
                    ->height('150')
                    ->addItem(
                        ImageHelper::url('listings/Screenshot-2026-01-02-at-12-40-43---pm-1770977490-698ef8d2ebadb.png', ['w' => 800, 'h' => 600]),
                        [
                            'alt' => 'Living Room',
                            'caption' => 'Spacious living area',
                        ]
                    )
                    ->gridColumnSpan(4)
            )
            ->addComponent(
                TextComponent::make('property-title')
                    ->content('
                    This is a beautiful 3-bedroom, 2-bathroom family home located in the heart of the city.
                    Featuring a spacious living area, modern kitchen, and a large backyard perfect for entertaining guests.
                    ')
                    ->variant('h5')
                    ->title(__('layout.property_title'))
                    ->meta(['fontWeight' => 'bold'])
                    ->gridColumnSpan(8)
            );

        // Create main grid for form
        $mainGrid = GridSection::make('view-main-grid')
            ->rows(1)
            ->gap('md');

        $mainGrid->add($overview);
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
     */
    private static function buildHeader(): SlotManager
    {
        // Create header center/left grid
        $centerSlot = SlotManager::make('view-header-center');
        $centerSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'column',
            'gap' => '1',
            'justify' => 'start',
            'items' => 'start',
            'gridColumnSpan' => 6,
        ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.listing_details'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.view_complete_property_info'))
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
                ->content(__('layout.active'))
                ->color('success')
                ->variant('standard')
                ->badgeConfig(ListingStatus::badgeConfig())
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
                    'tooltip' => __('layout.tooltip_edit_listing'),
                ])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->confirm([
                    'title' => __('layout.delete_listing'),
                    'message' => __('layout.delete_listing_confirmation'),
                    'confirmLabel' => __('layout.delete'),
                    'cancelLabel' => __('layout.cancel'),
                    'action' => 'delete',
                    'dataUrl' => '/api/listing/:id',
                    'method' => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => __('layout.tooltip_delete_listing'),
                    'color' => 'error',
                ])
        );
        $rightSlot->setComponent(
            ButtonComponent::make('more-btn')
                ->icon('ellipsisvertical')
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
                            'label' => __('layout.share'),
                            'icon' => 'share',
                            'action' => 'share',
                            'type' => 'button',
                        ],
                        [
                            'id' => 'print',
                            'label' => __('layout.print'),
                            'icon' => 'printer',
                            'action' => 'print',
                            'type' => 'button',
                        ],
                        [
                            'type' => 'divider',
                        ],
                        [
                            'id' => 'duplicate',
                            'label' => __('layout.duplicate'),
                            'icon' => 'duplicate',
                            'action' => 'duplicate',
                            'type' => 'button',
                        ],
                        [
                            'id' => 'archive',
                            'label' => __('layout.archive'),
                            'icon' => 'archive',
                            'action' => 'archive',
                            'type' => 'button',
                        ],
                        [
                            'type' => 'divider',
                        ],
                        [
                            'id' => 'export',
                            'label' => __('layout.export'),
                            'icon' => 'downloadcloud',
                            'action' => 'export',
                            'type' => 'button',
                        ],
                    ],
                ])
                ->meta(['tooltip' => __('layout.tooltip_more_options')])
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
                ->label(__('layout.close'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );
        $footerRightSlot->setComponent(
            ButtonComponent::make('fullscreen-btn')
                ->label(__('layout.view_fullscreen'))
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
                ->label(__('layout.print_details'))
                ->icon('printer')
                ->variant('contained')
                ->meta(['action' => 'print', 'tooltip' => __('layout.print_details'), 'color' => 'success'])
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
