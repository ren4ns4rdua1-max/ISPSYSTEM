<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        #sidebar { transition: width 0.3s cubic-bezier(0.4,0,0.2,1); background: linear-gradient(180deg, #0c0e1a 0%, #111827 60%, #0c0e1a 100%); }
        #main-content { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }

        .collapsible { transition: opacity 0.2s ease, max-width 0.3s ease; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }

        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #dc2626, #f87171);
        }

        .topbar {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(226,232,240,0.8);
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

        .avatar-grad { background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%); }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }

        html { overflow-x: hidden; }
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
            <p class="font-display font-bold text-white text-[14px] leading-tight tracking-tight">ADMIN</p>
            <p class="text-red-400 text-[10px] font-medium tracking-wide">ISP Control Center</p>
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

        <!-- Dashboard — active -->
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

        <!-- Subscription Rates -->
        <div class="nav-wrapper relative">
            <a href="{{ route('subscription-rates.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Subscription Rates</span>
            </a>
            <span class="nav-tooltip">Subscription Rates</span>
        </div>

        <!-- Sales -->
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

       

        <!-- Billing -->
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

        <!-- Payments -->
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

         <!-- Technicians -->
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


        <!-- ── ADMIN ── -->
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Admin</p>


        
        <!-- Reports -->
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

        <!-- Users -->
        <div class="nav-wrapper relative">
            <a href="{{ route('users.index') }}" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Users</span>
            </a>
            <span class="nav-tooltip">Users</span>
        </div>

        
        <!-- Settings -->
        <div class="nav-wrapper relative">
            <a href="#" class="nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[.06] transition-all">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <svg class="w-[17px] h-[17px] text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="collapsible text-sm font-medium text-gray-400" style="max-width:160px;">Settings</span>
            </a>
            <span class="nav-tooltip">Settings</span>
        </div>

    </nav>


    <div class="border-t border-white/[.06] p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/[.06] transition-colors">
            <div class="w-9 h-9 rounded-xl flex-shrink-0 avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="collapsible min-w-0" style="max-width:140px;">
                <p class="text-white text-[12px] font-semibold truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-gray-500 text-[10px] truncate">{{ Auth::user()->email }}</p>
            </div>
            <div class="collapsible ml-auto flex-shrink-0" style="max-width:40px;">
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" title="Logout" class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors hover:bg-red-500/20" style="background:rgba(255,255,255,.05);"><svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></button></form>
            </div>
        </div>
    </div>
</aside>

