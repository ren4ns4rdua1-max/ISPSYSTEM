<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subscription Plan — NetManager</title>
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

        /* Form input styles */
        .form-input {
            width: 100%;
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
            color: #111827;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            background: #fff;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }
        .form-input::placeholder { color: #9ca3af; }
        .form-input.error { border-color: #fca5a5; background: #fff5f5; }

        /* Input group icons */
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; pointer-events: none;
            transition: color 0.2s ease;
        }
        .input-wrapper:focus-within .input-icon { color: #dc2626; }
        .input-wrapper .form-input { padding-left: 2.5rem; }
        .input-wrapper-select { position: relative; }
        .input-wrapper-select .input-icon { transform: translateY(-50%); top: 50%; }
        .input-wrapper-select:focus-within .input-icon { color: #dc2626; }
        .input-wrapper-select .form-input { padding-left: 2.5rem; appearance: none; -webkit-appearance: none; }

        /* Checkbox */
        .checkbox-wrapper input:checked + div {
            background: #dc2626;
            border-color: #dc2626;
        }
        .checkbox-wrapper input:checked + div svg { display: block; }

        /* Card entrance */
        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .form-card { animation: slideUp 0.4s ease both; }

        /* Submit button shine */
        .btn-submit {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            position: relative; overflow: hidden;
        }
        .btn-submit::after {
            content: ''; position: absolute; top: -50%; left: -60%; width: 40%; height: 200%;
            background: rgba(255,255,255,.15); transform: skewX(-20deg);
            transition: left 0.4s ease;
        }
        .btn-submit:hover::after { left: 120%; }
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
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Edit Plan</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Update subscription plan details
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
                <span class="text-gray-600 font-semibold">Edit</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card w-full max-w-3xl">

            <!-- Card Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-7 py-5 border-b border-gray-100 flex items-center gap-4"
                     style="background:linear-gradient(90deg,#fff5f5,#fff);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                         style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-gray-900 text-base">Edit Plan Information</h2>
                        <p class="text-gray-400 text-[12px]">Update the subscription plan details below</p>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('subscription-rates.update', $subscriptionRate->id) }}" class="p-7 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Plan Name -->
                        <div class="space-y-1.5">
                            <label for="plan_name" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Plan Name
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <input id="plan_name" name="plan_name" type="text"
                                       value="{{ old('plan_name', $subscriptionRate->plan_name) }}"
                                       autofocus
                                       class="form-input {{ $errors->has('plan_name') ? 'error' : '' }}"/>
                            </div>
                            @error('plan_name')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Plan Type -->
                        <div class="space-y-1.5">
                            <label for="plan_type" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Plan Type
                            </label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                <select id="plan_type" name="plan_type"
                                        class="form-input {{ $errors->has('plan_type') ? 'error' : '' }}">
                                    <option value="">Select Plan Type</option>
                                    @foreach($planTypes as $type)
                                        <option value="{{ $type }}" {{ old('plan_type', $subscriptionRate->plan_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('plan_type')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Speed -->
                        <div class="space-y-1.5">
                            <label for="speed" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Speed
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <input id="speed" name="speed" type="text"
                                       value="{{ old('speed', $subscriptionRate->speed) }}"
                                       class="form-input {{ $errors->has('speed') ? 'error' : '' }}"/>
                            </div>
                            @error('speed')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Data Limit -->
                        <div class="space-y-1.5">
                            <label for="data_limit" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Data Limit
                            </label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                                <select id="data_limit" name="data_limit"
                                        class="form-input {{ $errors->has('data_limit') ? 'error' : '' }}">
                                    <option value="">Select Data Limit</option>
                                    @foreach($dataLimits as $limit)
                                        <option value="{{ $limit }}" {{ old('data_limit', $subscriptionRate->data_limit) == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('data_limit')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Monthly Fee -->
                        <div class="space-y-1.5">
                            <label for="monthly_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Monthly Fee (₱)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="monthly_fee" name="monthly_fee" type="number"
                                       value="{{ old('monthly_fee', $subscriptionRate->monthly_fee) }}"
                                       min="0" step="0.01"
                                       class="form-input {{ $errors->has('monthly_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('monthly_fee')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Cycle -->
                        <div class="space-y-1.5">
                            <label for="billing_cycle" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Billing Cycle
                            </label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <select id="billing_cycle" name="billing_cycle"
                                        class="form-input {{ $errors->has('billing_cycle') ? 'error' : '' }}">
                                    <option value="">Select Billing Cycle</option>
                                    @foreach($billingCycles as $cycle)
                                        <option value="{{ $cycle }}" {{ old('billing_cycle', $subscriptionRate->billing_cycle) == $cycle ? 'selected' : '' }}>{{ $cycle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('billing_cycle')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Installation Fee -->
                        <div class="space-y-1.5">
                            <label for="installation_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Installation Fee (₱)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="installation_fee" name="installation_fee" type="number"
                                       value="{{ old('installation_fee', $subscriptionRate->installation_fee) }}"
                                       min="0" step="0.01"
                                       class="form-input {{ $errors->has('installation_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('installation_fee')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Activation Fee -->
                        <div class="space-y-1.5">
                            <label for="activation_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Activation Fee (₱)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="activation_fee" name="activation_fee" type="number"
                                       value="{{ old('activation_fee', $subscriptionRate->activation_fee) }}"
                                       min="0" step="0.01"
                                       class="form-input {{ $errors->has('activation_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('activation_fee')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Router Fee -->
                        <div class="space-y-1.5">
                            <label for="router_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Router Fee (₱)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                <input id="router_fee" name="router_fee" type="number"
                                       value="{{ old('router_fee', $subscriptionRate->router_fee) }}"
                                       min="0" step="0.01"
                                       class="form-input {{ $errors->has('router_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('router_fee')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lock In Period -->
                        <div class="space-y-1.5">
                            <label for="lock_in_period" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Lock-in Period (months)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <input id="lock_in_period" name="lock_in_period" type="number"
                                       value="{{ old('lock_in_period', $subscriptionRate->lock_in_period) }}"
                                       min="0"
                                       class="form-input {{ $errors->has('lock_in_period') ? 'error' : '' }}"/>
                            </div>
                            @error('lock_in_period')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Late Penalty -->
                        <div class="space-y-1.5">
                            <label for="late_penalty" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Late Penalty (₱)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="late_penalty" name="late_penalty" type="number"
                                       value="{{ old('late_penalty', $subscriptionRate->late_penalty) }}"
                                       min="0" step="0.01"
                                       class="form-input {{ $errors->has('late_penalty') ? 'error' : '' }}"/>
                            </div>
                            @error('late_penalty')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reconnection Fee -->
                        <div class="space-y-1.5">
                            <label for="reconnection_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Reconnection Fee (₱)
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <input id="reconnection_fee" name="reconnection_fee" type="number"
                                       value="{{ old('reconnection_fee', $subscriptionRate->reconnection_fee) }}"
                                       min="0" step="0.01"
                                       class="form-input {{ $errors->has('reconnection_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('reconnection_fee')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Active -->
                        <div class="md:col-span-2">
                            <label class="checkbox-wrapper flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscriptionRate->is_active) ? 'checked' : '' }}
                                    class="sr-only" onchange="document.getElementById('active-toggle').classList.toggle('bg-green-500', this.checked); document.getElementById('active-toggle').classList.toggle('bg-gray-200', !this.checked); document.getElementById('active-toggle').querySelector('svg').style.display = this.checked ? 'block' : 'none';"/>
                                <div id="active-toggle" class="w-10 h-6 rounded-full transition-colors {{ old('is_active', $subscriptionRate->is_active) ? 'bg-green-500' : 'bg-gray-200' }}">
                                    <svg class="w-4 h-4 text-white m-1" style="display: {{ old('is_active', $subscriptionRate->is_active) ? 'block' : 'none' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Active</span>
                            </label>
                        </div>

                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 pt-5 flex items-center justify-between gap-3 flex-wrap">
                        <a href="{{ route('subscription-rates.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-submit inline-flex items-center gap-2 px-7 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Plan
                        </button>
                    </div>

                </form>
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
