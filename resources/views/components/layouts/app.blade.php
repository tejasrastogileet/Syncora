@props([
    'title' => config('app.name', 'Syncora'),
    'heading' => 'Meeting intelligence',
    'subheading' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Syncora</title>
    <meta name="theme-color" content="#f7f9fa">
    <link rel="icon" type="image/svg+xml" href="{{ asset('syncora.svg') }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
<body>
    @auth
        @php
            $workspaceName = auth()->user()->teams()->value('name') ?? 'Personal workspace';
            $topbarNotifications = $notifications ?? auth()->user()->unreadNotifications()->latest()->limit(5)->get();
        @endphp
        <div class="app-shell" id="app-shell">
            <button type="button" class="sidebar-backdrop" data-sidebar-close aria-label="Close navigation"></button>
            <aside class="app-sidebar" id="app-sidebar" aria-label="Main navigation">
                <div class="sidebar-brand">
                    <a href="{{ route('dashboard') }}" class="brand-lockup" aria-label="Syncora overview">
                        <x-brand-mark class="brand-mark" />
                        <span class="brand-copy">
                            <span class="brand-name">syncora</span>
                            <span class="brand-tagline">Turn conversations into clarity.</span>
                        </span>
                    </a>
                    <button type="button" class="icon-button sidebar-collapse-button" data-sidebar-toggle aria-label="Collapse navigation" aria-expanded="true">
                        <x-icon name="menu" />
                    </button>
                </div>

                <button type="button" class="workspace-switcher" disabled title="Workspace switching is not available yet">
                    <span class="workspace-avatar">{{ str($workspaceName)->substr(0, 1)->upper() }}</span>
                    <span class="workspace-copy">
                        <span class="workspace-label">WORKSPACE</span>
                        <span class="workspace-name">{{ $workspaceName }}</span>
                    </span>
                    <span class="workspace-status">Soon</span>
                </button>

                <div class="sidebar-section-label">Workspace</div>
                <nav class="sidebar-nav">
                    <a href="{{ route('dashboard') }}" title="Overview" @class(['sidebar-link', 'is-active' => request()->routeIs('dashboard')])>
                        <x-icon name="home" />
                        <span>Overview</span>
                    </a>
                    <a href="{{ route('meetings.index') }}" title="Meetings" @class(['sidebar-link', 'is-active' => request()->routeIs('meetings.index') || request()->routeIs('meetings.create') || request()->routeIs('meetings.edit')])>
                        <x-icon name="meetings" />
                        <span>Meetings</span>
                    </a>
                    <a href="{{ route('meetings.index') }}" title="AI Notes" @class(['sidebar-link', 'is-active' => request()->routeIs('meetings.show')])>
                        <x-icon name="notes" />
                        <span>AI Notes</span>
                    </a>
                    <span class="sidebar-link is-disabled" title="A dedicated action item workspace is not available yet" aria-disabled="true">
                        <x-icon name="tasks" />
                        <span>Action items</span>
                        <span class="nav-soon">Soon</span>
                    </span>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.analytics') }}" title="Analytics" @class(['sidebar-link', 'is-active' => request()->routeIs('admin.analytics')])>
                            <x-icon name="analytics" />
                            <span>Analytics</span>
                        </a>
                    @endif
                </nav>

                <div class="sidebar-section-label sidebar-section-spaced">Preferences</div>
                <div class="sidebar-nav">
                    <span class="sidebar-link is-disabled" title="Settings are not available yet" aria-disabled="true">
                        <x-icon name="settings" />
                        <span>Settings</span>
                        <span class="nav-soon">Soon</span>
                    </span>
                </div>

                <div class="sidebar-bottom">
                    <div class="sidebar-help">
                        <span class="help-indicator"></span>
                        <span class="help-copy">
                            <span class="help-title">Your workspace</span>
                            <span class="help-subtitle">Private by default</span>
                        </span>
                        <span class="help-secure" aria-label="Secure workspace">●</span>
                    </div>
                    <details class="profile-menu">
                        <summary class="profile-trigger">
                            <span class="profile-avatar">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                            <span class="profile-copy">
                                <span class="profile-name">{{ auth()->user()->name }}</span>
                                <span class="profile-email">{{ auth()->user()->email }}</span>
                            </span>
                            <span class="profile-more" aria-hidden="true">···</span>
                        </summary>
                        <div class="profile-popover">
                            <p class="popover-heading">{{ auth()->user()->name }}</p>
                            <p class="popover-caption">{{ auth()->user()->email }}</p>
                            <div class="popover-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="popover-action" type="submit"><x-icon name="logout" /> Sign out</button>
                            </form>
                        </div>
                    </details>
                </div>
            </aside>

            <div class="app-main">
                <header class="app-topbar">
                    <div class="topbar-leading">
                        <button type="button" class="icon-button mobile-menu-button" data-sidebar-toggle aria-label="Open navigation" aria-expanded="false">
                            <x-icon name="menu" />
                        </button>
                        <div class="topbar-breadcrumb">
                            <span>Workspace</span>
                            <x-icon name="chevron" class="h-4 w-4 rotate-0 text-slate-400" />
                            <span class="breadcrumb-current">{{ $heading }}</span>
                        </div>
                    </div>
                    <div class="topbar-actions">
                        <form action="{{ route('meetings.index') }}" method="GET" role="search" class="topbar-search">
                            <x-icon name="search" class="h-4 w-4 text-slate-400" />
                            <label for="global-meeting-search" class="sr-only">Search meetings</label>
                            <input id="global-meeting-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search meetings..." autocomplete="off">
                            <kbd>Ctrl K</kbd>
                        </form>

                        <details class="notification-menu">
                            <summary class="icon-button notification-trigger" aria-label="Notifications">
                                <x-icon name="bell" />
                                @if ($topbarNotifications->isNotEmpty())
                                    <span class="notification-dot" aria-label="{{ $topbarNotifications->count() }} unread notifications"></span>
                                @endif
                            </summary>
                            <div class="notification-popover">
                                <div class="popover-header">
                                    <div>
                                        <p class="popover-heading">Notifications</p>
                                        <p class="popover-caption">{{ $topbarNotifications->count() }} unread</p>
                                    </div>
                                </div>
                                <div class="popover-divider"></div>
                                @forelse ($topbarNotifications as $notification)
                                    <a class="notification-item" href="{{ $notification->data['url'] ?? route('dashboard') }}">
                                        <span class="notification-icon"><x-icon name="check" class="h-4 w-4" /></span>
                                        <span>
                                            <span class="notification-title">{{ $notification->data['meeting_title'] ?? 'Meeting update' }}</span>
                                            <span class="notification-copy">Status: {{ $notification->data['status'] ?? 'updated' }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <p class="empty-popover">You're all caught up.</p>
                                @endforelse
                            </div>
                        </details>
                    </div>
                </header>

                <main class="page-main">
                    <div class="page-heading">
                        <div>
                            <p class="eyebrow">{{ $workspaceName }}</p>
                            <h1>{{ $heading }}</h1>
                            @if ($subheading)
                                <p class="page-subheading">{{ $subheading }}</p>
                            @endif
                        </div>
                        @unless (request()->routeIs('meetings.create'))
                            <a href="{{ route('meetings.create') }}" class="button button-primary page-heading-cta">
                                <x-icon name="plus" class="h-4 w-4" />
                                <span>New meeting</span>
                            </a>
                        @endunless
                    </div>

                    @if (session('status'))
                        <div class="flash-message flash-success" role="status">
                            <x-icon name="check" class="h-5 w-5" />
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-message flash-error" role="alert">
                            <x-icon name="alert" class="h-5 w-5" />
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                    <footer class="app-footer">
                        <span>© {{ now()->year }} Syncora</span>
                        <span>Turn conversations into clarity.</span>
                    </footer>
                </main>
            </div>
        </div>
    @else
        <main class="guest-shell">
            @unless (request()->routeIs('welcome'))
                <div class="guest-topbar">
                    <a href="{{ route('welcome') }}" class="brand-lockup" aria-label="Syncora home">
                        <x-brand-mark class="brand-mark" />
                        <span class="brand-copy">
                            <span class="brand-name">syncora</span>
                            <span class="brand-tagline">Turn conversations into clarity.</span>
                        </span>
                    </a>
                    <a class="guest-back-link" href="{{ route('welcome') }}">Back to home <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>
            @endunless
            <div class="guest-content">
                @if (session('status'))
                    <div class="flash-message flash-success" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash-message flash-error" role="alert">
                        <x-icon name="alert" class="h-5 w-5" />
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{ $slot }}
            </div>
            <footer class="guest-footer">© {{ now()->year }} Syncora · Turn conversations into clarity.</footer>
        </main>
    @endauth

    @livewireScripts
</body>
</html>
