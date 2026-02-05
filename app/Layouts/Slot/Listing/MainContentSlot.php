<?php

namespace App\Layouts\Slot\Listing;

use Litepie\Layout\Components\TableComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\SlotManager;

/**
 * Listing Main Content Slot
 * 
 * Builds the main content area for listing asides with table and supporting components
 */
class MainContentSlot
{
    /**
     * Build main content slot with table and text
     *
     * @param array $columns Table columns
     * @param string $dataUrl Data URL for table
     * @param string $tableId Table identifier
     * @return SlotManager
     */
    public static function make(
        array $columns = [],
        string $dataUrl = '/api/listing',
        string $tableId = 'listing-table'
    ): SlotManager {
        // Create main content grid
        $mainGrid = GridSection::make('main-content-grid', 1)
            ->rows(2)
            ->gap('md');

        // Add table component
        $table = self::buildTableComponent($tableId, $dataUrl, $columns);
        $mainGrid->add($table);

        // Build and return SlotManager
        return SlotManager::make('main-slot')
            ->setSection($mainGrid)
            ->setComponent(self::buildTextComponent())
            ->setPriority(SlotManager::PRIORITY_SECTION)
            ->setConfig([
                'preserveOrder' => true,
                'orderLocked' => true,
                'renderSequence' => 'sequential',
                'colSpan' => 12,
            ]);
    }

    /**
     * Build table component
     *
     * @param string $tableId Table identifier
     * @param string $dataUrl Data URL
     * @param array $columns Table columns
     * @return TableComponent
     */
    private static function buildTableComponent(
        string $tableId,
        string $dataUrl,
        array $columns
    ): TableComponent {
        return TableComponent::make($tableId)
            ->dataUrl($dataUrl)
            ->columns($columns)
            ->selectable(true)
            ->pagination(true)
            ->perPage(10)
            ->hoverable(true)
            ->striped(true)
            ->meta(['styling' => 'mb-4']);
    }

    /**
     * Build text component for main content
     *
     * @return TextComponent
     */
    private static function buildTextComponent(): TextComponent
    {
        return TextComponent::make('info-text')
            ->content('Select a listing from the table to view details and manage properties.')
            ->variant('body1')
            ->meta(['color' => 'text-gray-700', 'styling' => 'mb-4']);
    }
}
