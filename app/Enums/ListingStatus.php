<?php

declare(strict_types=1);

namespace App\Enums;

enum ListingStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case SOLD = 'sold';
    case RENTED = 'rented';
    case EXPIRED = 'expired';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::SOLD => 'Sold',
            self::RENTED => 'Rented',
            self::EXPIRED => 'Expired',
            self::ARCHIVED => 'Archived',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'pen',
            self::ACTIVE => 'badgecheck',
            self::PENDING => 'clock',
            self::SOLD => 'price',
            self::RENTED => 'home',
            self::EXPIRED => 'calendar',
            self::ARCHIVED => 'archive',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::PENDING => 'yellow',
            self::SOLD => 'blue',
            self::RENTED => 'purple',
            self::EXPIRED => 'red',
            self::ARCHIVED => 'slate',
        };
    }

    /**
     * Get badge color for UI components (hex code)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => '#6B7280',     // Gray
            self::ACTIVE => '#10B981',    // Green
            self::PENDING => '#F59E0B',   // Amber
            self::SOLD => '#3B82F6',      // Blue
            self::RENTED => '#8B5CF6',    // Purple
            self::EXPIRED => '#EF4444',   // Red
            self::ARCHIVED => '#64748B',  // Slate
        };
    }

    /**
     * Get Lucide icon name for UI components
     */
    public function iconName(): string
    {
        return match ($this) {
            self::DRAFT => 'pen',
            self::ACTIVE => 'badgecheck',
            self::PENDING => 'clock',
            self::SOLD => 'price',
            self::RENTED => 'home',
            self::EXPIRED => 'calendar',
            self::ARCHIVED => 'archive',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::SOLD, self::RENTED, self::ARCHIVED]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get badge configuration for badge component
     * Returns array mapping status values to hex color codes and Lucide icon names
     *
     * Format: [
     *   'draft' => [
     *     'value' => 'draft',
     *     'label' => 'Draft',
     *     'color' => '#6B7280',  // Hex color code
     *     'icon' => 'edit',      // Lucide icon name
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
     * Get status configuration for a specific value
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
