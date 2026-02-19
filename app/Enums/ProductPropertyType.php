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
            self::LIVE => 'bolt',
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

    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }
}
