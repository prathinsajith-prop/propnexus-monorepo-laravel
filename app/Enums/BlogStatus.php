<?php

declare(strict_types=1);

namespace App\Enums;

enum BlogStatus: string
{
    case DRAFT = 'draft';
    case REVIEW = 'review';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    case TRASH = 'trash';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::REVIEW => 'In Review',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
            self::TRASH => 'Trash',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'pensquare',
            self::REVIEW => 'clock',
            self::PUBLISHED => 'badgecheck',
            self::ARCHIVED => 'archive',
            self::TRASH => 'binfull',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::REVIEW => 'yellow',
            self::PUBLISHED => 'green',
            self::ARCHIVED => 'slate',
            self::TRASH => 'red',
        };
    }

    /**
     * Get badge color for UI components (hex code)
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => '#6B7280',      // Gray
            self::REVIEW => '#F59E0B',     // Amber
            self::PUBLISHED => '#10B981',  // Green
            self::ARCHIVED => '#64748B',   // Slate
            self::TRASH => '#EF4444',      // Red
        };
    }

    /**
     * Get Lucide icon name for UI components
     */
    public function iconName(): string
    {
        return match ($this) {
            self::DRAFT => 'pensquare',
            self::REVIEW => 'clock',
            self::PUBLISHED => 'badgecheck',
            self::ARCHIVED => 'archive',
            self::TRASH => 'binfull',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function isArchived(): bool
    {
        return in_array($this, [self::ARCHIVED, self::TRASH]);
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
