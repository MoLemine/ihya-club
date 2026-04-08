<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Services\BloodRequestService;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->input('status', 'pending');

        $requests = BloodRequest::query()
            ->with('user')
            ->where('status', $status)
            ->when($request->filled('hospital'), fn ($query) => $query->where('hospital_name', (string) $request->input('hospital')))
            ->when($request->filled('published_date'), fn ($query) => $query->whereDate('created_at', (string) $request->input('published_date')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.requests.index', [
            'requests' => $requests,
            'status' => $status,
            'filters' => $request->only(['hospital', 'published_date']),
            'statusCounts' => [
                'pending' => BloodRequest::query()->where('status', 'pending')->count(),
                'approved' => BloodRequest::query()->where('status', 'approved')->count(),
                'rejected' => BloodRequest::query()->where('status', 'rejected')->count(),
                'completed' => BloodRequest::query()->where('status', 'completed')->count(),
                'archived' => BloodRequest::query()->where('status', 'archived')->count(),
            ],
            'hospitals' => BloodRequest::query()
                ->select('hospital_name')
                ->distinct()
                ->orderBy('hospital_name')
                ->pluck('hospital_name'),
        ]);
    }

    public function update(Request $request, BloodRequest $bloodRequest, BloodRequestService $bloodRequestService)
    {
        $action = $request->validate([
            'action' => ['required', 'in:approve,reject,archive'],
        ])['action'];

        if ($action === 'approve') {
            $bloodRequestService->approve($bloodRequest);
        } elseif ($action === 'reject') {
            $bloodRequestService->reject($bloodRequest);
        } else {
            $bloodRequest->update(['status' => 'archived', 'archived_at' => now()]);
        }

        return back()->with('status', __('messages.request_updated'));
    }
}
