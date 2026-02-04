<?php

declare(strict_types=1);

namespace App\Enums;

enum PropertyType: string
{
    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';
    case LAND = 'land';
    case INDUSTRIAL = 'industrial';

    public function label(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'Residential',
            self::COMMERCIAL => 'Commercial',
            self::LAND => 'Land',
            self::INDUSTRIAL => 'Industrial',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RESIDENTIAL => '🏠',
            self::COMMERCIAL => '🏢',
            self::LAND => '🌍',
            self::INDUSTRIAL => '🏭',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'blue',
            self::COMMERCIAL => 'purple',
            self::LAND => 'green',
            self::INDUSTRIAL => 'orange',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
