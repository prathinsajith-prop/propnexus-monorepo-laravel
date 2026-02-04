<?php

declare(strict_types=1);

namespace App\Enums;

enum ListingType: string
{
    case SALE = 'sale';
    case RENT = 'rent';
    case LEASE = 'lease';

    public function label(): string
    {
        return match ($this) {
            self::SALE => 'For Sale',
            self::RENT => 'For Rent',
            self::LEASE => 'For Lease',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SALE => '🏷️',
            self::RENT => '🔑',
            self::LEASE => '📋',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SALE => 'blue',
            self::RENT => 'green',
            self::LEASE => 'purple',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
