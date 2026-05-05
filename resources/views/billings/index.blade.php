<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing — NetManager</title>
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

        /* Table row animations */
        .trow { 
            transition: background 0.15s ease, transform 0.2s ease; 
            animation: fadeUp 0.35s ease both;
        }
        .trow:hover { background: #fef2f2; transform: scale(1.002); }
        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

        /* Status badges */
        .status-paid { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .status-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .status-overdue { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-partial { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

        /* Button hover effects */
        .action-btn {
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }

        /* Modal animation */
        #delete-modal { display: none; }
        #delete-modal.show { display: flex; }
        .modal-content {
            animation: modalPop 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1) both;
        }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
        }
        
        /* Pagination styling */
        nav[aria-label="Pagination"] span, nav[aria-label="Pagination"] a {
            border-radius: 10px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease;
        }
        nav[aria-label="Pagination"] a:hover {
            background: #fee2e2 !important;
            color: #dc2626 !important;
        }
        
        /* Search input focus */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(220,38,38,.15);
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

@include('partials.sidebar')


<!-- ===================== MAIN CONTENT ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Billing Management</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ $billings->total() }} {{ Str::plural('invoice', $billings->total()) }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <form method="GET" class="flex items-center">
                    <input type="text" id="search-input" name="search" value="{{ $search }}" placeholder="Search invoices..."
                        class="search-input w-64 text-sm bg-gray-100 rounded-xl pl-9 pr-4 py-2 text-gray-700 placeholder-gray-400 border-0 focus:outline-none focus:bg-white transition-all"/>
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>
            </div>
            <a href="{{ route('billings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
               style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Invoice
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6 space-y-5">

        <!-- Success Alert -->
        @if (session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl border animate-fadeIn" style="background:#f0fdf4;border-color:#bbf7d0;">
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Total Revenue</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                <span class="text-xs text-green-600 font-medium mt-2 inline-block">All paid invoices</span>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Pending Amount</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['pending_amount'], 2) }}</p>
                <span class="text-xs text-yellow-600 font-medium mt-2 inline-block">{{ $stats['pending_count'] }} pending invoices</span>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Overdue Amount</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['overdue_amount'], 2) }}</p>
                <span class="text-xs text-red-600 font-medium mt-2 inline-block">{{ $stats['overdue_count'] }} overdue invoices</span>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Total Invoices</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">{{ $billings->total() }}</p>
                <span class="text-xs text-gray-500 font-medium mt-2 inline-block">This period</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Search</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Invoice # or Client name..." 
                           class="search-input w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Status</label>
                    <select name="status" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">All Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ $status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Type</label>
                    <select name="billing_type" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">All Types</option>
                        <option value="monthly" {{ $billingType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ $billingType == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="yearly" {{ $billingType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="installation" {{ $billingType == 'installation' ? 'selected' : '' }}>Installation</option>
                        <option value="reconnection" {{ $billingType == 'reconnection' ? 'selected' : '' }}>Reconnection</option>
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div class="w-36">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl hover:shadow-md transition-all" 
                        style="background:linear-gradient(135deg,#dc2626,#b91c1c);">Filter</button>
                <a href="{{ route('billings.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">Clear</a>
            </form>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            @if($billings->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="font-display font-bold text-gray-800 text-lg mb-2">No Billings Found</p>
                    <p class="text-gray-400 text-sm mb-6 max-w-xs">Create your first billing invoice to get started.</p>
                    <a href="{{ route('billings.create') }}" class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all hover:shadow-lg"
                       style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Create First Invoice
                    </a>
                </div>
            @else

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background:linear-gradient(90deg,#fef2f2,#fff5f5);">
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Billing Date</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($billings as $index => $billing)
                                <tr class="trow" style="animation-delay: {{ $index * 40 }}ms;">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">{{ $billing->invoice_number }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $billing->client->name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $billing->client->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">{{ ucfirst($billing->billing_type) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">₱{{ number_format($billing->total_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $billing->billing_date->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm {{ $billing->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $billing->due_date->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($billing->status)
                                            @case('paid')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-paid">Paid</span>
                                                @break
                                            @case('pending')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-pending">Pending</span>
                                                @break
                                            @case('overdue')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-overdue">Overdue</span>
                                                @break
                                            @case('partial')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-partial">Partial</span>
                                                @break
                                            @case('cancelled')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-cancelled">Cancelled</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('billings.show', $billing->id) }}" 
                                               class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                               style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View
                                            </a>
                                            <a href="{{ route('billings.edit', $billing->id) }}" 
                                               class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                               style="color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <button onclick="confirmDelete({{ $billing->id }}, '{{ addslashes($billing->invoice_number) }}')" 
                                                    class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
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
                    @foreach ($billings as $index => $billing)
                        <div class="p-5 trow" style="animation-delay: {{ $index * 40 }}ms;">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $billing->invoice_number }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $billing->client->name }}</p>
                                </div>
                                @switch($billing->status)
                                    @case('paid')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-paid">Paid</span>
                                        @break
                                    @case('pending')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-pending">Pending</span>
                                        @break
                                    @case('overdue')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-overdue">Overdue</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-{{ $billing->status }}">{{ ucfirst($billing->status) }}</span>
                                @endswitch
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                <div><p class="text-gray-400">Amount</p><p class="text-gray-700 font-medium">₱{{ number_format($billing->total_amount, 2) }}</p></div>
                                <div><p class="text-gray-400">Due Date</p><p class="text-gray-700 font-medium">{{ $billing->due_date->format('M d, Y') }}</p></div>
                                <div><p class="text-gray-400">Type</p><p class="text-gray-700 font-medium">{{ ucfirst($billing->billing_type) }}</p></div>
                                <div><p class="text-gray-400">Status</p><p class="text-gray-700 font-medium">{{ ucfirst($billing->status) }}</p></div>
                            </div>
                            <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                                <a href="{{ route('billings.show', $billing->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#059669;background:#ecfdf5;">View</a>
                                <a href="{{ route('billings.edit', $billing->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#b91c1c;background:#fee2e2;">Edit</a>
                                <button onclick="confirmDelete({{ $billing->id }}, '{{ addslashes($billing->invoice_number) }}')" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#374151;background:#f3f4f6;">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($billings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                        <p class="text-[12px] text-gray-500">
                            Showing <span class="font-semibold text-gray-800">{{ $billings->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $billings->lastItem() }}</span>
                            of <span class="font-semibold text-gray-800">{{ $billings->total() }}</span> invoices
                        </p>
                        {{ $billings->links() }}
                    </div>
                @endif

            @endif
        </div>

    </main>
</div>

<!-- ===================== DELETE MODAL ===================== -->
<div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;">
    <div class="modal-content bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-sm">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-gray-900 text-lg">Delete Invoice</h3>
                <p class="text-gray-500 text-sm mt-1">Are you sure you want to delete <span id="delete-billing-number" class="font-semibold text-gray-800"></span>? This action cannot be undone.</p>
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
                        class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>


@include('partials.sidebar-js')

</body>
</html>