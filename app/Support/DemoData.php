<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Realistic demo leaderboard, used when demo_mode is on and the
 * database is empty/unreachable. Keeps the UI reviewable before
 * real donations start flowing in.
 */
final class DemoData
{
    public static function snapshot(): array
    {
        $festivalStart = new \DateTimeImmutable('2026-11-17');
        $today = new \DateTimeImmutable('today');
        $daysLeft = max(0, (int) $today->diff($festivalStart)->format('%a') * ($festivalStart >= $today ? 1 : -1));

        $donors = [
            ['Ramesh Sah', 'Kankarbagh, Patna', 51000, 'छठी मईया सबके कल्याण करें 🙏'],
            ['Sunita Devi', 'Danapur, Patna', 38500, 'जय छठी मईया 🌅'],
            ['Manoj Gupta', 'Boring Road, Patna', 27000, ''],
            ['Anil Kumar', 'Rajendra Nagar, Patna', 21000, ''],
            ['Priya Sharma', 'Patliputra, Patna', 15501, ''],
            ['Vikash Singh', 'Bailey Road, Patna', 12100, ''],
            ['Kavita Jha', 'Kankarbagh, Patna', 9100, ''],
            ['Deepak Yadav', 'Phulwari Sharif', 7501, ''],
            ['Seema Kumari', 'Patna City', 5100, ''],
            ['Rahul Verma', 'Khagaul', 3501, ''],
            ['Suresh Prasad', 'Digha', 3100, ''],
            ['Nisha Devi', 'Gardanibagh', 2501, ''],
            ['Amit Raj', 'Patrakar Nagar', 2100, ''],
            ['Ritu Sinha', 'Rajiv Nagar', 1501, ''],
            ['Sanjay Paswan', 'Kadamkuan', 1101, ''],
        ];

        $leaders = [];
        foreach ($donors as $index => [$name, $locality, $amount, $message]) {
            $rank = $index + 1;
            $leaders[] = [
                'rank'      => $rank,
                'donor_id'  => 100 + $rank,
                'name'      => $name,
                'locality'  => $locality,
                'message'   => $message,
                'amount'    => $amount,
                'photo'     => null,
                'badge'     => Badge::forRank($rank),
                'is_anonymous' => false,
            ];
        }

        $total = 245300;
        $target = 500000;

        return [
            'festival' => [
                'name'         => 'Chhath Puja 2026',
                'committee'    => 'Shri Chhath Puja Samiti, Patna',
                'year'         => 2026,
                'target'       => $target,
                'total'        => $total,
                'percent'      => $target > 0 ? min(100, round($total / $target * 100, 1)) : 0,
                'days_left'    => $daysLeft,
                'donors_count' => 187,
                'start_date'   => '2026-11-17',
            ],
            'leaders'    => $leaders,
            'updated_at' => date(DATE_ATOM),
            'demo'       => true,
        ];
    }
}
