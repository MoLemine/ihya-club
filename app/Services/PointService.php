<?php

namespace App\Services;

use App\Models\User;

class PointService
{
    public function award(User $user, string $action, int $value, array $meta = []): void
    {
        $alreadyRecorded = $user->points()
            ->where('action', $action)
            ->when(
                array_key_exists('request_id', $meta),
                fn ($query) => $query->where('meta->request_id', $meta['request_id'])
            )
            ->exists();

        if ($alreadyRecorded && in_array($action, ['first_interaction', 'share'], true)) {
            return;
        }

        $user->points()->create([
            'action' => $action,
            'value' => $value,
            'meta' => $meta,
        ]);

        $user->increment('points_balance', $value);
    }

    public function ensureFirstInteraction(User $user): void
    {
        if (! $user->points()->where('action', 'first_interaction')->exists()) {
            $this->award($user, 'first_interaction', 10);
        }
    }
}
