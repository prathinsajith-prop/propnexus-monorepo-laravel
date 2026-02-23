<?php

namespace App\Layouts;

use App\Layouts\Builder\Listing\LayoutBuilder;
use App\Layouts\Slot\Listing\EditAsideSlot;
use Litepie\Layout\LayoutBuilder as LitepieLayoutBuilder;

/**
 * ListingLayout
 *
 * Main entry point for the property listing management interface.
 *
 * **Structure**:
 * - Entry Point: This class (wires components together)
 * - Builder: App\Layouts\Builder\Listing\LayoutBuilder (construction logic)
 * - Slots: App\Layouts\Slot\Listing\* (reusable components)
 *
 * **Responsibilities**:
 * - Define page configuration (title, meta, sections)
 * - Register modal components (create, delete, confirmation)
 * - Register aside panels (view, edit, create)
 * - Delegate section building to LayoutBuilder
 *
 * **Architecture**:
 * Clean separation: Layout (config) → Builder (logic) → Slots (components)
 * Mirrors Blog structure for consistency
 *
 * @see \App\Layouts\Builder\Listing\LayoutBuilder
 * @see \App\Layouts\Slot\Listing
 */
class ListingLayout
{
    /**
     * Create listing management layout
     *
     * @param  array  $masterData  Master data for dropdowns and options
     * @return LayoutBuilder
     */
    public static function make($masterData)
    {
        return LitepieLayoutBuilder::create('listings', 'page')
            ->title(__('layout.listings'))
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/listing-master-data',
                'description' => __('layout.listing_management_system'),
                'version' => '1.0.0',
                'refreshInterval' => null,
            ])
            ->section('header', fn ($section) => LayoutBuilder::buildHeaderSection($section))
            ->section('main', fn ($section) => LayoutBuilder::buildMainSection($section, $masterData))
            ->section('search', fn ($section) => LayoutBuilder::buildSearchComponent($section, $masterData))
            ->section('actions', fn ($section) => LayoutBuilder::buildActionsComponent($section, $masterData))
            ->section('footer', fn ($section) => LayoutBuilder::buildFooterSection($section))
            ->build();
    }

    /**
     * Get component definition for modals and asides
     * Called dynamically when components are requested
     *
     * @param  string  $type  Component type ('modal' or 'aside')
     * @param  string  $componentName  Component identifier
     * @param  array  $masterData  Master data for forms
     * @return array|null Component definition
     */
    public static function getComponentDefinition($type, $componentName, $masterData)
    {
        if ($type === 'modal') {
            return match ($componentName) {
                'create-listing-modal' => LayoutBuilder::buildCreateListingModal($masterData),
                'delete-listing-modal' => LayoutBuilder::buildDeleteListingModal(),
                default => null,
            };
        }

        if ($type === 'aside') {
            return match ($componentName) {
                'view-listing' => LayoutBuilder::buildViewListingAside($masterData),
                'view-listing-full' => LayoutBuilder::buildViewListingAsideFullscreen($masterData),
                'create-listing' => LayoutBuilder::buildCreateListingAside($masterData),
                'create-listing-full' => LayoutBuilder::buildCreateListingAsideFullscreen($masterData),
                'edit-listing' => LayoutBuilder::buildEditListingAside($masterData),
                'edit-listing-full' => EditAsideSlot::make($masterData, true),
                default => null,
            };
        }

        return null;
    }
}
