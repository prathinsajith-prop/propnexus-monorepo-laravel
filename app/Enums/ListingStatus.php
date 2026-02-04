<?php

declare(strict_types=1);

namespace App\Enums;

enum ListingStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case SOLD = 'sold';
    case RENTED = 'rented';
    case EXPIRED = 'expired';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::SOLD => 'Sold',
            self::RENTED => 'Rented',
            self::EXPIRED => 'Expired',
            self::ARCHIVED => 'Archived',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => '✏️',
            self::ACTIVE => '✅',
            self::PENDING => '⏳',
            self::SOLD => '🤝',
            self::RENTED => '🔑',
            self::EXPIRED => '⏰',
            self::ARCHIVED => '📦',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::PENDING => 'yellow',
            self::SOLD => 'blue',
            self::RENTED => 'purple',
            self::EXPIRED => 'red',
            self::ARCHIVED => 'slate',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::SOLD, self::RENTED, self::ARCHIVED]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
