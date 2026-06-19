<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports — ISP Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* Fix body scrolling layout */
        body {
            overflow: hidden;
            height: 100vh;
        }

        /* Modern Sidebar Styling (collapsible) */
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
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 6px solid transparent; border-right-color: #1e293b;
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

        /* Animation for fade in */
        @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease forwards; }

        /* =================== PRINT STYLES =================== */
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

            body { overflow: visible !important; height: auto !important; background: white !important; }

            /* Hide everything except printable content */
            #sidebar, .mobile-menu-btn, #mobile-overlay,
            .topbar, .no-print { display: none !important; }

            #main-content {
                margin-left: 0 !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
            }

            main.main-scroll {
                overflow: visible !important;
                padding: 0 !important;
            }

            /* Print header injected by JS */
            #print-header { display: block !important; }

            /* Page layout */
            .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
            .stat-card { break-inside: avoid; }
            .grid { break-inside: avoid; }

            /* Section page breaks */
            .print-break { page-break-before: always; }

            /* Colors — force backgrounds to print */
            .bg-gradient-to-br { background: inherit !important; }
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

<!-- ═══════════════════ MODERN SIDEBAR (COLLAPSIBLE) ═══════════════════ -->

@include('partials.sidebar')


<!-- ===================== MAIN CONTENT (REPORTS DASHBOARD) ===================== -->
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
            <!-- Export CSV -->
            <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="no-print inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <!-- Print / Save as PDF -->
            <button onclick="printReport()"
               class="no-print inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print / PDF
            </button>
            <a href="{{ route('profile.edit') }}" class="no-print w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>    </header>

    <!-- PAGE BODY (Scrollable) -->
    <main class="flex-1 main-scroll p-6 space-y-6">
        <!-- Print Header (hidden on screen, shown when printing) -->
        <div id="print-header" style="display:none;"></div>

        <!-- Client Overview -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div><h2 class="font-display font-bold text-gray-900 text-lg">Client Overview</h2><p class="text-gray-400 text-xs">Total registered clients in the system</p></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200"><p class="text-blue-600 text-xs font-semibold uppercase">Total Clients</p><p class="text-2xl font-bold text-blue-900 mt-1">{{ $clientStats['total'] }}</p></div>
                <div class="stat-card bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200"><p class="text-emerald-600 text-xs font-semibold uppercase">Active</p><p class="text-2xl font-bold text-emerald-900 mt-1">{{ $clientStats['active'] }}</p></div>
                <div class="stat-card bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200"><p class="text-gray-600 text-xs font-semibold uppercase">Inactive</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $clientStats['inactive'] }}</p></div>
                <div class="stat-card bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200"><p class="text-amber-600 text-xs font-semibold uppercase">Suspended</p><p class="text-2xl font-bold text-amber-900 mt-1">{{ $clientStats['suspended'] }}</p></div>
                <div class="stat-card bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200"><p class="text-red-600 text-xs font-semibold uppercase">Cancelled</p><p class="text-2xl font-bold text-red-900 mt-1">{{ $clientStats['cancelled'] }}</p></div>
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200"><p class="text-purple-600 text-xs font-semibold uppercase">New This Month</p><p class="text-2xl font-bold text-purple-900 mt-1">{{ $clientStats['new_this_month'] }}</p></div>
            </div>
        </div>

        <!-- Billing & Payments -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-5"><div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#f59e0b,#d97706);"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div><div><h2 class="font-display font-bold text-gray-900 text-lg">Billing Summary</h2><p class="text-gray-400 text-xs">Invoice statistics</p></div></div>
                <div class="space-y-3"><div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span>Total Invoices</span><span class="font-bold">{{ $billingStats['total_invoices'] }}</span></div><div class="flex justify-between p-3 bg-emerald-50 rounded-xl"><span>Paid</span><span class="font-bold text-emerald-900">{{ $billingStats['paid'] }} (₱{{ number_format($billingStats['paid_amount'],2) }})</span></div><div class="flex justify-between p-3 bg-yellow-50 rounded-xl"><span>Pending</span><span class="font-bold text-yellow-900">{{ $billingStats['pending'] }} (₱{{ number_format($billingStats['pending_amount'],2) }})</span></div><div class="flex justify-between p-3 bg-red-50 rounded-xl"><span>Overdue</span><span class="font-bold text-red-900">{{ $billingStats['overdue'] }} (₱{{ number_format($billingStats['overdue_amount'],2) }})</span></div></div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-5"><div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#10b981,#059669);"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div><h2 class="font-display font-bold text-gray-900 text-lg">Payment Collection</h2><p class="text-gray-400 text-xs">Payment statistics</p></div></div>
                <div class="space-y-3"><div class="flex justify-between p-3 bg-gray-50 rounded-xl"><span>Total Payments</span><span class="font-bold">{{ $paymentStats['total_payments'] }}</span></div><div class="flex justify-between p-3 bg-emerald-50 rounded-xl"><span>Total Collected</span><span class="text-xl font-bold text-emerald-900">₱{{ number_format($paymentStats['total_collected'],2) }}</span></div><div class="flex justify-between p-3 bg-blue-50 rounded-xl"><span>Period Payments</span><span class="font-bold">{{ $paymentStats['period_payments'] }}</span></div><div class="flex justify-between p-3 bg-purple-50 rounded-xl"><span>Period Collected</span><span class="font-bold text-purple-900">₱{{ number_format($paymentStats['period_collected'],2) }}</span></div></div>
            </div>
        </div>

        <!-- Technician & Jobs -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5"><div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div><h2 class="font-display font-bold text-gray-900 text-lg">Technician Overview</h2></div></div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6"><div class="stat-card bg-purple-50 rounded-xl p-4"><p class="text-purple-600 text-xs">Total Techs</p><p class="text-2xl font-bold text-purple-900">{{ $technicianStats['total'] }}</p></div><div class="stat-card bg-emerald-50 rounded-xl p-4"><p class="text-emerald-600 text-xs">Available</p><p class="text-2xl font-bold text-emerald-900">{{ $technicianStats['available'] }}</p></div><div class="stat-card bg-amber-50 rounded-xl p-4"><p class="text-amber-600 text-xs">Busy</p><p class="text-2xl font-bold text-amber-900">{{ $technicianStats['busy'] }}</p></div><div class="stat-card bg-gray-50 rounded-xl p-4"><p class="text-gray-600 text-xs">Off Duty</p><p class="text-2xl font-bold text-gray-900">{{ $technicianStats['offduty'] }}</p></div></div>
            <h3 class="font-semibold text-gray-700 text-sm mb-3">Installation Jobs</h3>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4"><div class="p-3 bg-gray-50 text-center"><p class="text-gray-600 text-xs">Total</p><p class="text-xl font-bold">{{ $jobStats['total'] }}</p></div><div class="p-3 bg-yellow-50 text-center"><p class="text-yellow-600 text-xs">Pending</p><p class="text-xl font-bold">{{ $jobStats['pending'] }}</p></div><div class="p-3 bg-blue-50 text-center"><p class="text-blue-600 text-xs">Assigned</p><p class="text-xl font-bold">{{ $jobStats['assigned'] }}</p></div><div class="p-3 bg-amber-50 text-center"><p class="text-amber-600 text-xs">In Progress</p><p class="text-xl font-bold">{{ $jobStats['in_progress'] }}</p></div><div class="p-3 bg-emerald-50 text-center"><p class="text-emerald-600 text-xs">Completed</p><p class="text-xl font-bold">{{ $jobStats['completed'] }}</p></div><div class="p-3 bg-red-50 text-center"><p class="text-red-600 text-xs">Cancelled</p><p class="text-xl font-bold">{{ $jobStats['cancelled'] }}</p></div></div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-5"><div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#ec4899,#be185d);"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div><div><h2 class="font-display font-bold text-gray-900 text-lg">Payment Methods</h2><p class="text-gray-400 text-xs">Collection by method</p></div></div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4"><div class="p-4 bg-gray-50 rounded-xl text-center"><div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-gray-200 flex items-center justify-center"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div><p class="text-xs font-semibold">Cash</p><p class="text-lg font-bold">₱{{ number_format($paymentStats['by_method']['cash'],2) }}</p></div><div class="p-4 bg-blue-50 rounded-xl text-center border border-blue-100"><div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-blue-100 flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div><p class="text-xs font-semibold">GCash</p><p class="text-lg font-bold text-purple-900">₱{{ number_format($paymentStats['by_method']['gcash'],2) }}</p></div></div>
        </div>
    </main>
</div>


<script>
function printReport() {
    var title = document.title;
    var date  = new Date().toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'});
    var hdr   = document.getElementById('print-header');
    if (hdr) { hdr.innerHTML = '<h1 style="font-size:20px;font-weight:800;margin:0;">Business Reports</h1><p style="font-size:12px;color:#64748b;margin:4px 0 0;">Generated: ' + date + ' &nbsp;|&nbsp; Period: {{ $startDate }} to {{ $endDate }}</p><hr style="margin:12px 0;border-color:#e2e8f0;">'; }
    window.print();
}
</script>

@include('partials.sidebar-js')

</body>
</html>