<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestIdentityRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\UserIdentityService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function storeGuest(StoreGuestIdentityRequest $request, UserIdentityService $userIdentityService)
    {
        $user = $userIdentityService->resolve($request, $request->validated());

        if ($request->filled('last_donation_date')) {
            $user->update(['last_donation_date' => $request->date('last_donation_date')]);
        }

        return back()->with('status', __('messages.identity_saved'));
    }

    public function me(Request $request)
    {
        $user = $request->user() ?? User::query()->findOrFail($request->session()->get('guest_user_id'));

        return view('profiles.show', [
            'profileUser' => $user->load(['bloodRequests.comments', 'donations.bloodRequest', 'badges', 'points']),
            'isOwner' => true,
        ]);
    }

    public function show(User $user, Request $request)
    {
        $viewerId = $request->user()?->id ?? $request->session()->get('guest_user_id');
        $isOwner = (int) $viewerId === $user->id;

        abort_if($user->profile_locked && ! $isOwner && ! $request->user()?->isAdmin(), 403);

        return view('profiles.show', [
            'profileUser' => $user->load(['bloodRequests.comments', 'donations.bloodRequest', 'badges', 'points']),
            'isOwner' => $isOwner,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user() ?? User::query()->findOrFail($request->session()->get('guest_user_id'));

        $user->update([
            ...$request->safe()->except('profile_locked'),
            'profile_locked' => $request->boolean('profile_locked'),
        ]);

        return back()->with('status', __('messages.profile_updated'));
    }
}
