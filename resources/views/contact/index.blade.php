<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages — ISP Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* Fix body scrolling layout */
        body {
            overflow: hidden;
            height: 100vh;
        }

        /* Modern Sidebar Styling */
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

        .nav-item-inner {
            transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            position: relative;
        }
        .nav-item-inner:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        .sec-lbl {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .13em;
            color: rgba(255,255,255,.22);
            font-family: 'Syne', sans-serif;
            padding: 6px 10px 4px;
            margin-top: 8px;
            transition: all 0.2s;
        }

        .nav-tooltip {
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #f1f5f9; font-size: 12px; font-weight: 600;
            padding: 5px 12px; border-radius: 10px; white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity 0.2s ease; z-index: 999;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 6px solid transparent; border-right-color: #1e293b;
        }

        /* Topbar glass effect */
        .topbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        /* Avatar gradient with shimmer */
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

        /* Main scrollbar */
        .main-scroll {
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .main-scroll::-webkit-scrollbar { width: 6px; }
        .main-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Table row animations */
        .trow { 
            transition: background 0.15s ease, transform 0.2s ease; 
            animation: fadeUp 0.35s ease both;
        }
        .trow:hover { background: #fef2f2; transform: scale(1.002); }
        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

<!-- ═══════════════════ MODERN SIDEBAR (COLLAPSIBLE) ═══════════════════ -->

@include('partials.sidebar')


<!-- ===================== MAIN CONTENT (CONTACT MESSAGES) ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Contact Messages</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Messages sent from the website contact form
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($unread > 0)
                <span id="unread-badge" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-blue-100 text-blue-600">{{ $unread }} unread</span>
            @endif
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY (Scrollable) -->
    <main class="flex-1 main-scroll p-6 space-y-5">
        @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl border animate-fadeIn" style="background:#f0fdf4;border-color:#bbf7d0;">
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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($messages->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-red-50 flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="font-display font-bold text-gray-800 text-lg mb-2">No messages yet</p>
                    <p class="text-gray-400 text-sm max-w-xs">Messages from the website contact form will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background:linear-gradient(90deg,#fef2f2,#fff5f5);">
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($messages as $msg)
                                <tr class="trow cursor-pointer {{ !$msg->is_read ? 'bg-blue-50/30' : '' }}" id="msg-{{ $msg->id }}"
                                    onclick="viewMessage({{ $msg->id }}, '{{ addslashes($msg->name) }}', '{{ addslashes($msg->email) }}', '{{ addslashes($msg->message) }}', '{{ $msg->created_at->format('M d, Y g:i A') }}', {{ $msg->is_read ? 'true' : 'false' }})"
                                    style="animation-delay: {{ $loop->index * 40 }}ms;">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if(!$msg->is_read)
                                                <div class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                                            @endif
                                            <div>
                                                <p class="text-sm {{ !$msg->is_read ? 'font-bold text-gray-900' : 'font-semibold text-gray-700' }}">{{ $msg->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $msg->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs">
                                        <p class="text-sm text-gray-700 line-clamp-2 {{ !$msg->is_read ? 'font-medium' : '' }}">{{ $msg->message }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm text-gray-600">{{ $msg->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $msg->created_at->format('g:i A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap" id="status-{{ $msg->id }}">
                                        @if($msg->is_read)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Read</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-600">Unread</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="viewMessage({{ $msg->id }}, '{{ addslashes($msg->name) }}', '{{ addslashes($msg->email) }}', '{{ addslashes($msg->message) }}', '{{ $msg->created_at->format('M d, Y g:i A') }}', {{ $msg->is_read ? 'true' : 'false' }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all hover:shadow-sm"
                                                    style="color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View
                                            </button>
                                            <form method="POST" action="{{ route('contact.destroy', $msg->id) }}" onsubmit="return confirm('Delete this message?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all hover:shadow-sm"
                                                        style="color:#dc2626;background:#fee2e2;border:1px solid #fecaca;">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($messages->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
                        <p class="text-[12px] text-gray-500">
                            Showing <span class="font-semibold text-gray-800">{{ $messages->firstItem() }}</span>–<span class="font-semibold text-gray-800">{{ $messages->lastItem() }}</span>
                            of <span class="font-semibold text-gray-800">{{ $messages->total() }}</span> messages
                        </p>
                        {{ $messages->links() }}
                    </div>
                @endif
            @endif
        </div>
    </main>
</div>

<style>
    @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .animate-fadeIn { animation: fadeIn 0.3s ease forwards; }
    @keyframes modalPop { from{opacity:0;transform:scale(.95) translateY(-10px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .trow { cursor: pointer; }
</style>

<!-- ===================== VIEW MESSAGE MODAL ===================== -->
<div id="msg-modal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl mx-4 w-full max-w-lg" style="animation:modalPop .25s cubic-bezier(0.2,0.9,0.4,1.1) both;">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-start gap-4" style="background:linear-gradient(90deg,#eff6ff,#fff);">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="msg-modal-name" class="font-display font-bold text-gray-900 text-base truncate"></h3>
                <p id="msg-modal-email" class="text-gray-400 text-xs mt-0.5"></p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span id="msg-modal-date" class="text-gray-400 text-xs"></span>
                <button onclick="closeMsgModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <!-- Modal Body -->
        <div class="px-6 py-5">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Message</p>
            <p id="msg-modal-body" class="text-gray-700 text-sm leading-relaxed bg-gray-50 rounded-xl p-4 whitespace-pre-wrap"></p>
        </div>
        <!-- Modal Footer -->
        <div class="px-6 pb-5 flex justify-end">
            <button onclick="closeMsgModal()" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Close</button>
        </div>
    </div>
</div>

@include('partials.sidebar-js')

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let unreadCount = {{ $unread }};

    function viewMessage(id, name, email, message, date, isRead) {
        // Populate modal
        document.getElementById('msg-modal-name').textContent  = name;
        document.getElementById('msg-modal-email').textContent = email;
        document.getElementById('msg-modal-body').textContent  = message;
        document.getElementById('msg-modal-date').textContent  = date;

        // Show modal
        const modal = document.getElementById('msg-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Auto mark as read if unread
        if (!isRead) {
            fetch(`/contact-messages/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    // Update row UI
                    const row = document.getElementById('msg-' + id);
                    if (row) {
                        row.classList.remove('bg-blue-50/30');
                        // Remove blue dot
                        row.querySelector('.bg-blue-500')?.remove();
                        // Make name normal weight
                        const nameEl = row.querySelector('td:first-child p:first-child');
                        if (nameEl) { nameEl.classList.remove('font-bold'); nameEl.classList.add('font-semibold', 'text-gray-700'); }
                        // Update status badge
                        const statusCell = document.getElementById('status-' + id);
                        if (statusCell) statusCell.innerHTML = '<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Read</span>';
                    }
                    // Update unread count in header
                    unreadCount = Math.max(0, unreadCount - 1);
                    const badge = document.getElementById('unread-badge');
                    if (badge) {
                        if (unreadCount === 0) badge.remove();
                        else badge.textContent = unreadCount + ' unread';
                    }
                }
            });
        }
    }

    function closeMsgModal() {
        const modal = document.getElementById('msg-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('msg-modal').addEventListener('click', function(e) {
        if (e.target === this) closeMsgModal();
    });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMsgModal(); });
</script>
</body>
</html>