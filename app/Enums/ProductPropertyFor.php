<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductPropertyFor: string
{
    case RENTAL = 'Rental';
    case SALES = 'Sales';

    public function label(): string
    {
        return $this->value;
    }

    public function icon(): string
    {
        return match ($this) {
            self::RENTAL => 'key',
            self::SALES => 'cash',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RENTAL => 'purple',
            self::SALES => 'blue',
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
            self::RENTAL => '#8B5CF6', // Purple
            self::SALES => '#3B82F6', // Blue
        };
    }

    public function iconName(): string
    {
        return match ($this) {
            self::RENTAL => 'key',
            self::SALES => 'cash',
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
