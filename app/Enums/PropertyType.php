<?php

declare(strict_types=1);

namespace App\Enums;

enum PropertyType: string
{
    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';
    case LAND = 'land';
    case INDUSTRIAL = 'industrial';

    public function label(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'Residential',
            self::COMMERCIAL => 'Commercial',
            self::LAND => 'Land',
            self::INDUSTRIAL => 'Industrial',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RESIDENTIAL => '🏠',
            self::COMMERCIAL => '🏢',
            self::LAND => '🌍',
            self::INDUSTRIAL => '🏭',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'blue',
            self::COMMERCIAL => 'purple',
            self::LAND => 'green',
            self::INDUSTRIAL => 'orange',
        };
    }

    /**
     * Get badge color for UI components (hex code)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::RESIDENTIAL => '#3B82F6',  // Blue
            self::COMMERCIAL => '#8B5CF6',   // Purple
            self::LAND => '#10B981',         // Green
            self::INDUSTRIAL => '#F97316',   // Orange
        };
    }

    /**
     * Get Lucide icon name for UI components
     */
    public function iconName(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'home',
            self::COMMERCIAL => 'building',
            self::LAND => 'map',
            self::INDUSTRIAL => 'building',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get badge configuration for badge component
     * Returns array mapping property type values to hex color codes and Lucide icon names
     *
     * Format: [
     *   'residential' => [
     *     'value' => 'residential',
     *     'label' => 'Residential',
     *     'color' => '#3B82F6',  // Hex color code
     *     'icon' => 'home',      // Lucide icon name
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
     * Get property type configuration for a specific value
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
