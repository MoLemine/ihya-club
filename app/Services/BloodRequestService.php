<?php

namespace App\Services;

use App\Enums\BloodRequestStatus;
use App\Enums\DonationStatus;
use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodRequestApprovedNotification;
use App\Notifications\BloodRequestCreatedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BloodRequestService
{
    public function __construct(
        protected PointService $pointService,
    ) {
    }

    public function create(User $user, array $payload, ?UploadedFile $image = null): BloodRequest
    {
        return DB::transaction(function () use ($user, $payload, $image) {
            $this->pointService->ensureFirstInteraction($user);

            $neededOn = match ($payload['needed_on_option'] ?? null) {
                'today' => now()->toDateString(),
                'tomorrow' => now()->addDay()->toDateString(),
                'week' => now()->addWeek()->toDateString(),
                default => null,
            };

            $request = $user->bloodRequests()->create([
                ...collect($payload)->except('needed_on_option')->all(),
                'needed_on' => $neededOn,
                'image_path' => $image?->store('blood-requests', 'public'),
                'expires_at' => now()->addDays(7),
                'status' => BloodRequestStatus::Pending->value,
                'blood_type' => 'O-',
            ]);

            User::query()
                ->whereIn('role', ['admin', 'superadmin'])
                ->each(fn (User $admin) => $admin->notify(new BloodRequestCreatedNotification($request)));

            return $request;
        });
    }

    public function approve(BloodRequest $request): BloodRequest
    {
        $request->update([
            'status' => BloodRequestStatus::Approved,
            'approved_at' => now(),
        ]);

        User::query()
            ->where('is_suspended', false)
            ->each(fn (User $user) => $user->notify(new BloodRequestApprovedNotification($request)));

        return $request->refresh();
    }

    public function reject(BloodRequest $request): BloodRequest
    {
        $request->update([
            'status' => BloodRequestStatus::Rejected,
        ]);

        return $request->refresh();
    }

    public function archiveExpired(): int
    {
        return BloodRequest::query()
            ->whereIn('status', [
                BloodRequestStatus::Approved->value,
                BloodRequestStatus::Pending->value,
                BloodRequestStatus::Rejected->value,
                BloodRequestStatus::Completed->value,
            ])
            ->where('created_at', '<=', now()->subDays(7))
            ->update([
                'status' => BloodRequestStatus::Archived->value,
                'archived_at' => now(),
            ]);
    }

    public function refreshFulfillment(BloodRequest $request): BloodRequest
    {
        $fulfilledUnits = $request->donations()->where('status', DonationStatus::Completed)->count();

        $request->update(['fulfilled_units' => $fulfilledUnits]);

        if ($request->fulfilled_units >= $request->required_units) {
            $request->update([
                'status' => BloodRequestStatus::Completed,
                'completed_at' => now(),
            ]);
        }

        return $request->refresh();
    }
}
