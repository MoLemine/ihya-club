@extends('layouts.app')

@section('content')
    <section class="glass-panel rounded-3xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold">{{ __('messages.manage_users') }}</h2>
            <a href="{{ route('admin.dashboard') }}" class="secondary-pill">{{ __('messages.admin_panel') }}</a>
        </div>
        <div class="space-y-4">
            @foreach ($users as $user)
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    @csrf
                    @method('PATCH')
                    <div class="grid gap-4 lg:grid-cols-[1.2fr_0.7fr_0.7fr_0.7fr_auto] lg:items-center">
                        <div>
                            <div class="text-lg font-semibold">{{ $user->name }}</div>
                            <div class="text-sm text-slate-300">{{ $user->phone ?: $user->email ?: 'guest' }} • {{ $user->city }}</div>
                        </div>
                        <select name="role" class="field">
                            @foreach (['user', 'admin', 'superadmin'] as $role)
                                <option value="{{ $role }}" @selected($user->role->value === $role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" name="is_suspended" value="1" @checked($user->is_suspended)>
                            <span>{{ __('messages.suspended') }}</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="checkbox" name="posting_restricted" value="1" @checked($user->posting_restricted)>
                            <span>{{ __('messages.posting_restricted') }}</span>
                        </label>
                        <button class="primary-pill">{{ __('messages.save') }}</button>
                    </div>
                </form>
            @endforeach
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </section>
@endsection
