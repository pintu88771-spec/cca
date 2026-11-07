<?php

declare(strict_types=1);

namespace App\Support;

final class Badge
{
    /** Default rank cutoffs (editable by the super admin in Settings). */
    public const DEFAULTS = [
        'platinum' => 1,
        'gold'     => 2,
        'silver'   => 3,
        'bronze'   => 10,
    ];

    public const LABELS = [
        'platinum'  => 'Platinum',
        'gold'      => 'Gold',
        'silver'    => 'Silver',
        'bronze'    => 'Bronze',
        'supporter' => 'Supporter',
    ];

    /** @param array<string, int|string> $cutoffs */
    public static function forRank(int $rank, array $cutoffs = []): string
    {
        $cutoffs = array_merge(self::DEFAULTS, array_filter(
            $cutoffs,
            static fn ($value): bool => is_numeric($value)
        ));

        if ($rank <= (int) $cutoffs['platinum']) {
            return 'platinum';
        }
        if ($rank <= (int) $cutoffs['gold']) {
            return 'gold';
        }
        if ($rank <= (int) $cutoffs['silver']) {
            return 'silver';
        }
        if ($rank <= (int) $cutoffs['bronze']) {
            return 'bronze';
        }

        return 'supporter';
    }

    public static function label(string $badge): string
    {
        return self::LABELS[$badge] ?? 'Supporter';
    }
}
