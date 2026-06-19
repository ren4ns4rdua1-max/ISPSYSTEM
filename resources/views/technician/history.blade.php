<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - Technician</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        :root { --red-primary: #dc2626; --red-dark: #991b1b; }
        body { background: #fef9f9; overflow: hidden; height: 100vh; }
        #sidebar { background: linear-gradient(145deg, #0c0507 0%, #1e0c0c 50%, #2a1215 100%); position: fixed; top: 0; left: 0; bottom: 0; width: 260px; z-index: 50; display: flex; flex-direction: column; box-shadow: 6px 0 28px rgba(0,0,0,0.45); }
        .sidebar-brand { border-bottom: 1px solid rgba(220,38,38,0.35); padding: 22px 18px; }
        .brand-icon { width: 44px; height: 44px; border-radius: 14px; background: radial-gradient(circle at 30% 20%, #ef4444, #7f1d1d); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220,38,38,0.45); }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; color: rgba(255,255,255,0.55); font-size: 13.5px; font-weight: 500; text-decoration: none; transition: all 0.25s; margin: 3px 0; }
        .nav-link:hover { color: white; background: rgba(220,38,38,0.22); transform: translateX(5px); }
        .nav-link.active { color: #fff; background: linear-gradient(90deg, rgba(220,38,38,0.35), rgba(220,38,38,0.05)); border-left: 2px solid #ef4444; }
        .nav-icon { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.06); }
        .nav-section-label { font-size: 10px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(220,38,38,0.55); font-family: 'Syne', sans-serif; padding: 12px 12px 4px; }
        #main-content { margin-left: 260px; width: calc(100% - 260px); height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background: rgba(255, 248, 245, 0.98); border-bottom: 1px solid rgba(220,38,38,0.18); padding: 0 32px; height: 70px; display: flex; align-items: center; justify-content: space-between; }
        .main-scroll { flex: 1; overflow-y: auto; padding: 28px 32px; }
        .avatar { width: 44px; height: 44px; border-radius: 16px; background: linear-gradient(125deg, #dc2626, #ea580c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 16px; }
        .section-card { background: white; border-radius: 28px; border: 1px solid #ffe0dc; box-shadow: 0 8px 20px rgba(0,0,0,0.02); overflow: hidden; }
        .section-header { padding: 22px 28px; border-bottom: 1px solid #fff0ed; background: linear-gradient(98deg, #fffaf8, #ffffff); }
        .data-table thead tr { background: #fff6f3; }
        .data-table thead th { padding: 16px 24px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #b91c1c; }
        .data-table tbody tr { border-top: 1px solid #fff0ed; transition: background 0.2s; }
        .data-table tbody tr:hover { background: #fffbf9; }
        .client-avatar { width: 44px; height: 44px; border-radius: 16px; background: linear-gradient(145deg, #e11d48, #9f1239); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px; }
        .job-badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 40px; font-size: 11px; font-weight: 700; }
        .job-complete { background: #ecfdf7; color: #065f46; border: 1px solid #a7f3d0; }
        .btn-details { padding: 8px 18px; border-radius: 14px; font-size: 12px; font-weight: 700; background: #f2efed; color: #3f2e2a; border: 1px solid #f0dbd6; cursor: pointer; }
    </style>
</head>
<body>

<aside id="sidebar">
    <div class="sidebar-brand flex items-center gap-3">
        <div class="brand-icon">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <p class="font-display font-bold text-white text-[15px]">CRIMSON OPS</p>
            <p class="text-red-300 text-[9px] font-semibold tracking-widest">FIELD SUITE</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-5">
        <p class="nav-section-label">CORE</p>
        <a href="{{ route('technician.dashboard') }}" class="nav-link">
            <div class="nav-icon"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div>
            Dashboard
        </a>
        <p class="nav-section-label">WORKFLOW</p>
        <a href="{{ route('technician.tasks') }}" class="nav-link">
            <div class="nav-icon"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            My Tasks <span style="margin-left:auto;background:#dc2626;color:white;font-size:9px;padding:2px 8px;border-radius:30px;">{{ $stats['pendingJobs'] }}</span>
        </a>
        <a href="{{ route('technician.history') }}" class="nav-link active"><div class="nav-icon"><svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>History</a>
        <a href="{{ route('technician.tickets.index') }}" class="nav-link"><div class="nav-icon"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m-4 0v2m4-11a2 2 0 00-2-2h-2a2 2 0 00-2 2m12 0a9 9 0 11-18 0 9 9 0 0118 0zm-5 4a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>Support Tickets</a>
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
        <div><h1 class="font-display font-black text-2xl tracking-tight text-gray-900">Job History</h1><p class="text-red-600 text-xs font-bold mt-0.5">⚡ {{ $stats['completedJobs'] }} completed jobs</p></div>
        <div class="flex items-center gap-4">
            <div class="avatar">{{ $initials }}</div>
        </div>
    </header>

    <div class="main-scroll">
        <div class="section-card">
            <div class="section-header">
                <h2 class="font-display font-bold text-xl text-gray-800">✅ Completed Jobs</h2>
                <p class="text-gray-400 text-xs">Your completed installation jobs</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>CLIENT</th>
                            <th>TYPE</th>
                            <th>LOCATION</th>
                            <th>COMPLETED</th>
                            <th>NOTES</th>
                            <th class="text-right">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $job)
                        <tr>
                            <td>
                                <div class="flex gap-3">
                                    <div class="client-avatar">{{ substr($job->client->name, 0, 2) }}</div>
                                    <div><p class="font-black text-gray-800">{{ $job->client->name }}</p><p class="text-xs text-gray-400">PPPoE: {{ $job->client->username ?? 'N/A' }}</p></div>
                                </div>
                            </td>
                            <td><span class="job-badge job-complete">{{ $job->job_type_label }}</span></td>
                            <td><span class="text-gray-600 text-sm">{{ $job->client->address ?? 'N/A' }}</span></td>
                            <td class="text-sm font-semibold">{{ $job->completed_at ? $job->completed_at->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td class="text-sm text-gray-500">{{ $job->completion_notes ?? '-' }}</td>
                            <td class="text-right">
                                <button class="btn-details">View Report</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-500">No completed jobs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($history->hasPages())
            <div class="p-4 border-t border-red-50 flex justify-between items-center">
                <span class="text-xs text-gray-400">Showing {{ $history->count() }} of {{ $history->total() }} jobs</span>
                <div class="flex gap-1">{{ $history->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
