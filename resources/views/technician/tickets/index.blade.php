<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - Technician</title>
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
        .badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 40px; font-size: 11px; font-weight: 700; }
        .badge-open { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-progress { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .badge-resolved { background: #ecfdf7; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-closed { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
        .ticket-priority { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
        .priority-low { background: #d1fae5; color: #065f46; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-high { background: #fee2e2; color: #991b1b; }
        .btn-primary { padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary:hover { box-shadow: 0 8px 16px rgba(220,38,38,0.35); transform: translateY(-2px); }
        .btn-secondary { padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; background: #f2efed; color: #3f2e2a; border: 1px solid #f0dbd6; cursor: pointer; text-decoration: none; }
        .filter-select { border: 1.5px solid #ffcfc8; border-radius: 40px; padding: 8px 16px; font-size: 12px; font-weight: 600; background: white; outline: none; }
    </style>
</head>
<body>

<aside id="sidebar">
    <div class="sidebar-brand flex items-center gap-3">
        <div class="brand-icon">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <p class="font-display font-bold text-white text-[15px] tracking-tight">CRIMSON OPS</p>
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
            My Tasks
        </a>
        <a href="{{ route('technician.history') }}" class="nav-link"><div class="nav-icon"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>History</a>
        <a href="{{ route('technician.tickets.index') }}" class="nav-link active"><div class="nav-icon"><svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m-4 0v2m4-11a2 2 0 00-2-2h-2a2 2 0 00-2 2m12 0a9 9 0 11-18 0 9 9 0 0118 0zm-5 4a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>Support Tickets</a>
    </nav>

    <div class="sidebar-footer p-4 border-t border-red-900/30">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="flex items-center gap-3 p-2 rounded-xl mb-3 bg-white/5">
                <div class="avatar" style="width:38px;height:38px;font-size:13px;">{{ Auth::user()->name[0] ?? 'T' }}</div>
                <div><p class="text-white text-[12px] font-bold">{{ Auth::user()->name }}</p><p class="text-red-300 text-[9px]">{{ Auth::user()->email }}</p></div>
            </div>
            <button type="submit" class="nav-link w-full" style="background:none;"><div class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></div><span>Sign out</span></button>
        </form>
    </div>
</aside>

<div id="main-content">
    <header class="topbar">
        <div><h1 class="font-display font-black text-2xl tracking-tight text-gray-900">Support Tickets</h1><p class="text-red-600 text-xs font-bold mt-0.5">🎫 Manage client support requests</p></div>
        <div class="flex items-center gap-4">
            <div class="avatar">{{ Auth::user()->name[0] ?? 'T' }}</div>
        </div>
    </header>

    <div class="main-scroll">
        <div class="section-card">
            <div class="section-header flex justify-between items-center">
                <div>
                    <h2 class="font-display font-bold text-xl text-gray-800">🎫 Assigned Tickets</h2>
                    <p class="text-gray-400 text-xs">View, troubleshoot, update status, and add solutions</p>
                </div>
                <a href="{{ route('technician.tickets.create') }}" class="btn-primary">+ Create Ticket</a>
            </div>

            @if($tickets->isEmpty())
                <div style="padding:48px;text-align:center;"><p style="font-size:14px;color:#94a3b8;">No support tickets assigned. Create one or wait for new assignments.</p></div>
            @else
                <div style="padding:0;">
                    <table class="data-table w-full">
                        <thead>
                            <tr>
                                <th style="width:25%;">Client</th>
                                <th style="width:30%;">Subject</th>
                                <th style="width:15%;">Priority</th>
                                <th style="width:15%;">Status</th>
                                <th style="width:15%; text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td style="padding:16px 24px;">
                                        <div style="font-weight:800;color:#0f172a;">{{ $ticket->client->name }}</div>
                                        <div style="font-size:12px;color:#64748b;">{{ $ticket->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td style="padding:16px 24px;font-size:13px;color:#0f172a;">{{ $ticket->subject }}</td>
                                    <td style="padding:16px 24px;">
                                        <span class="ticket-priority priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                                    </td>
                                    <td style="padding:16px 24px;">
                                        <span class="badge badge-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                    </td>
                                    <td style="padding:16px 24px;text-align:right;">
                                        <a href="{{ route('technician.tickets.show', $ticket) }}" class="btn-secondary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div style="padding:14px 20px;border-top:1px solid #f8fafc;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:12px;color:#64748b;">Showing {{ $tickets->count() }} of {{ $tickets->total() }}</span>
                        <div>{{ $tickets->links() }}</div>
                    </div>
                @endif
            @endif
        </div>

        @if(!empty($status))
            <div style="margin-top:20px;">
                <a href="{{ route('technician.tickets.index') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;background:#f1f5f9;color:#0f172a;font-weight:800;font-size:13px;text-decoration:none;border:1px solid #e2e8f0;">
                    ← Clear Filter
                </a>
            </div>
        @endif
    </div>
</div>

</body>
</html>
