<?php

namespace App\Layouts\Slot\Listing;

use App\Enums\ListingStatus;
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
 * Fullscreen View Aside Slot
 * 
 * **Purpose**: Builds an optimized fullscreen layout for viewing a listing/property in immersive mode.
 * 
 * **Component Structure**:
 * - Header: Title, status badge, action buttons (edit, delete, more options) - sticky
 * - Main: Two-column layout:
 *   - Left (70%): Property image gallery with detailed media display
 *   - Right (30%): Property details form with scrollable content
 * - Footer: Action buttons (close, print, export) - sticky
 * 
 * **Features**:
 * - Fullscreen mode (100vw x 100vh)
 * - Two-column responsive layout optimized for large screens
 * - Large property image gallery with lightbox and carousel
 * - Detailed property overview card
 * - Sticky header and footer for easy access
 * - Full-width utilization for better visibility
 * - Rich media display with captions and navigation
 * 
 * **Key Differences from Regular ViewAsideSlot**:
 * - Uses full viewport width instead of aside constrained width
 * - Two-column layout instead of stacked single column
 * - Larger media gallery display
 * - More spacious form layout
 * - Optimized for immersive property viewing experience
 * 
 * **Architecture Pattern**:
 * This follows the fullscreen-specific slot pattern:
 * 1. make() - Main builder method
 * 2. buildHeader() - Private method for header construction
 * 3. buildFooter() - Private method for footer construction
 * 4. buildLeftPanel() - Private method for media gallery panel
 * 5. buildRightPanel() - Private method for details form panel
 * 
 * @package App\Layouts\Slot\Listing
 * @see \App\Layouts\Slot\Listing\ViewAsideSlot Regular view slot (aside drawer)
 */
class FullscreenViewAsideSlot
{
    /**
     * Build fullscreen listing view aside
     *
     * Uses proper builder pattern with separate header, main, left, right, footer slots
     * that can be composed together using DetailSection
     *
     * @param array $masterData Master data for form
     * @return array
     */
    public static function make(array $masterData = []): array
    {
        return DetailSection::make('fullscreen-view-listing')
            ->setHeader(self::buildHeaderSlot())
            // ->setMain(self::buildMainSlot())
            ->setRight(self::buildLeftSlot($masterData))
            ->setLeft(self::buildRightSlot($masterData))
            ->setFooter(self::buildFooterSlot())
            ->toArray();
    }

    /**
     * Build left slot with image gallery and property details
     *
     * @return SlotManager
     */
    private static function buildLeftSlot(array $masterData): SlotManager
    {
        $leftSlot = SlotManager::make('fullscreen-left-slot');
        $leftSlot
            ->setConfig([
                'colSpan' => 7,
            ]);
        // Add property gallery card
        $leftSlot->setComponent(ListingViewForm::make('view-listing-fullscreen-form', 'GET', '/api/listing/:id', $masterData, '/api/listing/:id'));

        return $leftSlot;
    }

    /**
     * Build right slot with property overview and details form
     *
     * @param array $masterData Master data for form
     * @return SlotManager
     */
    private static function buildRightSlot(array $masterData): SlotManager
    {
        $rightSlot = SlotManager::make('fullscreen-right-slot');
        $rightSlot->setConfig([
            'colSpan' => 5,
        ]);

        // Add property overview and form card
        $rightSlot->setComponent(self::buildRightPanel($masterData));

        return $rightSlot;
    }

    /**
     * Build header slot for fullscreen view
     *
     * @return SlotManager
     */
    private static function buildHeaderSlot(): SlotManager
    {
        // Create header center/left grid
        $centerSlot = SlotManager::make('fullscreen-header-center');
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
                ->content(__('layout.property_details_fullscreen'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.immersive_property_viewing'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        // Create header right grid with badge and actions
        $rightSlot = SlotManager::make('fullscreen-header-right');
        $rightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
            'gridColumnSpan' => 6,
        ]);

        // Status badge
        $rightSlot->setComponent(
            BadgeComponent::make('status-badge')
                ->content(__('layout.active'))
                ->badgeConfig(ListingStatus::badgeConfig())
                ->bordered(true)
                ->variant('standard')
                ->meta(['size' => 'sm'])
        );

        // Edit button
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
                ->data('params', ['id' => ':id'])
                ->data('url', '/api/listing/:id')
                ->meta([
                    'action' => 'edit',
                    'type' => 'aside',
                    'component' => 'edit-listing-full',
                    'tooltip' => __('layout.tooltip_edit_listing')
                ])
        );

        // Delete button
        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->color('error')
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

