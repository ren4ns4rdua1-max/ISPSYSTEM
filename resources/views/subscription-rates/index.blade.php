<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Rates — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        #sidebar { transition: width 0.3s cubic-bezier(0.4,0,0.2,1); background: linear-gradient(180deg, #0c0e1a 0%, #111827 60%, #0c0e1a 100%); }
        #main-content { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }

        .collapsible { transition: opacity 0.2s ease, max-width 0.3s ease; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }

        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #dc2626, #f87171);
        }

        .topbar {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(226,232,240,0.8);
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

        .avatar-grad { background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%); }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }

        @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
        .animate-ping { animation: ping 1.5s cubic-bezier(0,0,.2,1) infinite; }

        /* Table row hover */
        .trow { transition: background 0.15s ease; }
        .trow:hover { background: #fef2f2; }

        /* Fade in rows */
        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .trow { animation: fadeUp 0.3s ease both; }

        html { overflow-x: hidden; }

        /* Search focus */
        .search-input:focus { box-shadow: 0 0 0 3px rgba(220,38,38,.15); }

        /* Delete confirm modal */
        #delete-modal { display:none; }
        #delete-modal.show { display:flex; }

        /* Pagination override */
        nav[aria-label="Pagination"] span, nav[aria-label="Pagination"] a {
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex">

<!-- ===================== SIDEBAR ===================== -->
<aside id="sidebar" style="width:260px;" class="fixed left-0 top-0 h-full z-50 flex flex-col shadow-2xl">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-4 py-[18px] border-b border-white/[.06] min-h-[68px]">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg"
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

        <!-- Dashboard -->
        <div class="nav-wrapper relative">
            <a href="{{ route('dashboard') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Dashboard</span>
            </a>
            <span class="nav-tooltip">Dashboard</span>
        </div>

       

        <p class="collapsible px-2 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-[.14em] text-gray-600 font-display" style="max-width:200px;">Management</p>

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


        <!-- Subscription Rates — ACTIVE -->
        <div class="nav-wrapper relative">
            <a href="{{ route('subscription-rates.index') }}" class="nav-item-inner relative flex items-center gap-3 px-3 py-2.5 rounded-xl"
               style="background:linear-gradient(135deg,rgba(220,38,38,.18),rgba(185,28,28,.12));border:1px solid rgba(220,38,38,.28);">
                <div class="nav-active-bar"></div>
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-semibold text-red-200" style="max-width:160px;">Subscription Rates</span>
            </a>
            <span class="nav-tooltip">Subscription Rates</span>
        </div>

         <!-- Billing -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Billing</span>
            </a>
            <span class="nav-tooltip">Billing</span>
        </div>

        <!-- Payments -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Payments</span>
            </a>
            <span class="nav-tooltip">Payments</span>
        </div>

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

       
        <p class="collapsible px-2 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-[.14em] text-gray-600 font-display" style="max-width:200px;">System</p>


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


        <!-- Users -->
        <div class="nav-wrapper relative">
            <a href="{{ route('users.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:120px;">Users</span>
            </a>
            <span class="nav-tooltip">Users</span>
        </div>

        
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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Subscription Rates</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ $subscriptionRates->total() }} {{ Str::plural('plan', $subscriptionRates->total()) }} available
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <input type="text" id="search-input" placeholder="Search plans..."
                       class="search-input w-56 text-sm bg-gray-100 rounded-xl pl-9 pr-4 py-2 text-gray-700 placeholder-gray-400 border-0 focus:outline-none focus:bg-white transition-all"/>
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button class="relative w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY -->
    <main class="flex-1 p-6 space-y-5">

        <!-- Success Alert -->
        @if (session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl border" style="background:#f0fdf4;border-color:#bbf7d0;">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-emerald-800 text-sm font-semibold">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Top Controls Bar -->
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <!-- Stats pills -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-xs font-semibold text-gray-600">Total: <span class="text-gray-900">{{ $subscriptionRates->total() }}</span></span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-semibold text-gray-600">Page: <span class="text-gray-900">{{ $subscriptionRates->currentPage() }} / {{ $subscriptionRates->lastPage() }}</span></span>
                </div>
            </div>
            <!-- Add Plan Button -->
            <a href="{{ route('subscription-rates.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all"
               style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Add New Plan
            </a>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            @if($subscriptionRates->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p class="font-display font-bold text-gray-800 text-lg mb-2">No Subscription Plans Found</p>
                    <p class="text-gray-400 text-sm mb-6 max-w-xs">Your plan list is empty. Start by creating the first pricing plan.</p>
                    <a href="{{ route('subscription-rates.create') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all hover:shadow-lg"
                       style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Create First Plan
                    </a>
                </div>
            @else

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full" id="rates-table">
                        <thead>
                            <tr style="background:linear-gradient(90deg,#fef2f2,#fff5f5);">
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Plan Name</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Speed</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Monthly Fee</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Billing</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($subscriptionRates as $index => $rate)
                                <tr class="trow" style="animation-delay: {{ $index * 40 }}ms;">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">#{{ (($subscriptionRates->currentPage() - 1) * $subscriptionRates->perPage()) + $index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $rate->plan_name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $rate->data_limit ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ $rate->plan_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-700">{{ $rate->speed }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">₱{{ number_format($rate->monthly_fee, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $rate->billing_cycle }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($rate->is_active)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('subscription-rates.show', $rate->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                               style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View
                                            </a>
                                            <a href="{{ route('subscription-rates.edit', $rate->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                               style="color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <button onclick="confirmDelete({{ $rate->id }}, '{{ addslashes($rate->plan_name) }}')"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                                    style="color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-gray-50">
                    @foreach ($subscriptionRates as $index => $rate)
                        <div class="p-5 trow" style="animation-delay: {{ $index * 40 }}ms;">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $rate->plan_name }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $rate->speed }} • {{ $rate->plan_type }}</p>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">#{{ (($subscriptionRates->currentPage() - 1) * $subscriptionRates->perPage()) + $index + 1 }}</span>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-bold text-gray-900">₱{{ number_format($rate->monthly_fee, 2) }}/mo</span>
                                @if($rate->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                                <a href="{{ route('subscription-rates.show', $rate->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#059669;background:#ecfdf5;">View</a>
                                <a href="{{ route('subscription-rates.edit', $rate->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#b91c1c;background:#fee2e2;">Edit</a>
                                <button onclick="confirmDelete({{ $rate->id }}, '{{ addslashes($rate->plan_name) }}')" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#374151;background:#f3f4f6;">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($subscriptionRates->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                        <p class="text-[12px] text-gray-500">
                            Showing <span class="font-semibold text-gray-800">{{ $subscriptionRates->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $subscriptionRates->lastItem() }}</span>
                            of <span class="font-semibold text-gray-800">{{ $subscriptionRates->total() }}</span> plans
                        </p>
                        {{ $subscriptionRates->links() }}
                    </div>
                @endif

            @endif
        </div>

    </main>
</div>

<!-- ===================== DELETE MODAL ===================== -->
<div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-sm" style="animation: fadeUp 0.2s ease;">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-gray-900 text-lg">Delete Plan</h3>
                <p class="text-gray-500 text-sm mt-1">Are you sure you want to delete <span id="delete-rate-name" class="font-semibold text-gray-800"></span>? This action cannot be undone.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="closeModal()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                Cancel
            </button>
            <form id="delete-form" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md"
                        style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Sidebar toggle
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

    // Delete modal
    function confirmDelete(rateId, rateName) {
        document.getElementById('delete-rate-name').textContent = rateName;
        document.getElementById('delete-form').action = '/subscription-rates/' + rateId;
        document.getElementById('delete-modal').classList.add('show');
    }
    function closeModal() {
        document.getElementById('delete-modal').classList.remove('show');
    }
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Live search filter
    document.getElementById('search-input')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#rates-table tbody tr, .md\\:hidden > div').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
</script>
</body>
</html>
