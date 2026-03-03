<?php

declare(strict_types=1);

namespace App\Enums;

enum FollowUpType: string
{
    case Call = 'call';
    case Meeting = 'meeting';
    case Viewing = 'viewing';
    case Offer = 'offer';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Call => 'Call',
            self::Meeting => 'Meeting',
            self::Viewing => 'Viewing',
            self::Offer => 'Offer',
            self::Other => 'Other',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Call => '#3B82F6',    // Blue
            self::Meeting => '#8B5CF6', // Purple
            self::Viewing => '#10B981', // Green
            self::Offer => '#F59E0B',   // Amber
            self::Other => '#6B7280',   // Gray
        };
    }

    public function iconName(): string
    {
        return match ($this) {
            self::Call => 'phone',
            self::Meeting => 'users',
            self::Viewing => 'eye',
            self::Offer => 'document',
            self::Other => 'dotshorizontal',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get badge configuration for badge components.
     *
     * @return array<string, array{value: string, label: string, color: string, icon: string}>
     */
    public static function badgeConfig(): array
    {
        $config = [];
        foreach (self::cases() as $case) {
            $config[$case->value] = [
                'value' => $case->value,
                'label' => $case->label(),
                'color' => $case->badgeColor(),
                'icon' => $case->iconName(),
            ];
        }

        return $config;
    }

    /**
     * Get masterdata options array for dropdowns.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function getMasterdata(): array
    {
        return collect(self::cases())
            ->map(fn ($case) => ['value' => $case->value, 'label' => $case->label()])
            ->values()
            ->all();
    }
}
