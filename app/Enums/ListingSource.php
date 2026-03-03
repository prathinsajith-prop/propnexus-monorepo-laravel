<?php

declare(strict_types=1);

namespace App\Enums;

enum ListingSource: string
{
    case Direct = 'Direct';
    case Referral = 'Referral';
    case Portal = 'Portal';
    case SocialMedia = 'Social Media';
    case Other = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::Direct => __('product_property.source_direct'),
            self::Referral => __('product_property.source_referral'),
            self::Portal => __('product_property.source_portal'),
            self::SocialMedia => __('product_property.source_social_media'),
            self::Other => __('product_property.source_other'),
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
