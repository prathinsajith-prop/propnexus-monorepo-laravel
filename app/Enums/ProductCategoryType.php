<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductCategoryType: string
{
    case COMMERCIAL = 'Commercial';
    case RESIDENTIAL = 'Residential';

    public function label(): string
    {
        return $this->value;
    }

    public function icon(): string
    {
        return match ($this) {
            self::COMMERCIAL => 'building',
            self::RESIDENTIAL => 'home',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::COMMERCIAL => 'blue',
            self::RESIDENTIAL => 'green',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::COMMERCIAL => '#3B82F6',  // Blue
            self::RESIDENTIAL => '#10B981', // Green
        };
    }

    public function iconName(): string
    {
        return match ($this) {
            self::COMMERCIAL => 'building',
            self::RESIDENTIAL => 'home',
        };
    }

    public static function badgeConfig(): array
    {
        $config = [];
        foreach (self::cases() as $case) {
            $config[$case->value] = [
                'value' => $case->value,
                'label' => $case->label(),
                'color' => $case->badgeColor(),
                'icon' => $case->iconName(),
            ];
        }

        return $config;
    }
}
