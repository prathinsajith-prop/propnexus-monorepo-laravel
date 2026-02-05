<?php

namespace App\Layouts;

use App\Layouts\Builder\Blog\LayoutBuilder;
use App\Layouts\Slot\Blog\EditAsideSlot;
use App\Layouts\Slot\Blog\FormActivityAsideSlot;
use Litepie\Layout\LayoutBuilder as LitepieLayoutBuilder;

/**
 * BlogLayout
 *
 * Main entry point for the blog management interface.
 * 
 * **Structure**:
 * - Entry Point: This class (wires components together)
 * - Builder: App\Layouts\Builder\Blog\LayoutBuilder (construction logic)
 * - Slots: App\Layouts\Slot\Blog\* (reusable components)
 * 
 * **Responsibilities**:
 * - Define page configuration (title, meta, sections)
 * - Register modal components (create, delete, confirmation)
 * - Register aside panels (view, edit, create, form activity)
 * - Delegate section building to LayoutBuilder
 * 
 * **Architecture**:
 * Clean separation: Layout (config) → Builder (logic) → Slots (components)
 * 
 * @package App\Layouts
 * @see \App\Layouts\Builder\Blog\LayoutBuilder
 * @see \App\Layouts\Slot\Blog
 */
class BlogLayout
{
    /**
     * Create blog management layout
     *
     * @param array $masterData Master data for dropdowns and options
     * @return LayoutBuilder
     */
    public static function make($masterData)
    {
        return LitepieLayoutBuilder::create('blogs', 'page')
            ->title('Blog Management')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/blogs-master-data',
                'description' => 'Blog Management System',
                'version' => '1.0.0',
                'refreshInterval' => null,
            ])
            ->section('header', fn($section) => LayoutBuilder::buildHeaderSection($section))
            ->section('main', fn($section) => LayoutBuilder::buildMainSection($section, $masterData))
            ->section('search', fn($section) => LayoutBuilder::buildSearchComponent($section, $masterData))
            ->section('actions', fn($section) => LayoutBuilder::buildActionsComponent($section, $masterData))
            ->section('footer', fn($section) => LayoutBuilder::buildFooterSection($section))
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
                'create-blog-modal' => LayoutBuilder::buildCreateBlogModal($masterData),
                'delete-blog-modal' => LayoutBuilder::buildDeleteBlogModal(),
                default => null,
            };
        }

        if ($type === 'aside') {
            return match ($componentName) {
                'view-blog' => LayoutBuilder::buildViewBlogAside($masterData),
                'view-blog-full' => LayoutBuilder::buildViewBlogAsideFullscreen($masterData),
                'view-blog-forms' => LayoutBuilder::buildViewBlogFormActivityAside($masterData),
                'view-blog-forms-full' => LayoutBuilder::buildViewBlogFormActivityAside($masterData),
                'view-blog-fa-full' => FormActivityAsideSlot::make($masterData, true),
                'create-blog' => LayoutBuilder::buildCreateBlogAside($masterData),
                'create-blog-full' => LayoutBuilder::buildCreateBlogAsideFullscreen($masterData),
                'edit-blog' => LayoutBuilder::buildEditBlogAside($masterData),
                'edit-blog-full' => EditAsideSlot::make($masterData, true),
                default => null,
            };
        }

        return null;
    }
}
