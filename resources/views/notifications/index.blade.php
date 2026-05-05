<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — ISP Admin</title>
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

        /* Notifications specific styling */
        .notification-item {
            transition: all 0.2s ease;
            animation: fadeUp 0.3s ease both;
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
        .mark-read-btn {
            transition: all 0.2s ease;
        }
        .mark-read-btn:hover {
            transform: scale(1.05);
            color: #ef4444;
        }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
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

<!-- ═══════════════════ MODERN SIDEBAR (ISP ADMIN) ═══════════════════ -->

@include('partials.sidebar')


<!-- ===================== MAIN CONTENT (NOTIFICATIONS PAGE) ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR with Notifications header -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Notifications</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.658 6 8.009 6 10v4.158a2.032 2.032 0 01-.595 1.417L5 17h5m6 0a1 1 0 01-1 1h-1m-5 0v-4a1 1 0 011-1h1"/>
                </svg>
                View and manage your notifications
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <input type="text" id="notification-search" placeholder="Search notifications..." 
                    class="w-64 text-sm bg-gray-100 rounded-xl pl-9 pr-4 py-2 text-gray-700 placeholder-gray-400 border-0 focus:outline-none focus:bg-white transition-all"/>
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            @if($unreadCount > 0)
            <button onclick="markAllRead()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
                    style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Mark all as read
            </button>
            @endif
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Notifications List (Scrollable) -->
    <main class="flex-1 main-scroll p-6 space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($notifications as $notification)
            <div class="notification-item px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-all {{ $notification->is_read ? 'opacity-70' : '' }}" data-notification-id="{{ $notification->id }}" data-title="{{ $notification->title }}" data-message="{{ $notification->message }}">
                <div class="flex items-start gap-4">
                    @if(!$notification->is_read)
                    <div class="w-2.5 h-2.5 bg-red-500 rounded-full mt-2 flex-shrink-0 shadow-sm shadow-red-200"></div>
                    @else
                    <div class="w-2.5 h-2.5 bg-gray-300 rounded-full mt-2 flex-shrink-0"></div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <h3 class="font-semibold text-gray-800 text-[15px]">{{ $notification->title }}</h3>
                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-full">{{ $notification->created_at->format('M j, g:i A') }}</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-1.5 leading-relaxed">{{ $notification->message }}</p>
                        
@if($notification->data && isset($notification->data['job_id']))
                        <div class="mt-3">
                            <a href="{{ route('technicians.jobs') }}?search=" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-500 hover:text-red-700 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                View Job #{{ $notification->data['job_id'] }}
                            </a>
                        </div>
                        @endif
                        
                        {{-- Show photo proof if uploaded --}}
                        @if($notification->data && isset($notification->data['photo']))
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-gray-500 mb-1">📷 Proof Photo:</p>
                            <img src="{{ asset('storage/' . $notification->data['photo']) }}" 
                                 alt="Work completion photo" 
                                 class="w-32 h-24 object-cover rounded-lg border border-gray-200"
                                 onclick="window.open('{{ asset('storage/' . $notification->data['photo']) }}', '_blank')"
                                 style="cursor:pointer;"
                                 title="Click to view full size">
                        </div>
                        @endif
                        
                        {{-- Show completion notes if available --}}
                        @if($notification->data && isset($notification->data['completion_notes']))
                        <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded-lg">
                            <strong>Notes:</strong> {{ $notification->data['completion_notes'] }}
                        </div>
                        @endif
                    </div>
                    
                    @if(!$notification->is_read)
                    <button onclick="markAsRead({{ $notification->id }})" 
                            class="mark-read-btn text-xs font-medium text-gray-400 hover:text-red-500 transition-colors px-2 py-1 rounded-md hover:bg-red-50">
                        Mark read
                    </button>
                    @endif
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!$notification->is_read)
                        <button onclick="markAsRead({{ $notification->id }}, this)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                style="color:#059669;background:#ecfdf5;border:1px solid #d1fae5;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mark Read
                        </button>
                        @endif
                        <button onclick="viewNotification({{ $notification->id }}, '{{ addslashes($notification->title) }}', '{{ addslashes($notification->message) }}', '{{ $notification->created_at->format('M j, Y g:i A') }}')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                style="color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View
                        </button>
                        <button onclick="deleteNotification({{ $notification->id }}, this)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all hover:shadow-sm"
                                style="color:#dc2626;background:#fee2e2;border:1px solid #fecaca;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.658 6 8.009 6 10v4.158a2.032 2.032 0 01-.595 1.417L5 17h5m6 0a1 1 0 01-1 1h-1m-5 0v-4a1 1 0 011-1h1"/>
                    </svg>
                </div>
                <p class="font-display font-bold text-gray-800 text-lg mb-2">No notifications yet</p>
                <p class="text-gray-400 text-sm max-w-xs">When you receive notifications, they'll appear here.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4 flex items-center justify-between flex-wrap gap-3">
            <p class="text-[12px] text-gray-500">
                Showing <span class="font-semibold text-gray-800">{{ $notifications->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $notifications->lastItem() }}</span>
                of <span class="font-semibold text-gray-800">{{ $notifications->total() }}</span> notifications
            </p>
            {{ $notifications->links() }}
        </div>
        @endif
    </main>
