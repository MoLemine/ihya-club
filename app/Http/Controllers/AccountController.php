<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpgradeAccountRequest;
use App\Models\User;
use App\Services\UserIdentityService;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function upgrade(UpgradeAccountRequest $request, UserIdentityService $userIdentityService)
    {
        $user = $request->user() ?? User::query()->findOrFail($request->session()->get('guest_user_id'));

        $userIdentityService->upgrade($user, $request->validated());
        Auth::login($user);
        $request->session()->regenerate();

        return back()->with('status', __('messages.account_upgraded'));
    }
}
