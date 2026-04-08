<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->paginate(15),
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()?->isSuperadmin(), 403);

        $payload = $request->validate([
            'role' => ['nullable', 'in:admin,superadmin,user'],
            'is_suspended' => ['nullable', 'boolean'],
            'posting_restricted' => ['nullable', 'boolean'],
        ]);

        if (array_key_exists('role', $payload)) {
            $user->role = UserRole::from($payload['role']);
        }

        $user->is_suspended = $request->boolean('is_suspended');
        $user->posting_restricted = $request->boolean('posting_restricted');
        $user->save();

        return back()->with('status', __('messages.user_updated'));
    }
}
