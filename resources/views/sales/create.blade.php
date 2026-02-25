<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale - ISP Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        #sidebar { transition: width 0.3s cubic-bezier(0.4,0,0.2,1); background: linear-gradient(180deg, #0c0e1a 0%, #111827 60%, #0c0e1a 100%); }
        #main-content { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }
        .collapsible { transition: opacity 0.2s ease, max-width 0.3s ease; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }
        .sidebar-collapsed .sec-lbl { opacity: 0; }
        .nav-active-bar { position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 3px; height: 60%; border-radius: 0 4px 4px 0; background: linear-gradient(180deg, #dc2626, #f87171); }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        .topbar { background: rgba(255,255,255,0.88); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-bottom: 1px solid rgba(226,232,240,0.8); }
        .sec-lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .13em; color: rgba(255,255,255,.22); font-family: 'Syne', sans-serif; padding: 3px 10px 5px; transition: opacity .2s; }
        .nav-tooltip { position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%); background: #1f2937; color: #f9fafb; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 8px; white-space: nowrap; pointer-events: none; opacity: 0; transition: opacity 0.15s ease; z-index: 999; }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before { content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%); border: 5px solid transparent; border-right-color: #1f2937; }
        .avatar-grad { background: linear-gradient(135deg, #dc2626 0%, #f87171 50%, #ec4899 100%); }
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

    <!-- User Footer -->
    <div class="border-t border-white/[.06] p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/[.06] transition-colors">
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
                            class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors hover:bg-red-500/20"
                            style="background:rgba(255,255,255,.05);">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<div id="main-content" class="flex flex-col flex-1 min-h-screen" style="margin-left:260px;">
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div><h1 class="font-display font-bold text-gray-900 text-[20px]">New Sale</h="text-gray-1><p class400 text-[12px]">Create client & generate initial billing</p></div>
        <div class="flex items-center gap-3"><a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-200">Back to Sales</a></div>
    </header>
    <main class="flex-1 p-6">
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Client Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">@error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">@error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label><input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">@error('phone_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">PPPoE Name *</label><input type="text" name="pppoe_name" value="{{ old('pppoe_name') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">@error('pppoe_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Barangay *</label><input type="text" name="barangay" value="{{ old('barangay') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">@error('barangay')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">NAP Box *</label><input type="text" name="nap_box" value="{{ old('nap_box') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">@error('nap_box')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label><input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Subscription Plan</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Select Plan</label><select name="subscription_rate_id" id="subscription_rate_id" onchange="updatePlanDetails()" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300"><option value="">-- Select a Plan --</option>@foreach($subscriptionRates as $rate)<option value="{{ $rate->id }}" data-monthly="{{ $rate->monthly_fee }}" data-install="{{ $rate->installation_fee ?? 0 }}">{{ $rate->plan_name }} - ₱{{ number_format($rate->monthly_fee, 2) }}/month</option>@endforeach</select></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Plan Description *</label><input type="text" name="plan_description" id="plan_description" value="{{ old('plan_description') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300"></div>
                            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Due Date/Time *</label><input type="datetime-local" name="due_date_time" value="{{ old('due_date_time') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Initial Billing</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Billing Type *</label><select name="billing_type" id="billing_type" onchange="calculateTotal()" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300"><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="yearly">Yearly</option><option value="installation">Installation</option></select></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Billing Date *</label><input type="date" name="billing_date" value="{{ old('billing_date', now()->toDateString()) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label><input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tax Amount (₱)</label><input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" min="0" step="0.01" onchange="calculateTotal()" class="w-full px-3 py-2 border border-gray-200 rounded-lg"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount (₱)</label><input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="0.01" onchange="calculateTotal()" class="w-full px-3 py-2 border border-gray-200 rounded-lg"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><input type="text" name="notes" value="{{ old('notes') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg"></div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Base Amount</span><span class="font-medium" id="baseAmount">₱0.00</span></div>
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span class="font-medium" id="taxDisplay">₱0.00</span></div>
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="font-medium text-green-600" id="discountDisplay">-₱0.00</span></div>
                            <div class="border-t pt-3 flex justify-between"><span class="font-semibold text-gray-800">Total Amount</span><span class="font-bold text-xl text-red-600" id="totalAmount">₱0.00</span></div>
                        </div>
                        <div class="space-y-3">
                            <button type="submit" name="create_payment" value="0" class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">Create Sale</button>
                            <button type="submit" name="create_payment" value="1" class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">Create & Record Payment</button>
                            <a href="{{ route('sales.index') }}" class="block text-center text-gray-500 text-sm hover:text-gray-700 mt-2">Cancel</a>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
                        <h3 class="font-semibold text-blue-800 text-sm mb-2">Sales Flow</h3>
                        <div class="flex items-center justify-between text-xs text-blue-700"><span>Sales →</span><span>Client →</span><span>Billing →</span><span>Payment</span></div>
                        <p class="text-xs text-blue-600 mt-2">Creating a sale will automatically generate a client record and create the initial billing invoice.</p>
                    </div>
                </div>
            </div>
        </form>
    </main>
</div>
<script>
function updatePlanDetails(){const e=document.getElementById("subscription_rate_id"),t=e.options[e.selectedIndex],n=document.getElementById("plan_description");t.value&&(n.value||(n.value=t.text.split(" - ")[0]),calculateTotal())}function calculateTotal(){const e=document.getElementById("subscription_rate_id"),t=e.options[e.selectedIndex],n=document.getElementById("billing_type").value,r=parseFloat(document.getElementById("tax_amount").value)||0,a=parseFloat(document.getElementById("discount_amount").value)||0,o=parseFloat(t.dataset.monthly)||0,d=parseFloat(t.dataset.install)||0;let s=0;"monthly"===n?s=o:"quarterly"===n?s=3*o:"yearly"===n?s=12*o:"installation"===n&&(s=d);const l=s+r-a;document.getElementById("baseAmount").textContent="₱"+s.toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2}),document.getElementById("taxDisplay").textContent="₱"+r.toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2}),document.getElementById("discountDisplay").textContent="-₱"+a.toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2}),document.getElementById("totalAmount").textContent="₱"+l.toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2})}calculateTotal();let collapsed=!1;function toggleSidebar(){collapsed=!collapsed;const e=document.getElementById("sidebar"),t=document.getElementById("main-content"),n=document.getElementById("toggle-icon");collapsed?(e.style.width="72px",t.style.marginLeft="72px",e.classList.add("sidebar-collapsed"),n.innerHTML='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>'):(e.style.width="260px",t.style.marginLeft="260px",e.classList.remove("sidebar-collapsed"),n.innerHTML='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>')}
</script>
</body>
</html>
