<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP Dashboard — ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        #sidebar {
            transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
            background: linear-gradient(180deg, #0c0e1a 0%, #111827 60%, #0c0e1a 100%);
        }
        #main-content { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }

        .collapsible { transition: opacity 0.2s ease, max-width 0.3s ease; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }
        .sidebar-collapsed .sec-lbl { opacity: 0; }

        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #dc2626, #f87171);
        }

        .metric-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .metric-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,.09); }

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

        .sec-lbl {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .13em;
            color: rgba(255,255,255,.22);
            font-family: 'Syne', sans-serif;
            padding: 3px 10px 5px;
            transition: opacity .2s;
        }

        html { overflow-x: hidden; }

        /* ── Chart cards ── */
        .chart-card {
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid #f0f0f0;
            overflow: hidden;
            transition: box-shadow .25s ease, transform .25s ease;
        }
        .chart-card:hover { box-shadow: 0 12px 36px rgba(0,0,0,.07); transform: translateY(-2px); }

        .chart-head {
            padding: 16px 20px 14px;
            border-bottom: 1px solid #f5f5f5;
            display: flex; align-items: center; justify-content: space-between;
        }
        .chart-ico {
            width: 32px; height: 32px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* Period selector */
        .period-btn {
            font-size: 11px; font-weight: 700; padding: 4px 11px;
            border-radius: 8px; border: none; cursor: pointer;
            transition: all .18s; font-family: 'DM Sans', sans-serif;
        }
        .period-btn.on  { background: #dc2626; color: #fff; }
        .period-btn.off { background: #f3f4f6; color: #6b7280; }
        .period-btn.off:hover { background: #e5e7eb; }

        /* Legend dot */
        .ldot { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }

        /* Trend badge */
        .trend-up   { color: #059669; background: #ecfdf5; }
        .trend-down { color: #dc2626; background: #fef2f2; }
        .trend-pill {
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 6px; display: inline-flex; align-items: center; gap: 3px;
        }

        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .fu  { animation: fadeUp .38s ease both; }
        .fu2 { animation: fadeUp .38s .07s ease both; }
        .fu3 { animation: fadeUp .38s .14s ease both; }
        .fu4 { animation: fadeUp .38s .21s ease both; }
        .fu5 { animation: fadeUp .38s .28s ease both; }
        
        /* Submenu styles */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 2rem;
        }
        .submenu.open {
            max-height: 300px;
        }
        .submenu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.2s;
        }
        .submenu-item:hover {
            color: #fca5a5;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(3px);
        }
        .submenu-item.active {
            color: #f87171;
            background: rgba(220, 38, 38, 0.1);
        }
        .nav-item-has-sub {
            cursor: pointer;
        }
        .chevron-icon {
            transition: transform 0.3s ease;
        }
        .chevron-icon.rotated {
            transform: rotate(90deg);
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex">

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
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
            <p class="font-display font-bold text-white text-[14px] leading-tight tracking-tight">ISP Admin</p>
            <p class="text-red-400 text-[10px] font-medium tracking-wide">Control Center</p>
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

        <!-- ── OVERVIEW ── -->
        <p class="sec-lbl collapsible" style="max-width:200px;">Overview</p>

        <!-- Dashboard -->
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

        <!-- ── MANAGEMENT ── -->
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Management</p>

        <!-- Clients -->
        <div class="nav-wrapper relative">
            <a href="{{ route('clients.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Clients</span>
            </a>
            <span class="nav-tooltip">Clients</span>
        </div>

       
        <div class="nav-wrapper relative">
            <a href="{{ route('subscription-rates.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Subscription Plans</span>
            </a>
            <span class="nav-tooltip">Subscription Plans</span>
        </div>

        <div class="nav-wrapper relative">
            <a href="{{ route('sales.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
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

        <div class="nav-wrapper relative">
            <a href="{{ route('billings.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Billing</span>
            </a>
            <span class="nav-tooltip">Billing</span>
        </div>

        <div class="nav-wrapper relative">
            <a href="{{ route('payments.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
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
            <a href="{{ route('technicians.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M14.7 6.3a4 4 0 01-5.4 5.4l-5.6 5.6a2 2 0 102.8 2.8l5.6-5.6a4 4 0 005.4-5.4z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Technicians</span>
            </a>
            <span class="nav-tooltip">Technicians</span>
        </div>

        <!-- Reports & Analytics Section -->
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Reports </p>

        <div class="nav-wrapper relative">
            <a href="{{ route('reports.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Reports</span>
            </a>
            <span class="nav-tooltip">Reports</span>
        </div>

       

        <!-- Administration Section -->
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Administration</p>

        <div class="nav-wrapper relative">
            <a href="{{ route('users.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">User Management</span>
            </a>
            <span class="nav-tooltip">User Management</span>
        </div>

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

<!-- ═══════════════════ MAIN CONTENT ═══════════════════ -->
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
            
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY -->
    <main class="flex-1 p-6 space-y-5 overflow-y-auto">

        <!-- ── HERO BANNER ── -->
        <div class="fu relative rounded-2xl overflow-hidden" style="background:linear-gradient(135deg,#1c0a0a 0%,#450a0a 40%,#7f1d1d 100%);min-height:160px;">
            <div class="absolute inset-0 hero-grid"></div>
            <div class="absolute inset-0" style="background:radial-gradient(ellipse at 15% 60%,rgba(220,38,38,.35) 0%,transparent 55%),radial-gradient(ellipse at 85% 10%,rgba(248,113,113,.2) 0%,transparent 45%);"></div>
            <div class="relative flex items-center justify-between p-8 gap-6">
                <div class="flex-1">
                    <h2 class="font-display font-extrabold text-white mb-2" style="font-size:clamp(1.5rem,3vw,2rem);">
                        Your ISP is Running Smoothly
                    </h2>
                    <p class="text-red-200/80 text-sm max-w-lg">
                        All systems operational. Monitor clients, manage subscriptions, and track revenue from this central hub.
                    </p>
                    <div class="flex items-center gap-3 mt-5">
                        <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-red-700 font-semibold text-sm rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add Client
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl hover:bg-white/20 transition-all" style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);">
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

        <!-- ── METRIC CARDS ── -->
        <div class="fu2 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

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
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg" style="color:#6b7280;background:#f3f4f6;">{{ $totalClients > 0 ? round(($activeClients / $totalClients) * 100) : 0 }}%</span>
                </div>
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider mb-1">Active Clients</p>
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">{{ $activeClients }}</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:{{ $totalClients > 0 ? ($activeClients / $totalClients) * 100 : 0 }}%;background:linear-gradient(90deg,#34d399,#10b981);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">Currently subscribed</p>
                    <a href="{{ route('clients.index') }}" class="text-emerald-500 font-semibold" style="font-size:10px;">Manage →</a>
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
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">{{ \App\Models\SubscriptionRate::count() }}</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:0%;background:linear-gradient(90deg,#f87171,#dc2626);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">Available packages</p>
                    <a href="{{ route('subscription-rates.index') }}" class="text-red-500 font-semibold" style="font-size:10px;">Configure →</a>
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
                <p class="font-display font-extrabold text-gray-900 mb-3" style="font-size:2.1rem;line-height:1;">₱{{ number_format($totalRevenue, 2) }}</p>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="progress-fill h-full rounded-full" style="width:100%;background:linear-gradient(90deg,#fbbf24,#f59e0b);"></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-gray-400" style="font-size:10px;">From subscriptions</p>
                    <a href="{{ route('reports.index') }}" class="text-amber-500 font-semibold" style="font-size:10px;">Report →</a>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════
             CHARTS ROW 1 — Revenue Trend + Plan Donut
        ═══════════════════════════════════════════ -->
        <div class="fu3 grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Revenue Trend — full line chart (2/3) -->
            <div class="chart-card lg:col-span-2">
                <div class="chart-head">
                    <div class="flex items-center gap-3">
                        <div class="chart-ico" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-[15px] h-[15px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-display font-bold text-gray-800 text-[13px]">Revenue Trend</p>
                            <p class="text-gray-400 text-[11px]">Monthly billing collections</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button class="period-btn on" onclick="switchRevenue(this,'6m')">6M</button>
                        <button class="period-btn off" onclick="switchRevenue(this,'12m')">12M</button>
                    </div>
                </div>
                <div style="padding:18px 20px 16px;">
                    <div class="flex items-center gap-6 mb-4 flex-wrap">
                        <div>
                            <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Total YTD</p>
                            <p class="font-display font-extrabold text-gray-900 text-xl leading-tight">₱{{ number_format($totalRevenue, 2) }}</p>
                        </div>
                        <div>
                            <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Avg / Month</p>
                            <p class="font-display font-bold text-gray-700 text-base leading-tight">₱{{ number_format($totalRevenue > 0 ? $totalRevenue / 6 : 0, 2) }}</p>
                        </div>
                        <span class="trend-pill {{ $revenueGrowth >= 0 ? 'trend-up' : 'trend-down' }}">{{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ number_format(abs($revenueGrowth), 1) }}% vs last period</span>
                    </div>
                    <!-- Legend -->
                    <div class="flex items-center gap-4 mb-3">
                        <span class="flex items-center gap-1.5 text-[11px] text-gray-500 font-medium">
                            <span class="w-6 h-0.5 inline-block rounded-full" style="background:#dc2626;"></span>This period
                        </span>
                        <span class="flex items-center gap-1.5 text-[11px] text-gray-400 font-medium">
                            <span class="w-6 h-0.5 inline-block rounded-full border-t border-dashed border-gray-300"></span>Previous
                        </span>
                    </div>
                    <div style="position:relative;height:200px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Plan Distribution Donut (1/3) -->
            <div class="chart-card">
                <div class="chart-head">
                    <div class="flex items-center gap-3">
                        <div class="chart-ico" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                            <svg class="w-[15px] h-[15px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-display font-bold text-gray-800 text-[13px]">Plan Mix</p>
                            <p class="text-gray-400 text-[11px]">Active subscriptions by tier</p>
                        </div>
                    </div>
                </div>
                <div style="padding:16px 20px 20px;">
                    <div style="position:relative;height:170px;display:flex;align-items:center;justify-content:center;">
                        <canvas id="donutChart"></canvas>
                        <div style="position:absolute;text-align:center;pointer-events:none;">
                            <p class="font-display font-extrabold text-gray-800 text-2xl leading-none">{{ $totalClients }}</p>
                            <p style="font-size:10px;color:#9ca3af;font-weight:600;margin-top:2px;">Clients</p>
                        </div>
                    </div>
                    <div class="space-y-2.5 mt-4">
                        @foreach($planDistribution as $plan)
                        <div class="flex items-center justify-between text-[12px]">
                            <span class="flex items-center gap-2 text-gray-600 font-medium"><span class="ldot" style="background:{{ $plan['color'] }};"></span>{{ $plan['name'] }}</span>
                            <span class="font-bold text-gray-800">{{ $plan['count'] }} <span class="text-gray-400 font-normal text-[11px]">{{ $plan['percentage'] }}%</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ── STATUS BAR ── -->
        <div class="fu5 bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
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
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>Server Online</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>Database Connected</span>
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
// ── Sidebar toggle ──
let collapsed = false;
function toggleSidebar() {
    collapsed = !collapsed;
    const sidebar = document.getElementById('sidebar');
    const main    = document.getElementById('main-content');
    const icon    = document.getElementById('toggle-icon');
    if (collapsed) {
        sidebar.style.width = '72px'; main.style.marginLeft = '72px';
        sidebar.classList.add('sidebar-collapsed');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>';
    } else {
        sidebar.style.width = '260px'; main.style.marginLeft = '260px';
        sidebar.classList.remove('sidebar-collapsed');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>';
    }
}

// ── Animate progress bars on load ──
window.addEventListener('load', () => {
    document.querySelectorAll('.progress-fill').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = target; }, 200);
    });
});

// ── Chart.js global defaults ──
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.color = '#9ca3af';

// ═══════════════════════════════════
// 1. REVENUE TREND — Line Chart (Dynamic Data)
// ═══════════════════════════════════
// PHP data passed to JavaScript
const revenueData6m = @json($sixMonthRevenue);
const revenueData12m = @json($twelveMonthRevenue);

const revData = {
    '6m': {
        labels: revenueData6m.map(item => item.month),
        curr: revenueData6m.map(item => item.revenue),
        prev: revenueData6m.map(item => Math.round(item.revenue * 0.8)),
    },
    '12m': {
        labels: revenueData12m.map(item => item.month),
        curr: revenueData12m.map(item => item.revenue),
        prev: revenueData12m.map(item => Math.round(item.revenue * 0.8)),
    }
};

let revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: revData['6m'].labels,
        datasets: [
            {
                label: 'Revenue',
                data: revData['6m'].curr,
                borderColor: '#dc2626',
                borderWidth: 2.5,
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
                    g.addColorStop(0, 'rgba(220,38,38,.16)');
                    g.addColorStop(1, 'rgba(220,38,38,.00)');
                    return g;
                },
                tension: 0.42, fill: true,
                pointRadius: 4, pointHoverRadius: 6,
                pointBackgroundColor: '#fff', pointBorderColor: '#dc2626', pointBorderWidth: 2.5,
            },
            {
                label: 'Previous',
                data: revData['6m'].prev,
                borderColor: '#d1d5db',
                borderWidth: 1.5,
                backgroundColor: 'transparent',
                tension: 0.42, fill: false,
                pointRadius: 0, borderDash: [5, 4],
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#111827', titleColor: '#f9fafb', bodyColor: '#9ca3af',
                padding: 11, borderRadius: 10, boxPadding: 4,
                callbacks: { label: ctx => ' ₱' + ctx.parsed.y.toLocaleString() }
            }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                grid: { color: '#f3f4f6' }, border: { display: false },
                ticks: { font: { size: 11 }, callback: v => '₱' + (v/1000).toFixed(0) + 'k' }
            }
        }
    }
});

function switchRevenue(btn, period) {
    document.querySelectorAll('.period-btn').forEach(b => { b.classList.remove('on'); b.classList.add('off'); });
    btn.classList.remove('off'); btn.classList.add('on');
    revenueChart.data.labels = revData[period].labels;
    revenueChart.data.datasets[0].data = revData[period].curr;
    revenueChart.data.datasets[1].data = revData[period].prev;
    revenueChart.update();
}

// ═══════════════════════════════════
// 2. PLAN MIX — Doughnut (Dynamic Data)
// ═══════════════════════════════════
const planDistribution = @json($planDistribution);
const donutData = planDistribution.map(p => ({ name: p.name, count: p.count, color: p.color, percentage: p.percentage }));

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: donutData.map(d => d.name),
        datasets: [{
            data: donutData.map(d => d.count),
            backgroundColor: donutData.map(d => d.color),
            borderWidth: 3, borderColor: '#fff', hoverOffset: 7,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '74%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#111827', titleColor: '#f9fafb', bodyColor: '#d1d5db',
                padding: 11, borderRadius: 10,
                callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` }
            }
        }
    }
});
</script>
</body>
</html>