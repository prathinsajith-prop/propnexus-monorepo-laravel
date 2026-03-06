<?php

declare(strict_types=1);

namespace App\Enums;

enum PreviewType: string
{
    case WithMyDetails = 'with_my_details';
    case WithoutDetails = 'without_details';
    case AsAnonymous = 'as_anonymous';

    public function label(): string
    {
        return match ($this) {
            self::WithMyDetails => 'Preview With My Details',
            self::WithoutDetails => 'Preview Without Details',
            self::AsAnonymous => 'Preview As Anonymous',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public static function getMasterdata(): array
    {
        return self::options();
    }
}
