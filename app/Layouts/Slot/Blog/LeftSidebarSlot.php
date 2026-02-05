<?php

namespace App\Layouts\Slot\Blog;

use App\Forms\Blog\BlogChatForm;
use App\Forms\Blog\BlogNotesForm;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\SlotManager;

class LeftSidebarSlot
{
    /**
     * Build left sidebar grid with forms
     *
     * @return SlotManager
     */
    public static function make(): SlotManager
    {
        $leftSlot = SlotManager::make('main-slot');

        $leftGrid = GridSection::make('left-sidebar-grid', 1)
            ->rows(1)
            ->gap('md');

        $leftGrid->add(
            BlogNotesForm::make('add-notes-form', 'POST', '/api/blogs/:id/notes')->gridColumnSpan(12)
        );

        $leftGrid->add(
            BlogChatForm::make('new-chat-form', 'POST', '/api/blogs/:id/chats')->gridColumnSpan(12)
        );

        return $leftSlot
            ->setSection($leftGrid)
            ->setConfig([
                'colSpan' => 2,
            ]);
    }
}
