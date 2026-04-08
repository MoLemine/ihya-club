<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BloodRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $requestsByCity = BloodRequest::query()
            ->selectRaw('city, count(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'total_requests' => BloodRequest::query()->count(),
                'completed_requests' => BloodRequest::query()->where('status', BloodRequestStatus::Completed->value)->count(),
                'active_donors' => User::query()->whereNotNull('last_donation_date')->count(),
                'pending_requests' => BloodRequest::query()->where('status', BloodRequestStatus::Pending->value)->count(),
            ],
            'requestsByCity' => $requestsByCity,
            'recentRequests' => BloodRequest::query()->latest()->limit(8)->get(),
            'recentUsers' => User::query()->latest()->limit(8)->get(),
        ]);
    }
}
