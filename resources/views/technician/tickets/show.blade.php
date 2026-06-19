<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - Technician</title>
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
        .badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 40px; font-size: 11px; font-weight: 700; }
        .badge-open { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-progress { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .badge-resolved { background: #ecfdf7; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-closed { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
        .ticket-priority { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
        .priority-low { background: #d1fae5; color: #065f46; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-high { background: #fee2e2; color: #991b1b; }
        .message-box { padding: 16px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 14px; margin-bottom: 16px; }
        .success-box { padding: 16px; background: #ecfdf7; border: 1px solid #a7f3d0; border-radius: 14px; border-left: 4px solid #059669; margin-bottom: 16px; }
        .info-box { padding: 16px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; border-left: 4px solid #2563eb; margin-bottom: 16px; }
        .btn-primary { padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-secondary { padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; background: #f1f5f9; color: #0f172a; border: 1px solid #e2e8f0; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 12px; font-weight: 800; color: #374151; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; outline: none; }
        .form-input:focus { border-color: #dc2626; }
        .form-textarea { width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; outline: none; resize: vertical; }
        .form-textarea:focus { border-color: #dc2626; }
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
        <div><h1 class="font-display font-black text-2xl tracking-tight text-gray-900">Ticket Details</h1><p class="text-red-600 text-xs font-bold mt-0.5">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p></div>
        <div class="flex items-center gap-4">
            <div class="avatar">{{ Auth::user()->name[0] ?? 'T' }}</div>
        </div>
    </header>

    <div class="main-scroll">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Ticket Header -->
                <div class="section-card mb-6">
                    <div class="section-header">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h2 class="font-display font-bold text-2xl text-gray-800 mb-2">{{ $ticket->subject }}</h2>
                                <p style="font-size:12px;color:#64748b;">
                                    Client: <span style="font-weight:800;color:#0f172a;">{{ $ticket->client->name }}</span>
                                    · Created: {{ $ticket->created_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <div style="display:flex;gap:8px;">
                                <span class="ticket-priority priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }} Priority</span>
                                <span class="badge badge-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            </div>
                        </div>
                    </div>

                    <div style="padding:24px;">
                        <!-- Client Message -->
                        <div class="form-group">
                            <label class="form-label">Client Message</label>
                            <div class="message-box">{{ $ticket->message }}</div>
                        </div>

                        <!-- Troubleshooting Notes -->
                        @if($ticket->troubleshooting_notes)
                            <div class="form-group">
                                <label class="form-label">Troubleshooting Notes</label>
                                <div class="info-box">{{ $ticket->troubleshooting_notes }}</div>
                            </div>
                        @endif

                        <!-- Solution -->
                        @if($ticket->solution)
                            <div class="form-group">
                                <label class="form-label">Solution</label>
                                <div class="success-box">{{ $ticket->solution }}</div>
                            </div>
                        @endif

                        <!-- Reply -->
                        @if($ticket->admin_reply)
                            <div class="form-group">
                                <label class="form-label">Your Reply to Client</label>
                                <div class="success-box">
                                    <p style="font-size:11px;font-weight:900;color:#059669;margin-bottom:6px;">Sent: {{ $ticket->replied_at?->format('M d, Y h:i A') ?? '-' }}</p>
                                    <p style="font-size:13px;color:#065f46;">{{ $ticket->admin_reply }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status & Troubleshooting Form -->
                <div class="section-card mb-6">
                    <div class="section-header"><h3 class="font-display font-bold text-lg text-gray-800">Update Status</h3></div>
                    <div style="padding:24px;">
                        <form method="POST" action="{{ route('technician.tickets.updateStatus', $ticket) }}">
                            @csrf
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-input">
                                        <option value="open" {{ $ticket->status==='open'?'selected':'' }}>Open</option>
                                        <option value="in_progress" {{ $ticket->status==='in_progress'?'selected':'' }}>In Progress</option>
                                        <option value="resolved" {{ $ticket->status==='resolved'?'selected':'' }}>Resolved</option>
                                        <option value="closed" {{ $ticket->status==='closed'?'selected':'' }}>Closed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Troubleshooting Notes</label>
                                <textarea name="troubleshooting_notes" rows="4" class="form-textarea" placeholder="Add troubleshooting steps you've taken...">{{ old('troubleshooting_notes', $ticket->troubleshooting_notes) }}</textarea>
                            </div>
                            <button type="submit" class="btn-primary">💾 Save Status</button>
                        </form>
                    </div>
                </div>

                <!-- Solution Form -->
                <div class="section-card mb-6">
                    <div class="section-header"><h3 class="font-display font-bold text-lg text-gray-800">Add Solution</h3></div>
                    <div style="padding:24px;">
                        <form method="POST" action="{{ route('technician.tickets.addSolution', $ticket) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Solution (What fixed the issue)</label>
                                <textarea name="solution" rows="4" class="form-textarea" placeholder="Describe the solution provided...">{{ old('solution', $ticket->solution) }}</textarea>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                                <div class="form-group">
                                    <label class="form-label">Mark as Status</label>
                                    <select name="solution_status" class="form-input">
                                        <option value="">Keep current status</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="open">Open</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#059669,#047857);">✅ Save Solution</button>
                        </form>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="section-card">
                    <div class="section-header"><h3 class="font-display font-bold text-lg text-gray-800">Send Reply to Client</h3></div>
                    <div style="padding:24px;">
                        <form method="POST" action="{{ route('technician.tickets.addReply', $ticket) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Message (visible to client)</label>
                                <textarea name="admin_reply" rows="4" class="form-textarea" placeholder="Type your response to the client...">{{ old('admin_reply') }}</textarea>
                            </div>
                            <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">📧 Send Reply</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Ticket Info -->
                <div class="section-card mb-6">
                    <div class="section-header"><h3 class="font-display font-bold text-lg text-gray-800">Ticket Info</h3></div>
                    <div style="padding:24px;">
                        <div style="margin-bottom:16px;">
                            <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Ticket ID</p>
                            <p style="font-size:13px;color:#0f172a;font-weight:800;">#{{ $ticket->id }}</p>
                        </div>
                        <div style="margin-bottom:16px;">
                            <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Client</p>
                            <p style="font-size:13px;color:#0f172a;font-weight:800;">{{ $ticket->client->name }}</p>
                        </div>
                        <div style="margin-bottom:16px;">
                            <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Priority</p>
                            <span class="ticket-priority priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                        </div>
                        <div style="margin-bottom:16px;">
                            <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Status</p>
                            <span class="badge badge-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        </div>
                        <div style="margin-bottom:16px;">
                            <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Created</p>
                            <p style="font-size:13px;color:#0f172a;">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($ticket->resolved_at)
                            <div style="margin-bottom:16px;">
                                <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Resolved</p>
                                <p style="font-size:13px;color:#0f172a;">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                        @if($ticket->client_confirmed_at)
                            <div style="margin-bottom:16px;">
                                <p style="font-size:11px;font-weight:900;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Confirmed</p>
                                <p style="font-size:13px;color:#0f172a;">{{ $ticket->client_confirmed_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <a href="{{ route('technician.tickets.index') }}" class="btn-secondary w-full" style="text-align:center;">← Back to Tickets</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
