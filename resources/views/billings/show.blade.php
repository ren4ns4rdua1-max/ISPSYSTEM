<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details — NetManager</title>
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
        .status-paid { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .status-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .status-overdue { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-partial { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        
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
        
        /* Detail cards */
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .detail-card { animation: fadeUp 0.4s ease both; }
        
        /* Action buttons */
        .action-btn {
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Invoice Details</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View and manage billing information
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('billings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="{{ route('billings.edit', $billing->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6">

        <!-- Success Alert -->
        @if (session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl border animate-fadeIn mb-6" style="background:#f0fdf4;border-color:#bbf7d0;">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Invoice Info Card -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="font-display font-bold text-gray-900 text-2xl">{{ $billing->invoice_number }}</h2>
                            <p class="text-gray-400 text-sm mt-1">Created {{ $billing->created_at->format('M d, Y') }}</p>
                        </div>
                        @switch($billing->status)
                            @case('paid')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold status-paid">Paid</span>
                                @break
                            @case('pending')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold status-pending">Pending</span>
                                @break
                            @case('overdue')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold status-overdue">Overdue</span>
                                @break
                            @case('partial')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold status-partial">Partial</span>
                                @break
                            @case('cancelled')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold status-cancelled">Cancelled</span>
                                @break
                        @endswitch
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Client</p>
                            <p class="font-semibold text-gray-900">{{ $billing->client->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $billing->client->email }}</p>
                            <p class="text-gray-500 text-sm">{{ $billing->client->phone_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Plan</p>
                            @if($billing->subscriptionRate)
                                <p class="font-semibold text-gray-900">{{ $billing->subscriptionRate->plan_name }}</p>
                                <p class="text-gray-500 text-sm">{{ $billing->subscriptionRate->speed }}</p>
                            @else
                                <p class="text-gray-500 text-sm">No plan selected</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Billing Date</p>
                            <p class="font-semibold text-gray-900">{{ $billing->billing_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Due Date</p>
                            <p class="font-semibold {{ $billing->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">{{ $billing->due_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Billing Type</p>
                            <p class="font-semibold text-gray-900">{{ ucfirst($billing->billing_type) }}</p>
                        </div>
                    </div>

                    @if($billing->notes)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Notes</p>
                            <p class="text-gray-600 text-sm">{{ $billing->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Payment Details (if paid) -->
                @if($billing->status === 'paid' || $billing->paid_date)
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.1s;">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Payment Details
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Paid Date</p>
                            <p class="font-semibold text-gray-900">{{ $billing->paid_date?->format('M d, Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Payment Method</p>
                            <p class="font-semibold text-gray-900">{{ $billing->payment_method ? ucwords(str_replace('_', ' ', $billing->payment_method)) : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Reference #</p>
                            <p class="font-semibold text-gray-900">{{ $billing->payment_reference ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payments for this Invoice -->
                @if($billing->payments->count() > 0)
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay: 0.2s;">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="font-display font-bold text-gray-800 text-base flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Payment Records
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Receipt #</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($billing->payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $payment->receipt_number }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-green-600">₱{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('payments.show', $payment->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all hover:shadow-sm"
                                           style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column (1/3) -->
            <div class="space-y-6">
                
                <!-- Amount Summary -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Amount Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Subtotal</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($billing->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Tax</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($billing->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Discount</span>
                            <span class="font-semibold text-green-600">-₱{{ number_format($billing->discount_amount, 2) }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                            <span class="font-bold text-gray-800">Total</span>
                            <span class="font-bold text-2xl text-red-600">₱{{ number_format($billing->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if($billing->status !== 'paid' && $billing->status !== 'cancelled')
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.1s;">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h3>
                    <form method="POST" action="{{ route('billings.markAsPaid', $billing->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Paid Date</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" name="paid_date" value="{{ date('Y-m-d') }}" class="form-input">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Payment Method</label>
                            <div class="input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <select name="payment_method" class="form-input">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Reference Number (Optional)</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <input type="text" name="payment_reference" placeholder="e.g. Transaction ID" class="form-input">
                            </div>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#059669,#10b981);">
                            Mark as Paid
                        </button>
                    </form>
                </div>
                @endif

                <!-- Meta Information -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.2s;">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Created By</span>
                            <span class="font-semibold text-gray-800">{{ $billing->creator->name ?? 'System' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Created At</span>
                            <span class="font-semibold text-gray-800">{{ $billing->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Last Updated</span>
                            <span class="font-semibold text-gray-800">{{ $billing->updated_at->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delete Action -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6" style="animation-delay: 0.3s;">
                    <button onclick="confirmDelete()" class="w-full px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all">
                        Delete Invoice
                    </button>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Delete Modal -->
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
                <p class="text-gray-500 text-sm mt-1">Are you sure you want to delete this invoice? This action cannot be undone.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="closeModal()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                Cancel
            </button>
            <form method="POST" action="{{ route('billings.destroy', $billing->id) }}" class="flex-1">
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