<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserIdentityService
{
    public function resolve(Request $request, array $attributes): User
    {
        if ($request->user()) {
            $user = $request->user();
            $user->fill(array_filter([
                'name' => $attributes['name'] ?? null,
                'phone' => $attributes['phone'] ?? null,
                'city' => $attributes['city'] ?? null,
            ], fn ($value) => filled($value)))->save();

            return $user;
        }

        $phone = $attributes['phone'] ?? null;
        $sessionId = $request->session()->getId();
        $user = null;

        if ($phone) {
            $user = User::query()->where('phone', $phone)->first();
        }

        if (! $user && $request->session()->has('guest_user_id')) {
            $user = User::query()->find($request->session()->get('guest_user_id'));
        }

        if (! $user) {
            $user = User::create([
                'name' => $attributes['name'] ?? 'Guest donor',
                'phone' => $phone,
                'city' => $attributes['city'] ?? null,
                'guest_session_id' => $sessionId ?: Str::uuid()->toString(),
                'preferred_locale' => $request->session()->get('locale', 'ar'),
                'password' => Hash::make(Str::password(16)),
                'is_guest' => true,
            ]);
        } else {
            $user->fill(array_filter([
                'name' => $attributes['name'] ?? null,
                'city' => $attributes['city'] ?? null,
                'guest_session_id' => $sessionId,
            ], fn ($value) => filled($value)))->save();
        }

        $request->session()->put('guest_user_id', $user->id);

        return $user;
    }

    public function upgrade(User $user, array $attributes): User
    {
        $user->update([
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'is_guest' => false,
        ]);

        return $user->refresh();
    }
}
