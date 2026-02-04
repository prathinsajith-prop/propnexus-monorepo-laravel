<?php

namespace App\Slots\Blog;

use Litepie\Layout\Components\ListComponent;
use Litepie\Layout\Components\TimelineComponent;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\SlotManager;

class BlogRightSidebarSlot
{
    /**
     * Build right sidebar grid with activity timeline and chat history
     *
     * @return SlotManager
     */
    public static function make(): SlotManager
    {
        $rightGridSlot = SlotManager::make('right-sidebar-slot');
        $rightGrid = GridSection::make('right-sidebar-grid', 1)
            ->rows(1)
            ->gap('md');

        $rightGrid->add(
            TimelineComponent::make('activity-timeline')
                ->vertical()
                ->position('left')
                ->showDates(true)
                ->showIcons(true)
                ->dateFormat('relative')
                ->addEvent([
                    'key' => 'activity-1',
                    'title' => 'Blog post published',
                    'description' => 'Post was published and is now live',
                    'date' => '2 hours ago',
                    'icon' => 'checksquare',
                    'color' => 'success',
                ])
                ->addEvent([
                    'key' => 'activity-2',
                    'title' => 'Content updated',
                    'description' => 'Main content section was revised',
                    'date' => '4 hours ago',
                    'icon' => 'pen',
                    'color' => 'info',
                ])
                ->addEvent([
                    'key' => 'activity-3',
                    'title' => 'Featured image changed',
                    'description' => 'New featured image uploaded',
                    'date' => '1 day ago',
                    'icon' => 'image',
                    'color' => 'warning',
                ])
                ->addEvent([
                    'key' => 'activity-4',
                    'title' => 'Draft created',
                    'description' => 'Initial draft of the blog post',
                    'date' => '3 days ago',
                    'icon' => 'filetext',
                    'color' => 'default',
                ])
                ->meta([
                    'dataUrl' => '/api/blogs/:id/activity',
                    'emptyMessage' => 'No activity yet',
                    'showTimestamps' => true,
                    'compact' => false,
                ])
                ->gridColumnSpan(6)
        );

        $rightGrid->add(
            self::buildChatHistoryCard()
        );

        return $rightGridSlot
            ->setSection($rightGrid)
            ->setConfig([
                'colSpan' => 3,
            ]);
    }

    /**
     * Build Chat History card
     *
     * @return ListComponent
     */
    private static function buildChatHistoryCard(): ListComponent
    {
        return ListComponent::make('chat-history-list')
            ->dense(false)
            ->disablePadding(false)
            ->items([
                [
                    'id' => 'chat-1',
                    'primary' => 'John Doe',
                    'secondary' => 'Can we review this before publishing?',
                    'timestamp' => '2 hours ago',
                    'avatar' => 'JD',
                    'color' => 'primary',
                ],
                [
                    'id' => 'chat-2',
                    'primary' => 'Jane Smith',
                    'secondary' => 'I made some edits to the introduction.',
                    'timestamp' => '4 hours ago',
                    'avatar' => 'JS',
                    'color' => 'success',
                ],
                [
                    'id' => 'chat-3',
                    'primary' => 'John Doe',
                    'secondary' => 'Looks great! Just need to update the images.',
                    'timestamp' => '1 day ago',
                    'avatar' => 'JD',
                    'color' => 'primary',
                ],
                [
                    'id' => 'chat-4',
                    'primary' => 'System',
                    'secondary' => 'Draft auto-saved successfully.',
                    'timestamp' => '2 days ago',
                    'avatar' => 'SYS',
                    'color' => 'default',
                ],
            ])
            ->gridColumnSpan(6)
            ->meta([
                'dataUrl' => '/api/blogs/:id/chats',
                'emptyMessage' => 'No messages yet',
                'showTimestamps' => true,
                'showAvatars' => true,
            ]);
    }
}
