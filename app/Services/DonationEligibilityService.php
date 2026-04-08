<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;

class DonationEligibilityService
{
    public function isEligible(?User $user, ?CarbonInterface $at = null): bool
    {
        if (! $user || ! $user->last_donation_date) {
            return true;
        }

        $at ??= now();

        return $user->last_donation_date->lte($at->copy()->subMonthsNoOverflow(3));
    }
}
