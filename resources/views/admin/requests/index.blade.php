@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="hero-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="eyebrow">{{ __('messages.review_requests') }}</p>
                    <h2 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">{{ __('messages.moderation_queue') }}</h2>
                    <p class="mt-4 max-w-3xl text-base leading-8 text-slate-600">{{ __('messages.review_requests_help') }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach (['pending', 'approved', 'rejected', 'completed', 'archived'] as $option)
                        <a href="{{ route('admin.requests.index', ['status' => $option]) }}" class="filter-pill {{ $status === $option ? 'filter-pill-active' : '' }}">
                            {{ __('messages.'.$option) }}
                            <span>{{ $statusCounts[$option] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="surface-panel rounded-[1.75rem] p-6">
            <form method="GET" class="grid gap-4 md:grid-cols-4">
                <input type="hidden" name="status" value="{{ $status }}">
                <div>
                    <label class="form-label">{{ __('messages.hospital_name') }}</label>
                    <select name="hospital" class="field">
                        <option value="">{{ __('messages.all_hospitals') }}</option>
                        @foreach ($hospitals as $hospital)
                            <option value="{{ $hospital }}" @selected(($filters['hospital'] ?? '') === $hospital)>{{ $hospital }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('messages.publish_date') }}</label>
                    <input type="date" name="published_date" value="{{ $filters['published_date'] ?? '' }}" class="field">
                </div>
                <div class="md:col-span-2 flex items-end gap-3">
                    <button class="secondary-pill">{{ __('messages.apply_filters') }}</button>
                    <a href="{{ route('admin.requests.index', ['status' => $status]) }}" class="secondary-pill">{{ __('messages.clear_filters') }}</a>
                </div>
            </form>
        </section>

        @if ($requests->isEmpty())
            <section class="empty-state">
                <h3 class="text-2xl font-semibold text-slate-900">{{ __('messages.no_requests_in_queue') }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ __('messages.no_requests_in_queue_help') }}</p>
            </section>
        @endif

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ($requests as $request)
                <article class="post-card overflow-hidden">
                    @if ($request->image_path)
                        <div class="image-frame">
                            <button type="button" class="image-open-button" data-image-modal-open data-image-src="{{ asset('storage/'.$request->image_path) }}" data-image-alt="{{ $request->hospital_name }}">
                                <img src="{{ asset('storage/'.$request->image_path) }}" alt="" class="request-image-fit">
                                <span class="image-zoom-chip">{{ __('messages.zoom_image') }}</span>
                            </button>
                        </div>
                    @else
                        <div class="flex h-72 items-center justify-center rounded-[1.5rem] border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-500">{{ __('messages.no_image_uploaded') }}</div>
                    @endif

                    <div class="mt-5 flex flex-wrap items-center gap-2">
                        <span class="badge-pill badge-{{ $request->urgency_level->value }}">{{ __('messages.'.$request->urgency_level->value) }}</span>
                        <span class="badge-pill">{{ __('messages.'.$request->status->value) }}</span>
                        <span class="badge-pill">O-</span>
                    </div>

                    <h3 class="mt-4 text-2xl font-semibold text-slate-900">{{ $request->hospital_name }}</h3>
                    <div class="mt-4 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                        <div><strong class="text-slate-900">{{ __('messages.name') }}:</strong> {{ $request->user->name }}</div>
                        <div><strong class="text-slate-900">{{ __('messages.phone') }}:</strong> {{ $request->user->phone ?: '--' }}</div>
                        <div><strong class="text-slate-900">{{ __('messages.city') }}:</strong> {{ $request->city }}</div>
                        <div><strong class="text-slate-900">{{ __('messages.required_units') }}:</strong> {{ $request->required_units }}</div>
                        <div><strong class="text-slate-900">{{ __('messages.patient_name') }}:</strong> {{ $request->patient_name ?: __('messages.anonymous_patient') }}</div>
                        <div><strong class="text-slate-900">{{ __('messages.when_needed') }}:</strong> {{ $request->needed_on?->format('Y-m-d') ?: __('messages.not_specified') }}</div>
                    </div>

                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $request->description }}</p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        @if ($status === 'pending')
                            <form method="POST" action="{{ route('admin.requests.update', $request) }}">
                                @csrf
                                @method('PATCH')
                                <button name="action" value="approve" class="primary-pill">{{ __('messages.approve') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.requests.update', $request) }}">
                                @csrf
                                @method('PATCH')
                                <button name="action" value="reject" class="secondary-pill">{{ __('messages.reject') }}</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.requests.update', $request) }}">
                            @csrf
                            @method('PATCH')
                            <button name="action" value="archive" class="secondary-pill">{{ __('messages.archive') }}</button>
                        </form>
                        @if ($request->image_path)
                            <button type="button" class="secondary-pill" data-image-modal-open data-image-src="{{ asset('storage/'.$request->image_path) }}" data-image-alt="{{ $request->hospital_name }}">{{ __('messages.view_full_image') }}</button>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div>{{ $requests->links() }}</div>
    </div>

    <div class="image-modal hidden" data-image-modal>
        <button type="button" class="image-modal-close" data-image-modal-close>&times;</button>
        <img src="" alt="" class="image-modal-content" data-image-modal-image>
    </div>
@endsection
