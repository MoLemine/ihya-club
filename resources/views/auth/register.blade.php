@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-xl">
        <section class="glass-panel rounded-[2rem] p-8">
            <p class="text-xs uppercase tracking-[0.3em] text-rose-200/70">{{ __('messages.member_access') }}</p>
            <h2 class="mt-3 text-3xl font-semibold">{{ __('messages.create_account') }}</h2>
            <p class="mt-2 text-sm text-slate-300">{{ __('messages.register_help') }}</p>
            <form method="POST" action="{{ route('register.store') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                <input name="name" value="{{ old('name') }}" class="field" placeholder="{{ __('messages.name') }}">
                <input type="email" name="email" value="{{ old('email') }}" class="field" placeholder="{{ __('messages.email') }}">
                <input name="phone" value="{{ old('phone') }}" class="field" placeholder="{{ __('messages.phone') }}">
                <input name="city" value="{{ old('city') }}" class="field" placeholder="{{ __('messages.city') }}">
                <select name="age_range" class="field">
                    <option value="">{{ __('messages.age_range') }}</option>
                    <option value="18-25" @selected(old('age_range') === '18-25')>{{ __('messages.age_18-25') }}</option>
                    <option value="26-55" @selected(old('age_range') === '26-55')>{{ __('messages.age_26-55') }}</option>
                    <option value="56-69" @selected(old('age_range') === '56-69')>{{ __('messages.age_56-69') }}</option>
                    <option value="70+" @selected(old('age_range') === '70+')>{{ __('messages.age_70+') }}</option>
                </select>
                <div></div>
                <input type="password" name="password" class="field" placeholder="{{ __('messages.password') }}">
                <input type="password" name="password_confirmation" class="field" placeholder="{{ __('messages.password_confirmation') }}">
                <button class="primary-pill md:col-span-2">{{ __('messages.create_account') }}</button>
            </form>
            <p class="mt-5 text-sm text-slate-300">
                {{ __('messages.already_have_account') }}
                <a href="{{ route('login') }}" class="text-rose-200 hover:text-white">{{ __('messages.login') }}</a>
            </p>
        </section>
    </div>
@endsection
