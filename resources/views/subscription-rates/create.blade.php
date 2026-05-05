<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Subscription Plan — NetManager</title>
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

        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .form-card { animation: slideUp 0.4s ease both; }

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
            <a href="{{ route('subscription-rates.index') }}"
               class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Create New Plan</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Fill in the details to create a new subscription plan
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
                <a href="{{ route('subscription-rates.index') }}" class="hover:text-red-500 transition-colors font-medium">Subscription Plans</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-semibold">Create</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card w-full max-w-3xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Card Header -->
                <div class="px-7 py-5 border-b border-gray-100 flex items-center gap-4"
                     style="background:linear-gradient(90deg,#fff5f5,#fff);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                         style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-gray-900 text-base">Plan Information</h2>
                        <p class="text-gray-400 text-[12px]">All fields are required to create a new subscription plan</p>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('subscription-rates.store') }}" class="p-7 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Plan Name -->
                        <div class="space-y-1.5">
                            <label for="plan_name" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Plan Name</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <input id="plan_name" name="plan_name" type="text"
                                       value="{{ old('plan_name') }}" placeholder="e.g. Basic 10Mbps" autofocus
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
                            <label for="plan_type" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Plan Type</label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                <select id="plan_type" name="plan_type" class="form-input {{ $errors->has('plan_type') ? 'error' : '' }}">
                                    <option value="">Select Plan Type</option>
                                    @foreach($planTypes as $type)
                                        <option value="{{ $type }}" {{ old('plan_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
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
                            <label for="speed" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Speed</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <input id="speed" name="speed" type="text"
                                       value="{{ old('speed') }}" placeholder="e.g. 10 Mbps"
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
                            <label for="data_limit" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Data Limit</label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                                <select id="data_limit" name="data_limit" class="form-input {{ $errors->has('data_limit') ? 'error' : '' }}">
                                    <option value="">Select Data Limit</option>
                                    @foreach($dataLimits as $limit)
                                        <option value="{{ $limit }}" {{ old('data_limit') == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('data_limit')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Monthly Fee -->
                        <div class="space-y-1.5">
                            <label for="monthly_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Monthly Fee (₱)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="monthly_fee" name="monthly_fee" type="number"
                                       value="{{ old('monthly_fee') }}" placeholder="0.00" min="0" step="0.01"
                                       class="form-input {{ $errors->has('monthly_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('monthly_fee')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Cycle -->
                        <div class="space-y-1.5">
                            <label for="billing_cycle" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Billing Cycle</label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <select id="billing_cycle" name="billing_cycle" class="form-input {{ $errors->has('billing_cycle') ? 'error' : '' }}">
                                    <option value="">Select Billing Cycle</option>
                                    @foreach($billingCycles as $cycle)
                                        <option value="{{ $cycle }}" {{ old('billing_cycle') == $cycle ? 'selected' : '' }}>{{ ucfirst($cycle) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('billing_cycle')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Installation Fee -->
                        <div class="space-y-1.5">
                            <label for="installation_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Installation Fee (₱)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="installation_fee" name="installation_fee" type="number"
                                       value="{{ old('installation_fee', 0) }}" placeholder="0.00" min="0" step="0.01"
                                       class="form-input {{ $errors->has('installation_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('installation_fee')<p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Activation Fee -->
                        <div class="space-y-1.5">
                            <label for="activation_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Activation Fee (₱)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="activation_fee" name="activation_fee" type="number"
                                       value="{{ old('activation_fee', 0) }}" placeholder="0.00" min="0" step="0.01"
                                       class="form-input {{ $errors->has('activation_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('activation_fee')<p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Router Fee -->
                        <div class="space-y-1.5">
                            <label for="router_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Router Fee (₱)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                <input id="router_fee" name="router_fee" type="number"
                                       value="{{ old('router_fee', 0) }}" placeholder="0.00" min="0" step="0.01"
                                       class="form-input {{ $errors->has('router_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('router_fee')<p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Lock-in Period -->
                        <div class="space-y-1.5">
                            <label for="lock_in_period" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Lock-in Period (months)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <input id="lock_in_period" name="lock_in_period" type="number"
                                       value="{{ old('lock_in_period') }}" placeholder="0" min="0"
                                       class="form-input {{ $errors->has('lock_in_period') ? 'error' : '' }}"/>
                            </div>
                            @error('lock_in_period')<p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Late Penalty -->
                        <div class="space-y-1.5">
                            <label for="late_penalty" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Late Penalty (₱)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="late_penalty" name="late_penalty" type="number"
                                       value="{{ old('late_penalty', 0) }}" placeholder="0.00" min="0" step="0.01"
                                       class="form-input {{ $errors->has('late_penalty') ? 'error' : '' }}"/>
                            </div>
                            @error('late_penalty')<p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Reconnection Fee -->
                        <div class="space-y-1.5">
                            <label for="reconnection_fee" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Reconnection Fee (₱)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <input id="reconnection_fee" name="reconnection_fee" type="number"
                                       value="{{ old('reconnection_fee', 0) }}" placeholder="0.00" min="0" step="0.01"
                                       class="form-input {{ $errors->has('reconnection_fee') ? 'error' : '' }}"/>
                            </div>
                            @error('reconnection_fee')<p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Is Active toggle -->
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-3 cursor-pointer select-none w-fit">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="sr-only"
                                       onchange="
                                           const t=document.getElementById('active-toggle');
                                           t.style.background=this.checked?'#22c55e':'#d1d5db';
                                           document.getElementById('active-dot').style.transform=this.checked?'translateX(20px)':'translateX(2px)';
                                           document.getElementById('active-lbl').textContent=this.checked?'Active':'Inactive';
                                       "/>
                                <div id="active-toggle" class="w-11 h-6 rounded-full relative transition-colors duration-200"
                                     style="background:{{ old('is_active', true) ? '#22c55e' : '#d1d5db' }}">
                                    <div id="active-dot" class="absolute top-[3px] w-[18px] h-[18px] bg-white rounded-full shadow transition-transform duration-200"
                                         style="transform:translateX({{ old('is_active', true) ? '20px' : '2px' }})"></div>
                                </div>
                                <span id="active-lbl" class="text-sm font-semibold text-gray-700">{{ old('is_active', true) ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>

                    </div>

                    <!-- Actions -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Create Plan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</div>


@include('partials.sidebar-js')

</body>
</html>