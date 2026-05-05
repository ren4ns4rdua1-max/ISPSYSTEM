<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Details — NetManager</title>
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

        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .detail-card { animation: fadeUp 0.4s ease both; }
        
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
        <div class="flex items-center gap-3">
            <a href="{{ route('clients.index') }}"
               class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Client Details</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    View client information
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6 flex flex-col items-center justify-start">

        <!-- Breadcrumb -->
        <div class="w-full max-w-3xl mb-5">
            <nav class="flex items-center gap-2 text-[12px] text-gray-400">
                <a href="{{ route('dashboard') }}" class="hover:text-red-500 transition-colors font-medium">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('clients.index') }}" class="hover:text-red-500 transition-colors font-medium">Clients</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-semibold">Details</span>
            </nav>
        </div>

        <!-- Detail Cards -->
        <div class="w-full max-w-3xl space-y-5">

            <!-- Profile Card -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between"
                     style="background:linear-gradient(90deg,#fff5f5,#fff);">
                        <div class="flex items-center gap-4">
<div class="relative w-16 h-16 rounded-2xl shadow-lg overflow-hidden flex-shrink-0">
                                @if($client->photo)
                                    <img src="{{ asset('storage/' . $client->photo) }}" alt="{{ $client->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                @endif
                                <div class="w-full h-full avatar-grad flex items-center justify-center text-white font-bold text-2xl absolute inset-0 {{ $client->photo ? 'hidden' : '' }}">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <h2 class="font-display font-bold text-gray-900 text-xl">{{ $client->name }}</h2>
                                <p class="text-gray-400 text-sm">{{ $client->email }}</p>
                                <span class="mt-2 inline-flex px-3 py-1 rounded-full text-xs font-semibold status-{{ $client->status }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </div>
                        </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('clients.edit', $client->id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition-all hover:shadow-md"
                           style="color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <button onclick="confirmDelete({{ $client->id }}, '{{ addslashes($client->name) }}')"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl transition-all hover:shadow-md"
                                style="color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>

                <div class="p-7">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h3 class="font-display font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-100 pb-2">Contact Information</h3>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Phone Number</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->phone_number }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">PPPoE Name</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->pppoe_name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Location & Plan -->
                        <div class="space-y-4">
                            <h3 class="font-display font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-100 pb-2">Location & Plan</h3>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Barangay</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->barangay }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">NAP Box</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->nap_box }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Plan Description</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->plan_description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Details -->
                        <div class="space-y-4">
                            <h3 class="font-display font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-100 pb-2">Billing Details</h3>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Start Date</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->start_date->format('F d, Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Due Date & Time</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->due_date_time->format('F d, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="space-y-4">
                            <h3 class="font-display font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-100 pb-2">Additional Info</h3>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Created By</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->user->name ?? 'System' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Created At</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->created_at->format('F d, Y g:i A') }}</p>
                                </div>
                            </div>

                            @if($client->notes)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Notes</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $client->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Workflow -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background:linear-gradient(90deg,#f0fdf4,#fff);">
                    <h3 class="font-display font-bold text-gray-800">Quick Actions</h3>
                </div>
                <div class="p-6 grid grid-cols-1 gap-3">
                    <a href="{{ route('sales.create', ['client_id' => $client->id]) }}" 
                       class="flex items-center gap-3 px-4 py-3 bg-red-50 text-red-700 rounded-xl hover:bg-red-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-sm">New Subscription</p>
                            <p class="text-xs text-red-600">Create new service</p>
                        </div>
                    </a>
                    <a href="{{ route('billings.create', ['client_id' => $client->id]) }}" 
                       class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-sm">Create Billing</p>
                            <p class="text-xs text-blue-600">Generate invoice</p>
                        </div>
                    </a>
                    <a href="{{ route('payments.create', ['client_id' => $client->id]) }}" 
                       class="flex items-center gap-3 px-4 py-3 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-sm">Record Payment</p>
                            <p class="text-xs text-green-600">Receive payment</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Workflow Status -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100" style="background:linear-gradient(90deg,#fef3c7,#fff);">
                    <h3 class="font-display font-bold text-gray-800">Account Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $client->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium {{ $client->status === 'active' ? 'text-gray-700' : 'text-gray-400' }}">Account Active</p>
                            <p class="text-xs text-gray-400">{{ $client->status === 'active' ? 'Client is subscribed' : 'Client account is inactive' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $client->billings->count() > 0 ? ($client->billings->where('status', 'pending')->count() > 0 ? 'bg-amber-100 text-amber-600' : 'bg-green-100 text-green-600') : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium {{ $client->billings->count() > 0 ? 'text-gray-700' : 'text-gray-400' }}">Billing Generated</p>
                            <p class="text-xs text-gray-400">{{ $client->billings->count() }} invoice(s) - {{ $client->billings->where('status', 'pending')->count() }} pending</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $client->billings->where('status', 'paid')->count() > 0 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium {{ $client->billings->where('status', 'paid')->count() > 0 ? 'text-gray-700' : 'text-gray-400' }}">Payment Received</p>
                            <p class="text-xs text-gray-400">₱{{ number_format($client->billings->where('status', 'paid')->sum('total_amount'), 2) }} collected</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing History -->
            @if($client->billings->count() > 0)
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-display font-bold text-gray-800">Billing History</h3>
                    <a href="{{ route('billings.index', ['client_id' => $client->id]) }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($client->billings->take(5) as $billing)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm">{{ $billing->invoice_number }}</td>
                                <td class="px-6 py-3 text-sm">{{ ucfirst($billing->billing_type) }}</td>
                                <td class="px-6 py-3 text-sm font-medium">₱{{ number_format($billing->total_amount, 2) }}</td>
                                <td class="px-6 py-3">
                                    @php $bs = ['paid' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'overdue' => 'bg-red-100 text-red-700', 'partial' => 'bg-blue-100 text-blue-700', 'cancelled' => 'bg-gray-100 text-gray-700']; @endphp
                                    <span class="px-2 py-1 rounded-full text-xs {{ $bs[$billing->status] ?? 'bg-gray-100' }}">{{ ucfirst($billing->status) }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $billing->billing_date->format('M d, Y') }}</td>
                                <td class="px-6 py-3">
                                    @if($billing->status !== 'paid')
                                    <a href="{{ route('payments.create', ['client_id' => $client->id, 'billing_id' => $billing->id]) }}" class="text-xs text-green-600 hover:text-green-800 font-medium">Pay Now</a>
                                    @else
                                    <a href="{{ route('billings.show', $billing->id) }}" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Payment History -->
            @php $payments = \App\Models\Payment::where('client_id', $client->id)->latest()->take(5)->get(); @endphp
            @if($payments->count() > 0)
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-display font-bold text-gray-800">Payment History</h3>
                    <a href="{{ route('payments.index', ['client_id' => $client->id]) }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Receipt #</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Method</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm">{{ $payment->receipt_number }}</td>
                                <td class="px-6 py-3 text-sm font-medium">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="px-6 py-3 text-sm">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('payments.show', $payment->id) }}" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </main>
</div>

<!-- DELETE MODAL -->
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


@include('partials.sidebar-js')

</body>
</html>