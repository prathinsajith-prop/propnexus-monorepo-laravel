<?php

declare(strict_types=1);

namespace App\Enums;

enum NoteType: string
{
    case Rejected = 'Rejected';
    case LeadComment = 'Lead Comment';
    case PublicNotes = 'Public Notes';
    case LeadInterested = 'Lead Interested';
    case LeadContactLater = 'Lead Contact Later';

    public function label(): string
    {
        return match ($this) {
            self::Rejected => 'Rejected',
            self::LeadComment => 'Lead Comment',
            self::PublicNotes => 'Public Notes',
            self::LeadInterested => 'Lead Interested',
            self::LeadContactLater => 'Lead Contact Later',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Rejected => 'red',
            self::LeadComment => 'blue',
            self::PublicNotes => 'gray',
            self::LeadInterested => 'green',
            self::LeadContactLater => 'amber',
        };
    }

    public function iconName(): string
    {
        return match ($this) {
            self::Rejected => 'x-circle',
            self::LeadComment => 'chat-bubble-left',
            self::PublicNotes => 'document-text',
            self::LeadInterested => 'star',
            self::LeadContactLater => 'clock',
        };
    }

    public function badgeConfig(): array
    {
        return [
            'label' => $this->label(),
            'color' => $this->badgeColor(),
            'icon' => $this->iconName(),
        ];
    }

    /** @return array<int, array{value: string, label: string, color: string, icon: string}> */
    public static function getMasterdata(): array
    {
        return array_map(
            fn (self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'color' => $case->badgeColor(),
                'icon' => $case->iconName(),
            ],
            self::cases()
        );
    }
}
