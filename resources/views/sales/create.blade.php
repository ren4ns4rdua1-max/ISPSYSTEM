<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale - ISP Management</title>
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
            font-family: 'DM Sans', sans-serif;
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

        /* Card entrance */
        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .form-card { animation: slideUp 0.4s ease both; }
        
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
        
        /* Summary card */
        .summary-card {
            position: sticky;
            top: 1.5rem;
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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">New Sale</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Create client & generate initial billing
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
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm" class="max-w-7xl mx-auto">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Client Information Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white">
                            <h2 class="font-display font-bold text-gray-800 text-base flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Client Information
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Full Name *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <input type="text" name="name" value="{{ old('name') }}" required 
                                               class="form-input {{ $errors->has('name') ? 'error' : '' }}" 
                                               placeholder="e.g. John Doe">
                                    </div>
                                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email Address *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <input type="email" name="email" value="{{ old('email') }}" required 
                                               class="form-input {{ $errors->has('email') ? 'error' : '' }}" 
                                               placeholder="e.g. john@example.com">
                                    </div>
                                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Phone Number *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" required 
                                               class="form-input {{ $errors->has('phone_number') ? 'error' : '' }}" 
                                               placeholder="e.g. 09123456789">
                                    </div>
                                    @error('phone_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">PPPoE Name *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <input type="text" name="pppoe_name" value="{{ old('pppoe_name') }}" required 
                                               class="form-input {{ $errors->has('pppoe_name') ? 'error' : '' }}" 
                                               placeholder="e.g. johndoe123">
                                    </div>
                                    @error('pppoe_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Barangay *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <input type="text" name="barangay" value="{{ old('barangay') }}" required 
                                               class="form-input {{ $errors->has('barangay') ? 'error' : '' }}" 
                                               placeholder="e.g. Poblacion">
                                    </div>
                                    @error('barangay')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">NAP Box *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                        </svg>
                                        <input type="text" name="nap_box" value="{{ old('nap_box') }}" required 
                                               class="form-input {{ $errors->has('nap_box') ? 'error' : '' }}" 
                                               placeholder="e.g. NAP-001">
                                    </div>
                                    @error('nap_box')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Start Date *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required 
                                               class="form-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Plan Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white">
                            <h2 class="font-display font-bold text-gray-800 text-base flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Subscription Plan
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Select Plan</label>
                                    <div class="input-wrapper-select">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                        <select name="subscription_rate_id" id="subscription_rate_id" onchange="updatePlanDetails()" 
                                                class="form-input">
                                            <option value="">-- Select a Plan --</option>
                                            @foreach($subscriptionRates as $rate)
                                                <option value="{{ $rate->id }}" data-monthly="{{ $rate->monthly_fee }}" data-install="{{ $rate->installation_fee ?? 0 }}">
                                                    {{ $rate->plan_name }} - ₱{{ number_format($rate->monthly_fee, 2) }}/month
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Plan Description *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        <input type="text" name="plan_description" id="plan_description" value="{{ old('plan_description') }}" required 
                                               class="form-input" placeholder="e.g. Basic Plan - 10 Mbps">
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Due Date/Time *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <input type="datetime-local" name="due_date_time" value="{{ old('due_date_time') }}" required 
                                               class="form-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Initial Billing Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white">
                            <h2 class="font-display font-bold text-gray-800 text-base flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Initial Billing
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Billing Type *</label>
                                    <div class="input-wrapper-select">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                        <select name="billing_type" id="billing_type" onchange="calculateTotal()" class="form-input">
                                            <option value="monthly">Monthly</option>
                                            <option value="quarterly">Quarterly</option>
                                            <option value="yearly">Yearly</option>
                                            <option value="installation">Installation</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Billing Date *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <input type="date" name="billing_date" value="{{ old('billing_date', now()->toDateString()) }}" required 
                                               class="form-input">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Due Date *</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" required 
                                               class="form-input">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tax Amount (₱)</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" min="0" step="0.01" onchange="calculateTotal()" 
                                               class="form-input">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Discount Amount (₱)</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="0.01" onchange="calculateTotal()" 
                                               class="form-input">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Notes</label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <input type="text" name="notes" value="{{ old('notes') }}" class="form-input" placeholder="Additional notes...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (1/3) - Order Summary -->
                <div class="space-y-6">
                    <div class="summary-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white">
                            <h2 class="font-display font-bold text-gray-800 text-base flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Order Summary
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Base Amount</span>
                                    <span class="font-medium text-gray-800" id="baseAmount">₱0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Tax</span>
                                    <span class="font-medium text-gray-800" id="taxDisplay">₱0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Discount</span>
                                    <span class="font-medium text-green-600" id="discountDisplay">-₱0.00</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between">
                                    <span class="font-semibold text-gray-800">Total Amount</span>
                                    <span class="font-bold text-xl text-red-600" id="totalAmount">₱0.00</span>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <button type="submit" name="create_payment" value="0" 
                                        class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Create Sale Only
                                </button>
                                <button type="submit" name="create_payment" value="1" 
                                        class="w-full px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Create & Record Payment
                                </button>
                                <a href="{{ route('sales.index') }}" class="block text-center text-gray-500 text-sm hover:text-gray-700 mt-2 transition-colors">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl border border-blue-200 p-5">
                        <h3 class="font-semibold text-blue-800 text-sm mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Sales Flow
                        </h3>
                        <div class="flex items-center justify-between text-xs text-blue-700 mb-3">
                            <span>Sales →</span>
                            <span>Client →</span>
                            <span>Billing →</span>
                            <span>Payment</span>
                        </div>
                        <p class="text-xs text-blue-600 mt-2">
                            Creating a sale will automatically generate a client record and create the initial billing invoice.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>


@include('partials.sidebar-js')


<script>
    // Store plan data from PHP
    const plans = {
        @foreach($subscriptionRates as $rate)
        {{ $rate->id }}: {
            name:         "{{ $rate->plan_name }}",
            speed:        "{{ $rate->speed ?? '' }}",
            monthly:      {{ $rate->monthly_fee }},
            installation: {{ $rate->installation_fee ?? 0 }},
            description:  "{{ $rate->plan_name }}{{ $rate->speed ? ' - ' . $rate->speed : '' }}"
        },
        @endforeach
    };

    let selectedMonthly = 0;
    let selectedInstall  = 0;

    // ── When plan is selected ──────────────────────────────────────────
    document.getElementById('subscription_rate_id').addEventListener('change', function() {
        const id = parseInt(this.value);
        if (!id || !plans[id]) {
            selectedMonthly = 0;
            selectedInstall  = 0;
            document.getElementById('plan_description').value = '';
            calculateTotal();
            return;
        }

        const plan = plans[id];
        selectedMonthly = plan.monthly;
        selectedInstall  = plan.installation;

        // Auto-fill plan description
        document.getElementById('plan_description').value = plan.description;

        // Recalculate based on current billing type
        calculateTotal();
    });

    // ── When billing type changes ──────────────────────────────────────
    document.getElementById('billing_type').addEventListener('change', calculateTotal);

    // ── When start date changes → set due_date_time (+1 month) and billing due_date ──
    document.querySelector('input[name="start_date"]').addEventListener('change', function() {
        if (!this.value) return;
        const start = new Date(this.value);

        // due_date_time = start + 1 month at 12:00
        const ddt = new Date(start);
        ddt.setMonth(ddt.getMonth() + 1);
        ddt.setHours(12, 0, 0, 0);
        const pad = n => String(n).padStart(2, '0');
        document.querySelector('input[name="due_date_time"]').value =
            `${ddt.getFullYear()}-${pad(ddt.getMonth()+1)}-${pad(ddt.getDate())}T${pad(ddt.getHours())}:${pad(ddt.getMinutes())}`;

        // billing due_date = start + 30 days
        const bd = new Date(start);
        bd.setDate(bd.getDate() + 30);
        document.querySelector('input[name="due_date"]').value =
            `${bd.getFullYear()}-${pad(bd.getMonth()+1)}-${pad(bd.getDate())}`;
    });

    // ── Calculate total & update summary ──────────────────────────────
    function calculateTotal() {
        const billingType = document.getElementById('billing_type').value;
        let base = 0;

        if (billingType === 'monthly')      base = selectedMonthly;
        else if (billingType === 'quarterly') base = selectedMonthly * 3;
        else if (billingType === 'yearly')    base = selectedMonthly * 12;
        else if (billingType === 'installation') base = selectedInstall;
        else base = selectedMonthly;

        const tax      = parseFloat(document.getElementById('tax_amount').value)      || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const total    = base + tax - discount;

        const fmt = v => '₱' + v.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('baseAmount').textContent     = fmt(base);
        document.getElementById('taxDisplay').textContent     = fmt(tax);
        document.getElementById('discountDisplay').textContent = '-' + fmt(discount);
        document.getElementById('totalAmount').textContent    = fmt(total);
    }

    // ── Init on load ───────────────────────────────────────────────────
    calculateTotal();

    // Set default due_date_time if start_date already has a value
    const startInput = document.querySelector('input[name="start_date"]');
    if (startInput.value) startInput.dispatchEvent(new Event('change'));
</script>
</body>
</html>
