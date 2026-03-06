<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductPropertyType: string
{
    case LIVE = 'Live';
    case POCKET = 'Pocket';
    case DEVELOPER = 'Developer';
    case VERIFIED_POCKET = 'Verified Pocket';

    public function label(): string
    {
        return $this->value;
    }

    public function icon(): string
    {
        return match ($this) {
            self::LIVE => 'antenna',
            self::POCKET => 'briefcase',
            self::DEVELOPER => 'building',
            self::VERIFIED_POCKET => 'shield',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LIVE => 'green',
            self::POCKET => 'blue',
            self::DEVELOPER => 'orange',
            self::VERIFIED_POCKET => 'teal',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::LIVE => '#10B981',
            self::POCKET => '#3B82F6',
            self::DEVELOPER => '#F59E0B',
            self::VERIFIED_POCKET => '#14B8A6',
        };
    }

    public function iconName(): string
    {
        return $this->icon();
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

    public static function options(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }
}
