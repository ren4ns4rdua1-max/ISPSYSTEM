<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Account') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        body { background: #f8fafc; }
        .sidebar { background: linear-gradient(180deg, #0a0c18 0%, #0f111e 100%); width: 240px; min-height: 100vh; position: fixed; top: 0; left: 0; bottom: 0; z-index: 40; display: flex; flex-direction: column; }
        .main { margin-left: 240px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 12px; color: rgba(255,255,255,.55); font-size: 13px; font-weight: 500; text-decoration: none; transition: all .2s; margin: 2px 0; }
        .nav-item:hover { color: white; background: rgba(255,255,255,.08); transform: translateX(3px); }
        .nav-item.active { color: white; background: rgba(220,38,38,.25); border-left: 2px solid #ef4444; }
        .nav-icon { width: 30px; height: 30px; border-radius: 9px; background: rgba(255,255,255,.06); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .status-active { background:#ecfdf5;color:#059669;border:1px solid #d1fae5; }
        .status-suspended { background:#fef3c7;color:#d97706;border:1px solid #fde68a; }
        .status-cancelled,.status-inactive { background:#fef2f2;color:#dc2626;border:1px solid #fecaca; }
        .status-pending_approval,.status-pending_installation { background:#f3e8ff;color:#7c3aed;border:1px solid #e9d5ff; }
        @yield('styles')
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="padding:20px 16px;border-bottom:1px solid rgba(255,255,255,.07);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#dc2626,#b91c1c);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:18px;height:18px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
            </div>
            <div>
                <p class="font-display" style="color:white;font-size:13px;font-weight:700;line-height:1.2;">{{ config('app.name') }}</p>
                <p style="color:rgba(255,255,255,.35);font-size:10px;font-weight:600;letter-spacing:.08em;">CLIENT PORTAL</p>
            </div>
        </div>
    </div>

    <div style="padding:12px 10px;border-bottom:1px solid rgba(255,255,255,.07);">
        <div style="display:flex;align-items:center;gap:10px;padding:10px 8px;background:rgba(255,255,255,.05);border-radius:12px;">
            @php $photo = Auth::user()->photo; @endphp
            @if($photo)
                <img src="{{ asset('storage/'.$photo) }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
            @else
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#dc2626,#f97316);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            @endif
            <div style="overflow:hidden;">
                <p style="color:white;font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name }}</p>
                <span class="status-{{ $client->status ?? 'inactive' }}" style="font-size:9px;font-weight:700;padding:2px 8px;border-radius:20px;display:inline-block;margin-top:2px;">{{ ucfirst($client->status ?? 'N/A') }}</span>
            </div>
        </div>
    </div>

    <nav style="flex:1;padding:10px 10px;overflow-y:auto;">
        <p style="font-size:9px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.2);padding:8px 6px 4px;">MENU</p>
        <a href="{{ route('portal.dashboard') }}" class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div>
            Dashboard
        </a>
        <a href="{{ route('portal.billing') }}" class="nav-item {{ request()->routeIs('portal.billing') ? 'active' : '' }}">
            <div class="nav-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            Billing & Invoices
        </a>
        <a href="{{ route('portal.payments') }}" class="nav-item {{ request()->routeIs('portal.payments') ? 'active' : '' }}">
            <div class="nav-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
            Payments
        </a>
        <a href="{{ route('portal.tickets') }}" class="nav-item {{ request()->routeIs('portal.tickets') ? 'active' : '' }}">
            <div class="nav-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
            Support Tickets
        </a>
        <a href="{{ route('portal.profile') }}" class="nav-item {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
            <div class="nav-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            Profile Settings
        </a>
    </nav>

    <div style="padding:12px 10px;border-top:1px solid rgba(255,255,255,.07);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full" style="background:none;border:none;cursor:pointer;width:100%;">
                <div class="nav-icon"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></div>
                Sign Out
            </button>
        </form>
    </div>
</aside>

<div class="main">
    <header style="background:rgba(255,255,255,.95);backdrop-filter:blur(20px);border-bottom:1px solid rgba(0,0,0,.05);padding:0 28px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:30;">
        <div>
            <h1 class="font-display" style="font-size:18px;font-weight:700;color:#0f172a;">@yield('page-title', 'Dashboard')</h1>
            <p style="font-size:11px;color:#94a3b8;margin-top:1px;">@yield('page-subtitle', config('app.name') . ' Client Portal')</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:11px;color:#64748b;font-weight:600;">Account #{{ str_pad($client->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
    </header>

    <main style="padding:24px 28px;">
        @if(session('success'))
            <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;margin-bottom:20px;">
                <svg style="width:18px;height:18px;color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <p style="color:#065f46;font-size:13px;font-weight:600;">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#fef2f2;border:1px solid #fecaca;border-radius:14px;margin-bottom:20px;">
                <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <p style="color:#991b1b;font-size:13px;font-weight:600;">{{ session('error') }}</p>
            </div>
        @endif
        @yield('content')
    </main>
</div>

@yield('scripts')
</body>
</html>
