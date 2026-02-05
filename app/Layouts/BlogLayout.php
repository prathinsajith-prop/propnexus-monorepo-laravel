<?php

namespace App\Layouts;

use App\Layout\Builders\BlogLayoutBuilder;
use App\Slots\Blog\BlogEditAsideSlot;
use App\Slots\Blog\BlogFormActivityAsideSlot;
use Litepie\Layout\LayoutBuilder;

/**
 * BlogLayout
 *
 * Comprehensive blog management layout following clean architecture principles.
 * Delegates all section building to BlogLayoutBuilder for better organization.
 * 
 * Architecture:
 * - Minimal layout configuration (this class)
 * - Section builders extracted to BlogLayoutBuilder
 * - Component definitions routed to slot classes
 * - Clean separation of concerns
 * 
 * @package App\Layouts
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
        return LayoutBuilder::create('blogs', 'page')
            ->title('Blog Management')
            ->type('layouts')
            ->meta([
                'masterDataUrl' => '/api/blogs-master-data',
                'description' => 'Blog Management System',
                'version' => '1.0.0',
                'refreshInterval' => null,
            ])
            ->section('header', fn($section) => BlogLayoutBuilder::buildHeaderSection($section))
            ->section('main', fn($section) => BlogLayoutBuilder::buildMainSection($section, $masterData))
            ->section('search', fn($section) => BlogLayoutBuilder::buildSearchComponent($section, $masterData))
            ->section('actions', fn($section) => BlogLayoutBuilder::buildActionsComponent($section, $masterData))
            ->section('footer', fn($section) => BlogLayoutBuilder::buildFooterSection($section))
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
                'create-blog-modal' => BlogLayoutBuilder::buildCreateBlogModal($masterData),
                'delete-blog-modal' => BlogLayoutBuilder::buildDeleteBlogModal(),
                default => null,
            };
        }

        if ($type === 'aside') {
            return match ($componentName) {
                'view-blog' => BlogLayoutBuilder::buildViewBlogAside($masterData),
                'view-blog-full' => BlogLayoutBuilder::buildViewBlogAsideFullscreen($masterData),
                'view-blog-forms' => BlogLayoutBuilder::buildViewBlogFormActivityAside($masterData),
                'view-blog-forms-full' => BlogLayoutBuilder::buildViewBlogFormActivityAside($masterData),
                'view-blog-fa-full' => BlogFormActivityAsideSlot::make($masterData, true),
                'create-blog' => BlogLayoutBuilder::buildCreateBlogAside($masterData),
                'create-blog-full' => BlogLayoutBuilder::buildCreateBlogAsideFullscreen($masterData),
                'edit-blog' => BlogLayoutBuilder::buildEditBlogAside($masterData),
                'edit-blog-full' => BlogEditAsideSlot::make($masterData, true),
                default => null,
            };
        }

        return null;
    }
}
