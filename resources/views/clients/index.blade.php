<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients — NetManager</title>
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
        .status-active { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .status-inactive { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .status-suspended { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .status-cancelled { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-pending_approval { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }

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
        @media (max-width: 1023px) {
            .mobile-menu-btn {
                display: block;
            }
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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Clients</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ $clients->total() }} registered {{ Str::plural('client', $clients->total()) }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('clients.index') }}" class="relative hidden md:block">
                <input type="text" name="search" id="search-input" value="{{ $search }}" placeholder="Search clients..."
                       class="w-64 text-sm bg-gray-100 rounded-xl pl-9 pr-4 py-2 text-gray-700 placeholder-gray-400 border-0 focus:outline-none focus:ring-2 focus:ring-red-300 focus:bg-white transition-all duration-200"/>
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
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

        <!-- Top Controls Bar -->
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-xs font-semibold text-gray-600">Total: <span class="text-gray-900">{{ $clients->total() }}</span></span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-semibold text-gray-600">Page: <span class="text-gray-900">{{ $clients->currentPage() }} / {{ $clients->lastPage() }}</span></span>
                </div>
                <form method="GET" action="{{ route('clients.index') }}" class="flex items-center gap-2">
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
<select name="status" onchange="this.form.submit()" class="text-xs font-semibold bg-white border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-300 cursor-pointer">
                        <option value="">All Status</option>
                        <option value="pending_approval" {{ $status == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ $status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            </div>
            <a href="{{ route('clients.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
               style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Add New Client
            </a>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            @if($clients->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="font-display font-bold text-gray-800 text-lg mb-2">No Clients Found</p>
                    <p class="text-gray-400 text-sm mb-6 max-w-xs">Your client list is empty. Start by adding the first client.</p>
                    <a href="{{ route('clients.create') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all hover:shadow-lg"
                       style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add First Client
                    </a>
                </div>
            @else

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full" id="clients-table">
                        <thead>
                            <tr style="background:linear-gradient(90deg,#fef2f2,#fff5f5);">
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">PPPoE</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($clients as $index => $client)
                                <tr class="trow" style="animation-delay: {{ $index * 40 }}ms;">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">#{{ (($clients->currentPage() - 1) * $clients->perPage()) + $index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
<div class="relative w-9 h-9 rounded-xl flex-shrink-0 shadow-sm overflow-hidden">
                                                @if($client->photo)
                                                    <img src="{{ asset('storage/' . $client->photo) }}" alt="{{ $client->name }}" class="w-full h-full object-cover"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="w-full h-full avatar-grad items-center justify-center text-white font-bold text-sm absolute inset-0" style="display:none;">
                                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                                    </div>
                                                @else
                                                    <div class="w-full h-full avatar-grad flex items-center justify-center text-white font-bold text-sm">
                                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $client->name }}</p>
                                                <p class="text-[10px] text-gray-400">Since {{ $client->start_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-xs text-gray-600">{{ $client->email }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                <span class="text-xs text-gray-600">{{ $client->phone_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $client->pppoe_name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs">
                                            <p class="text-gray-700 font-medium">{{ $client->barangay }}</p>
                                            <p class="text-gray-400">NAP: {{ $client->nap_box }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-medium text-gray-700">{{ $client->plan_description }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs">
                                            <p class="text-gray-700 font-medium">{{ $client->due_date_time->format('M d, Y') }}</p>
                                            <p class="text-gray-400">{{ $client->due_date_time->format('g:i A') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-{{ $client->status }}">
                                            {{ ucfirst($client->status) }}
                                        </span>
                                    </td>
<td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($client->status === 'pending_approval')
                                                <!-- Approve button for pending clients -->
                                                <form method="POST" action="{{ route('clients.approve', $client->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm text-white bg-green-600 hover:bg-green-700">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <!-- Approve & Assign button -->
                                                <button type="button" onclick="openAssignModal({{ $client->id }}, '{{ addslashes($client->name) }}')"
                                                        class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                                        style="color:#7c3aed;background:#f3e8ff;border:1px solid #e9d5ff;">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                    </svg>
                                                    Assign
                                                </button>
                                            @else
                                                <!-- Regular buttons for non-pending clients -->
                                                <a href="{{ route('clients.show', $client->id) }}"
                                                   class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                                   style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </a>
                                                <a href="{{ route('clients.edit', $client->id) }}"
                                                   class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                                   style="color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Edit
                                                </a>
                                                <button onclick="confirmDelete({{ $client->id }}, '{{ addslashes($client->name) }}')"
                                                        class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                                        style="color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-gray-50">
                    @foreach ($clients as $index => $client)
                        <div class="p-5 trow" style="animation-delay: {{ $index * 40 }}ms;">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-11 h-11 rounded-xl flex-shrink-0 shadow-sm overflow-hidden">
                                        @if($client->photo)
                                            <img src="{{ asset('storage/' . $client->photo) }}" alt="{{ $client->name }}" class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-full h-full avatar-grad items-center justify-center text-white font-bold text-sm absolute inset-0" style="display:none;">
                                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                            </div>
                                        @else
                                            <div class="w-full h-full avatar-grad flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $client->name }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $client->email }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold status-{{ $client->status }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                <div><p class="text-gray-400">Phone</p><p class="text-gray-700 font-medium">{{ $client->phone_number }}</p></div>
                                <div><p class="text-gray-400">PPPoE</p><p class="text-gray-700 font-medium">{{ $client->pppoe_name }}</p></div>
                                <div><p class="text-gray-400">Location</p><p class="text-gray-700 font-medium">{{ $client->barangay }}</p></div>
                                <div><p class="text-gray-400">Due Date</p><p class="text-gray-700 font-medium">{{ $client->due_date_time->format('M d, Y') }}</p></div>
                            </div>
                            <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                                <a href="{{ route('clients.show', $client->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#059669;background:#ecfdf5;">View</a>
                                <a href="{{ route('clients.edit', $client->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#b91c1c;background:#fee2e2;">Edit</a>
                                <button onclick="confirmDelete({{ $client->id }}, '{{ addslashes($client->name) }}')" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg" style="color:#374151;background:#f3f4f6;">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($clients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                        <p class="text-[12px] text-gray-500">
                            Showing <span class="font-semibold text-gray-800">{{ $clients->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $clients->lastItem() }}</span>
                            of <span class="font-semibold text-gray-800">{{ $clients->total() }}</span> clients
                        </p>
                        {{ $clients->links() }}
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
                <h3 class="font-display font-bold text-gray-900 text-lg">Delete Client</h3>
                <p class="text-gray-500 text-sm mt-1">Are you sure you want to delete <span id="delete-client-name" class="font-semibold text-gray-800"></span>? This action cannot be undone.</p>
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

<!-- ===================== APPROVE & ASSIGN MODAL ===================== -->
<div id="assign-modal" class="fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;">
    <div class="modal-content bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-md">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-gray-900 text-lg">Approve & Assign Task</h3>
                <p class="text-gray-500 text-sm mt-1">Client: <span id="modal-client-name" class="font-semibold text-gray-800"></span></p>
            </div>
        </div>
        
        <form id="assign-form" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Technician</label>
                    <select name="technician_id" id="technician-select" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" required>
                        <option value="">-- Select Technician --</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }} ({{ ucfirst($tech->status) }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Job Type</label>
                    <select name="job_type" id="job-type" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" required>
                        <option value="new_installation">New Installation</option>
                        <option value="repair">Repair</option>
                        <option value="reconnection">Reconnection</option>
                        <option value="upgrade">Upgrade</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Scheduled Date</label>
                    <input type="datetime-local" name="scheduled_date" id="scheduled-date" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="Additional instructions for technician..."></textarea>
                </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md hover:-translate-y-0.5" style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                    Approve & Assign
                </button>
            </div>
        </form>
    </div>
</div>


@include('partials.sidebar-js')

</body>
</html>


<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // ── Delete modal ──────────────────────────────────────────────────
    function confirmDelete(clientId, clientName) {
        document.getElementById('delete-client-name').textContent = clientName;
        document.getElementById('delete-form').action = '/clients/' + clientId;
        document.getElementById('delete-modal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('delete-modal').style.display = 'none';
    }
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // ── Approve & Assign modal ────────────────────────────────────────
    function openAssignModal(clientId, clientName) {
        document.getElementById('modal-client-name').textContent = clientName;
        document.getElementById('assign-form').action = '/clients/' + clientId + '/approve-and-assign';
        document.getElementById('assign-modal').style.display = 'flex';
        // Set default scheduled date to now
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const el = document.getElementById('scheduled-date');
        if (el && !el.value) el.value = now.toISOString().slice(0, 16);
    }
    function closeAssignModal() {
        document.getElementById('assign-modal').style.display = 'none';
    }
    document.getElementById('assign-modal').addEventListener('click', function(e) {
        if (e.target === this) closeAssignModal();
    });

    // ── Escape closes any open modal ──────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeModal(); closeAssignModal(); }
    });

    // ── Live search ───────────────────────────────────────────────────
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        let t;
        searchInput.addEventListener('input', function() {
            clearTimeout(t);
            t = setTimeout(() => { this.form.submit(); }, 400);
        });
    }

    // ── Fade-in alerts ────────────────────────────────────────────────
    const s = document.createElement('style');
    s.textContent = '@keyframes fadeIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}.animate-fadeIn{animation:fadeIn .3s ease forwards}';
    document.head.appendChild(s);
</script>
</body>
</html>
