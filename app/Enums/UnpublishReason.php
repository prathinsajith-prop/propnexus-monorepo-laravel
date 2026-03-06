<?php

declare(strict_types=1);

namespace App\Enums;

enum UnpublishReason: string
{
    case Duplicate = 'Duplicate';
    case WrongInformation = 'Wrong Information';
    case PriceChange = 'Price Change';
    case PropertySold = 'Property Sold';
    case PropertyRented = 'Property Rented';
    case UnderMaintenance = 'Under Maintenance';
    case OwnerRequest = 'Owner Request';
    case Other = 'Other';

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

    public static function getMasterdata(): array
    {
        return self::options();
    }
}
