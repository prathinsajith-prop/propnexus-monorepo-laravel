<?php

declare(strict_types=1);

namespace App\Enums;

enum Availability: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case SOLD = 'sold';
    case RENTED = 'rented';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::RESERVED => 'Reserved',
            self::SOLD => 'Sold',
            self::RENTED => 'Rented',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::AVAILABLE => '✅',
            self::RESERVED => '⏳',
            self::SOLD => '🤝',
            self::RENTED => '🔑',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'green',
            self::RESERVED => 'yellow',
            self::SOLD => 'blue',
            self::RENTED => 'purple',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
