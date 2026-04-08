@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md">
        <section class="glass-panel rounded-[2rem] p-8">
            <p class="text-xs uppercase tracking-[0.3em] text-rose-200/70">{{ __('messages.member_access') }}</p>
            <h2 class="mt-3 text-3xl font-semibold">{{ __('messages.login') }}</h2>
            <p class="mt-2 text-sm text-slate-300">{{ __('messages.login_help') }}</p>
            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="email" name="email" class="field" placeholder="{{ __('messages.email') }}">
                <input type="password" name="password" class="field" placeholder="{{ __('messages.password') }}">
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="remember">
                    <span>{{ __('messages.remember_me') }}</span>
                </label>
                <button class="primary-pill w-full">{{ __('messages.login') }}</button>
            </form>
            <p class="mt-5 text-sm text-slate-300">
                {{ __('messages.no_account_yet') }}
                <a href="{{ route('register') }}" class="text-rose-200 hover:text-white">{{ __('messages.create_account') }}</a>
            </p>
        </section>
    </div>
@endsection
