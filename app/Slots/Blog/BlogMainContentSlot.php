<?php

namespace App\Slots\Blog;

use App\Forms\Blog\BlogForm;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\SlotManager;

/**
 * Blog Main Content Slot
 * 
 * Builds the main content area for blog asides with form and supporting components
 */
class BlogMainContentSlot
{
    /**
     * Build main content slot with blog form and text
     *
     * @param array $masterData Master data for form
     * @param string $formId Form identifier
     * @param string $method HTTP method
     * @param string $submitUrl Submit URL
     * @param string|null $dataUrl Optional data URL for editing
     * @return SlotManager
     */
    public static function make(
        array $masterData = [],
        string $formId = 'blog-form',
        string $method = 'POST',
        string $submitUrl = '/api/blogs',
        ?string $dataUrl = null
    ): SlotManager {
        // Create main content grid
        $mainGrid = GridSection::make('main-content-grid', 1)
            ->rows(2)
            ->gap('md');

        // Add blog form
        $blogForm = $dataUrl
            ? BlogForm::make($formId, $method, $submitUrl, $masterData, $dataUrl)
            : BlogForm::make($formId, $method, $submitUrl, $masterData);

        $mainGrid->add($blogForm->gridColumnSpan(12));

        // Create form grid for additional form component
        $blogFormGrid = self::buildBlogFormGrid($formId . '-activity', $method, $submitUrl, $masterData, $dataUrl);

        // Build and return SlotManager
        return SlotManager::make('main-slot')
            ->setSection($mainGrid)
            ->setComponent(self::buildTextComponent())
            ->setSection($blogFormGrid)
            ->setPriority(SlotManager::PRIORITY_SECTION)
            ->setConfig([
                'preserveOrder' => true,
                'orderLocked' => true,
                'renderSequence' => 'sequential',
                'colSpan' => 7,
            ]);
    }

    /**
     * Build blog form grid component
     *
     * @param string $formId Form identifier
     * @param string $method HTTP method
     * @param string $submitUrl Submit URL
     * @param array $masterData Master data
     * @param string|null $dataUrl Optional data URL
     * @return GridSection
     */
    private static function buildBlogFormGrid(
        string $formId,
        string $method,
        string $submitUrl,
        array $masterData,
        ?string $dataUrl = null
    ): GridSection {
        $config = [
            'columns' => 1,
            'rows' => 1,
            'gap' => 'md',
            'styling' => 'w-full',
        ];

        $formGrid = GridSection::make("{$formId}-grid", $config['columns'])
            ->rows($config['rows'])
            ->gap($config['gap'])
            ->meta(['styling' => $config['styling']]);

        $blogForm = $dataUrl
            ? BlogForm::make($formId, $method, $submitUrl, $masterData, $dataUrl)
            : BlogForm::make($formId, $method, $submitUrl, $masterData);

        $blogForm->gridColumnSpan(12);
        $formGrid->add($blogForm);

        return $formGrid;
    }

    /**
     * Build text component for main content
     *
     * @return TextComponent
     */
    private static function buildTextComponent(): TextComponent
    {
        return TextComponent::make('info-text')
            ->content('Select a blog from the table to view details, activity, and chat history.')
            ->variant('body1')
            ->meta(['color' => 'text-gray-700', 'styling' => 'mb-4']);
    }

    /**
     * Quick helper to create main content for creating a blog
     *
     * @param array $masterData Master data
     * @return SlotManager
     */
    public static function forCreate(array $masterData = []): SlotManager
    {
        return self::make($masterData, 'create-blog-form', 'POST', '/api/blogs');
    }

    /**
     * Quick helper to create main content for editing a blog
     *
     * @param array $masterData Master data
     * @return SlotManager
     */
    public static function forEdit(array $masterData = []): SlotManager
    {
        return self::make($masterData, 'edit-blog-form', 'PUT', '/api/blogs/:id', '/api/blogs/:id');
    }

    /**
     * Quick helper to create main content for viewing a blog
     *
     * @param array $masterData Master data
     * @return SlotManager
     */
    public static function forView(array $masterData = []): SlotManager
    {
        return self::make($masterData, 'view-blog-form', 'GET', '/api/blogs/:id', '/api/blogs/:id');
    }
}
