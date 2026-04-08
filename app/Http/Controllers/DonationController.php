<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonationActionRequest;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Services\DonationService;
use App\Services\UserIdentityService;

class DonationController extends Controller
{
    public function store(
        DonationActionRequest $request,
        BloodRequest $bloodRequest,
        UserIdentityService $userIdentityService,
        DonationService $donationService
    ) {
        $user = $request->user() ?: $userIdentityService->resolve($request, array_filter($request->validated()));

        $donationService->addToCart($bloodRequest, $user);

        return back()->with('status', __('messages.added_to_cart'));
    }

    public function complete(Donation $donation, DonationService $donationService)
    {
        $this->authorizeDonation($donation);
        $donationService->complete($donation);

        return back()->with('status', __('messages.donation_completed'));
    }

    public function cancel(DonationActionRequest $request, Donation $donation, DonationService $donationService)
    {
        $this->authorizeDonation($donation);
        $donationService->cancel($donation, (string) $request->input('reason', __('messages.no_reason')));

        return back()->with('status', __('messages.donation_cancelled'));
    }

    public function share(BloodRequest $bloodRequest, DonationActionRequest $request, UserIdentityService $userIdentityService, DonationService $donationService)
    {
        $user = $request->user() ?: $userIdentityService->resolve($request, array_filter($request->validated()));
        $donationService->share($bloodRequest, $user);

        return back()->with('status', __('messages.shared_successfully'));
    }

    protected function authorizeDonation(Donation $donation): void
    {
        $user = request()->user();
        $guestUserId = request()->session()->get('guest_user_id');

        abort_unless(
            $user?->isAdmin() || $donation->user_id === $user?->id || $donation->user_id === $guestUserId,
            403
        );
    }
}
