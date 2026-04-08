<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonationActionRequest;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Services\DonationService;
use App\Services\UserIdentityService;

class DonationApiController extends Controller
{
    public function store(
        DonationActionRequest $request,
        BloodRequest $bloodRequest,
        UserIdentityService $userIdentityService,
        DonationService $donationService
    ) {
        $user = $request->user() ?: $userIdentityService->resolve($request, array_filter($request->validated()));
        $donation = $donationService->addToCart($bloodRequest, $user);

        return response()->json($donation, 201);
    }

    public function complete(Donation $donation, DonationService $donationService)
    {
        return response()->json($donationService->complete($donation));
    }

    public function cancel(DonationActionRequest $request, Donation $donation, DonationService $donationService)
    {
        return response()->json(
            $donationService->cancel($donation, (string) $request->input('reason', 'No reason'))
        );
    }

    public function share(
        DonationActionRequest $request,
        BloodRequest $bloodRequest,
        UserIdentityService $userIdentityService,
        DonationService $donationService
    ) {
        $user = $request->user() ?: $userIdentityService->resolve($request, array_filter($request->validated()));
        $donationService->share($bloodRequest, $user);

        return response()->json(['status' => 'shared']);
    }
}
