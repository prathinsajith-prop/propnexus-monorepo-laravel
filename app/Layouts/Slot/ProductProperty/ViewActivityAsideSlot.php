<?php

namespace App\Layouts\Slot\ProductProperty;

use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Components\TimelineComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * View Activity Aside Slot — Product Property
 *
 * Aside drawer showing the full activity history for a property as a timeline.
 */
class ViewActivityAsideSlot
{
    /**
     * Build the view activity history aside.
     */
    public static function make(): array
    {
        $timeline = TimelineComponent::make('activities-full-timeline')
            ->vertical()
            ->showDates(true)
            ->showIcons(true)
            ->addEvent([])
            ->dateFormat('d M Y, H:i')
            ->dataUrl('/api/product-property/:id/activities')
            ->dataParams(['id' => ':eid'])
            ->meta([
                'emptyIcon' => 'clock',
                'emptyText' => __('layout.activities_empty'),
                'emptySubtext' => __('layout.activities_empty_hint'),
            ])
            ->gridColumnSpan(12);

        $mainGrid = GridSection::make('view-activity-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($timeline);

        return DetailSection::make('view-property-activity')
            ->setHeader(self::buildHeader())
            ->setMain(
                SlotManager::make('view-activity-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter(self::buildFooter())
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────────────────

    private static function buildHeader(): SlotManager
    {
        $centerSlot = SlotManager::make('view-activity-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'start',
                'items' => 'start',
                'gridColumnSpan' => 6,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.recent_activities'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.activity_history_description'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $rightSlot = SlotManager::make('view-activity-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('view-activity-header-slot');
        $headerSlot->setSection(
            HeaderSection::make('view-activity-aside-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $headerSlot;
    }

    private static function buildFooter(): SlotManager
    {
        $footerRightSlot = SlotManager::make('view-activity-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 12,
            ]);

        $footerRightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->label(__('layout.close'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerSlot = SlotManager::make('view-activity-footer-slot');
        $footerSlot->setSection(
            FooterSection::make('view-activity-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
