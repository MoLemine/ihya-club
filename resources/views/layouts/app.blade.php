<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Ihya') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--app-bg)] text-slate-900">
    @php
        $viewer = auth()->user() ?? \App\Models\User::find(session('guest_user_id'));
        $notifications = auth()->check() ? auth()->user()->notifications()->latest()->take(6)->get() : collect();
        $unreadNotifications = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    @endphp

    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.95),_rgba(244,247,251,0.92)_35%,_rgba(233,238,245,1)_100%)]">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <nav class="surface-panel sticky top-4 z-30 mb-6 flex flex-col gap-4 rounded-[1.75rem] px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <img src="{{ asset('logo-life.svg') }}" alt="Ihya logo" class="h-12 w-12 rounded-2xl object-cover shadow-lg shadow-rose-200">
                        <div>
                            <p class="text-xs uppercase tracking-[0.28em] text-rose-500">Ihya</p>
                            <h1 class="text-lg font-semibold text-slate-900">{{ __('messages.platform_name') }}</h1>
                        </div>
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home', 'feed.index') ? 'nav-link-active' : '' }}">{{ __('messages.feed') }}</a>
                    <a href="{{ route('requests.create') }}" class="nav-link {{ request()->routeIs('requests.create') ? 'nav-link-active' : '' }}">{{ __('messages.create_request') }}</a>
                    @if ($viewer)
                        <a href="{{ route('profile.me') }}" class="nav-link {{ request()->routeIs('profile.me') ? 'nav-link-active' : '' }}">{{ __('messages.my_profile') }}</a>
                    @endif
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.requests.index') }}" class="nav-link {{ request()->routeIs('admin.requests.*') ? 'nav-link-active' : '' }}">{{ __('messages.review_requests') }}</a>
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">{{ __('messages.admin_panel') }}</a>
                        @endif
                    @endauth
                    <a href="#about" class="nav-link">{{ __('messages.about_us') }}</a>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <details class="notification-menu">
                            <summary class="notification-trigger">
                                {{ __('messages.notifications') }}
                                @if ($unreadNotifications > 0)
                                    <span class="notification-badge">{{ $unreadNotifications }}</span>
                                @endif
                            </summary>
                            <div class="notification-panel">
                                <div class="mb-3 flex items-center justify-between">
                                    <strong class="text-sm text-slate-900">{{ __('messages.notifications') }}</strong>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-slate-500">{{ $unreadNotifications }} {{ __('messages.unread') }}</span>
                                        @if ($unreadNotifications > 0)
                                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                                @csrf
                                                <button class="text-xs font-medium text-rose-600 hover:text-rose-700">{{ __('messages.mark_all_read') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    @forelse ($notifications as $notification)
                                        <a href="{{ route('notifications.open', $notification) }}" class="notification-card {{ is_null($notification->read_at) ? 'notification-card-unread' : '' }}">
                                            <div class="text-sm font-medium text-slate-900">
                                                {{ __($notification->data['title_key'] ?? 'messages.notifications', [
                                                    'hospital' => $notification->data['hospital_name'] ?? '',
                                                    'city' => $notification->data['city'] ?? '',
                                                ]) }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</div>
                                        </a>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-slate-200 px-3 py-4 text-sm text-slate-500">{{ __('messages.no_notifications') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                    @endauth

                    <form method="POST" action="{{ route('locale.update', 'ar') }}">
                        @csrf
                        <button class="lang-chip {{ app()->getLocale() === 'ar' ? 'lang-chip-active' : '' }}">AR</button>
                    </form>
                    <form method="POST" action="{{ route('locale.update', 'fr') }}">
                        @csrf
                        <button class="lang-chip {{ app()->getLocale() === 'fr' ? 'lang-chip-active' : '' }}">FR</button>
                    </form>

                    @if (auth()->check())
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="secondary-pill">{{ __('messages.logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('register') }}" class="primary-pill">{{ __('messages.create_account') }}</a>
                        <a href="{{ route('login') }}" class="secondary-pill">{{ __('messages.login') }}</a>
                    @endif
                </div>
            </nav>

            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')

            <footer id="about" class="surface-panel mt-10 rounded-[1.75rem] px-6 py-8">
                <div class="grid gap-8 lg:grid-cols-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('logo-life.svg') }}" alt="Ihya logo" class="h-14 w-14 rounded-2xl object-cover shadow-lg shadow-rose-200">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ __('messages.about_us') }}</h3>
                                <p class="text-sm text-rose-500">{{ __('messages.logo_caption') }}</p>
                            </div>
                        </div>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">{{ __('messages.about_us_text') }}</p>
                    </div>
                <div>
                    <h4 class="text-lg font-semibold text-slate-900">{{ __('messages.quick_links') }}</h4>
                    <div class="mt-4 flex flex-col gap-2 text-sm">
                        <a href="{{ route('home') }}" class="footer-link">{{ __('messages.published_requests') }}</a>
                        <a href="{{ route('requests.create') }}" class="footer-link">{{ __('messages.create_request') }}</a>
                        <a href="{{ route('home') }}#about" class="footer-link">{{ __('messages.about_us') }}</a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-slate-900">{{ __('messages.blood_donation_info') }}</h4>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <div>{{ __('messages.blood_info_1') }}</div>
                        <div>{{ __('messages.blood_info_2') }}</div>
                        <div>{{ __('messages.blood_info_3') }}</div>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-slate-900">{{ __('messages.contact_us') }}</h4>
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <div>mohamedleminetah333@gmail.com</div>
                            <div>+22249707375</div>
                            <div>{{ __('messages.saving_lives_caption') }}</div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
