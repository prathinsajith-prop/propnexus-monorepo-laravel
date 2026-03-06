<?php

declare(strict_types=1);

namespace App\Enums;

enum Availability: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case SOLD = 'sold';
    case RENTED = 'rented';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::RESERVED => 'Reserved',
            self::SOLD => 'Sold',
            self::RENTED => 'Rented',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::AVAILABLE => '✅',
            self::RESERVED => '⏳',
            self::SOLD => '🤝',
            self::RENTED => '🔑',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'green',
            self::RESERVED => 'yellow',
            self::SOLD => 'blue',
            self::RENTED => 'purple',
        };
    }

    /**
     * Get badge color for UI components (hex code)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::AVAILABLE => '#10B981',  // Green
            self::RESERVED => '#F59E0B',   // Amber
            self::SOLD => '#3B82F6',       // Blue
            self::RENTED => '#8B5CF6',     // Purple
        };
    }

    /**
     * Get Lucide icon name for UI components
     */
    public function iconName(): string
    {
        return match ($this) {
            self::AVAILABLE => 'badgecheck',
            self::RESERVED => 'clock',
            self::SOLD => 'price',
            self::RENTED => 'key',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get badge configuration for badge component
     * Returns array mapping availability values to hex color codes and Lucide icon names
     *
     * Format: [
     *   'available' => [
     *     'value' => 'available',
     *     'label' => 'Available',
     *     'color' => '#10B981',      // Hex color code
     *     'icon' => 'check-circle',  // Lucide icon name
     *   ]
     * ]
     */
    public static function badgeConfig(): array
    {
        $colors = [];
        foreach (self::cases() as $status) {
            $colors[$status->value] = [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->badgeColor(),
                'icon' => $status->iconName(),
            ];
        }

        return $colors;
    }

    /**
     * Get availability configuration for a specific value
     */
    public static function getStatusConfig(string $value): ?array
    {
        $status = self::tryFrom($value);
        if (! $status) {
            return null;
        }

        return [
            'value' => $status->value,
            'label' => $status->label(),
            'color' => $status->badgeColor(),
            'icon' => $status->iconName(),
        ];
    }
}
