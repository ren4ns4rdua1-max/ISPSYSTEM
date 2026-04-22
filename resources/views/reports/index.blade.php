<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* Fix for body scrolling */
        body {
            overflow: hidden;
            height: 100vh;
        }

        /* Enhanced Sidebar Styling */
        #sidebar { 
            transition: width 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1); 
            background: linear-gradient(180deg, #0a0c18 0%, #0f111e 100%);
            backdrop-filter: blur(2px);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
        }
        #main-content { 
            transition: margin-left 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            margin-left: 260px;
            width: calc(100% - 260px);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Mobile sidebar */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            #sidebar.mobile-open {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0;
                width: 100%;
            }
            .mobile-menu-btn {
                display: block !important;
            }
        }

        .collapsible { 
            transition: opacity 0.25s ease, max-width 0.3s ease; 
            overflow: hidden; 
            white-space: nowrap; 
        }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }
        .sidebar-collapsed .sec-lbl { opacity: 0; height: 0; margin: 0; padding: 0; overflow: hidden; }

        /* Active Navigation Bar */
        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 6px 6px 0;
            background: linear-gradient(180deg, #ef4444, #f97316);
            box-shadow: 0 0 6px rgba(239,68,68,0.6);
        }

        /* Navigation item hover effect */
        .nav-item-inner {
            transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            position: relative;
        }
        .nav-item-inner:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        /* Section labels */
        .sec-lbl {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .13em;
            color: rgba(255,255,255,.22);
            font-family: 'Syne', sans-serif;
            padding: 6px 10px 4px;
            margin-top: 8px;
            transition: all 0.2s;
        }

        /* Tooltip for collapsed sidebar */
        .nav-tooltip {
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #f1f5f9; font-size: 12px; font-weight: 600;
            padding: 5px 12px; border-radius: 10px; white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity 0.2s ease; z-index: 999;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            letter-spacing: 0.3px;
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 6px solid transparent; border-right-color: #1e293b;
        }

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
        .chevron-icon {
            transition: transform 0.3s ease;
        }
        .chevron-icon.rotated {
            transform: rotate(90deg);
        }

        /* Topbar glass effect */
        .topbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        /* Avatar gradient with animation */
        .avatar-grad { 
            background: linear-gradient(125deg, #dc2626, #f97316, #ec4899);
            background-size: 200% 200%;
            animation: shimmerAvatar 4s ease infinite;
        }
        @keyframes shimmerAvatar {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Main content scrollbar */
        .main-scroll {
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .main-scroll::-webkit-scrollbar { width: 6px; }
        .main-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Stat card hover */
        .stat-card { 
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .stat-card:hover { 
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
        }

        /* Status badges */
        .status-active { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .status-inactive { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .status-suspended { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .status-cancelled { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
        }
    </style>
</head>
<body class="bg-slate-100">

<!-- Mobile Menu Button -->
<div class="mobile-menu-btn">
    <button onclick="toggleMobileSidebar()"
            class="p-2.5 rounded-xl bg-white shadow-lg text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

<!-- Mobile Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeMobileSidebar()"></div>

<!-- ===================== SIDEBAR ===================== -->
<aside id="sidebar" style="width:260px;" class="fixed left-0 top-0 h-full z-50 flex flex-col shadow-2xl">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-4 py-[18px] border-b border-white/[.08] min-h-[68px]">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-red-900/40 transition-all duration-300 hover:scale-105"
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
                class="ml-auto flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-white/10 hover:rotate-180 duration-300"
                style="background:rgba(255,255,255,.05);">
            <svg id="toggle-icon" class="w-[14px] h-[14px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        <!-- Overview Section -->
        <p class="sec-lbl collapsible" style="max-width:200px;">Overview</p>

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

        <!-- Management Section -->
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Management</p>

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

        <!-- Reports (Active) -->
        <div class="nav-wrapper relative">
            <a href="{{ route('reports.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
               style="background:linear-gradient(135deg,rgba(220,38,38,.12),rgba(185,28,28,.08));border:1px solid rgba(220,38,38,.2);">
                <div class="nav-active-bar"></div>
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-semibold text-red-300" style="max-width:160px;">Reports</span>
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


       
    </nav>

    <!-- User Footer -->
    <div class="border-t border-white/[.08] p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/[.06] transition-all">
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
                            class="w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-red-500/20 hover:scale-105">
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
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Business Reports</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Summarized data from all modules
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Date Filter -->
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="text-xs font-semibold bg-white border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-300"/>
                <span class="text-gray-400 text-xs">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="text-xs font-semibold bg-white border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-300"/>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white rounded-lg hover:shadow-md transition-all"
                        style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    Filter
                </button>
            </form>
            <a href="{{ route('reports.export') }}" class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors" title="Export Reports">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6 space-y-6">

        <!-- ==================== CLIENTS SECTION ==================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-display font-bold text-gray-900 text-lg">Client Overview</h2>
                    <p class="text-gray-400 text-xs">Total registered clients in the system</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                    <p class="text-blue-600 text-xs font-semibold uppercase">Total Clients</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $clientStats['total'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
                    <p class="text-emerald-600 text-xs font-semibold uppercase">Active</p>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">{{ $clientStats['active'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Inactive</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $clientStats['inactive'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200">
                    <p class="text-amber-600 text-xs font-semibold uppercase">Suspended</p>
                    <p class="text-2xl font-bold text-amber-900 mt-1">{{ $clientStats['suspended'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                    <p class="text-red-600 text-xs font-semibold uppercase">Cancelled</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ $clientStats['cancelled'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                    <p class="text-purple-600 text-xs font-semibold uppercase">New This Month</p>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ $clientStats['new_this_month'] }}</p>
                </div>
            </div>
        </div>

        <!-- ==================== BILLING & PAYMENTS SECTION ==================== -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Billing Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-gray-900 text-lg">Billing Summary</h2>
                        <p class="text-gray-400 text-xs">Invoice statistics</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">Total Invoices</span>
                        <span class="text-lg font-bold text-gray-900">{{ $billingStats['total_invoices'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-sm text-emerald-700">Paid Invoices</span>
                        <div class="text-right">
                            <span class="text-lg font-bold text-emerald-900">{{ $billingStats['paid'] }}</span>
                            <span class="text-xs text-emerald-600 ml-1">(₱{{ number_format($billingStats['paid_amount'], 2) }})</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                        <span class="text-sm text-yellow-700">Pending Invoices</span>
                        <div class="text-right">
                            <span class="text-lg font-bold text-yellow-900">{{ $billingStats['pending'] }}</span>
                            <span class="text-xs text-yellow-600 ml-1">(₱{{ number_format($billingStats['pending_amount'], 2) }})</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                        <span class="text-sm text-red-700">Overdue Invoices</span>
                        <div class="text-right">
                            <span class="text-lg font-bold text-red-900">{{ $billingStats['overdue'] }}</span>
                            <span class="text-xs text-red-600 ml-1">(₱{{ number_format($billingStats['overdue_amount'], 2) }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#10b981,#059669);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-gray-900 text-lg">Payment Collection</h2>
                        <p class="text-gray-400 text-xs">Payment statistics</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">Total Payments</span>
                        <span class="text-lg font-bold text-gray-900">{{ $paymentStats['total_payments'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-sm text-emerald-700">Total Collected</span>
                        <span class="text-xl font-bold text-emerald-900">₱{{ number_format($paymentStats['total_collected'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <span class="text-sm text-blue-700">Period Payments</span>
                        <span class="text-lg font-bold text-blue-900">{{ $paymentStats['period_payments'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-xl border border-purple-100">
                        <span class="text-sm text-purple-700">Period Collected</span>
                        <span class="text-lg font-bold text-purple-900">₱{{ number_format($paymentStats['period_collected'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TECHNICIAN SECTION ==================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-display font-bold text-gray-900 text-lg">Technician Overview</h2>
                    <p class="text-gray-400 text-xs">Technician status and jobs</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                    <p class="text-purple-600 text-xs font-semibold uppercase">Total Technicians</p>
                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ $technicianStats['total'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
                    <p class="text-emerald-600 text-xs font-semibold uppercase">Available</p>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">{{ $technicianStats['available'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200">
                    <p class="text-amber-600 text-xs font-semibold uppercase">Busy</p>
                    <p class="text-2xl font-bold text-amber-900 mt-1">{{ $technicianStats['busy'] }}</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Off Duty</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $technicianStats['offduty'] }}</p>
                </div>
            </div>

            <!-- Job Stats -->
            <h3 class="font-semibold text-gray-700 text-sm mb-3">Installation Jobs</h3>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="p-3 bg-gray-50 rounded-xl text-center">
                    <p class="text-gray-600 text-xs font-semibold">Total</p>
                    <p class="text-xl font-bold text-gray-900">{{ $jobStats['total'] }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-xl text-center border border-yellow-100">
                    <p class="text-yellow-600 text-xs font-semibold">Pending</p>
                    <p class="text-xl font-bold text-yellow-900">{{ $jobStats['pending'] }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-center border border-blue-100">
                    <p class="text-blue-600 text-xs font-semibold">Assigned</p>
                    <p class="text-xl font-bold text-blue-900">{{ $jobStats['assigned'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-center border border-amber-100">
                    <p class="text-amber-600 text-xs font-semibold">In Progress</p>
                    <p class="text-xl font-bold text-amber-900">{{ $jobStats['in_progress'] }}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-center border border-emerald-100">
                    <p class="text-emerald-600 text-xs font-semibold">Completed</p>
                    <p class="text-xl font-bold text-emerald-900">{{ $jobStats['completed'] }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-xl text-center border border-red-100">
                    <p class="text-red-600 text-xs font-semibold">Cancelled</p>
                    <p class="text-xl font-bold text-red-900">{{ $jobStats['cancelled'] }}</p>
                </div>
            </div>
        </div>

        <!-- ==================== PAYMENT METHODS SECTION ==================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#ec4899,#be185d);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-display font-bold text-gray-900 text-lg">Payment Methods Breakdown</h2>
                    <p class="text-gray-400 text-xs">Total collection by payment method</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl text-center">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-gray-200 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-semibold">Cash</p>
                    <p class="text-lg font-bold text-gray-900">₱{{ number_format($paymentStats['by_method']['cash'], 2) }}</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl text-center border border-blue-100">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-blue-600 font-semibold">Bank Transfer</p>
                    <p class="text-lg font-bold text-blue-900">₱{{ number_format($paymentStats['by_method']['bank_transfer'], 2) }}</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl text-center border border-purple-100">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-purple-600 font-semibold">GCash</p>
                    <p class="text-lg font-bold text-purple-900">₱{{ number_format($paymentStats['by_method']['gcash'], 2) }}</p>
                </div>
                <div class="p-4 bg-indigo-50 rounded-xl text-center border border-indigo-100">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-indigo-600 font-semibold">PayMaya</p>
                    <p class="text-lg font-bold text-indigo-900">₱{{ number_format($paymentStats['by_method']['paymaya'], 2) }}</p>
                </div>
                <div class="p-4 bg-amber-50 rounded-xl text-center border border-amber-100">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-amber-600 font-semibold">Cheque</p>
                    <p class="text-lg font-bold text-amber-900">₱{{ number_format($paymentStats['by_method']['cheque'], 2) }}</p>
                </div>
                <div class="p-4 bg-rose-50 rounded-xl text-center border border-rose-100">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <p class="text-xs text-rose-600 font-semibold">Other</p>
                    <p class="text-lg font-bold text-rose-900">₱{{ number_format($paymentStats['by_method']['other'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- ==================== RECENT ACTIVITY SECTION ==================== -->
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Recent Clients -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-display font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Recent Clients
                </h3>
                <div class="space-y-3">
                    @forelse($recentClients as $client)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 rounded-lg avatar-grad flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $client->name }}</p>
                                <p class="text-xs text-gray-400">{{ $client->created_at->format('M d, Y') }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full status-{{ $client->status }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No recent clients</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-display font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Payments
                </h3>
                <div class="space-y-3">
                    @forelse($recentPayments as $payment)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $payment->client->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $payment->payment_date->format('M d, Y') }}</p>
                            </div>
                            <span class="text-sm font-bold text-emerald-600">₱{{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No recent payments</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-display font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Recent Jobs
                </h3>
                <div class="space-y-3">
                    @forelse($recentJobs as $job)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $job->client->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $job->technician->name ?? 'Unassigned' }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full 
                                @if($job->status == 'completed') bg-emerald-100 text-emerald-700
                                @elseif($job->status == 'in_progress') bg-amber-100 text-amber-700
                                @elseif($job->status == 'pending') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No recent jobs</p>
                    @endforelse
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    // Sidebar toggle with enhanced animation
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

    // Mobile sidebar functions
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        sidebar.classList.toggle('mobile-open');
        if (sidebar.classList.contains('mobile-open')) {
            overlay.classList.remove('hidden');
        } else {
            overlay.classList.add('hidden');
        }
    }
    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        sidebar.classList.remove('mobile-open');
        overlay.classList.add('hidden');
    }

    // Close mobile sidebar on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.add('hidden');
            }
        }
    });
</script>
</body>
</html>