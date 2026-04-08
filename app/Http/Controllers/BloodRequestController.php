<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBloodRequestRequest;
use App\Services\BloodRequestService;
use App\Services\UserIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function create(Request $request)
    {
        return view('requests.create', [
            'currentUser' => $request->user() ?? \App\Models\User::query()->find($request->session()->get('guest_user_id')),
            'cities' => StoreBloodRequestRequest::mauritanianCities(),
            'hospitals' => StoreBloodRequestRequest::mainHospitals(),
        ]);
    }

    public function store(
        StoreBloodRequestRequest $request,
        UserIdentityService $userIdentityService,
        BloodRequestService $bloodRequestService
    ): RedirectResponse {
        $user = $userIdentityService->resolve($request, $request->validated());

        abort_if($user->is_suspended || $user->posting_restricted, 403);

        $bloodRequestService->create(
            $user,
            [
                ...$request->safe()->except(['name', 'phone', 'last_donation_date', 'image', 'hospital_name_select', 'hospital_name_other']),
                'hospital_name' => $request->filled('hospital_name')
                    ? $request->input('hospital_name')
                    : ($request->input('hospital_name_select') === 'Autre'
                        ? $request->input('hospital_name_other')
                        : $request->input('hospital_name_select')),
            ],
            $request->file('image')
        );

        return redirect()->route('home')->with('status', __('messages.request_submitted'));
    }
}
