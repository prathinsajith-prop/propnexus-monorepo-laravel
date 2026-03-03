<?php

declare(strict_types=1);

namespace App\Enums;

enum ConstructionStatus: string
{
    case Completed = 'Completed';
    case UnderConstruction = 'Under Construction';

    public function label(): string
    {
        return match ($this) {
            self::Completed => __('product_property.construction_completed'),
            self::UnderConstruction => __('product_property.construction_under_construction'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }
}
