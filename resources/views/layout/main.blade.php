<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/sebro-logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('template/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div id="toast-container" aria-live="polite" aria-atomic="true"></div>
    @if (session('success'))
        <div data-flash="success" data-message="{{ session('success') }}"></div>
    @endif
    @if (session('error'))
        <div data-flash="error" data-message="{{ session('error') }}"></div>
    @endif
    @if ($errors->any())
        <div data-flash="error" data-message="{{ $errors->first() }}"></div>
    @endif

    <div id="sidebar-overlay" class="sidebar-overlay"></div>
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-brand">
            <img class="company-logo sidebar-company-logo" src="{{ asset('images/sebro-logo.jpeg') }}" alt="Logo PT. Sebro Precision Engineering" width="42" height="46">
            <span class="brand-name company-brand-name">{{ config('app.name') }}</span>
            <button id="sidebar-close-btn" class="sidebar-close-btn" aria-label="Tutup sidebar"><i class="bi bi-x-lg"></i></button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Monitoring</div>
            <ul class="nav-list">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="bi bi-grid-1x2-fill nav-icon"></i><span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <a href="{{ route('projects.index') }}" class="nav-link">
                        <i class="bi bi-kanban nav-icon"></i><span class="nav-label">Project</span>
                    </a>
                </li>
            </ul>

            @if (auth()->user()->isAdmin())
                <div class="nav-section-label">Administrasi</div>
                <ul class="nav-list">
                    <li class="nav-item {{ request()->routeIs('project-types.*') ? 'active' : '' }}">
                        <a href="{{ route('project-types.index') }}" class="nav-link">
                            <i class="bi bi-diagram-3-fill nav-icon"></i><span class="nav-label">Tipe & Tahapan</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="nav-link">
                            <i class="bi bi-people-fill nav-icon"></i><span class="nav-label">Pengguna</span>
                        </a>
                    </li>
                </ul>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user-mini">
                <div class="avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                    <span class="sidebar-user-role">{{ auth()->user()->roleLabel() }}</span>
                </div>
            </div>
        </div>
    </aside>

    <div id="main-wrapper" class="main-wrapper">
        <header class="navbar">
            <div class="navbar-left">
                <button id="sidebar-toggle-btn" class="icon-btn" aria-label="Buka menu"><i class="bi bi-list"></i></button>
                <div class="breadcrumb-nav">
                    <span class="breadcrumb-parent">Monitoring</span>
                    <i class="bi bi-chevron-right breadcrumb-sep"></i>
                    <span class="breadcrumb-current">@yield('breadcrumb', 'Dashboard')</span>
                </div>
            </div>
            <div class="navbar-right">
                <div class="notification-wrapper">
                    <button class="icon-btn notif-btn" id="notification-trigger" aria-label="Notifikasi">
                        <i class="bi bi-bell"></i>
                        @if (auth()->user()->unreadNotifications()->exists())
                            <span class="notif-count">{{ min(auth()->user()->unreadNotifications()->count(), 99) }}</span>
                        @endif
                    </button>
                    <div class="notification-dropdown" id="notification-dropdown">
                        <div class="notification-head">
                            <strong>Notifikasi</strong>
                            @if (auth()->user()->unreadNotifications()->exists())
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf @method('PUT')
                                    <button class="link-button" type="submit">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div class="notification-list">
                            @forelse (auth()->user()->notifications()->latest()->limit(7)->get() as $notification)
                                <a class="notification-item {{ $notification->read_at ? '' : 'unread' }}" href="{{ route('notifications.show', $notification) }}">
                                    <span class="notification-icon"><i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }}"></i></span>
                                    <span><strong>{{ $notification->data['title'] ?? 'Notifikasi' }}</strong><small>{{ $notification->data['message'] ?? '' }}</small></span>
                                </a>
                            @empty
                                <div class="empty-compact">Belum ada notifikasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown-wrapper" id="profile-dropdown-wrapper">
                    <button class="profile-trigger" id="profile-trigger" aria-haspopup="true">
                        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="profile-info"><span class="profile-name">{{ auth()->user()->name }}</span><span class="profile-role">{{ auth()->user()->roleLabel() }}</span></div>
                        <i class="bi bi-chevron-down profile-chevron"></i>
                    </button>
                    <div class="profile-dropdown" id="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="avatar avatar-lg">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <div><p class="dropdown-name">{{ auth()->user()->name }}</p><p class="dropdown-email">{{ auth()->user()->email }}</p></div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item dropdown-item-danger dropdown-submit" type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">@yield('content')</main>
    </div>

    <div class="modal-overlay" id="confirm-modal" aria-hidden="true">
        <div class="modal modal-sm" role="dialog" aria-modal="true">
            <div class="modal-header modal-header-danger">
                <h3 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi</h3>
                <button class="modal-close" data-close-modal="confirm-modal" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="modal-body"><p id="confirm-message">Apakah Anda yakin?</p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-close-modal="confirm-modal">Batal</button>
                <form id="confirm-form" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, lanjutkan</button>
                </form>
            </div>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
