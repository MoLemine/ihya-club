@extends('layouts.app')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_330px]">
        <main class="space-y-6">
            <section class="hero-panel">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="eyebrow">{{ __('messages.feed') }}</p>
                        <h2 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">{{ __('messages.published_requests') }}</h2>
                        <p class="mt-4 text-base leading-8 text-slate-600">{{ __('messages.feed_intro') }}</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <div class="metric-chip">{{ $stats['total_requests'] }} {{ __('messages.published_requests') }}</div>
                            <div class="metric-chip">{{ $stats['pending_requests'] }} {{ __('messages.pending') }}</div>
                            <div class="metric-chip">{{ $stats['active_donors'] }} {{ __('messages.active_donors') }}</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('requests.create') }}" class="primary-pill">{{ __('messages.create_request') }}</a>
                        @if (! $currentUser)
                            <a href="{{ route('register') }}" class="secondary-pill">{{ __('messages.create_account') }}</a>
                        @endif
                    </div>
                </div>
            </section>

            <section class="surface-panel rounded-[1.75rem] p-6">
                <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">{{ __('messages.find_request') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.only_published_requests') }}</p>
                    </div>
                    <form method="GET" class="grid gap-3 md:grid-cols-3">
                        <select name="city" class="field">
                            <option value="">{{ __('messages.select_city') }}</option>
                            @foreach (\App\Http\Requests\StoreBloodRequestRequest::mauritanianCities() as $city)
                                <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                        <select name="urgency" class="field">
                            <option value="">{{ __('messages.all_urgencies') }}</option>
                            <option value="urgent" @selected(($filters['urgency'] ?? '') === 'urgent')>{{ __('messages.urgent') }}</option>
                            <option value="normal" @selected(($filters['urgency'] ?? '') === 'normal')>{{ __('messages.normal') }}</option>
                        </select>
                        <button class="secondary-pill">{{ __('messages.apply_filters') }}</button>
                    </form>
                </div>
            </section>

            <section class="space-y-5">
                @forelse ($requests as $bloodRequest)
                    <article class="post-card">
                        <div class="flex items-start gap-4">
                            <div class="avatar-circle">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($bloodRequest->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('profiles.show', $bloodRequest->user) }}" class="font-semibold text-slate-900 hover:text-rose-600">{{ $bloodRequest->user->name }}</a>
                                            <span class="badge-pill badge-{{ $bloodRequest->urgency_level->value }}">{{ __('messages.'.$bloodRequest->urgency_level->value) }}</span>
                                            <span class="badge-pill">{{ __('messages.'.$bloodRequest->status->value) }}</span>
                                            <span class="badge-pill">O-</span>
                                        </div>
                                        <div class="mt-1 text-sm text-slate-500">{{ $bloodRequest->created_at->format('Y-m-d H:i') }}</div>
                                    </div>
                                    <div class="request-progress">
                                        <span class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ __('messages.required_units') }}</span>
                                        <strong class="mt-2 block text-3xl text-slate-900">{{ $bloodRequest->fulfilled_units }}/{{ $bloodRequest->required_units }}</strong>
                                        <div class="mt-4 h-3 rounded-full bg-slate-100">
                                            <div class="h-3 rounded-full bg-gradient-to-r from-rose-500 to-orange-400" style="width: {{ min(100, ($bloodRequest->fulfilled_units / max($bloodRequest->required_units, 1)) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h3 class="text-2xl font-semibold text-slate-900">{{ $bloodRequest->hospital_name }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $bloodRequest->patient_name ?: __('messages.anonymous_patient') }} • {{ $bloodRequest->city }}</p>
                                    @if ($bloodRequest->needed_on)
                                        <div class="mt-2 inline-flex rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">{{ __('messages.needed_on') }} {{ $bloodRequest->needed_on->format('Y-m-d') }}</div>
                                    @endif
                                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $bloodRequest->description }}</p>
                                </div>

                                @if ($bloodRequest->image_path)
                                    <div class="image-frame mt-5">
                                        <button type="button" class="image-open-button" data-image-modal-open data-image-src="{{ asset('storage/'.$bloodRequest->image_path) }}" data-image-alt="{{ $bloodRequest->hospital_name }}">
                                            <img src="{{ asset('storage/'.$bloodRequest->image_path) }}" alt="" class="request-image-fit">
                                            <span class="image-zoom-chip">{{ __('messages.zoom_image') }}</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="mt-5 flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('donations.store', $bloodRequest) }}">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $currentUser?->name }}">
                                        <input type="hidden" name="phone" value="{{ $currentUser?->phone }}">
                                        <input type="hidden" name="city" value="{{ $currentUser?->city }}">
                                        <button class="primary-pill" @disabled(! $canDonate)>{{ __('messages.i_want_to_donate') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('donations.share', $bloodRequest) }}">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $currentUser?->name }}">
                                        <input type="hidden" name="phone" value="{{ $currentUser?->phone }}">
                                        <input type="hidden" name="city" value="{{ $currentUser?->city }}">
                                        <button class="secondary-pill">{{ __('messages.share') }}</button>
                                    </form>
                                    <a href="{{ route('profiles.show', $bloodRequest->user) }}" class="secondary-pill">{{ __('messages.view_profile') }}</a>
                                    @if (! $canDonate)
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-2 text-xs text-amber-700">{{ __('messages.donation_locked') }}</span>
                                    @endif
                                </div>

                                <div class="mt-6 border-t border-slate-200 pt-5">
                                    <div class="space-y-4">
                                        @foreach ($bloodRequest->comments->take(4) as $comment)
                                            <div class="comment-item">
                                                <div class="avatar-small">
                                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($comment->name, 0, 1)) }}
                                                </div>
                                                <div class="comment-bubble">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="font-semibold text-slate-900">{{ $comment->name }}</span>
                                                        <span class="text-xs text-slate-400">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                    <p class="mt-1 text-sm text-slate-600">{{ $comment->content }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <form method="POST" action="{{ route('comments.store', $bloodRequest) }}" class="mt-4 flex items-start gap-3">
                                        @csrf
                                        <div class="avatar-small">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($currentUser?->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <textarea name="content" rows="2" class="field" placeholder="{{ __('messages.write_comment') }}"></textarea>
                                            <button class="secondary-pill mt-3">{{ __('messages.comment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <section class="empty-state">
                        <h3 class="text-2xl font-semibold text-slate-900">{{ __('messages.no_published_requests') }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ __('messages.only_published_requests') }}</p>
                    </section>
                @endforelse

                <div>{{ $requests->links() }}</div>
            </section>
        </main>

        <aside class="space-y-6">
            <section class="surface-panel rounded-[1.75rem] p-6">
                <h3 class="text-xl font-semibold text-slate-900">{{ __('messages.quick_actions') }}</h3>
                <div class="mt-4 flex flex-col gap-3">
                    <a href="{{ route('requests.create') }}" class="primary-pill w-full">{{ __('messages.create_request') }}</a>
                    @if (! $currentUser)
                        <a href="{{ route('register') }}" class="secondary-pill w-full">{{ __('messages.create_account') }}</a>
                    @endif
                </div>
            </section>

            <section class="surface-panel rounded-[1.75rem] p-6">
                <h3 class="text-xl font-semibold text-slate-900">{{ __('messages.donation_cart') }}</h3>
                <div class="mt-4 space-y-4">
                    @forelse ($donationCart as $donation)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-sm font-semibold text-slate-900">{{ $donation->bloodRequest->hospital_name }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ __('messages.'.$donation->status->value) }}</div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if ($donation->status->value !== 'completed')
                                    <form method="POST" action="{{ route('donations.complete', $donation) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="primary-pill">{{ __('messages.mark_completed') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('donations.cancel', $donation) }}" class="flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="reason" class="field min-w-0" placeholder="{{ __('messages.cancel_reason') }}">
                                        <button class="secondary-pill">{{ __('messages.cancel') }}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">{{ __('messages.empty_cart') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="surface-panel rounded-[1.75rem] p-6">
                <h3 class="text-xl font-semibold text-slate-900">{{ __('messages.requests_by_city') }}</h3>
                <div class="mt-4 space-y-4">
                    @foreach ($cityStats as $city)
                        <div>
                            <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                <span>{{ $city->city }}</span>
                                <span>{{ $city->total }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-gradient-to-r from-sky-500 to-emerald-400" style="width: {{ min(100, ($city->total / max($stats['total_requests'], 1)) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>

    <div class="image-modal hidden" data-image-modal>
        <button type="button" class="image-modal-close" data-image-modal-close>&times;</button>
        <img src="" alt="" class="image-modal-content" data-image-modal-image>
    </div>
@endsection
