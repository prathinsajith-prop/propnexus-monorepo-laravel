<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductPropertyStatus: string
{
    case DRAFT = 'Draft';
    case PENDING = 'Pending';
    case WAITING_PUBLISH = 'Waiting Publish';
    case PUBLISHED = 'Published';
    case WAITING_UNPUBLISH = 'Waiting Unpublish';
    case UNPUBLISHED = 'Unpublished';
    case ARCHIVED = 'Archived';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case POCKET_PUBLISH = 'Pocket Publish';
    case PENDING_VERIFICATION = 'Pending Verification';
    case VERIFIED = 'Verified';
    case COMPLETED = 'Completed';
    case PUBLIC = 'Public';
    case PRIVATE = 'Private';
    case JUNK = 'Junk';
    case WAITING_TEAMLEADER = 'Waiting Teamleader';
    case WAITING_TEAM_LEADER = 'Waiting Team Leader';

    public function label(): string
    {
        return $this->value;
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'pencil',
            self::PENDING, self::WAITING_PUBLISH, self::WAITING_UNPUBLISH,
            self::WAITING_TEAMLEADER, self::WAITING_TEAM_LEADER => 'clock',
            self::PUBLISHED, self::APPROVED, self::VERIFIED, self::COMPLETED => 'check-circle',
            self::UNPUBLISHED, self::REJECTED, self::JUNK => 'cross',
            self::ARCHIVED => 'archive',
            self::POCKET_PUBLISH => 'banknote',
            self::PENDING_VERIFICATION => 'clipboard-check',
            self::PUBLIC => 'eye',
            self::PRIVATE => 'lock',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING, self::WAITING_PUBLISH, self::WAITING_UNPUBLISH,
            self::WAITING_TEAMLEADER, self::WAITING_TEAM_LEADER => 'yellow',
            self::PUBLISHED, self::APPROVED => 'green',
            self::UNPUBLISHED => 'slate',
            self::ARCHIVED => 'slate',
            self::REJECTED, self::JUNK => 'red',
            self::POCKET_PUBLISH => 'purple',
            self::PENDING_VERIFICATION => 'orange',
            self::VERIFIED, self::COMPLETED => 'blue',
            self::PUBLIC => 'teal',
            self::PRIVATE => 'indigo',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::ARCHIVED, self::REJECTED, self::JUNK, self::UNPUBLISHED]);
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PUBLISHED, self::PUBLIC]);
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => '#6B7280', // gray
            self::PENDING => '#F59E0B', // amber
            self::WAITING_PUBLISH,
            self::WAITING_UNPUBLISH,
            self::WAITING_TEAMLEADER,
            self::WAITING_TEAM_LEADER => '#F59E0B', // amber
            self::PUBLISHED => '#10B981', // green
            self::APPROVED => '#22C55E', // green-500
            self::UNPUBLISHED => '#94A3B8', // slate
            self::ARCHIVED => '#64748B', // slate-600
            self::REJECTED => '#EF4444', // red
            self::JUNK => '#DC2626', // red-600
            self::POCKET_PUBLISH => '#8B5CF6', // purple
            self::PENDING_VERIFICATION => '#F97316', // orange
            self::VERIFIED => '#3B82F6', // blue
            self::COMPLETED => '#2563EB', // blue-600
            self::PUBLIC => '#14B8A6', // teal
            self::PRIVATE => '#6366F1', // indigo
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'color' => $case->color(),
            'icon' => $case->icon(),
        ])->toArray();
    }

    /**
     * Badge configuration for BadgeComponent (mirrors ListingStatus::badgeConfig).
     */
    public static function badgeConfig(): array
    {
        $config = [];
        foreach (self::cases() as $case) {
            $config[$case->value] = [
                'value' => $case->value,
                'label' => $case->label(),
                'color' => $case->badgeColor(),
                'icon' => $case->icon(),
            ];
        }

        return $config;
    }
}
