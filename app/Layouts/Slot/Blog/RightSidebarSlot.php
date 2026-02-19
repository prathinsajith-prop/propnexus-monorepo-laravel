<?php

namespace App\Layouts\Slot\Blog;

use Litepie\Layout\Components\ListComponent;
use Litepie\Layout\Components\TimelineComponent;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\SlotManager;

class RightSidebarSlot
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
                ->position('right')
                ->showDates(true)
                ->showIcons(true)
                ->dateFormat('relative')
                ->addEvent([
                    'key' => 'activity-1',
                    'title' => __('layout.blog_post_published'),
                    'description' => __('layout.post_published_live'),
                    'date' => '2 hours ago',
                    'icon' => 'checksquare',
                    'color' => 'success',
                ])
                ->addEvent([
                    'key' => 'activity-2',
                    'title' => __('layout.content_updated'),
                    'description' => __('layout.main_content_revised'),
                    'date' => '4 hours ago',
                    'icon' => 'pen',
                    'color' => 'info',
                ])
                ->addEvent([
                    'key' => 'activity-3',
                    'title' => __('layout.featured_image_changed'),
                    'description' => __('layout.new_image_uploaded'),
                    'date' => '1 day ago',
                    'icon' => 'image',
                    'color' => 'warning',
                ])
                ->addEvent([
                    'key' => 'activity-4',
                    'title' => __('layout.draft_created'),
                    'description' => __('layout.initial_draft_description'),
                    'date' => '3 days ago',
                    'icon' => 'filetext',
                    'color' => 'default',
                ])
                ->meta([
                    'dataUrl' => '/api/blogs/:id/activity',
                    'emptyMessage' => __('layout.no_activity_yet'),
                    'showTimestamps' => true,
                    'compact' => false,
                ])
                ->gridColumnSpan(12)
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
                    'secondary' => __('layout.chat_review_request'),
                    'timestamp' => '2 hours ago',
                    'avatar' => 'JD',
                    'color' => 'primary',
                ],
                [
                    'id' => 'chat-2',
                    'primary' => 'Jane Smith',
                    'secondary' => __('layout.chat_edits_intro'),
                    'timestamp' => '4 hours ago',
                    'avatar' => 'JS',
                    'color' => 'success',
                ],
                [
                    'id' => 'chat-3',
                    'primary' => 'John Doe',
                    'secondary' => __('layout.chat_looks_great'),
                    'timestamp' => '1 day ago',
                    'avatar' => 'JD',
                    'color' => 'primary',
                ],
                [
                    'id' => 'chat-4',
                    'primary' => 'System',
                    'secondary' => __('layout.chat_autosave_success'),
                    'timestamp' => '2 days ago',
                    'avatar' => 'SYS',
                    'color' => 'default',
                ],
            ])
            ->gridColumnSpan(12)
            ->meta([
                'dataUrl' => '/api/blogs/:id/chats',
                'emptyMessage' => __('layout.no_messages_yet'),
                'showTimestamps' => true,
                'showAvatars' => true,
            ]);
    }
}
