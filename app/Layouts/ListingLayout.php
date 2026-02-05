<?php

namespace App\Layouts;

use App\Layout\Builders\ListingLayoutBuilder;
use App\Slots\Listing\ListingEditAsideSlot;
use Litepie\Layout\LayoutBuilder;

/**
 * ListingLayout
 *
 * Property listing management layout following clean architecture principles.
 * Delegates all section building to ListingLayoutBuilder for better organization.
 * 
 * Architecture:
 * - Minimal layout configuration (this class)
 * - Section builders extracted to ListingLayoutBuilder
 * - Component definitions routed to slot classes
 * - Clean separation of concerns
 * 
 * @package App\Layouts
 */
class ListingLayout
{
    /**
     * Create listing management layout
     *
     * @param array $masterData Master data for dropdowns and options
     * @return LayoutBuilder
     */
    public static function make($masterData)
    {
        return LayoutBuilder::create('listings', 'page')
            ->title('Listing Management')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/listings-master-data',
                'description' => 'Property Listing Management System',
                'version' => '1.0.0',
                'refreshInterval' => null,
            ])
            ->section('header', fn($section) => ListingLayoutBuilder::buildHeaderSection($section))
            ->section('main', fn($section) => ListingLayoutBuilder::buildMainSection($section, $masterData))
            ->section('search', fn($section) => ListingLayoutBuilder::buildSearchComponent($section, $masterData))
            ->section('actions', fn($section) => ListingLayoutBuilder::buildActionsComponent($section, $masterData))
            ->section('footer', fn($section) => ListingLayoutBuilder::buildFooterSection($section))
            ->build();
    }

    /**
     * Get component definition for modals and asides
     * Called dynamically when components are requested
     * 
     * @param string $type Component type ('modal' or 'aside')
     * @param string $componentName Component identifier
     * @param array $masterData Master data for forms
     * @return array|null Component definition
     */
    public static function getComponentDefinition($type, $componentName, $masterData)
    {
        if ($type === 'modal') {
            return match ($componentName) {
                'create-listing-modal' => ListingLayoutBuilder::buildCreateListingModal($masterData),
                'delete-listing-modal' => ListingLayoutBuilder::buildDeleteListingModal(),
                default => null,
            };
        }

        if ($type === 'aside') {
            return match ($componentName) {
                'view-listing' => ListingLayoutBuilder::buildViewListingAside($masterData),
                'view-listing-full' => ListingLayoutBuilder::buildViewListingAsideFullscreen($masterData),
                'create-listing' => ListingLayoutBuilder::buildCreateListingAside($masterData),
                'create-listing-full' => ListingLayoutBuilder::buildCreateListingAsideFullscreen($masterData),
                'edit-listing' => ListingLayoutBuilder::buildEditListingAside($masterData),
                'edit-listing-full' => ListingEditAsideSlot::make($masterData, true),
                default => null,
            };
        }

        return null;
    }
}
