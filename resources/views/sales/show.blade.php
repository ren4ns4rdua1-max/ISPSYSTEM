<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Details - ISP Management</title>
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

        /* Status badges */
        .status-active { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .status-inactive { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .status-suspended { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .status-cancelled { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* Billing status badges */
        .billing-paid { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .billing-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .billing-overdue { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .billing-partial { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
        }
        @media (max-width: 1023px) {
            .mobile-menu-btn {
                display: block;
            }
        }
        
        /* Detail cards */
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .detail-card { animation: fadeUp 0.4s ease both; }
        
        /* Quick action buttons */
        .quick-action-btn {
            transition: all 0.2s ease;
        }
        .quick-action-btn:hover {
            transform: translateX(5px);
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

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside id="sidebar" style="width:260px;" class="fixed left-0 top-0 h-full z-50 flex flex-col shadow-2xl">

    <!-- Brand Area -->
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
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Reports</p>

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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Sales Details</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                View client and subscription details
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Sales
            </a>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6">
        
        <!-- Success Alert -->
        @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl border animate-fadeIn mb-6" style="background:#f0fdf4;border-color:#bbf7d0;">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Client Information Card -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-2xl shadow-md">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $client->name }}</h2>
                                <p class="text-gray-500 text-sm">{{ $client->email }}</p>
                                <p class="text-gray-400 text-xs mt-1">{{ $client->phone_number }}</p>
                            </div>
                        </div>
                        @php $statusColors=['active'=>'status-active','inactive'=>'status-inactive','suspended'=>'status-suspended','cancelled'=>'status-cancelled']; @endphp
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColors[$client->status] ?? 'status-inactive' }}">
                            {{ ucfirst($client->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">PPPoE Name</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $client->pppoe_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Barangay</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $client->barangay }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">NAP Box</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $client->nap_box }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Start Date</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $client->start_date ? $client->start_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Plan</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $client->plan_description }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Due Date</p>
                            <p class="font-medium text-gray-800 mt-1">{{ $client->due_date_time ? $client->due_date_time->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Billing History Table -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="font-display font-bold text-gray-800 text-base">Billing History</h3>
                        <a href="{{ route('billings.create', ['client_id' => $client->id]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all"
                           style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            New Billing
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($client->billings as $billing)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $billing->invoice_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($billing->billing_type) }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">₱{{ number_format($billing->total_amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold billing-{{ $billing->status }}">
                                            {{ ucfirst($billing->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $billing->billing_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('billings.show', $billing->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all hover:shadow-sm"
                                           style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">No billing records found</p>
                                            <p class="text-gray-400 text-sm mt-1">Create the first billing for this client</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3) -->
            <div class="space-y-6">
                
                <!-- Account Summary -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Account Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Total Billings</span>
                            <span class="font-semibold text-gray-800">{{ $client->billings->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Total Paid</span>
                            <span class="font-semibold text-green-600">₱{{ number_format($client->billings->where('status','paid')->sum('total_amount'),2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-500">Pending Balance</span>
                            <span class="font-semibold text-amber-600">₱{{ number_format($client->billings->whereIn('status',['pending','overdue','partial'])->sum('total_amount'),2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Subscription Plan Details -->
                @if($client->subscriptionRate)
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Subscription Plan
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Plan Name</span>
                            <span class="font-semibold text-gray-800">{{ $client->subscriptionRate->plan_name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Monthly Fee</span>
                            <span class="font-bold text-green-600">₱{{ number_format($client->subscriptionRate->monthly_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-500">Speed</span>
                            <span class="font-semibold text-gray-800">{{ $client->subscriptionRate->speed }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('billings.create', ['client_id' => $client->id]) }}" 
                           class="quick-action-btn flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Create Billing</p>
                                <p class="text-xs text-blue-600">Generate new invoice</p>
                            </div>
                        </a>
                        <a href="{{ route('payments.create', ['client_id' => $client->id]) }}" 
                           class="quick-action-btn flex items-center gap-3 px-4 py-3 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Record Payment</p>
                                <p class="text-xs text-green-600">Receive payment</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Account Status Workflow -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Account Status
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $client->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium {{ $client->status === 'active' ? 'text-gray-700' : 'text-gray-400' }}">Account Active</p>
                                <p class="text-xs text-gray-400">{{ $client->status === 'active' ? 'Client is subscribed' : 'Client account is inactive' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $client->billings->where('status', 'pending')->count() > 0 ? 'bg-amber-100 text-amber-600' : ($client->billings->where('status', 'paid')->count() > 0 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium {{ $client->billings->where('status', 'pending')->count() > 0 ? 'text-gray-700' : 'text-gray-400' }}">Billing Generated</p>
                                <p class="text-xs text-gray-400">{{ $client->billings->count() }} invoice(s) - {{ $client->billings->where('status', 'pending')->count() }} pending</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $client->billings->where('status', 'paid')->count() > 0 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium {{ $client->billings->where('status', 'paid')->count() > 0 ? 'text-gray-700' : 'text-gray-400' }}">Payment Received</p>
                                <p class="text-xs text-gray-400">₱{{ number_format($client->billings->where('status', 'paid')->sum('total_amount'), 2) }} collected</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($client->billings->whereIn('status', ['pending', 'overdue'])->count() > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-amber-600 mb-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pending Payments:
                        </p>
                        @foreach($client->billings->whereIn('status', ['pending', 'overdue'])->take(2) as $billing)
                        <a href="{{ route('payments.create', ['client_id' => $client->id, 'billing_id' => $billing->id]) }}" 
                           class="block text-xs text-blue-600 hover:text-blue-800 mb-1 transition-colors">
                            {{ $billing->invoice_number }} - ₱{{ number_format($billing->total_amount, 2) }}
                        </a>
                        @endforeach
                    </div>
                    @endif
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
    
    // Smooth fade-in for success alerts
    const style = document.createElement('style');
    style.textContent = `@keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } } .animate-fadeIn { animation: fadeIn 0.3s ease forwards; }`;
    document.head.appendChild(style);
</script>
</body>
</html>