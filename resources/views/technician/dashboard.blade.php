<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>NetManager | Technician Dashboard — Crimson Ops</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        :root {
            --red-primary: #dc2626;
            --red-dark:    #991b1b;
            --red-deeper:  #7f1d1d;
            --red-glow:    rgba(220, 38, 38, 0.35);
            --crimson-pulse: rgba(220,38,38,0.2);
        }

        body {
            background: #fef9f9;
            overflow: hidden;
            height: 100vh;
        }

        /* ─── SIDEBAR (LUXE CRIMSON THEME) ────────────────── */
        #sidebar {
            background: linear-gradient(145deg, #0c0507 0%, #1e0c0c 50%, #2a1215 100%);
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 260px; z-index: 50;
            display: flex; flex-direction: column;
            box-shadow: 6px 0 28px rgba(0,0,0,0.45);
            transition: width 0.35s cubic-bezier(0.2,0.9,0.4,1.1);
            backdrop-filter: blur(2px);
        }
        #sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image: 
                radial-gradient(circle at 20% 40%, rgba(220,38,38,0.08) 2px, transparent 2px),
                linear-gradient(rgba(220,38,38,0.03) 1px, transparent 1px);
            background-size: 28px 28px, 100% 28px;
            pointer-events: none;
        }
        .sidebar-brand {
            border-bottom: 1px solid rgba(220,38,38,0.35);
            padding: 22px 18px;
            position: relative;
        }
        .brand-icon {
            width: 44px; height: 44px; border-radius: 14px;
            background: radial-gradient(circle at 30% 20%, #ef4444, #7f1d1d);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 20px rgba(220,38,38,0.45);
            flex-shrink: 0;
        }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 12px;
            color: rgba(255,255,255,0.55);
            font-size: 13.5px; font-weight: 500;
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
            margin: 3px 0;
        }
        .nav-link:hover {
            color: white;
            background: rgba(220,38,38,0.22);
            transform: translateX(5px);
        }
        .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(220,38,38,0.35), rgba(220,38,38,0.05));
            border-left: 2px solid #ef4444;
            box-shadow: -6px 0 12px -8px var(--red-glow);
        }
        .nav-icon {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.06);
            transition: all 0.2s;
        }
        .nav-link.active .nav-icon { background: rgba(220,38,38,0.35); }
        .nav-section-label {
            font-size: 10px; font-weight: 800;
            letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(220,38,38,0.55);
            font-family: 'Syne', sans-serif;
            padding: 12px 12px 4px;
        }

        /* ─── MAIN LAYOUT ───────────────────────────────── */
        #main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            height: 100vh;
            display: flex; flex-direction: column;
            overflow: hidden;
            background: #fffaf7;
        }

        .topbar {
            background: rgba(255, 248, 245, 0.98);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(220,38,38,0.18);
            box-shadow: 0 2px 14px rgba(0,0,0,0.03);
            padding: 0 32px;
            height: 70px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .topbar-date {
            background: #fff0ed;
            border: 1px solid #fecdca;
            color: #b91c1c;
            font-size: 12px; font-weight: 700;
            padding: 6px 16px; border-radius: 40px;
            letter-spacing: 0.2px;
            box-shadow: inset 0 0 2px #ffeeee, 0 2px 6px rgba(0,0,0,0.02);
        }
        .avatar {
            width: 44px; height: 44px; border-radius: 16px;
            background: linear-gradient(125deg, #dc2626, #ea580c, #be123c);
            background-size: 180% 180%;
            animation: avatarFlow 6s ease infinite;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 16px;
            box-shadow: 0 6px 14px rgba(220,38,38,0.4);
            cursor: pointer;
            transition: all 0.2s;
        }
        @keyframes avatarFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .main-scroll {
            flex: 1; overflow-y: auto;
            padding: 28px 32px;
            scrollbar-width: thin;
            scrollbar-color: #f9a8a8 #fee2e2;
        }
        .main-scroll::-webkit-scrollbar { width: 5px; }
        .main-scroll::-webkit-scrollbar-track { background: #fff5f2; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #f87171; border-radius: 10px; }

        /* glow cards */
        .stat-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 22px 20px;
            border: 1px solid #ffe5e2;
            box-shadow: 0 10px 25px -8px rgba(0,0,0,0.05), 0 0 0 1px rgba(220,38,38,0.02);
            transition: all 0.3s cubic-bezier(0.2,0.9,0.4,1.1);
            animation: fadeUp 0.5s ease both;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 4px;
            border-radius: 4px;
        }
        .stat-card.red::after   { background: linear-gradient(90deg, #e11d48, #f97316); }
        .stat-card.amber::after { background: linear-gradient(90deg, #f97316, #fbbf24); }
        .stat-card.green::after { background: linear-gradient(90deg, #059669, #34d399); }
        .stat-card.gray::after  { background: linear-gradient(90deg, #475569, #94a3b8); }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -12px rgba(220,38,38,0.2);
            border-color: #fed7d4;
        }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            background: #fff6f4;
        }
        .stat-value {
            font-size: 38px; font-weight: 800; line-height: 1;
            font-family: 'Syne', sans-serif;
            color: #1e1b1a;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }
        .section-card {
            background: white;
            border-radius: 28px;
            border: 1px solid #ffe0dc;
            box-shadow: 0 8px 20px rgba(0,0,0,0.02);
            overflow: hidden;
            backdrop-filter: blur(2px);
        }
        .section-header {
            padding: 22px 28px;
            border-bottom: 1px solid #fff0ed;
            background: linear-gradient(98deg, #fffaf8, #ffffff);
        }

        /* table elegance */
        .data-table thead tr { background: #fff6f3; }
        .data-table thead th {
            padding: 16px 24px;
            font-size: 11px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: #b91c1c;
        }
        .data-table tbody tr {
            border-top: 1px solid #fff0ed;
            transition: background 0.2s;
        }
        .data-table tbody tr:hover { background: #fffbf9; }
        .client-avatar {
            width: 44px; height: 44px; border-radius: 16px;
            background: linear-gradient(145deg, #e11d48, #9f1239);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 14px;
            box-shadow: 0 4px 10px rgba(220,38,38,0.25);
            flex-shrink: 0;
        }
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 40px;
            font-size: 11px; font-weight: 700;
        }
        .badge-pending  { background: #fffbeb; color: #b45309; border: 1px solid #fed7aa; }
        .badge-progress { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .badge-done     { background: #ecfdf7; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .job-badge {
            display: inline-flex; align-items: center;
            padding: 5px 14px; border-radius: 40px; font-size: 11px; font-weight: 700;
        }
        .job-install   { background: #f3e8ff; color: #6b21a5; border: 1px solid #e9d5ff; }
        .job-repair    { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .job-upgrade   { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

        .btn-start {
            padding: 8px 18px; border-radius: 14px;
            font-size: 12px; font-weight: 700;
            background: linear-gradient(105deg, #dc2626, #b91c1c);
            color: white; border: none; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 2px 8px rgba(220,38,38,0.4);
        }
        .btn-start:hover { transform: translateY(-2px); box-shadow: 0 10px 18px -6px #dc2626; background: linear-gradient(105deg, #ef4444, #dc2626);}
        .btn-details {
            padding: 8px 18px; border-radius: 14px;
            font-size: 12px; font-weight: 700;
            background: #f2efed; color: #3f2e2a;
            border: 1px solid #f0dbd6;
            cursor: pointer;
        }
        .quick-action {
            background: #ffffff; border: 1.5px solid #ffdbd5;
            border-radius: 24px; padding: 14px 16px;
            display: flex; align-items: center; gap: 14px;
            cursor: pointer; transition: all 0.25s;
            text-decoration: none;
        }
        .quick-action:hover {
            border-color: #dc2626; box-shadow: 0 12px 22px -12px rgba(220,38,38,0.3);
            transform: scale(1.01) translateY(-2px);
            background: #fffbfa;
        }
        .quick-action-icon {
            width: 48px; height: 48px; border-radius: 20px;
            background: #fef2ef; display: flex; align-items: center; justify-content: center;
        }

        /* welcome banner redesign */
        .welcome-banner {
            background: radial-gradient(circle at 10% 20%, #b91c1c 0%, #4c0519 100%);
            border-radius: 28px; padding: 28px 32px;
            position: relative; overflow: hidden;
            margin-bottom: 28px;
            box-shadow: 0 18px 35px -12px rgba(180, 20, 20, 0.45);
        }
        .welcome-banner::before {
            content: '⚡';
            position: absolute; right: 20px; top: 20px;
            font-size: 70px; opacity: 0.08;
            font-weight: 100;
        }

        .filter-select {
            border: 1.5px solid #ffcfc8;
            border-radius: 40px; padding: 8px 18px;
            font-size: 12px; font-weight: 600; 
            background: white; outline: none;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- SIDEBAR enhanced with brand depth -->
<aside id="sidebar">
    <div class="sidebar-brand flex items-center gap-3">
        <div class="brand-icon">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <p class="font-display font-bold text-white text-[15px] tracking-tight">CRIMSON OPS</p>
            <p class="text-red-300 text-[9px] font-semibold tracking-widest">FIELD SUITE</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-5">
        <p class="nav-section-label">CORE</p>
<a href="{{ route('technician.dashboard') }}" class="nav-link active">
            <div class="nav-icon"><svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div>
            Dashboard
        </a>
        <p class="nav-section-label">WORKFLOW</p>
        <a href="{{ route('technician.tasks') }}" class="nav-link"><div class="nav-icon"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>My Tasks <span style="margin-left:auto;background:#dc2626;color:white;font-size:9px;padding:2px 8px;border-radius:30px;">{{ $stats['pendingJobs'] }}</span></a>
        <a href="{{ route('technician.history') }}" class="nav-link"><div class="nav-icon"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>History</a>
       
    </nav>

<div class="sidebar-footer p-4 border-t border-red-900/30">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="flex items-center gap-3 p-2 rounded-xl mb-3 bg-white/5">
                <div class="avatar" style="width:38px;height:38px;font-size:13px;">{{ $initials }}</div>
                <div><p class="text-white text-[12px] font-bold">{{ $techName }}</p><p class="text-red-300 text-[9px]">{{ $techEmail }}</p></div>
            </div>
            <button type="submit" class="nav-link w-full" style="background:none;"><div class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></div><span>Sign out</span></button>
        </form>
    </div>
</aside>

<div id="main-content">
<header class="topbar">
        <div><h1 class="font-display font-black text-2xl tracking-tight text-gray-900">Technician Hub</h1><p class="text-red-600 text-xs font-bold mt-0.5">⚡ Welcome back, <span class="bg-red-50 px-1.5 rounded">{{ explode(' ', $techName)[0] }}</span></p></div>
        <div class="flex items-center gap-4">
            <button class="w-10 h-10 rounded-2xl bg-white border border-red-200 flex items-center justify-center relative hover:bg-red-50 transition"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg><span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-600 rounded-full"></span></button>
            <div class="topbar-date" id="live-date"></div>
            <div class="avatar">{{ $initials }}</div>
        </div>
    </header>

    <div class="main-scroll">
        <!-- Enhanced banner -->
        <div class="welcome-banner">
            <div class="flex flex-col gap-1 relative z-10">
                <span class="text-red-200 text-xs font-semibold tracking-wider">⭐ TODAY'S MISSION</span>
                <h2 class="font-display font-bold text-white text-2xl">Field jobs ready</h2>
                <p class="text-red-100 text-sm max-w-md">You have <strong class="text-white text-lg" id="bannerPending">3</strong> pending appointments — high priority fiber installs.</p>
            </div>
            <div class="absolute right-6 bottom-5 flex gap-3 z-10"><button class="bg-white/20 backdrop-blur px-4 py-2 rounded-xl text-white font-semibold text-xs border border-white/30">VIEW ROUTE</button></div>
        </div>

        <!-- Stats row refined -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <div class="stat-card red"><div class="stat-badge absolute top-4 right-5 bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-1 rounded-full">TODAY</div><div class="stat-icon"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="stat-value" id="todayJobs">0</div><div class="stat-label">Pending Jobs</div></div>
            <div class="stat-card amber"><div class="stat-badge absolute top-4 right-5 bg-amber-50 text-amber-700 text-[10px] font-bold">ACTIVE</div><div class="stat-icon"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div><div class="stat-value" id="inProgressJobs">0</div><div class="stat-label">In Progress</div></div>
            <div class="stat-card green"><div class="stat-badge absolute top-4 right-5 bg-emerald-50 text-emerald-700 text-[10px] font-bold">DONE</div><div class="stat-icon"><svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div><div class="stat-value" id="completedJobs">0</div><div class="stat-label">Completed Today</div></div>
            <div class="stat-card gray"><div class="stat-badge absolute top-4 right-5 bg-slate-100 text-slate-700 text-[10px] font-bold">ALL TIME</div><div class="stat-icon"><svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div><div class="stat-value" id="totalJobs">0</div><div class="stat-label">Total Jobs</div></div>
        </div>

        <!-- Quick Tools + Progress combo -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-1 section-card p-0">
                <div class="section-header"><h3 class="font-display font-bold text-xl text-gray-800">⚡ Quick Tools</h3><p class="text-gray-400 text-xs">Field shortcuts</p></div>
                <div class="p-4 flex flex-col gap-3">
                    <div class="quick-action"><div class="quick-action-icon"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div><div><p class="font-bold text-gray-800 text-sm">Start New Job</p><p class="text-gray-400 text-xs">Create field ticket</p></div></div>
                    <div class="quick-action"><div class="quick-action-icon"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><div><p class="font-bold text-gray-800 text-sm">Submit Report</p><p class="text-gray-400 text-xs">End-of-day summary</p></div></div>
                    <div class="quick-action"><div class="quick-action-icon"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div><div><p class="font-bold text-gray-800 text-sm">Contact Dispatch</p><p class="text-gray-400 text-xs">Live support</p></div></div>
                </div>
            </div>
            <div class="lg:col-span-2 section-card">
                <div class="section-header flex justify-between"><div><h3 class="font-display font-bold text-xl text-gray-800">Today's Pulse</h3><p class="text-gray-400 text-xs">Completion dashboard</p></div><div class="bg-red-50 rounded-2xl px-5 py-2 text-center"><p class="font-display font-black text-3xl text-red-600" id="pctDisplay">0%</p><p class="text-red-400 text-[10px] font-bold">PROGRESS</p></div></div>
                <div class="p-6"><div class="bg-pink-50 rounded-full h-3 w-full overflow-hidden"><div class="h-full bg-gradient-to-r from-red-600 to-orange-400 rounded-full w-0 transition-all duration-1000" id="progressBar"></div></div>
                <div class="flex justify-between mt-6 gap-4"><div class="flex gap-2 items-center"><span class="w-3 h-3 rounded-full bg-amber-500"></span><span class="text-sm font-medium">Pending</span><span class="ml-auto font-black text-gray-800" id="pendingCount">0</span></div><div class="flex gap-2 items-center"><span class="w-3 h-3 rounded-full bg-blue-500"></span><span class="text-sm font-medium">Ongoing</span><span class="font-black text-gray-800" id="inProgCount">0</span></div><div class="flex gap-2 items-center"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="text-sm font-medium">Completed</span><span class="font-black text-gray-800" id="doneCount">0</span></div></div></div>
            </div>
        </div>

        <!-- tasks table refined -->
        <div class="section-card">
            <div class="section-header flex flex-wrap justify-between gap-3"><div><h2 class="font-display font-bold text-xl text-gray-800">📋 Priority Routes</h2><p class="text-gray-400 text-xs">Assigned service tickets</p></div><div class="flex gap-2"><input type="text" placeholder="Search client..." class="border border-red-100 rounded-full py-2 px-4 text-sm w-44 focus:border-red-300 outline-none"><select class="filter-select"><option>All Status</option><option>Pending</option><option>In Progress</option><option>Completed</option></select></div></div>
<tbody>
                @forelse($tasks as $task)
                <tr>
                    <td>
                        <div class="flex gap-3">
                            <div class="client-avatar">{{ substr($task->client->name, 0, 2) }}</div>
                            <div><p class="font-black text-gray-800">{{ $task->client->name }}</p><p class="text-xs text-gray-400">PPPoE: {{ $task->client->username ?? 'N/A' }}</p></div>
                        </div>
                    </td>
                    <td><span class="job-badge job-install">{{ $task->job_type_label }}</span></td>
                    <td><span class="flex gap-1 items-center text-gray-600 text-sm">📍 {{ $task->client->address ?? 'N/A' }}</span></td>
                    <td>
                        <span class="badge badge-{{ $task->status == 'in_progress' ? 'progress' : ($task->status == 'completed' ? 'done' : 'pending') }}">
                            <span class="badge-dot"></span>{{ ucfirst($task->status) }}
                        </span>
                    </td>
                    <td class="text-sm font-semibold">{{ $task->scheduled_date ? $task->scheduled_date->format('M d, Y h:i A') : 'Not scheduled' }}</td>
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">
                            @if($task->status == 'assigned')
                            <button class="btn-start">Start</button>
                            @elseif($task->status == 'in_progress')
                            <button class="btn-start" style="background:#f97316;">Update</button>
                            @elseif($task->status == 'completed')
                            <button class="btn-details">Report</button>
                            @else
                            <button class="btn-start">Start</button>
                            @endif
                            <button class="btn-details">Details</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">No pending tasks.</td></tr>
                @endforelse
            </tbody>

        </div>
    </div>
</div>

<script>
    const updateDate = () => { const d = new Date(); document.getElementById('live-date').innerText = d.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' }); };
    updateDate(); setInterval(updateDate, 60000);
    const stats = { todayJobs: {{ $stats['todayJobs'] }}, inProgressJobs: {{ $stats['inProgressJobs'] }}, completedJobs: {{ $stats['completedJobs'] }}, totalJobs: {{ $stats['totalJobs'] }} };
    function animateCounter(id, target) { let el=document.getElementById(id), curr=0, step=target/50; let t=setInterval(()=>{ curr=Math.min(curr+step, target); el.innerText=Math.floor(curr).toLocaleString(); if(curr>=target)clearInterval(t);}, 18);}
    animateCounter('todayJobs', {{ $stats['todayJobs'] }}); animateCounter('inProgressJobs', {{ $stats['inProgressJobs'] }}); animateCounter('completedJobs', {{ $stats['completedJobs'] }}); animateCounter('totalJobs', {{ $stats['totalJobs'] }});
    document.getElementById('bannerPending').innerText = stats.todayJobs;
    document.getElementById('pendingCount').innerText = stats.todayJobs;
    document.getElementById('inProgCount').innerText = stats.inProgressJobs;
    document.getElementById('doneCount').innerText = stats.completedJobs;
    const totalStat = stats.todayJobs + stats.inProgressJobs + stats.completedJobs;
    const pct = totalStat > 0 ? Math.round((stats.completedJobs / totalStat) * 100) : 0;
    setTimeout(() => { document.getElementById('progressBar').style.width = pct + '%'; document.getElementById('pctDisplay').innerText = pct + '%'; }, 200);
</script>
</body>
</html>