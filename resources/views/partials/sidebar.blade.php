{{-- ═══════════════════ SHARED ADMIN SIDEBAR ═══════════════════ --}}
{{-- Usage: @include('partials.sidebar') --}}

@include('partials.sidebar-styles')

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

<aside id="sidebar" style="width:260px;" class="fixed left-0 top-0 h-full z-50 flex flex-col shadow-2xl"
       style="background:linear-gradient(180deg,#0a0c18 0%,#0f111e 100%);">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-4 py-[18px] border-b border-white/[.08] min-h-[68px]">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-red-900/40 hover:scale-105 transition-all duration-300"
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
                class="ml-auto flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-white/10 hover:rotate-180 duration-300"
                style="background:rgba(255,255,255,.05);">
            <svg id="toggle-icon" class="w-[14px] h-[14px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        {{-- OVERVIEW --}}
        <p class="sec-lbl collapsible" style="max-width:200px;">Overview</p>

        @php
            function sidebarLink($route, $label, $icon, $active = false, $badge = null) {
                $isActive = $active || request()->routeIs($route) || request()->routeIs($route . '.*');
                $activeStyle = $isActive
                    ? 'style="background:linear-gradient(135deg,rgba(220,38,38,.12),rgba(185,28,28,.08));border:1px solid rgba(220,38,38,.2);"'
                    : '';
                $textClass = $isActive ? 'text-red-300 font-semibold' : 'text-gray-400 font-medium';
                $iconClass  = $isActive ? 'text-red-400' : 'text-gray-500';
                $activeBar  = $isActive ? '<div class="nav-active-bar"></div>' : '';
                $badgeHtml  = $badge ? "<span class=\"collapsible ml-auto text-[10px] font-bold px-2 py-0.5 rounded-full\" style=\"max-width:50px;{$badge['style']}\">{$badge['text']}</span>" : '';
                $url = route($route);
                return "
                <div class=\"nav-wrapper relative\">
                    <a href=\"{$url}\" class=\"nav-item-inner flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all\" {$activeStyle}>
                        {$activeBar}
                        <div class=\"w-8 h-8 flex items-center justify-center flex-shrink-0\">
                            <svg class=\"w-[17px] h-[17px] {$iconClass}\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">{$icon}</svg>
                        </div>
                        <span class=\"collapsible text-sm {$textClass}\" style=\"max-width:160px;\">{$label}</span>
                        {$badgeHtml}
                    </a>
                    <span class=\"nav-tooltip\">{$label}</span>
                </div>";
            }
        @endphp

        {!! sidebarLink('dashboard', 'Dashboard', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>') !!}

        {{-- MANAGEMENT --}}
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Management</p>

        {!! sidebarLink('clients.index', 'Clients', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
            request()->routeIs('clients.*')) !!}

        {!! sidebarLink('subscription-rates.index', 'Subscription Plans', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
            request()->routeIs('subscription-rates.*')) !!}

        {!! sidebarLink('sales.index', 'Sales', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
            request()->routeIs('sales.*'),
            ['text' => 'New', 'style' => 'background:rgba(16,185,129,.15);color:#6ee7b7;']) !!}

        {!! sidebarLink('billings.index', 'Billing', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
            request()->routeIs('billings.*')) !!}

        {!! sidebarLink('payments.index', 'Payments', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
            request()->routeIs('payments.*')) !!}

        {!! sidebarLink('technicians.index', 'Technicians', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a4 4 0 01-5.4 5.4l-5.6 5.6a2 2 0 102.8 2.8l5.6-5.6a4 4 0 005.4-5.4z"/>',
            request()->routeIs('technicians.*')) !!}

        {{-- REPORTS --}}
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Reports</p>

        {!! sidebarLink('reports.index', 'Reports', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>') !!}

        {{-- ADMINISTRATION --}}
        <p class="sec-lbl collapsible mt-3" style="max-width:200px;padding-top:12px;">Administration</p>

        {!! sidebarLink('users.index', 'User Management', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
            request()->routeIs('users.*')) !!}

        {!! sidebarLink('admin.templates', 'Template Setup', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>') !!}

    </nav>

    {{-- User Footer --}}
    <div class="border-t border-white/[.08] p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/[.06] transition-all">
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
                            class="w-7 h-7 rounded-lg flex items-center justify-center transition-all hover:bg-red-500/20 hover:scale-105">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
