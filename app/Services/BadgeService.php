<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

class BadgeService
{
    public function sync(User $user): void
    {
        $completedDonations = $user->donations()->where('status', 'completed')->count();

        $this->awardIfEligible($user, 'life-saver', $completedDonations >= 1);
        $this->awardIfEligible($user, 'active-donor', $completedDonations >= 3);
    }

    protected function awardIfEligible(User $user, string $badgeKey, bool $eligible): void
    {
        if (! $eligible) {
            return;
        }

        $badge = Badge::query()->where('key', $badgeKey)->first();

        if (! $badge || $user->badges()->whereKey($badge->id)->exists()) {
            return;
        }

        $user->badges()->attach($badge->id, ['awarded_at' => now()]);
    }
}
