<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subscription Plan — NetManager</title>
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

        html { overflow-x: hidden; }

        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .detail-card { animation: slideUp 0.4s ease both; }
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
        <div class="flex items-center gap-3">
            <a href="{{ route('subscription-rates.index') }}"
               class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Plan Details</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View subscription plan information
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
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
    <main class="flex-1 p-6 flex flex-col items-center justify-start">

        <!-- Breadcrumb -->
        <div class="w-full max-w-3xl mb-5">
            <nav class="flex items-center gap-2 text-[12px] text-gray-400">
                <a href="{{ route('dashboard') }}" class="hover:text-red-500 transition-colors font-medium">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('subscription-rates.index') }}" class="hover:text-red-500 transition-colors font-medium">Subscription Rates</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-semibold">{{ $subscriptionRate->plan_name }}</span>
            </nav>
        </div>

        <!-- Detail Cards -->
        <div class="w-full max-w-3xl space-y-5">

            <!-- Header Card -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center"
                             style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-display font-bold text-gray-900 text-xl">{{ $subscriptionRate->plan_name }}</h2>
                            <p class="text-gray-400 text-sm">{{ $subscriptionRate->speed }} • {{ $subscriptionRate->plan_type }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($subscriptionRate->is_active)
                            <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                ✓ Active
                            </span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-500">
                                ✕ Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pricing & Plan Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Plan Information -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.1s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Plan Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Plan Name</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->plan_name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Plan Type</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $subscriptionRate->plan_type }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Speed</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->speed }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Data Limit</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->data_limit ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Billing Cycle</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->billing_cycle }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.2s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pricing Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Monthly Fee</span>
                            <span class="font-bold text-xl text-green-600">₱{{ number_format($subscriptionRate->monthly_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Installation Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->installation_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Activation Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->activation_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Router Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->router_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Lock-in Period</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->lock_in_period ? $subscriptionRate->lock_in_period . ' months' : 'None' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Fees -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.3s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Additional Fees
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Late Penalty</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->late_penalty, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Reconnection Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->reconnection_fee, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.4s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Record Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Created At</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Updated At</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->updated_at->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.5s;">
                <div class="flex items-center justify-between">
                    <form action="{{ route('subscription-rates.destroy', $subscriptionRate->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Plan
                        </button>
                    </form>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('subscription-rates.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                            Back to List
                        </a>
                        <a href="{{ route('subscription-rates.edit', $subscriptionRate->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Plan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>
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
</script>
</body>
</html>
