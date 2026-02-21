<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP Dashboard — ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        #sidebar { transition: width 0.3s cubic-bezier(0.4,0,0.2,1); }
        #main-content { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }

        .collapsible { transition: opacity 0.2s ease, max-width 0.3s ease; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }

        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #dc2626, #f87171);
        }

        .metric-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .metric-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(99,102,241,.12); }

        .qa-icon { transition: transform 0.2s ease; }
        .qa-row:hover .qa-icon { transform: scale(1.12) rotate(-6deg); }

        .user-row { transition: background 0.15s ease; }
        .user-row:hover { background: linear-gradient(90deg, rgba(220,38,38,.05), transparent); }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }

        @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
        .animate-ping { animation: ping 1.5s cubic-bezier(0,0,.2,1) infinite; }

        .progress-fill { transition: width 1.2s cubic-bezier(0.4,0,0.2,1); }

        .topbar {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(226,232,240,0.8);
        }

        #sidebar {
            background: linear-gradient(180deg, #0c0e1a 0%, #111827 60%, #0c0e1a 100%);
        }

        .hero-grid {
            background-image: linear-gradient(rgba(255,255,255,.07) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.07) 1px, transparent 1px);
            background-size: 36px 36px;
        }

        .nav-tooltip {
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1f2937; color: #f9fafb; font-size: 12px; font-weight: 600;
            padding: 4px 10px; border-radius: 8px; white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity 0.15s ease; z-index: 999;
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 5px solid transparent; border-right-color: #1f2937;
        }

        .avatar-grad { background: linear-gradient(135deg, #dc2626 0%, #f87171 50%, #ec4899 100%); }

        html { overflow-x: hidden; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex">

<!-- ===================== SIDEBAR ===================== -->
<aside id="sidebar" style="width:260px;" class="fixed left-0 top-0 h-full z-50 flex flex-col shadow-2xl">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-4 py-[18px] border-b border-white/[.06] min-h-[68px]">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-red-900/40"
             style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
            <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
        </div>
        <div class="collapsible" style="max-width:200px;">
            <p class="font-display font-bold text-white text-[14px] leading-tight tracking-tight">ADMIN</p>
            <p class="text-red-400 text-[10px] font-medium tracking-wide">ISP Control Center</p>
        </div>
        <button onclick="toggleSidebar()" id="toggle-btn"
                class="ml-auto flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-white/10"
                style="background:rgba(255,255,255,.05);">
            <svg id="toggle-icon" class="w-[14px] h-[14px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        <p class="collapsible px-2 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-[.14em] text-gray-600 font-display" style="max-width:200px;">Main</p>

        <!-- Dashboard (active) -->
        <div class="nav-wrapper relative">
            <a href="{{ route('dashboard') }}" class="nav-item-inner relative flex items-center gap-3 px-3 py-2.5 rounded-xl"
               style="background:linear-gradient(135deg,rgba(220,38,38,.18),rgba(185,28,28,.12));border:1px solid rgba(220,38,38,.28);">
                <div class="nav-active-bar"></div>
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-semibold text-red-200" style="max-width:160px;">Dashboard</span>
            </a>
            <span class="nav-tooltip">Dashboard</span>
        </div>

        <!-- Clients -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Clients</span>
            </a>
            <span class="nav-tooltip">Clients</span>
        </div>

        <!-- Users -->
        <div class="nav-wrapper relative">
            <a href="{{ route('users.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:120px;">Users</span>
                <span class="collapsible ml-auto text-[10px] font-bold px-2 py-0.5 rounded-full"
                      style="max-width:50px;background:rgba(220,38,38,.2);color:#fca5a5;">{{ \App\Models\User::count() }}</span>
            </a>
            <span class="nav-tooltip">Users</span>
        </div>

        <p class="collapsible px-2 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-[.14em] text-gray-600 font-display" style="max-width:200px;">Management</p>

        <!-- Subscription Rates -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Subscription Rates</span>
            </a>
            <span class="nav-tooltip">Subscription Rates</span>
        </div>

        <!-- Sales -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:120px;">Sales</span>
                <span class="collapsible ml-auto text-[10px] font-bold px-2 py-0.5 rounded-full"
                      style="max-width:50px;background:rgba(16,185,129,.15);color:#6ee7b7;">New</span>
            </a>
            <span class="nav-tooltip">Sales</span>
        </div>

        <!-- Reports -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Reports</span>
            </a>
            <span class="nav-tooltip">Reports</span>
        </div>

        <p class="collapsible px-2 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-[.14em] text-gray-600 font-display" style="max-width:200px;">System</p>

        <!-- Settings -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Settings</span>
            </a>
            <span class="nav-tooltip">Settings</span>
        </div>

    </nav>

    <!-- User Footer -->
    <div class="border-t border-white/[.06] p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/[.06] transition-colors">
            <div class="w-9 h-9 rounded-xl flex-shrink-0 avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="collapsible min-w-0" style="max-width:140px;">
                <p class="text-white text-[12px] font-semibold truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-gray-500 text-[10px] truncate">{{ Auth::user()->email }}</p>
            </div>
            <div class="collapsible ml-auto flex-shrink-0" style="max-width:40px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors hover:bg-red-500/20"
                            style="background:rgba(255,255,255,.05);">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- ===================== MAIN CONTENT ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen" style="margin-left:260px;">

    <!-- TOP BAR -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Dashboard Overview</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ now()->format('l, F j, Y') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <input type="text" placeholder="Search anything..."
                       class="w-56 text-sm bg-gray-100 rounded-xl pl-9 pr-4 py-2 text-gray-700 placeholder-gray-400 border-0 focus:outline-none focus:ring-2 focus:ring-red-300 focus:bg-white transition-all"/>
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button class="relative w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-[9px] font-bold text-white flex items-center justify-center" style="background:#dc2626;">2</span>
            </button>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY -->
    <main class="flex-1 p-6 space-y-6 overflow-y-auto">

        <!-- HERO BANNER -->
        <div class="relative rounded-2xl overflow-hidden" style="background:linear-gradient(135deg,#1c0a0a 0%,#450a0a 40%,#7f1d1d 100%);min-height:160px;">
            <div class="absolute inset-0 hero-grid"></div>
            <div class="absolute inset-0" style="background:radial-gradient(ellipse at 15% 60%,rgba(220,38,38,.35) 0%,transparent 55%),radial-gradient(ellipse at 85% 10%,rgba(248,113,113,.2) 0%,transparent 45%);"></div>
            <div class="relative flex items-center justify-between p-8 gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,.15);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <p class="text-red-300 text-sm font-medium">
                            Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name }} 👋
                        </p>
                    </div>
                    <h2 class="font-display font-extrabold text-white mb-2" style="font-size:clamp(1.5rem,3vw,2rem);">
                        Your ISP is Running Smoothly
                    </h2>
                    <p class="text-red-200/80 text-sm max-w-lg">
                        All systems operational. Monitor clients, manage subscriptions, and track revenue from this central hub.
                    </p>
                    <div class="flex items-center gap-3 mt-5">
                        <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-red-700 font-semibold text-sm rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add Client
                        </a>
                        <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl hover:bg-white/20 transition-all" style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);">
                            View Reports
                        </a>
                    </div>
                </div>
                <div class="hidden lg:flex items-center gap-3 flex-shrink-0">
                    <div class="rounded-2xl px-5 py-4 text-center" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);">
                        <p class="text-red-300 font-semibold mb-1.5" style="font-size:9px;text-transform:uppercase;letter-spacing:.12em;">System</p>
                        <div class="flex items-center gap-2 justify-center">
                            <div class="relative w-2.5 h-2.5">
                                <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full"></div>
                                <div class="absolute inset-0 bg-emerald-400 rounded-full animate-ping" style="opacity:.6;"></div>
                            </div>
                            <p class="text-white font-bold text-sm">Online</p>
                        </div>
                    </div>
                    <div class="rounded-2xl px-5 py-4 text-center" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);">
                        <p class="text-red-300 font-semibold mb-1.5" style="font-size:9px;text-transform:uppercase;letter-spacing:.12em;">Uptime</p>
                        <p class="text-white font-bold text-sm">99.9%</p>
                    </div>
                    <div class="rounded-2xl px-5 py-4 text-center" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);">
                        <p class="text-red-300 font-semibold mb-1.5" style="font-size:9px;text-transform:uppercase;letter-spacing:.12em;">Today</p>
                        <p class="text-white font-bold text-sm">{{ now()->format('M d') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            <div class="metric-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg" style="color:#059669;background:#ecfdf5;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        +12.5%
                    </span>
                </div>
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider mb-1">Total Users</p>
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">{{ \App\Models\User::count() }}</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:45%;background:linear-gradient(90deg,#fca5a5,#dc2626);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">Active registered accounts</p>
                    <a href="{{ route('users.index') }}" class="text-red-500 hover:text-red-700 font-semibold transition-colors" style="font-size:10px;">View →</a>
                </div>
            </div>

            <div class="metric-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg" style="color:#6b7280;background:#f3f4f6;">0%</span>
                </div>
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider mb-1">Active Clients</p>
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">0</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:0%;background:linear-gradient(90deg,#34d399,#10b981);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">Currently subscribed</p>
                    <a href="#" class="text-emerald-500 hover:text-emerald-700 font-semibold transition-colors" style="font-size:10px;">Manage →</a>
                </div>
            </div>

            <div class="metric-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg" style="color:#b91c1c;background:#fef2f2;">
                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full"></div>Active
                    </span>
                </div>
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider mb-1">Subscription Plans</p>
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">0</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:0%;background:linear-gradient(90deg,#f87171,#dc2626);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">Available packages</p>
                    <a href="#" class="text-red-500 hover:text-red-700 font-semibold transition-colors" style="font-size:10px;">Configure →</a>
                </div>
            </div>

            <div class="metric-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg" style="color:#d97706;background:#fffbeb;">This Month</span>
                </div>
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider mb-1">Total Revenue</p>
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">₱0.00</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:0%;background:linear-gradient(90deg,#fbbf24,#f59e0b);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">From subscriptions</p>
                    <a href="#" class="text-amber-500 hover:text-amber-700 font-semibold transition-colors" style="font-size:10px;">Report →</a>
                </div>
            </div>
        </div>

        <!-- BOTTOM GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            <!-- Quick Actions -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#dc2626;">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-gray-900 text-sm">Quick Actions</h4>
                        <p class="text-gray-400" style="font-size:10px;">Common shortcuts</p>
                    </div>
                </div>
                <div class="p-4 space-y-2.5">
                    <a href="{{ route('users.create') }}" class="qa-row group flex items-center gap-3.5 p-3.5 rounded-xl transition-all hover:scale-[1.02]" style="background:#fef2f2;border:1px solid #fee2e2;">
                        <div class="qa-icon w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm" style="background:#dc2626;">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold" style="color:#7f1d1d;">Add New User</p>
                            <p style="font-size:11px;color:#dc2626;">Create account</p>
                        </div>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform flex-shrink-0" style="color:#fca5a5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('users.index') }}" class="qa-row group flex items-center gap-3.5 p-3.5 rounded-xl transition-all hover:scale-[1.02]" style="background:#ecfdf5;border:1px solid #d1fae5;">
                        <div class="qa-icon w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm" style="background:#059669;">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold" style="color:#064e3b;">Manage Users</p>
                            <p style="font-size:11px;color:#10b981;">View all accounts</p>
                        </div>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform flex-shrink-0" style="color:#6ee7b7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="#" class="qa-row group flex items-center gap-3.5 p-3.5 rounded-xl transition-all hover:scale-[1.02]" style="background:#f5f3ff;border:1px solid #ede9fe;">
                        <div class="qa-icon w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm" style="background:#dc2626;">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold" style="color:#7f1d1d;">Generate Report</p>
                            <p style="font-size:11px;color:#8b5cf6;">Analytics & insights</p>
                        </div>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform flex-shrink-0" style="color:#fca5a5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="#" class="qa-row group flex items-center gap-3.5 p-3.5 rounded-xl transition-all hover:scale-[1.02]" style="background:#f9fafb;border:1px solid #e5e7eb;">
                        <div class="qa-icon w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm" style="background:#374151;">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">System Settings</p>
                            <p class="text-gray-500" style="font-size:11px;">Configuration</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-1 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#dc2626,#dc2626);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-gray-900 text-sm">Recent Users</h4>
                            <p class="text-gray-400" style="font-size:10px;">Latest registered accounts</p>
                        </div>
                    </div>
                    <a href="{{ route('users.index') }}" class="text-[12px] font-semibold px-3 py-1.5 rounded-lg transition-colors hover:bg-red-100" style="color:#b91c1c;background:#fef2f2;">
                        View All →
                    </a>
                </div>

                @if(\App\Models\User::count() > 0)
                    <div class="divide-y divide-gray-50/80">
                        @foreach(\App\Models\User::latest()->take(6)->get() as $user)
                            <div class="user-row flex items-center justify-between px-5 py-3.5 cursor-pointer">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-10 h-10 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-gray-400" style="font-size:11px;">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-semibold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg" style="font-size:11px;">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-gray-400 mt-0.5" style="font-size:10px;">{{ $user->created_at->format('g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-800 mb-1">No Users Yet</p>
                        <p class="text-gray-400 text-sm mb-5">Start by adding your first user to the system</p>
                        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-xl text-white transition-all hover:shadow-md" style="background:#dc2626;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add First User
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- STATUS BAR -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-5 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="relative w-2.5 h-2.5 flex-shrink-0">
                            <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></div>
                            <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping" style="opacity:.5;"></div>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">System Status:</span>
                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-lg" style="color:#065f46;background:#ecfdf5;">Operational</span>
                    </div>
                    <div class="w-px h-4 bg-gray-200 hidden sm:block"></div>
                    <div class="flex items-center gap-4" style="font-size:11px;color:#9ca3af;">
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-red-500 rounded-full inline-block"></span>Server Online</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-red-500 rounded-full inline-block"></span>Database Connected</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>API Active</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-gray-400" style="font-size:11px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Last updated: {{ now()->format('g:i A') }}
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    let collapsed = false;
    function toggleSidebar() {
        collapsed = !collapsed;
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('main-content');
        const icon = document.getElementById('toggle-icon');
        if (collapsed) {
            sidebar.style.width = '72px';
            main.style.marginLeft = '72px';
            sidebar.classList.add('sidebar-collapsed');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>';
        } else {
            sidebar.style.width = '260px';
            main.style.marginLeft = '260px';
            sidebar.classList.remove('sidebar-collapsed');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>';
        }
    }
    window.addEventListener('load', () => {
        document.querySelectorAll('.progress-fill').forEach(bar => {
            const target = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = target; }, 200);
        });
    });
</script>
</body>
</html>