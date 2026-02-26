<?php

namespace App\Layouts;

use App\Layouts\Builder\ProductProperty\LayoutBuilder;
use Litepie\Layout\LayoutBuilder as LitepieLayoutBuilder;

/**
 * ProductPropertyLayout
 *
 * Main entry point for the product property management interface.
 *
 * **Structure**:
 * - Entry Point: This class (wires components together)
 * - Builder: App\Layouts\Builder\ProductProperty\LayoutBuilder (construction logic)
 * - Slots: App\Layouts\Slot\ProductProperty\* (reusable components)
 *
 * **Responsibilities**:
 * - Define page configuration (title, meta, sections)
 * - Register modal components (create, delete, confirmation)
 * - Register aside panels (view, edit, create)
 * - Delegate section building to LayoutBuilder
 */
class ProductPropertyLayout
{
    /**
     * Create product property management layout
     *
     * @param  array  $masterData  Master data for dropdowns and options
     * @return LayoutBuilder
     */
    public static function make($masterData)
    {
        return LitepieLayoutBuilder::create('product-properties', 'page')
            ->title(__('product_property.title'))
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/product-property-master-data',
                'description' => __('product_property.management_system'),
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
                'create-property-modal' => LayoutBuilder::buildCreatePropertyModal($masterData),
                'delete-property-modal' => LayoutBuilder::buildDeletePropertyModal(),
                'create-property-followup' => LayoutBuilder::buildCreatePropertyFollowupModal(),
                'edit-property-followup' => LayoutBuilder::buildEditPropertyFollowupModal(),
                default => null,
            };
        }

        if ($type === 'aside') {
            return match ($componentName) {
                'view-property' => LayoutBuilder::buildViewPropertyAside($masterData),
                'view-property-full' => LayoutBuilder::buildViewPropertyAsideFullscreen($masterData),
                'create-property' => LayoutBuilder::buildCreatePropertyAside($masterData),
                'create-property-full' => LayoutBuilder::buildCreatePropertyAsideFullscreen($masterData),
                'edit-property' => LayoutBuilder::buildEditPropertyAside($masterData),
                'edit-property-full' => LayoutBuilder::buildEditPropertyAsideFullscreen($masterData),
                default => null,
            };
        }

        return null;
    }
}