        // More options dropdown
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
                            'icon' => 'copy',
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
                            'id' => 'compare',
                            'label' => __('layout.compare'),
                            'icon' => 'maximize',
                            'action' => 'compare',
                            'type' => 'button',
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

        // Close button
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->size('md')
                ->isIconButton(true)
                ->meta(['action' => 'close'])
        );

        // Wrap header in SlotManager
        $headerSlot = SlotManager::make('fullscreen-header-slot');
        $headerSlot->setSection(
            HeaderSection::make('fullscreen-aside-header')
                ->setLeft($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('lg')
        );

        return $headerSlot;
    }

    /**
     * Build left panel with image gallery
     *
     * @return CardComponent
     */
    private static function buildLeftPanel(): CardComponent
    {
        $card = CardComponent::make('property-gallery-card');

        // Add carousel gallery
        $mediaComponent = MediaComponent::make('property-image-fullscreen')
            ->gallery()           // Set type to gallery
            ->carousel()          // Carousel slider
            ->lightbox(true)      // Full lightbox support
            ->captions(true)      // Show captions
            ->aspectRatio('16:9') // Widescreen aspect ratio
            ->addItem(
                ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1600, 'h' => 900]),
                [
                    'alt' => __('layout.image_living_room_alt'),
                    'caption' => __('layout.image_living_room_caption')
                ]
            )
            ->addItem(
                ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1600, 'h' => 900]),
                [
                    'alt' => __('layout.image_kitchen_alt'),
                    'caption' => __('layout.image_kitchen_caption')
                ]
            )
            ->addItem(
                ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1600, 'h' => 900]),
                [
                    'alt' => __('layout.image_bedroom_alt'),
                    'caption' => __('layout.image_bedroom_caption')
                ]
            )
            ->gridColumnSpan(12);

        $card->addComponent($mediaComponent);

        // Add property summary section below gallery
        $card->addComponent(
            TextComponent::make('gallery-label')
                ->content(__('layout.property_gallery'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-900'])
                ->gridColumnSpan(12)
        );

        $card->addComponent(
            TextComponent::make('gallery-description')
                ->content('Click any image to enlarge in lightbox mode. Swipe or use arrows to navigate through all available property images.')
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
                ->gridColumnSpan(12)
        );

        // Add location info
        $card->addComponent(
            TextComponent::make('location-label')
                ->content(__('layout.location'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-900'])
                ->gridColumnSpan(12)
        );

        $card->addComponent(
            TextComponent::make('location-address')
                ->content('123 Main Street, Springfield, IL 62701')
                ->variant('caption')
                ->meta(['color' => 'text-gray-700'])
                ->gridColumnSpan(12)
        );

        // Add price and status info
        $card->addComponent(
            TextComponent::make('price-label')
                ->content(__('layout.price_and_status'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-900'])
                ->gridColumnSpan(12)
        );

        $card->addComponent(
            TextComponent::make('price-info')
                ->content('Listed Price: $450,000 | Days on Market: 45 | Status: Active')
                ->variant('caption')
                ->meta(['color' => 'text-green-700', 'fontWeight' => '500'])
                ->gridColumnSpan(12)
        );

        // Add property type and features
        $card->addComponent(
            TextComponent::make('type-label')
                ->content(__('layout.property_type'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-900'])
                ->gridColumnSpan(12)
        );

        $card->addComponent(
            TextComponent::make('type-info')
                ->content('Type: Single Family Home | Condition: Excellent | Property Class: Residential')
                ->variant('caption')
                ->meta(['color' => 'text-gray-700'])
                ->gridColumnSpan(12)
        );

        return $card;
    }

    /**
     * Build right panel with property details form
     *
     * @param array $masterData Master data for form
     * @return CardComponent
     */
    private static function buildRightPanel(array $masterData): CardComponent
    {
        // Create overview card with property image and description
        $overviewCard = CardComponent::make('property-overview-card');

        // Add property images gallery - one by one listing
        $overviewCard->addComponent(
            MediaComponent::make('property-images-gallery')
                ->gallery()  // Set type to gallery
                ->grid()     // Layout: grid for one-by-one arrangement
                ->columns(1) // 1 image per row - sequential listing
                ->lightbox(true)
                ->captions(true)
                ->aspectRatio('16:9')
                ->addItem(
                    ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1200, 'h' => 675]),
                    [
                        'alt' => __('layout.image_living_room_alt'),
                        'caption' => __('layout.image_living_room_caption')
                    ]
                )
                ->addItem(
                    ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1200, 'h' => 675]),
                    [
                        'alt' => __('layout.image_kitchen_alt'),
                        'caption' => __('layout.image_kitchen_caption')
                    ]
                )
                ->addItem(
                    ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1200, 'h' => 675]),
                    [
                        'alt' => __('layout.image_bedroom_alt'),
                        'caption' => __('layout.image_bedroom_caption')
                    ]
                )
                ->addItem(
                    ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1200, 'h' => 675]),
                    [
                        'alt' => 'Master Bedroom',
                        'caption' => 'Spacious master bedroom with ensuite'
                    ]
                )
                ->addItem(
                    ImageHelper::url('listings/Screenshot-2026-02-02-at-1-48-23---pm-1770641982-6989da3ed134e.png', ['w' => 1200, 'h' => 675]),
                    [
                        'alt' => 'Backyard',
                        'caption' => 'Large backyard with patio area'
                    ]
                )
                ->gridColumnSpan(12)
        );

        // Add property title and description
        $overviewCard->addComponent(
            TextComponent::make('property-title')
                ->content(__('layout.property_overview'))
                ->variant('h5')
                ->meta(['fontWeight' => 'bold'])
                ->gridColumnSpan(12)
        );

        $overviewCard->addComponent(
            TextComponent::make('property-description')
                ->content('This is a beautiful 3-bedroom, 2-bathroom family home located in the heart of the city. Featuring a spacious living area, modern kitchen, and a large backyard perfect for entertaining guests.')
                ->variant('body2')
                ->meta(['color' => 'text-gray-700'])
                ->gridColumnSpan(12)
        );

        // Add property key details
        $overviewCard->addComponent(
            TextComponent::make('property-details-label')
                ->content(__('layout.key_details'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-900'])
                ->gridColumnSpan(12)
        );

        // Create a grid for key details
        $detailsText = 'Bedrooms: 3 | Bathrooms: 2 | Area: 2,500 sq ft | Year Built: 2018 | Lot Size: 0.5 acres';
        $overviewCard->addComponent(
            TextComponent::make('property-key-details')
                ->content($detailsText)
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
                ->gridColumnSpan(12)
        );

        // Add amenities
        $overviewCard->addComponent(
            TextComponent::make('amenities-label')
                ->content(__('layout.amenities'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-900'])
                ->gridColumnSpan(12)
        );

        $amenitiesText = '• Central Air & Heat • Swimming Pool • Garage • Hardwood Floors • Modern Kitchen • Large Backyard';
        $overviewCard->addComponent(
            TextComponent::make('amenities-list')
                ->content($amenitiesText)
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
                ->gridColumnSpan(12)
        );

        return $overviewCard->gridColumnSpan(4);
    }

    /**
     * Build footer slot for fullscreen view
     *
     * @return SlotManager
     */
    private static function buildFooterSlot(): SlotManager
    {
        // Create footer center grid with summary info
        $centerSlot = SlotManager::make('fullscreen-footer-center');
        $centerSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '4',
            'justify' => 'start',
            'items' => 'center',
            'gridColumnSpan' => 6,
        ]);

        // Property specs badges
        $centerSlot->setComponent(
            TextComponent::make('specs-label')
                ->content(__('layout.property_specs'))
                ->variant('caption')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-gray-700'])
        );

        // Create footer right grid with action buttons
        $rightSlot = SlotManager::make('fullscreen-footer-right');
        $rightSlot->setConfig([
            'layout' => 'flex',
            'direction' => 'row',
            'gap' => '2',
            'justify' => 'end',
            'items' => 'center',
            'gridColumnSpan' => 6,
        ]);

        // Print button
        $rightSlot->setComponent(
            ButtonComponent::make('close-footer-btn')
                ->label(__('layout.close'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        // Property features/overview button (supplementary content like blog's forms-activity)
        $rightSlot->setComponent(
            ButtonComponent::make('features-btn')
                ->label(__('layout.features'))
                ->icon('listcheck')
                ->variant('contained')
                ->meta(['action' => 'view', 'type' => 'aside', 'component' => 'view-listing-features', 'tooltip' => __('layout.view_property_features'), 'color' => 'info'])
        );

        // Export button
        $rightSlot->setComponent(
            ButtonComponent::make('export-btn')
                ->label(__('layout.export_pdf'))
                ->icon('downloadcloud')
                ->variant('contained')
                ->meta(['action' => 'export', 'color' => 'success', 'tooltip' => __('layout.export_property_details')])
        );

        // Wrap footer in SlotManager
        $footerSlot = SlotManager::make('fullscreen-footer-slot');
        $footerSlot->setSection(
            FooterSection::make('fullscreen-aside-footer')
                ->setLeft($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('lg')
        );

        return $footerSlot;
    }
}
