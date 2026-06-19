<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        body { overflow: hidden; height: 100vh; }
        #sidebar { transition: width 0.35s cubic-bezier(0.2,0.9,0.4,1.1); background: linear-gradient(180deg,#0a0c18 0%,#0f111e 100%); position: fixed; top:0; left:0; bottom:0; z-index:50; }
        #main-content { transition: margin-left 0.35s cubic-bezier(0.2,0.9,0.4,1.1); margin-left:260px; width:calc(100% - 260px); height:100vh; overflow:hidden; display:flex; flex-direction:column; }
        @media(max-width:1023px){#sidebar{transform:translateX(-100%);transition:transform .3s ease-in-out;}#sidebar.mobile-open{transform:translateX(0);}#main-content{margin-left:0;width:100%;}.mobile-menu-btn{display:block!important;}}
        .collapsible{transition:opacity .25s ease,max-width .3s ease;overflow:hidden;white-space:nowrap;}
        .sidebar-collapsed .collapsible{opacity:0;max-width:0!important;pointer-events:none;}
        .sidebar-collapsed .nav-item-inner{justify-content:center;padding-left:.75rem;padding-right:.75rem;}
        .sidebar-collapsed .sec-lbl{opacity:0;height:0;margin:0;padding:0;overflow:hidden;}
        .nav-active-bar{position:absolute;left:0;top:50%;transform:translateY(-50%);width:3px;height:60%;border-radius:0 6px 6px 0;background:linear-gradient(180deg,#ef4444,#f97316);box-shadow:0 0 6px rgba(239,68,68,.6);}
        .nav-item-inner{transition:all .2s cubic-bezier(0.2,0.9,0.4,1.1);position:relative;}
        .nav-item-inner:hover{background:rgba(255,255,255,.08);transform:translateX(4px);}
        .sec-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.13em;color:rgba(255,255,255,.22);font-family:'Syne',sans-serif;padding:6px 10px 4px;margin-top:8px;transition:all .2s;}
        .nav-tooltip{position:absolute;left:calc(100% + 12px);top:50%;transform:translateY(-50%);background:#1e293b;color:#f1f5f9;font-size:12px;font-weight:600;padding:5px 12px;border-radius:10px;white-space:nowrap;pointer-events:none;opacity:0;transition:opacity .2s ease;z-index:999;box-shadow:0 8px 20px rgba(0,0,0,.2);}
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip{opacity:1;}
        .nav-tooltip::before{content:'';position:absolute;right:100%;top:50%;transform:translateY(-50%);border:6px solid transparent;border-right-color:#1e293b;}
        .submenu{max-height:0;overflow:hidden;transition:max-height .3s cubic-bezier(.4,0,.2,1);margin-left:2rem;}
        .submenu.open{max-height:300px;}
        .submenu-item{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:10px;font-size:.75rem;font-weight:500;color:#9ca3af;text-decoration:none;transition:all .2s;}
        .submenu-item:hover{color:#fca5a5;background:rgba(255,255,255,.05);transform:translateX(3px);}
        .chevron-icon{transition:transform .3s ease;}
        .chevron-icon.rotated{transform:rotate(90deg);}
        .topbar{background:rgba(255,255,255,.92);backdrop-filter:blur(20px);border-bottom:1px solid rgba(0,0,0,.05);box-shadow:0 2px 12px rgba(0,0,0,.02);flex-shrink:0;}
        .avatar-grad{background:linear-gradient(125deg,#dc2626,#f97316,#ec4899);background-size:200% 200%;animation:shimmerAvatar 4s ease infinite;}
        @keyframes shimmerAvatar{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-scroll{overflow-y:auto;scrollbar-width:thin;}
        .main-scroll::-webkit-scrollbar{width:6px;}
        .main-scroll::-webkit-scrollbar-track{background:#f1f5f9;border-radius:10px;}
        .main-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px;}
        .trow{transition:background .15s ease,transform .2s ease;animation:fadeUp .35s ease both;}
        .trow:hover{background:#fef2f2;transform:scale(1.002);}
        @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .badge-cash{background:#ecfdf5;color:#059669;border:1px solid #d1fae5;}
        .badge-bank_transfer{background:#dbeafe;color:#2563eb;border:1px solid #bfdbfe;}
        .badge-gcash{background:#fef3c7;color:#d97706;border:1px solid #fde68a;}
        .badge-paymaya{background:#fce7f3;color:#db2777;border:1px solid #fbcfe8;}
        .badge-cheque{background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;}
        .action-btn{transition:all .2s ease;}
        .action-btn:hover{transform:translateY(-2px);}
        .mobile-menu-btn{position:fixed;top:1rem;left:1rem;z-index:60;display:none;}
        nav[aria-label="Pagination"] span,nav[aria-label="Pagination"] a{border-radius:10px!important;font-size:13px!important;font-weight:600!important;transition:all .2s ease;}
        nav[aria-label="Pagination"] a:hover{background:#fee2e2!important;color:#dc2626!important;}
        .search-input:focus{box-shadow:0 0 0 3px rgba(220,38,38,.15);}
        @keyframes modalPop{from{opacity:0;transform:scale(.95) translateY(-10px)}to{opacity:1;transform:scale(1) translateY(0)}}
    </style>
</head>
<body class="bg-slate-100">

<div class="mobile-menu-btn">
    <button onclick="toggleMobileSidebar()" class="p-2.5 rounded-xl bg-white shadow-lg text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</div>
<div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeMobileSidebar()"></div>

@include('partials.sidebar')

<div id="main-content" class="flex flex-col flex-1 min-h-screen">
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Payments</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                View and manage payment records
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <form method="GET" class="flex items-center">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search client..."
                        class="search-input w-64 text-sm bg-gray-100 rounded-xl pl-9 pr-4 py-2 text-gray-700 placeholder-gray-400 border-0 focus:outline-none focus:bg-white transition-all"/>
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
            </div>
            <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Payment
            </a>
        </div>
    </header>

    <main class="flex-1 main-scroll p-6 space-y-5">

        @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 rounded-xl border" style="background:#f0fdf4;border-color:#bbf7d0;">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
            <p class="text-emerald-800 text-sm font-semibold">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-4 rounded-xl border" style="background:#fef2f2;border-color:#fecaca;">
            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>
            <p class="text-red-800 text-sm font-semibold">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Total Collected</p>
                        <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['total_collected'],2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Today's Collection</p>
                        <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['today_collection'],2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">This Month</p>
                        <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['this_month'],2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Pending Approval</p>
                        <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">{{ $pendingApprovalCount }}</p>

                        <span class="text-xs text-orange-600 font-medium mt-2 inline-block">Awaiting verification</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Search Client</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Client name or receipt..."
                           class="search-input w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Payment Method</label>
                    <select name="payment_method" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">All Methods</option>
                        <option value="cash" {{ $paymentMethod=='cash'?'selected':'' }}>Cash</option>
                        <option value="bank_transfer" {{ $paymentMethod=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                        <option value="gcash" {{ $paymentMethod=='gcash'?'selected':'' }}>GCash</option>
                        <option value="paymaya" {{ $paymentMethod=='paymaya'?'selected':'' }}>PayMaya</option>
                        <option value="cheque" {{ $paymentMethod=='cheque'?'selected':'' }}>Cheque</option>
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
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl hover:shadow-md transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">Filter</button>
                @if($search || $paymentMethod || $dateFrom || $dateTo)
                    <a href="{{ route('payments.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">Clear</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($payments->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="font-display font-bold text-gray-800 text-lg mb-2">No Payments Found</p>
                <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all hover:shadow-lg" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Record First Payment
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background:linear-gradient(90deg,#fef2f2,#fff5f5);">
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Payments</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Paid</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Last Method</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Last Date</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Approval</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($payments as $index => $row)
                        <tr class="trow" style="animation-delay:{{ $index*40 }}ms;">
                            <td class="px-6 py-4">
                                <button onclick="openPaymentHistory({{ $row->client_id }})" class="text-left">
                                    <p class="text-sm font-bold text-gray-900 hover:text-red-600 transition-colors">{{ $row->client->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $row->client->email }}</p>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ $row->total_payments }} {{ Str::plural('payment', $row->total_payments) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-green-600">₱{{ number_format($row->total_paid,2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium badge-{{ $row->latest_method }}">{{ $row->latest_method_label }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($row->latest_date)->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->pending_count > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $row->pending_count }} Pending
                                    </span>
                                @elseif($row->latest_approval === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">✓ Approved</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">✗ Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="openPaymentHistory({{ $row->client_id }})"
                                            class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg"
                                            style="color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        History
                                    </button>
                                    <a href="{{ route('payments.create', ['client_id' => $row->client_id]) }}"
                                       class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg"
                                       style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        New
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <p class="text-[12px] text-gray-500">
                    Showing <span class="font-semibold text-gray-800">{{ $payments->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $payments->lastItem() }}</span>
                    of <span class="font-semibold text-gray-800">{{ $payments->total() }}</span> clients
                </p>
                {{ $payments->links() }}
            </div>
            @endif
            @endif
        </div>
    </main>
</div>

<!-- Payment History Modal -->
<div id="payment-history-modal" class="fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;" onclick="if(event.target===this)closePaymentHistory()">
    <div class="bg-white rounded-2xl shadow-2xl mx-4 w-full max-w-3xl max-h-[85vh] flex flex-col" style="animation:modalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
            <div>
                <h3 id="ph-client-name" class="font-display font-bold text-gray-900 text-lg"></h3>
                <p id="ph-client-email" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="closePaymentHistory()" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <div id="ph-loading" class="flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </div>
            <div id="ph-content" style="display:none;">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Receipt #</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Approval</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ph-rows" class="divide-y divide-gray-50"></tbody>
                </table>
                <p id="ph-empty" class="text-center text-gray-400 text-sm py-8" style="display:none;">No payment records found.</p>
            </div>
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div id="reject-modal" class="fixed inset-0 z-[60] items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-sm" style="animation:modalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Reject Payment</h3>
                <p class="text-gray-500 text-sm mt-1">Rejecting payment from <span id="reject-client-name" class="font-semibold text-gray-800"></span>.</p>
            </div>
        </div>
        <form id="reject-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Reason (optional)</label>
                <textarea name="reason" rows="3" placeholder="e.g. Unclear screenshot, wrong amount..." class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl hover:shadow-md transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">Reject</button>
            </div>
        </form>
    </div>
</div>

@include('partials.sidebar-js')

<script>
const approvalBadge = {
    pending:  '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">⏳ Pending</span>',
    approved: '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;">✓ Approved</span>',
    rejected: '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">✗ Rejected</span>',
};

function openPaymentHistory(clientId) {
    document.getElementById('payment-history-modal').style.display = 'flex';
    document.getElementById('ph-loading').style.display = 'flex';
    document.getElementById('ph-content').style.display = 'none';

    fetch(`/payments/client/${clientId}/history`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('ph-client-name').textContent = data.client;
            document.getElementById('ph-client-email').textContent = data.email;
            const tbody = document.getElementById('ph-rows');
            tbody.innerHTML = '';
            if (data.history.length === 0) {
                document.getElementById('ph-empty').style.display = 'block';
            } else {
                document.getElementById('ph-empty').style.display = 'none';
                data.history.forEach(p => {
                    const actions = p.approval_status === 'pending'
                        ? `<div class="flex gap-1 justify-end">
                            <form method="POST" action="/payments/${p.id}/approve" style="display:inline;" onsubmit="return confirm('Approve this payment?')"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="text-[11px] font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded hover:bg-green-100">Approve</button></form>
                            <button onclick="openRejectModal(${p.id},'${data.client}')" class="text-[11px] font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded hover:bg-red-100">Reject</button>
                           </div>`
                        : `<a href="/payments/${p.id}" class="text-[11px] font-semibold text-blue-600 hover:underline">View</a>`;
                    tbody.innerHTML += `<tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-bold text-gray-800">${p.receipt_number}</td>
                        <td class="px-4 py-3 text-gray-600">${p.invoice_number}</td>
                        <td class="px-4 py-3 font-bold text-green-600">₱${p.amount}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">${p.payment_method}</span></td>
                        <td class="px-4 py-3 text-gray-600 text-xs">${p.payment_date}</td>
                        <td class="px-4 py-3">${approvalBadge[p.approval_status] || p.approval_status}</td>
                        <td class="px-4 py-3 text-right">${actions}</td>
                    </tr>`;
                });
            }
            document.getElementById('ph-loading').style.display = 'none';
            document.getElementById('ph-content').style.display = 'block';
        });
}

function closePaymentHistory() {
    document.getElementById('payment-history-modal').style.display = 'none';
}

function openRejectModal(paymentId, clientName) {
    document.getElementById('reject-client-name').textContent = clientName;
    document.getElementById('reject-form').action = '/payments/' + paymentId + '/reject';
    document.getElementById('reject-modal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}
</script>
</body>
</html>
