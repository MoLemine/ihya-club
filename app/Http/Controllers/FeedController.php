<?php

namespace App\Http\Controllers;

use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use App\Models\User;
use App\Services\DonationEligibilityService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request, DonationEligibilityService $eligibilityService)
    {
        $publishedStatuses = [BloodRequestStatus::Approved->value, BloodRequestStatus::Completed->value];

        $query = BloodRequest::query()
            ->with(['user.badges', 'comments', 'donations'])
            ->feed();

        if ($request->filled('city')) {
            $query->where('city', (string) $request->input('city'));
        }

        if ($request->filled('urgency')) {
            $query->where('urgency_level', (string) $request->input('urgency'));
        }

        if ($request->filled('status') && in_array($request->input('status'), $publishedStatuses, true)) {
            $query->where('status', (string) $request->input('status'));
        }

        $currentUser = $request->user()
            ?? User::query()->find($request->session()->get('guest_user_id'));

        $requests = $query->paginate(10)->withQueryString();
        $donationCart = $currentUser?->donations()->with('bloodRequest')->latest()->get() ?? collect();

        $cityStats = BloodRequest::query()
            ->whereIn('status', $publishedStatuses)
            ->selectRaw('city, count(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('feed.index', [
            'requests' => $requests,
            'donationCart' => $donationCart,
            'currentUser' => $currentUser,
            'canDonate' => $eligibilityService->isEligible($currentUser),
            'cityStats' => $cityStats,
            'filters' => $request->only(['city', 'urgency', 'status']),
            'stats' => [
                'total_requests' => BloodRequest::query()->whereIn('status', $publishedStatuses)->count(),
                'completed_requests' => BloodRequest::query()->where('status', BloodRequestStatus::Completed->value)->count(),
                'active_donors' => User::query()->whereNotNull('last_donation_date')->count(),
                'pending_requests' => BloodRequest::query()->where('status', BloodRequestStatus::Pending->value)->count(),
            ],
        ]);
    }
}
