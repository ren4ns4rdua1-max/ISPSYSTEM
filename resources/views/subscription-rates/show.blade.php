<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subscription Plan — NetManager</title>
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

        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .detail-card { animation: slideUp 0.4s ease both; }
        
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
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Plan Details</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View subscription plan information
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
                <span class="text-gray-600 font-semibold">{{ $subscriptionRate->plan_name }}</span>
            </nav>
        </div>

        <!-- Detail Cards -->
        <div class="w-full max-w-3xl space-y-5">

            <!-- Header Card -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center"
                             style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-display font-bold text-gray-900 text-xl">{{ $subscriptionRate->plan_name }}</h2>
                            <p class="text-gray-400 text-sm">{{ $subscriptionRate->speed }} • {{ ucfirst($subscriptionRate->plan_type) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($subscriptionRate->is_active)
                            <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                ✓ Active
                            </span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-500">
                                ✕ Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pricing & Plan Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Plan Information -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.1s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Plan Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Plan Name</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->plan_name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Plan Type</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ ucfirst($subscriptionRate->plan_type) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Speed</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->speed }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Data Limit</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->data_limit ?? 'Unlimited' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Billing Cycle</span>
                            <span class="font-semibold text-gray-800">{{ ucfirst($subscriptionRate->billing_cycle) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.2s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pricing Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Monthly Fee</span>
                            <span class="font-bold text-xl text-green-600">₱{{ number_format($subscriptionRate->monthly_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Installation Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->installation_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Activation Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->activation_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Router Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->router_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Lock-in Period</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->lock_in_period ? $subscriptionRate->lock_in_period . ' months' : 'None' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Fees -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.3s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Additional Fees
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Late Penalty</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->late_penalty, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Reconnection Fee</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($subscriptionRate->reconnection_fee, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.4s;">
                    <h3 class="font-display font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Record Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Created At</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Updated At</span>
                            <span class="font-semibold text-gray-800">{{ $subscriptionRate->updated_at->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.5s;">
                <div class="flex items-center justify-between">
                    <form action="{{ route('subscription-rates.destroy', $subscriptionRate->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Plan
                        </button>
                    </form>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('subscription-rates.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                            Back to List
                        </a>
                        <a href="{{ route('subscription-rates.edit', $subscriptionRate->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Plan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>


@include('partials.sidebar-js')

</body>
</html>