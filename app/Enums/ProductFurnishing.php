<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductFurnishing: string
{
    case FURNISHED = 'Furnished';
    case UNFURNISHED = 'Unfurnished';
    case PARTLY_FURNISHED = 'Partly Furnished';
    case FITTED = 'Fitted';
    case NOT_FITTED = 'Not Fitted';
    case SHELL_AND_CORE = 'Shell And Core';

    public function label(): string
    {
        return $this->value;
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }
}
