<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodRequestRequest;
use App\Models\BloodRequest;
use App\Services\BloodRequestService;
use App\Services\UserIdentityService;
use Illuminate\Http\Request;

class RequestApiController extends Controller
{
    public function index(Request $request)
    {
        $requests = BloodRequest::query()
            ->with(['user', 'comments', 'donations'])
            ->when($request->filled('city'), fn ($query) => $query->where('city', (string) $request->input('city')))
            ->when($request->filled('urgency'), fn ($query) => $query->where('urgency_level', (string) $request->input('urgency')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->input('status')))
            ->feed()
            ->paginate(12);

        return response()->json($requests);
    }

    public function store(
        StoreBloodRequestRequest $request,
        UserIdentityService $userIdentityService,
        BloodRequestService $bloodRequestService
    ) {
        $user = $request->user() ?: $userIdentityService->resolve($request, $request->validated());

        $bloodRequest = $bloodRequestService->create(
            $user,
            $request->safe()->except(['name', 'phone', 'last_donation_date', 'image']),
            $request->file('image')
        );

        return response()->json($bloodRequest->load('user'), 201);
    }

    public function show(BloodRequest $request)
    {
        return response()->json($request->load(['user.badges', 'comments', 'donations.user']));
    }

    public function updateStatus(Request $httpRequest, BloodRequest $request, BloodRequestService $bloodRequestService)
    {
        $httpRequest->validate([
            'action' => ['required', 'in:approve,reject,archive'],
        ]);

        if ($httpRequest->input('action') === 'approve') {
            $bloodRequestService->approve($request);
        } elseif ($httpRequest->input('action') === 'reject') {
            $bloodRequestService->reject($request);
        } else {
            $request->update(['status' => 'archived', 'archived_at' => now()]);
        }

        return response()->json($request->refresh());
    }
}
