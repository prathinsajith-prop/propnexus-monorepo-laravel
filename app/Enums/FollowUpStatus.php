<?php

declare(strict_types=1);

namespace App\Enums;

enum FollowUpStatus: string
{
    case Pending = 'Pending';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
    case Overdue = 'Overdue';
    case InProgress = 'In Progress';
    case Rescheduled = 'Rescheduled';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => '#F59E0B', // Amber
            self::Completed => '#10B981', // Green
            self::Cancelled => '#6B7280', // Gray
            self::Overdue => '#EF4444', // Red
            self::InProgress => '#3B82F6', // Blue
            self::Rescheduled => '#8B5CF6', // Purple
        };
    }

    public function iconName(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Completed => 'checkfull',
            self::Cancelled => 'crossfull',
            self::Overdue => 'exclamationsquare',
            self::InProgress => 'loader',
            self::Rescheduled => 'calendar',
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