<!-- ===================== MAIN CONTENT ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen" style="margin-left:260px;">
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Create New Invoice</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Fill in the details to create a new billing invoice
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('billings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Billing
            </a>
        </div>
    </header>

    <main class="flex-1 p-6">
        <form method="POST" action="{{ route('billings.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Client & Plan Selection -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-display font-bold text-gray-800 text-lg mb-4">Client Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Client <span class="text-red-500">*</span></label>
                                <select name="client_id" id="client_id" required class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $prefillClientId) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }} - {{ $client->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Subscription Plan</label>
                                <select name="subscription_rate_id" id="subscription_rate_id" class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <option value="">Select Plan (Optional)</option>
                                    @foreach($subscriptionRates as $rate)
                                        <option value="{{ $rate->id }}" data-price="{{ $rate->monthly_fee }}">
                                            {{ $rate->plan_name }} - ₱{{ number_format($rate->monthly_fee, 2) }}/mo
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Client Details Display -->
                        <div id="client-details" class="mt-4 p-4 bg-gray-50 rounded-xl hidden">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-400 text-xs">PPPoE Name</p>
                                    <p id="detail-pppoe" class="font-semibold text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Phone</p>
                                    <p id="detail-phone" class="font-semibold text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Barangay</p>
                                    <p id="detail-baranggay" class="font-semibold text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs">Current Plan</p>
                                    <p id="detail-plan" class="font-semibold text-gray-800">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Details -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-display font-bold text-gray-800 text-lg mb-4">Billing Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Billing Type <span class="text-red-500">*</span></label>
                                <select name="billing_type" required class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <option value="monthly" {{ old('billing_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly" {{ old('billing_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    <option value="installation" {{ old('billing_type') == 'installation' ? 'selected' : '' }}>Installation</option>
                                    <option value="reconnection" {{ old('billing_type') == 'reconnection' ? 'selected' : '' }}>Reconnection</option>
                                    <option value="other" {{ old('billing_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('billing_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Billing Date <span class="text-red-500">*</span></label>
                                <input type="date" name="billing_date" value="{{ old('billing_date', date('Y-m-d')) }}" required class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                @error('billing_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Due Date <span class="text-red-500">*</span></label>
                                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amount Section -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-display font-bold text-gray-800 text-lg mb-4">Amount</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                    <input type="number" name="amount" id="amount" value="{{ old('amount', 0) }}" step="0.01" min="0" required class="w-full text-sm bg-gray-50 rounded-lg pl-8 pr-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                </div>
                                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tax Amount</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                    <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0" class="w-full text-sm bg-gray-50 rounded-lg pl-8 pr-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Amount</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₱</span>
                                    <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0" class="w-full text-sm bg-gray-50 rounded-lg pl-8 pr-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-600">Total Amount</span>
                                    <span id="total-display" class="text-xl font-bold text-gray-900">₱0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info (if paid) -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-display font-bold text-gray-800 text-lg mb-4">Payment Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Paid Date</label>
                                <input type="date" name="paid_date" value="{{ old('paid_date') }}" class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                                <select name="payment_method" class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <option value="">Select Method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="paymaya" {{ old('payment_method') == 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                                    <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Reference</label>
                                <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" placeholder="Transaction ID, Reference #" class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-display font-bold text-gray-800 text-lg mb-4">Notes</h3>
                        <textarea name="notes" rows="3" placeholder="Additional notes..." class="w-full text-sm bg-gray-50 rounded-lg px-4 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3">
                        <a href="{{ route('billings.index') }}" class="flex-1 px-6 py-3 text-center text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">Cancel</a>
                        <button type="submit" class="flex-1 px-6 py-3 text-center text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">Create Invoice</button>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>

<script>
// Auto-calculate total
function calculateTotal() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = (amount + tax) - discount;
    document.getElementById('total-display').textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

document.getElementById('amount').addEventListener('input', calculateTotal);
document.getElementById('tax_amount').addEventListener('input', calculateTotal);
document.getElementById('discount_amount').addEventListener('input', calculateTotal);

// Auto-fill amount when plan is selected
document.getElementById('subscription_rate_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const price = option.dataset.price;
    if (price) {
        document.getElementById('amount').value = price;
        calculateTotal();
    }
});

// Client data - embedded from server
const clientsData = {
    @foreach($clients as $client)
    "{{ $client->id }}": {
        name: "{{ $client->name }}",
        email: "{{ $client->email }}",
        phone: "{{ $client->phone_number }}",
        pppoe: "{{ $client->pppoe_name }}",
        barangay: "{{ $client->barangay }}",
        plan_id: "{{ $client->subscription_rate_id }}",
        plan_name: "{{ $client->subscriptionRate ? $client->subscriptionRate->plan_name : 'No Plan' }}",
        plan_price: "{{ $client->subscriptionRate ? $client->subscriptionRate->monthly_fee : 0 }}"
    },
    @endforeach
};

// Auto-fill when client is selected
document.getElementById('client_id').addEventListener('change', function() {
    const clientId = this.value;
    const clientDetails = document.getElementById('client-details');
    
    if (clientId && clientsData[clientId]) {
        const client = clientsData[clientId];
        
        // Show client details
        clientDetails.classList.remove('hidden');
        document.getElementById('detail-pppoe').textContent = client.pppoe;
        document.getElementById('detail-phone').textContent = client.phone;
        document.getElementById('detail-baranggay').textContent = client.barangay;
        document.getElementById('detail-plan').textContent = client.plan_name;
        
        // Auto-select the client's subscription plan
        const planSelect = document.getElementById('subscription_rate_id');
        if (client.plan_id) {
            planSelect.value = client.plan_id;
            // Auto-fill amount with plan price
            if (client.plan_price && client.plan_price > 0) {
                document.getElementById('amount').value = client.plan_price;
                calculateTotal();
            }
        }
    } else {
        clientDetails.classList.add('hidden');
    }
});

// Check if there's a pre-selected client (from URL parameter)
window.addEventListener('DOMContentLoaded', function() {
    const clientSelect = document.getElementById('client_id');
    if (clientSelect.value) {
        clientSelect.dispatchEvent(new Event('change'));
    }
calculateTotal();
});

// Sidebar toggle
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
</script>
</body>
</html>
