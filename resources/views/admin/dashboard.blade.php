@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="hero-panel">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="eyebrow">{{ __('messages.admin_panel') }}</p>
                    <h2 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">{{ __('messages.moderation_overview') }}</h2>
                    <p class="mt-4 max-w-3xl text-base leading-8 text-slate-600">{{ __('messages.moderation_overview_help') }}</p>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="primary-pill">{{ __('messages.review_requests') }}</a>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="stat-card"><span>{{ __('messages.pending') }}</span><strong>{{ $stats['pending_requests'] }}</strong></div>
            <div class="stat-card"><span>{{ __('messages.total_requests') }}</span><strong>{{ $stats['total_requests'] }}</strong></div>
            <div class="stat-card"><span>{{ __('messages.completed_requests') }}</span><strong>{{ $stats['completed_requests'] }}</strong></div>
            <div class="stat-card"><span>{{ __('messages.active_donors') }}</span><strong>{{ $stats['active_donors'] }}</strong></div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="surface-panel rounded-[1.75rem] p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-2xl font-semibold text-slate-900">{{ __('messages.requests_by_city') }}</h3>
                    <a href="{{ route('admin.requests.index') }}" class="secondary-pill">{{ __('messages.review_requests') }}</a>
                </div>
                <div class="space-y-4">
                    @foreach ($requestsByCity as $city)
                        <div>
                            <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                <span>{{ $city->city }}</span>
                                <span>{{ $city->total }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-gradient-to-r from-rose-500 to-orange-400" style="width: {{ min(100, ($city->total / max($stats['total_requests'], 1)) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="surface-panel rounded-[1.75rem] p-6">
                <h3 class="text-2xl font-semibold text-slate-900">{{ __('messages.recent_requests') }}</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($recentRequests as $request)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="font-medium text-slate-900">{{ $request->hospital_name }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $request->city }} • {{ __('messages.'.$request->status->value) }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
