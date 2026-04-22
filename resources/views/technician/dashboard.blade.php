<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        :root {
            --red-primary: #dc2626;
            --red-dark:    #991b1b;
            --red-deeper:  #7f1d1d;
            --red-glow:    rgba(220, 38, 38, 0.35);
        }

        body {
            background: #fafafa;
            overflow: hidden;
            height: 100vh;
        }

        /* ─── Sidebar ───────────────────────────── */
        #sidebar {
            background: linear-gradient(170deg, #0a0505 0%, #1a0808 50%, #200c0c 100%);
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 260px; z-index: 50;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 40px rgba(0,0,0,0.4);
            transition: width 0.35s cubic-bezier(0.2,0.9,0.4,1.1);
        }

        /* subtle grid texture on sidebar */
        #sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(220,38,38,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(220,38,38,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .sidebar-brand {
            border-bottom: 1px solid rgba(220,38,38,0.2);
            padding: 20px 16px;
            position: relative;
        }
        .brand-icon {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, var(--red-primary), var(--red-dark));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px var(--red-glow);
            flex-shrink: 0;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 12px;
            color: rgba(255,255,255,0.5);
            font-size: 13.5px; font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }
        .nav-link:hover {
            color: rgba(255,255,255,0.85);
            background: rgba(220,38,38,0.1);
            transform: translateX(3px);
        }
        .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(220,38,38,0.25), rgba(220,38,38,0.08));
            border: 1px solid rgba(220,38,38,0.3);
        }
        .nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #ef4444, #dc2626);
            box-shadow: 0 0 8px rgba(239,68,68,0.6);
        }
        .nav-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.05);
            transition: background 0.2s;
        }
        .nav-link.active .nav-icon {
            background: rgba(220,38,38,0.25);
        }
        .nav-link:hover .nav-icon {
            background: rgba(220,38,38,0.15);
        }

        .nav-section-label {
            font-size: 9.5px; font-weight: 700;
            letter-spacing: 0.14em; text-transform: uppercase;
            color: rgba(255,255,255,0.2);
            font-family: 'Syne', sans-serif;
            padding: 4px 10px;
            margin: 10px 0 4px;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(220,38,38,0.15);
            padding: 12px;
        }

        /* ─── Main content ──────────────────────── */
        #main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            height: 100vh;
            display: flex; flex-direction: column;
            overflow: hidden;
        }

        /* ─── Topbar ────────────────────────────── */
        .topbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 16px rgba(0,0,0,0.04);
            padding: 0 32px;
            height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
            z-index: 40;
        }

        .topbar-date {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 12px; font-weight: 600;
            padding: 6px 14px; border-radius: 20px;
            letter-spacing: 0.2px;
        }

        .avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, #dc2626, #f97316, #ec4899);
            background-size: 200% 200%;
            animation: avatarShimmer 4s ease infinite;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 15px;
            box-shadow: 0 4px 12px rgba(220,38,38,0.35);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .avatar:hover { transform: scale(1.08); }
        @keyframes avatarShimmer {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ─── Scrollable body ───────────────────── */
        .main-scroll {
            flex: 1; overflow-y: auto;
            padding: 32px;
            scrollbar-width: thin;
            scrollbar-color: #fca5a5 #f1f5f9;
        }
        .main-scroll::-webkit-scrollbar { width: 6px; }
        .main-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }

        /* ─── Stat cards ────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stat-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #f3f4f6;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: all 0.25s cubic-bezier(0.2,0.9,0.4,1.1);
            animation: fadeUp 0.5s ease both;
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 0 0 20px 20px;
        }
        .stat-card.red::after   { background: linear-gradient(90deg, #dc2626, #f87171); }
        .stat-card.amber::after { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
        .stat-card.green::after { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card.gray::after  { background: linear-gradient(90deg, #64748b, #94a3b8); }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .stat-value {
            font-size: 36px; font-weight: 800; line-height: 1;
            font-family: 'Syne', sans-serif;
            color: #111827;
            margin-bottom: 6px;
        }
        .stat-label { font-size: 13px; color: #6b7280; font-weight: 500; }
        .stat-badge {
            position: absolute; top: 20px; right: 20px;
            font-size: 10px; font-weight: 700;
            padding: 4px 10px; border-radius: 20px;
            letter-spacing: 0.3px; text-transform: uppercase;
        }

        /* ─── Section header ────────────────────── */
        .section-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #f3f4f6;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.15s both;
        }
        .section-header {
            padding: 22px 28px;
            border-bottom: 1px solid #f9fafb;
            display: flex; align-items: center; justify-content: space-between;
            background: linear-gradient(90deg, #fff5f5, #fff);
        }

        /* ─── Table ─────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr { background: #fef2f2; }
        .data-table thead th {
            padding: 14px 24px;
            font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: #b91c1c; text-align: left;
        }
        .data-table tbody tr {
            border-top: 1px solid #f9fafb;
            transition: background 0.15s;
        }
        .data-table tbody tr:hover { background: #fff7f7; }
        .data-table tbody td { padding: 16px 24px; }

        .client-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 13px;
            flex-shrink: 0;
        }

        /* Status badges */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-pending .badge-dot  { background: #f59e0b; }
        .badge-progress { background: #dbeafe; color: #1e40af; }
        .badge-progress .badge-dot { background: #3b82f6; }
        .badge-done     { background: #d1fae5; color: #065f46; }
        .badge-done .badge-dot     { background: #10b981; }

        .job-badge {
            display: inline-flex; align-items: center;
            padding: 5px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .job-install   { background: #ede9fe; color: #5b21b6; }
        .job-repair    { background: #fee2e2; color: #991b1b; }
        .job-upgrade   { background: #ecfdf5; color: #065f46; }

        /* Action buttons */
        .btn-start {
            padding: 7px 16px; border-radius: 10px;
            font-size: 12px; font-weight: 700;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white; border: none; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 2px 8px rgba(220,38,38,0.3);
        }
        .btn-start:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(220,38,38,0.4); }

        .btn-details {
            padding: 7px 16px; border-radius: 10px;
            font-size: 12px; font-weight: 700;
            background: #f3f4f6; color: #374151;
            border: none; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-details:hover { background: #e5e7eb; }

        /* ─── Filter select ──────────────────────── */
        .filter-select {
            border: 1.5px solid #fecaca;
            border-radius: 12px; padding: 8px 14px;
            font-size: 13px; font-weight: 600; color: #374151;
            background: #fff; outline: none; cursor: pointer;
            transition: border-color 0.2s;
            appearance: none;
        }
        .filter-select:focus { border-color: #dc2626; }

        /* ─── Welcome banner ─────────────────────── */
        .welcome-banner {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #7f1d1d 100%);
            border-radius: 20px; padding: 28px 32px;
            position: relative; overflow: hidden;
            margin-bottom: 28px;
            animation: fadeUp 0.4s ease both;
            box-shadow: 0 8px 32px rgba(220,38,38,0.3);
        }
        .welcome-banner::before {
            content: '';
            position: absolute; right: -30px; top: -30px;
            width: 200px; height: 200px; border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .welcome-banner::after {
            content: '';
            position: absolute; right: 60px; bottom: -50px;
            width: 150px; height: 150px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .banner-icon {
            width: 52px; height: 52px; border-radius: 16px;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }

        /* ─── Quick action buttons ───────────────── */
        .quick-action {
            background: #fff; border: 1.5px solid #fecaca;
            border-radius: 16px; padding: 16px 20px;
            display: flex; align-items: center; gap: 14px;
            cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .quick-action:hover {
            border-color: #dc2626;
            box-shadow: 0 4px 20px rgba(220,38,38,0.12);
            transform: translateY(-2px);
        }
        .quick-action-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: #fef2f2; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background 0.2s;
        }
        .quick-action:hover .quick-action-icon { background: #fee2e2; }

        /* ─── Empty state ────────────────────────── */
        .empty-state {
            padding: 60px 24px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand flex items-center gap-3">
        <div class="brand-icon">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M14.7 6.3a4 4 0 01-5.4 5.4l-5.6 5.6a2 2 0 102.8 2.8l5.6-5.6a4 4 0 005.4-5.4z"/>
            </svg>
        </div>
        <div>
            <p class="font-display font-bold text-white text-[14px] leading-tight">TECH SUPPORT</p>
            <p class="text-red-400 text-[10px] font-medium tracking-wide">Field Operations</p>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <p class="nav-section-label">Overview</p>

        <a href="{{ route('technician.dashboard') }}" class="nav-link active">
            <div class="nav-icon">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            Dashboard
        </a>

        <p class="nav-section-label">Work</p>

        <a href="#" class="nav-link">
            <div class="nav-icon">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            My Tasks
            <span style="margin-left:auto;background:rgba(220,38,38,0.15);color:#f87171;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;">3</span>
        </a>

        <a href="#" class="nav-link">
            <div class="nav-icon">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            Work History
        </a>

        <p class="nav-section-label">Account</p>

        <a href="#" class="nav-link">
            <div class="nav-icon">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            Profile
        </a>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div class="flex items-center gap-3 p-2 rounded-xl mb-2 hover:bg-white/5 transition-all cursor-pointer">
            <div class="avatar" style="width:36px;height:36px;font-size:13px;border-radius:10px;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div style="min-width:0;flex:1;">
                <p class="text-white text-[12px] font-semibold truncate">{{ Auth::user()->name }}</p>
                <p class="text-red-400/60 text-[10px] truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="nav-link w-full" style="border:none;background:none;cursor:pointer;">
                <div class="nav-icon">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span style="color:rgba(255,255,255,0.5);font-size:13.5px;">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- ═══════════ MAIN CONTENT ═══════════ -->
<div id="main-content">

    <!-- Topbar -->
    <header class="topbar">
        <div>
            <h1 class="font-display font-bold text-gray-900" style="font-size:22px;line-height:1.2;">Technician Dashboard</h1>
            <p class="text-gray-400 text-xs mt-0.5">Welcome back, <span class="text-red-600 font-semibold">{{ Auth::user()->name }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Notification bell -->
            <button style="width:38px;height:38px;border-radius:11px;background:#fef2f2;border:1.5px solid #fecaca;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;transition:all 0.2s;"
                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span style="position:absolute;top:6px;right:6px;width:7px;height:7px;border-radius:50%;background:#dc2626;border:1.5px solid #fef2f2;"></span>
            </button>
            <div class="topbar-date" id="live-date"></div>
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        </div>
    </header>

    <!-- Scrollable body -->
    <div class="main-scroll">

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="banner-icon">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a4 4 0 01-5.4 5.4l-5.6 5.6a2 2 0 102.8 2.8l5.6-5.6a4 4 0 005.4-5.4z"/>
                </svg>
            </div>
            <h2 class="font-display font-bold text-white text-xl mb-1">Ready for today's jobs?</h2>
            <p class="text-red-200 text-sm">You have <span class="font-bold text-white" id="bannerPending">3</span> pending tasks scheduled for today.</p>
            <div style="position:absolute;right:32px;bottom:24px;display:flex;gap:10px;z-index:2;">
                <button style="padding:10px 20px;background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.25);border-radius:12px;font-size:12px;font-weight:700;cursor:pointer;backdrop-filter:blur(10px);transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    View All Tasks
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px;">

            <div class="stat-card red" style="animation-delay:0s;">
                <div class="stat-badge" style="background:#fef2f2;color:#dc2626;">Today</div>
                <div class="stat-icon" style="background:#fef2f2;">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-value" id="todayJobs">0</div>
                <div class="stat-label">Pending Jobs</div>
            </div>

            <div class="stat-card amber" style="animation-delay:0.07s;">
                <div class="stat-badge" style="background:#fffbeb;color:#d97706;">Active</div>
                <div class="stat-icon" style="background:#fffbeb;">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="stat-value" id="inProgressJobs">0</div>
                <div class="stat-label">In Progress</div>
            </div>

            <div class="stat-card green" style="animation-delay:0.14s;">
                <div class="stat-badge" style="background:#ecfdf5;color:#059669;">Done</div>
                <div class="stat-icon" style="background:#ecfdf5;">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="stat-value" id="completedJobs">0</div>
                <div class="stat-label">Completed Today</div>
            </div>

            <div class="stat-card gray" style="animation-delay:0.21s;">
                <div class="stat-badge" style="background:#f8fafc;color:#475569;">All Time</div>
                <div class="stat-icon" style="background:#f8fafc;">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="stat-value" id="totalJobs">0</div>
                <div class="stat-label">Total Jobs</div>
            </div>

        </div>

        <!-- Quick Actions + Tasks (2-col layout) -->
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:28px;">

            <!-- Quick Actions -->
            <div>
                <div class="section-card" style="animation-delay:0.1s;">
                    <div class="section-header">
                        <div>
                            <h3 class="font-display font-bold text-gray-900 text-base">Quick Actions</h3>
                            <p class="text-gray-400 text-xs mt-0.5">Shortcuts for common tasks</p>
                        </div>
                    </div>
                    <div style="padding:16px;display:flex;flex-direction:column;gap:10px;">
                        <a href="#" class="quick-action">
                            <div class="quick-action-icon">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold text-sm">Log New Job</p>
                                <p class="text-gray-400 text-xs">Record a field visit</p>
                            </div>
                        </a>
                        <a href="#" class="quick-action">
                            <div class="quick-action-icon">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold text-sm">Submit Report</p>
                                <p class="text-gray-400 text-xs">End-of-day summary</p>
                            </div>
                        </a>
                        <a href="#" class="quick-action">
                            <div class="quick-action-icon">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold text-sm">Contact Dispatch</p>
                                <p class="text-gray-400 text-xs">Reach the ops team</p>
                            </div>
                        </a>
                        <a href="#" class="quick-action">
                            <div class="quick-action-icon">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold text-sm">View Map</p>
                                <p class="text-gray-400 text-xs">See job locations</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mini progress card -->
            <div class="section-card" style="animation-delay:0.2s;">
                <div class="section-header">
                    <div>
                        <h3 class="font-display font-bold text-gray-900 text-base">Today's Progress</h3>
                        <p class="text-gray-400 text-xs mt-0.5">Job completion overview</p>
                    </div>
                    <div style="background:#fef2f2;border-radius:12px;padding:8px 16px;text-align:center;">
                        <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:22px;color:#dc2626;line-height:1;" id="pctDisplay">0%</p>
                        <p style="font-size:10px;color:#9ca3af;font-weight:600;">Complete</p>
                    </div>
                </div>
                <div style="padding:24px;">
                    <!-- Progress bar -->
                    <div style="background:#f3f4f6;border-radius:99px;height:10px;margin-bottom:20px;overflow:hidden;">
                        <div id="progressBar" style="height:100%;border-radius:99px;background:linear-gradient(90deg,#dc2626,#f87171);width:0%;transition:width 1s cubic-bezier(0.2,0.9,0.4,1.1);box-shadow:0 0 8px rgba(220,38,38,0.4);"></div>
                    </div>

                    <!-- Job breakdown rows -->
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:10px;height:10px;border-radius:3px;background:#f59e0b;flex-shrink:0;"></div>
                                <span style="font-size:13px;color:#374151;font-weight:500;">Pending</span>
                            </div>
                            <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:#111827;" id="pendingCount">3</span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:10px;height:10px;border-radius:3px;background:#3b82f6;flex-shrink:0;"></div>
                                <span style="font-size:13px;color:#374151;font-weight:500;">In Progress</span>
                            </div>
                            <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:#111827;" id="inProgCount">1</span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:10px;height:10px;border-radius:3px;background:#10b981;flex-shrink:0;"></div>
                                <span style="font-size:13px;color:#374151;font-weight:500;">Completed</span>
                            </div>
                            <span style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:#111827;" id="doneCount">12</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Assigned Tasks Table -->
        <div class="section-card" style="animation-delay:0.25s;">
            <div class="section-header">
                <div>
                    <h2 class="font-display font-bold text-gray-900" style="font-size:17px;">Assigned Tasks</h2>
                    <p class="text-gray-400 text-xs mt-0.5">Manage your service jobs for today</p>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <!-- Search -->
                    <div style="position:relative;">
                        <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Search client..."
                               style="padding:8px 12px 8px 34px;border:1.5px solid #fecaca;border-radius:12px;font-size:13px;font-weight:500;color:#374151;background:#fff;outline:none;width:180px;transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#fecaca'">
                    </div>
                    <select class="filter-select">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                    </select>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Job Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Scheduled</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div class="client-avatar">JD</div>
                                    <div>
                                        <p style="font-weight:600;color:#111827;font-size:14px;">Juan Dela Cruz</p>
                                        <p style="font-size:12px;color:#6b7280;">PPPoE: juandc01</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="job-badge job-install">New Installation</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:5px;color:#6b7280;font-size:13px;">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    Brgy. Poblacion
                                </div>
                            </td>
                            <td><span class="badge badge-pending"><span class="badge-dot"></span>Pending</span></td>
                            <td style="font-size:13px;font-weight:600;color:#374151;">Apr 25, 9:00 AM</td>
                            <td style="text-align:right;">
                                <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                    <button class="btn-start">Start</button>
                                    <button class="btn-details">Details</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div class="client-avatar" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);">MR</div>
                                    <div>
                                        <p style="font-weight:600;color:#111827;font-size:14px;">Maria Reyes</p>
                                        <p style="font-size:12px;color:#6b7280;">PPPoE: mariareyes</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="job-badge job-repair">Repair</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:5px;color:#6b7280;font-size:13px;">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    Brgy. San Roque
                                </div>
                            </td>
                            <td><span class="badge badge-progress"><span class="badge-dot"></span>In Progress</span></td>
                            <td style="font-size:13px;font-weight:600;color:#374151;">Apr 25, 10:30 AM</td>
                            <td style="text-align:right;">
                                <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                    <button class="btn-start" style="background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 2px 8px rgba(245,158,11,0.3);">Update</button>
                                    <button class="btn-details">Details</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div class="client-avatar" style="background:linear-gradient(135deg,#0891b2,#67e8f9);">RL</div>
                                    <div>
                                        <p style="font-weight:600;color:#111827;font-size:14px;">Roberto Lim</p>
                                        <p style="font-size:12px;color:#6b7280;">PPPoE: robslim</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="job-badge job-upgrade">Upgrade</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:5px;color:#6b7280;font-size:13px;">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    Brgy. Lahug
                                </div>
                            </td>
                            <td><span class="badge badge-done"><span class="badge-dot"></span>Completed</span></td>
                            <td style="font-size:13px;font-weight:600;color:#374151;">Apr 25, 8:00 AM</td>
                            <td style="text-align:right;">
                                <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                    <button class="btn-details">View Report</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding:16px 24px;border-top:1px solid #f9fafb;display:flex;align-items:center;justify-content:between;gap:8px;">
                <span style="font-size:12px;color:#9ca3af;font-weight:500;flex:1;">Showing 3 of 16 tasks</span>
                <div style="display:flex;gap:6px;">
                    <button style="width:32px;height:32px;border-radius:8px;border:1.5px solid #fecaca;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button style="width:32px;height:32px;border-radius:8px;background:#dc2626;border:none;color:white;font-size:13px;font-weight:700;cursor:pointer;">1</button>
                    <button style="width:32px;height:32px;border-radius:8px;border:1.5px solid #fecaca;background:#fff;font-size:13px;font-weight:600;color:#374151;cursor:pointer;transition:all 0.2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">2</button>
                    <button style="width:32px;height:32px;border-radius:8px;border:1.5px solid #fecaca;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </div><!-- end main-scroll -->
</div><!-- end main-content -->

<script>
// ── Live date ──────────────────────────────────────────────
function updateDate() {
    const now = new Date();
    const opts = { weekday:'short', month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true };
    document.getElementById('live-date').textContent = now.toLocaleDateString('en-US', opts);
}
updateDate();
setInterval(updateDate, 60000);

// ── Animated counters ──────────────────────────────────────
const stats = { todayJobs: 3, inProgressJobs: 1, completedJobs: 12, totalJobs: 156 };

function animateCounter(id, target, duration = 900) {
    const el = document.getElementById(id);
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start = Math.min(start + step, target);
        el.textContent = Math.floor(start).toLocaleString();
        if (start >= target) clearInterval(timer);
    }, 16);
}

// ── Progress bar ───────────────────────────────────────────
function updateProgress() {
    const total    = stats.todayJobs + stats.inProgressJobs + stats.completedJobs;
    const pct      = Math.round((stats.completedJobs / total) * 100);
    setTimeout(() => {
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('pctDisplay').textContent  = pct + '%';
    }, 400);
}

document.addEventListener('DOMContentLoaded', () => {
    animateCounter('todayJobs',      stats.todayJobs);
    animateCounter('inProgressJobs', stats.inProgressJobs);
    animateCounter('completedJobs',  stats.completedJobs);
    animateCounter('totalJobs',      stats.totalJobs, 1400);
    document.getElementById('bannerPending').textContent = stats.todayJobs;
    document.getElementById('pendingCount').textContent  = stats.todayJobs;
    document.getElementById('inProgCount').textContent   = stats.inProgressJobs;
    document.getElementById('doneCount').textContent     = stats.completedJobs;
    updateProgress();
});
</script>
</body>
</html>