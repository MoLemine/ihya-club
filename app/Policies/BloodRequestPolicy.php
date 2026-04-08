<?php

namespace App\Policies;

use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use App\Models\User;

class BloodRequestPolicy
{
    public function update(User $user, BloodRequest $bloodRequest): bool
    {
        return $user->isAdmin() || $bloodRequest->user_id === $user->id;
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }

    public function donate(User $user, BloodRequest $bloodRequest): bool
    {
        return ! $user->is_suspended
            && $bloodRequest->status === BloodRequestStatus::Approved
            && $bloodRequest->fulfilled_units < $bloodRequest->required_units;
    }

    public function manageUsers(User $user): bool
    {
        return $user->isSuperadmin();
    }
}
