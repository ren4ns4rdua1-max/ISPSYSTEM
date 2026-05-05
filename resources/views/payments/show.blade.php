<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details — NetManager</title>
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

        /* Payment method badges */
        .badge-cash { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .badge-bank_transfer { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-gcash { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .badge-paymaya { background: #fce7f3; color: #db2777; border: 1px solid #fbcfe8; }
        .badge-cheque { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        
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
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Payment Details</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View payment details
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6">

        <!-- Success Alert -->
        @if(session('success'))
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
            
            <!-- Left Column (2/3) - Receipt Card -->
            <div class="lg:col-span-2">
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-xs font-semibold uppercase tracking-wider">Receipt Number</p>
                                <p class="font-display font-bold text-2xl mt-1">{{ $payment->receipt_number }}</p>
                            </div>
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Client</p>
                                <p class="font-semibold text-gray-800">{{ $payment->client->name }}</p>
                                <p class="text-gray-500 text-sm">{{ $payment->client->email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Invoice</p>
                                @if($payment->billing)
                                    <a href="{{ route('billings.show', $payment->billing->id) }}" class="font-semibold text-red-600 hover:text-red-800 transition-colors">
                                        {{ $payment->billing->invoice_number }}
                                    </a>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Payment Method</p>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold badge-{{ $payment->payment_method }}">
                                    {{ $payment->payment_method_label }}
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Payment Date</p>
                                <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</p>
                            </div>
                            @if($payment->payment_reference)
                            <div>
                                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Payment Reference</p>
                                <p class="font-semibold text-gray-800">{{ $payment->payment_reference }}</p>
                            </div>
                            @endif
                            @if($payment->notes)
                            <div class="col-span-2">
                                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Notes</p>
                                <p class="text-gray-600 text-sm">{{ $payment->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Amount Summary Footer -->
                    <div class="border-t border-gray-100 p-6 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Amount Paid</span>
                            <span class="font-display font-bold text-xl text-gray-800">₱{{ number_format($payment->amount, 2) }}</span>
                        </div>
                        @if($payment->change_amount > 0)
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Change</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($payment->change_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200 mt-2">
                            <span class="font-semibold text-gray-800">Total Received</span>
                            <span class="font-display font-bold text-2xl text-green-600">₱{{ number_format($payment->total_paid, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3) -->
            <div class="space-y-6">
                
                <!-- Recorded By Card -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5" style="animation-delay: 0.1s;">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Recorded By
                    </h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-semibold shadow-md">
                            {{ $payment->user ? substr($payment->user->name, 0, 1) : '?' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $payment->user ? $payment->user->name : 'Unknown' }}</p>
                            <p class="text-gray-500 text-xs">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5" style="animation-delay: 0.2s;">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('payments.edit', $payment->id) }}" 
                           class="action-btn flex items-center justify-center gap-2 w-full px-4 py-2.5 text-white rounded-xl text-sm font-semibold transition-all shadow-md hover:shadow-lg"
                           style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Payment
                        </a>
                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="action-btn flex items-center justify-center gap-2 w-full px-4 py-2.5 text-red-600 border border-red-200 rounded-xl text-sm font-semibold hover:bg-red-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Payment
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Info Card -->
                <div class="detail-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5" style="animation-delay: 0.3s;">
                    <h3 class="font-display font-bold text-gray-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Quick Info
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 text-sm">Transaction ID</span>
                            <span class="font-semibold text-gray-800 text-xs">{{ $payment->receipt_number }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 text-sm">Payment Status</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                Completed
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>


@include('partials.sidebar-js')

</body>
</html>