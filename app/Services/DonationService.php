<?php

namespace App\Services;

use App\Enums\DonationStatus;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DonationService
{
    public function __construct(
        protected DonationEligibilityService $eligibilityService,
        protected BloodRequestService $bloodRequestService,
        protected PointService $pointService,
        protected BadgeService $badgeService,
    ) {
    }

    public function addToCart(BloodRequest $bloodRequest, User $user): Donation
    {
        if (! $this->eligibilityService->isEligible($user)) {
            throw ValidationException::withMessages([
                'donation' => __('messages.donation_locked'),
            ]);
        }

        $this->pointService->ensureFirstInteraction($user);

        return Donation::query()->updateOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'user_id' => $user->id,
            ],
            [
                'status' => DonationStatus::Cart->value,
                'cancellation_reason' => null,
            ]
        );
    }

    public function complete(Donation $donation): Donation
    {
        $donation->update([
            'status' => DonationStatus::Completed,
            'completed_at' => now(),
        ]);

        $donation->user->update([
            'last_donation_date' => now()->toDateString(),
        ]);

        $this->pointService->award($donation->user, 'donation', 10, ['request_id' => $donation->blood_request_id]);
        $this->badgeService->sync($donation->user);
        $this->bloodRequestService->refreshFulfillment($donation->bloodRequest);

        return $donation->refresh();
    }

    public function cancel(Donation $donation, string $reason): Donation
    {
        $donation->update([
            'status' => DonationStatus::Cancelled,
            'cancellation_reason' => $reason,
        ]);

        $this->bloodRequestService->refreshFulfillment($donation->bloodRequest);

        return $donation->refresh();
    }

    public function share(BloodRequest $bloodRequest, User $user): void
    {
        $bloodRequest->increment('share_count');
        $this->pointService->award($user, 'share', 2, ['request_id' => $bloodRequest->id]);
    }
}