</div>


@include('partials.sidebar-js')

<!-- ===================== VIEW NOTIFICATION MODAL ===================== -->
<div id="view-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-md" style="animation:modalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.658 6 8.009 6 10v4.158a2.032 2.032 0 01-.595 1.417L5 17h5m6 0a1 1 0 01-1 1h-1m-5 0v-4a1 1 0 011-1h1"/></svg>
            </div>
            <div class="flex-1">
                <h3 id="modal-title" class="font-display font-bold text-gray-900 text-base"></h3>
                <p id="modal-date" class="text-gray-400 text-xs mt-0.5"></p>
            </div>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p id="modal-message" class="text-gray-600 text-sm leading-relaxed bg-gray-50 rounded-xl p-4"></p>
        <div class="mt-4 flex justify-end">
            <button onclick="closeViewModal()" class="px-5 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Close</button>
        </div>
    </div>
</div>

<style>@keyframes modalPop{from{opacity:0;transform:scale(.95) translateY(-10px)}to{opacity:1;transform:scale(1) translateY(0)}}</style>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function markAsRead(id, btn) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(() => {
            const row = document.querySelector(`[data-notification-id="${id}"]`);
            if (row) {
                row.classList.add('opacity-70');
                row.querySelector('.w-2\.5.h-2\.5.bg-red-500')?.classList.replace('bg-red-500','bg-gray-300');
            }
            if (btn) btn.closest('.flex.items-center.gap-2')?.querySelector('[onclick^="markAsRead"]')?.remove();
        });
    }

    function viewNotification(id, title, message, date) {
        document.getElementById('modal-title').textContent   = title;
        document.getElementById('modal-message').textContent = message;
        document.getElementById('modal-date').textContent    = date;
        const modal = document.getElementById('view-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // auto mark as read
        fetch(`/notifications/${id}/read`, {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
    }

    function closeViewModal() {
        const modal = document.getElementById('view-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('view-modal').addEventListener('click', function(e) {
        if (e.target === this) closeViewModal();
    });

    function deleteNotification(id, btn) {
        if (!confirm('Delete this notification?')) return;
        fetch(`/notifications/${id}`, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                const row = document.querySelector(`[data-notification-id="${id}"]`);
                if (row) { row.style.transition = 'all .3s'; row.style.opacity = '0'; row.style.height = '0'; setTimeout(() => row.remove(), 300); }
            }
        });
    }

    function markAllRead() {
        fetch('/notifications/read-all', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(() => location.reload());
    }

    // Live search
    document.getElementById('notification-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.notification-item').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>

</body>
</html>