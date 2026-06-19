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
        .nav-tooltip{position:absolute;left:calc(100% + 12px);top:50%;transform:translateY(-50%);background:#1e293b;color:#f1f5f9;font-size:12px;font-weight:600;padding:5px 12px;border-radius:10px;white-space:nowrap;pointer-events:none;opacity:0;transition:opacity .2s ease;z-index:999;box-shadow:0 8px 20px rgba(0,0,0,.2);letter-spacing:.3px;}
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
        .status-paid{background:#ecfdf5;color:#059669;border:1px solid #d1fae5;}
        .status-pending{background:#fef3c7;color:#d97706;border:1px solid #fde68a;}
        .status-overdue{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;}
        .status-partial{background:#dbeafe;color:#2563eb;border:1px solid #bfdbfe;}
        .status-cancelled{background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;}
        .action-btn{transition:all .2s ease;}
        .action-btn:hover{transform:translateY(-2px);}
        .mobile-menu-btn{position:fixed;top:1rem;left:1rem;z-index:60;display:none;}
        nav[aria-label="Pagination"] span,nav[aria-label="Pagination"] a{border-radius:10px!important;font-size:13px!important;font-weight:600!important;transition:all .2s ease;}
        nav[aria-label="Pagination"] a:hover{background:#fee2e2!important;color:#dc2626!important;}
        .search-input:focus{box-shadow:0 0 0 3px rgba(220,38,38,.15);}
        .client-link{cursor:pointer;transition:color .2s;}
        .client-link:hover{color:#dc2626;text-decoration:underline;}
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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Billing Management</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ $billings->total() }} {{ Str::plural('client', $billings->total()) }} with invoices
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
            <a href="{{ route('billings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Invoice
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
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Total Revenue</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['total_revenue'],2) }}</p>
                <span class="text-xs text-green-600 font-medium mt-2 inline-block">All paid invoices</span>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Pending Amount</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['pending_amount'],2) }}</p>
                <span class="text-xs text-yellow-600 font-medium mt-2 inline-block">{{ $stats['pending_count'] }} pending invoices</span>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Overdue Amount</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">₱{{ number_format($stats['overdue_amount'],2) }}</p>
                <span class="text-xs text-red-600 font-medium mt-2 inline-block">{{ $stats['overdue_count'] }} overdue invoices</span>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-[11px] font-semibold uppercase tracking-wider">Total Clients</p>
                <p class="font-display font-extrabold text-gray-900 text-2xl mt-1">{{ $billings->total() }}</p>
                <span class="text-xs text-gray-500 font-medium mt-2 inline-block">With billing records</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Search Client</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Client name or email..."
                           class="search-input w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Status</label>
                    <select name="status" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">All Status</option>
                        <option value="pending" {{ $status=='pending'?'selected':'' }}>Pending</option>
                        <option value="paid" {{ $status=='paid'?'selected':'' }}>Paid</option>
                        <option value="overdue" {{ $status=='overdue'?'selected':'' }}>Overdue</option>
                        <option value="partial" {{ $status=='partial'?'selected':'' }}>Partial</option>
                        <option value="cancelled" {{ $status=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Type</label>
                    <select name="billing_type" class="w-full text-sm bg-gray-50 rounded-xl px-3 py-2.5 border-0 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">All Types</option>
                        <option value="monthly" {{ $billingType=='monthly'?'selected':'' }}>Monthly</option>
                        <option value="quarterly" {{ $billingType=='quarterly'?'selected':'' }}>Quarterly</option>
                        <option value="yearly" {{ $billingType=='yearly'?'selected':'' }}>Yearly</option>
                        <option value="installation" {{ $billingType=='installation'?'selected':'' }}>Installation</option>
                        <option value="reconnection" {{ $billingType=='reconnection'?'selected':'' }}>Reconnection</option>
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
                <a href="{{ route('billings.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">Clear</a>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($billings->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="font-display font-bold text-gray-800 text-lg mb-2">No Billings Found</p>
                <a href="{{ route('billings.create') }}" class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all hover:shadow-lg" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Create First Invoice
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="background:linear-gradient(90deg,#fef2f2,#fff5f5);">
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Invoices</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Billed</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Unpaid</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Latest Due</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($billings as $index => $row)
                        <tr class="trow" style="animation-delay:{{ $index*40 }}ms;">
                            <td class="px-6 py-4">
                                <button onclick="openBillingHistory({{ $row->client_id }})" class="text-left client-link">
                                    <p class="text-sm font-bold text-gray-900 hover:text-red-600 transition-colors">{{ $row->client->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $row->client->email }}</p>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">{{ $row->total_invoices }} {{ Str::plural('invoice', $row->total_invoices) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">₱{{ number_format($row->total_amount,2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->unpaid_amount > 0)
                                    <span class="text-sm font-bold text-red-600">₱{{ number_format($row->unpaid_amount,2) }}</span>
                                @else
                                    <span class="text-sm text-green-600 font-semibold">Fully Paid</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm {{ $row->has_overdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ \Carbon\Carbon::parse($row->latest_due)->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->has_overdue)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-overdue">Overdue</span>
                                @elseif($row->has_pending)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-pending">Pending</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-paid">Paid</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="openBillingHistory({{ $row->client_id }})"
                                            class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg"
                                            style="color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        History
                                    </button>
                                    <a href="{{ route('billings.create', ['client_id' => $row->client_id]) }}"
                                       class="action-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[12px] font-semibold rounded-lg"
                                       style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        New Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($billings->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <p class="text-[12px] text-gray-500">
                    Showing <span class="font-semibold text-gray-800">{{ $billings->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $billings->lastItem() }}</span>
                    of <span class="font-semibold text-gray-800">{{ $billings->total() }}</span> clients
                </p>
                {{ $billings->links() }}
            </div>
            @endif
            @endif
        </div>
    </main>
</div>

<!-- Billing History Modal -->
<div id="billing-history-modal" class="fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;" onclick="if(event.target===this)closeBillingHistory()">
    <div class="bg-white rounded-2xl shadow-2xl mx-4 w-full max-w-3xl max-h-[85vh] flex flex-col" style="animation:modalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100" style="background:linear-gradient(135deg,#fef2f2,#fff5f5);">
            <div>
                <h3 id="bh-client-name" class="font-display font-bold text-gray-900 text-lg"></h3>
                <p id="bh-client-email" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="closeBillingHistory()" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6">
            <div id="bh-loading" class="flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </div>
            <div id="bh-content" style="display:none;">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider rounded-tl-lg">Invoice #</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider rounded-tr-lg">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider rounded-tr-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bh-rows" class="divide-y divide-gray-50"></tbody>
                </table>
                <p id="bh-empty" class="text-center text-gray-400 text-sm py-8" style="display:none;">No billing records found.</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal (kept for use from history modal) -->
<div id="delete-modal" class="fixed inset-0 z-[60] items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-sm" style="animation:modalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-gray-900 text-lg">Delete Invoice</h3>
                <p class="text-gray-500 text-sm mt-1">Delete <span id="delete-billing-number" class="font-semibold text-gray-800"></span>? This cannot be undone.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="closeModal()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
            <form id="delete-form" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

@include('partials.sidebar-js')

<script>
const statusBadge = {
    paid:      '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#ecfdf5;color:#059669;border:1px solid #d1fae5;">Paid</span>',
    pending:   '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#fef3c7;color:#d97706;border:1px solid #fde68a;">Pending</span>',
    overdue:   '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">Overdue</span>',
    partial:   '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#dbeafe;color:#2563eb;border:1px solid #bfdbfe;">Partial</span>',
    cancelled: '<span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;">Cancelled</span>',
};

function openBillingHistory(clientId) {
    document.getElementById('billing-history-modal').style.display = 'flex';
    document.getElementById('bh-loading').style.display = 'flex';
    document.getElementById('bh-content').style.display = 'none';

    fetch(`/billings/client/${clientId}/history`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('bh-client-name').textContent = data.client;
            document.getElementById('bh-client-email').textContent = data.email;
            const tbody = document.getElementById('bh-rows');
            tbody.innerHTML = '';
            if (data.history.length === 0) {
                document.getElementById('bh-empty').style.display = 'block';
            } else {
                document.getElementById('bh-empty').style.display = 'none';
                data.history.forEach(b => {
                    const unpaidActions = ['pending','overdue','partial'].includes(b.status)
                        ? `<a href="/billings/${b.id}/edit" class="text-[11px] font-semibold text-red-600 hover:underline">Edit</a>`
                        : `<a href="/billings/${b.id}" class="text-[11px] font-semibold text-blue-600 hover:underline">View</a>`;
                    tbody.innerHTML += `<tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-bold text-gray-800">${b.invoice_number}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700">${b.billing_type}</span></td>
                        <td class="px-4 py-3 font-bold text-gray-900">₱${b.total_amount}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">${b.due_date}</td>
                        <td class="px-4 py-3">${statusBadge[b.status] || b.status}</td>
                        <td class="px-4 py-3 text-right">${unpaidActions}</td>
                    </tr>`;
                });
            }
            document.getElementById('bh-loading').style.display = 'none';
            document.getElementById('bh-content').style.display = 'block';
        });
}

function closeBillingHistory() {
    document.getElementById('billing-history-modal').style.display = 'none';
}

function confirmDelete(id, number) {
    document.getElementById('delete-billing-number').textContent = number;
    document.getElementById('delete-form').action = '/billings/' + id;
    document.getElementById('delete-modal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('delete-modal').style.display = 'none';
}
</script>
</body>
</html>
