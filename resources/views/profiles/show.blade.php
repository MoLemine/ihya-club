@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="glass-panel rounded-[2rem] p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-gradient-to-br from-rose-500/90 to-orange-400/90 text-2xl font-bold">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($profileUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-3xl font-semibold">{{ $profileUser->name }}</h2>
                            <span class="badge-pill">{{ $profileUser->profile_locked ? __('messages.locked') : __('messages.public') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-300">{{ $profileUser->city ?: __('messages.city_not_set') }}</p>
                        @if ($profileUser->age_range)
                            <p class="mt-2 text-sm text-slate-300">{{ __('messages.age_range') }}: {{ __('messages.age_'.$profileUser->age_range) }}</p>
                        @endif
                        @if ($profileUser->bio)
                            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-200">{{ $profileUser->bio }}</p>
                        @endif
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($profileUser->badges as $badge)
                                <span class="badge-pill">{{ $badge->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="stat-card"><span>{{ __('messages.points') }}</span><strong>{{ $profileUser->points_balance }}</strong></div>
                    <div class="stat-card"><span>{{ __('messages.total_posts') }}</span><strong>{{ $profileUser->bloodRequests->count() }}</strong></div>
                    <div class="stat-card"><span>{{ __('messages.total_donations') }}</span><strong>{{ $profileUser->donations->count() }}</strong></div>
                </div>
            </div>
        </section>

        @if ($isOwner)
            <section class="glass-panel rounded-[2rem] p-6">
                <h3 class="text-2xl font-semibold">{{ __('messages.manage_profile') }}</h3>
                <form method="POST" action="{{ route('profile.update') }}" class="mt-5 grid gap-3 md:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <input name="name" value="{{ old('name', $profileUser->name) }}" class="field" placeholder="{{ __('messages.name') }}">
                    <input name="phone" value="{{ old('phone', $profileUser->phone) }}" class="field" placeholder="{{ __('messages.phone') }}">
                    <input name="city" value="{{ old('city', $profileUser->city) }}" class="field" placeholder="{{ __('messages.city') }}">
                    <select name="age_range" class="field">
                        <option value="">{{ __('messages.age_range') }}</option>
                        <option value="18-25" @selected(old('age_range', $profileUser->age_range) === '18-25')>{{ __('messages.age_18-25') }}</option>
                        <option value="26-55" @selected(old('age_range', $profileUser->age_range) === '26-55')>{{ __('messages.age_26-55') }}</option>
                        <option value="56-69" @selected(old('age_range', $profileUser->age_range) === '56-69')>{{ __('messages.age_56-69') }}</option>
                        <option value="70+" @selected(old('age_range', $profileUser->age_range) === '70+')>{{ __('messages.age_70+') }}</option>
                    </select>
                    <select name="preferred_locale" class="field">
                        <option value="ar" @selected($profileUser->preferred_locale === 'ar')>Arabic</option>
                        <option value="fr" @selected($profileUser->preferred_locale === 'fr')>French</option>
                    </select>
                    <input type="date" name="last_donation_date" value="{{ old('last_donation_date', optional($profileUser->last_donation_date)->format('Y-m-d')) }}" class="field">
                    <label class="field flex items-center gap-3">
                        <input type="checkbox" name="profile_locked" value="1" @checked($profileUser->profile_locked)>
                        <span>{{ __('messages.lock_profile') }}</span>
                    </label>
                    <textarea name="bio" rows="4" class="field md:col-span-2" placeholder="{{ __('messages.bio') }}">{{ old('bio', $profileUser->bio) }}</textarea>
                    <button class="primary-pill md:col-span-2">{{ __('messages.save_profile') }}</button>
                </form>
            </section>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="glass-panel rounded-[2rem] p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-2xl font-semibold">{{ __('messages.published_requests') }}</h3>
                    <span class="text-sm text-slate-400">{{ $profileUser->bloodRequests->count() }} {{ __('messages.posts') }}</span>
                </div>
                <div class="space-y-4">
                    @forelse ($profileUser->bloodRequests->sortByDesc('created_at') as $request)
                        <article class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge-pill badge-{{ $request->urgency_level->value }}">{{ __('messages.'.$request->urgency_level->value) }}</span>
                                <span class="badge-pill">{{ __('messages.'.$request->status->value) }}</span>
                            </div>
                            <h4 class="mt-3 text-xl font-semibold">{{ $request->hospital_name }}</h4>
                            <p class="mt-2 text-sm text-slate-300">{{ $request->city }} | {{ $request->required_units }} {{ __('messages.units') }}</p>
                            <p class="mt-3 text-sm leading-7 text-slate-200">{{ $request->description }}</p>
                        </article>
                    @empty
                        <p class="text-sm text-slate-300">{{ __('messages.no_posts_yet') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="space-y-6">
                <div class="glass-panel rounded-[2rem] p-6">
                    <h3 class="text-2xl font-semibold">{{ __('messages.donation_history') }}</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($profileUser->donations->sortByDesc('created_at')->take(10) as $donation)
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="font-medium">{{ $donation->bloodRequest?->hospital_name ?? __('messages.deleted_request') }}</div>
                                <div class="mt-1 text-sm text-slate-300">{{ __('messages.'.$donation->status->value) }}</div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-300">{{ __('messages.no_donations_yet') }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="glass-panel rounded-[2rem] p-6">
                    <h3 class="text-2xl font-semibold">{{ __('messages.points_history') }}</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($profileUser->points->sortByDesc('created_at')->take(10) as $point)
                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 p-4">
                                <span class="text-sm text-slate-200">{{ $point->action }}</span>
                                <strong>+{{ $point->value }}</strong>
                            </div>
                        @empty
                            <p class="text-sm text-slate-300">{{ __('messages.no_points_yet') }}</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
