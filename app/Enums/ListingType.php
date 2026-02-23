<?php

declare(strict_types=1);

namespace App\Enums;

enum ListingType: string
{
    case SALE = 'sale';
    case RENT = 'rent';
    case LEASE = 'lease';

    public function label(): string
    {
        return match ($this) {
            self::SALE => 'For Sale',
            self::RENT => 'For Rent',
            self::LEASE => 'For Lease',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SALE => '🏷️',
            self::RENT => '🔑',
            self::LEASE => '📋',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SALE => 'blue',
            self::RENT => 'green',
            self::LEASE => 'purple',
        };
    }

    /**
     * Get badge color for UI components (hex code)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::SALE => '#3B82F6',   // Blue
            self::RENT => '#10B981',   // Green
            self::LEASE => '#8B5CF6',  // Purple
        };
    }

    /**
     * Get Lucide icon name for UI components
     */
    public function iconName(): string
    {
        return match ($this) {
            self::SALE => 'tag',
            self::RENT => 'key',
            self::LEASE => 'file-text',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get badge configuration for badge component
     * Returns array mapping listing type values to hex color codes and Lucide icon names
     *
     * Format: [
     *   'sale' => [
     *     'value' => 'sale',
     *     'label' => 'For Sale',
     *     'color' => '#3B82F6',  // Hex color code
     *     'icon' => 'tag',       // Lucide icon name
     *   ]
     * ]
     */
    public static function badgeConfig(): array
    {
        $colors = [];
        foreach (self::cases() as $type) {
            $colors[$type->value] = [
                'value' => $type->value,
                'label' => $type->label(),
                'color' => $type->badgeColor(),
                'icon' => $type->iconName(),
            ];
        }

        return $colors;
    }

    /**
     * Get listing type configuration for a specific value
     */
    public static function getStatusConfig(string $value): ?array
    {
        $type = self::tryFrom($value);
        if (! $type) {
            return null;
        }

        return [
            'value' => $type->value,
            'label' => $type->label(),
            'color' => $type->badgeColor(),
            'icon' => $type->iconName(),
        ];
    }
}
