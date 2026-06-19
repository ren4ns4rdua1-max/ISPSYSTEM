<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Support Ticket - Technician</title>
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
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 800; color: #374151; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; outline: none; box-sizing: border-box; }
        .form-input:focus { border-color: #dc2626; }
        .form-textarea { width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; outline: none; resize: vertical; box-sizing: border-box; }
        .form-textarea:focus { border-color: #dc2626; }
        .error-message { color: #dc2626; font-size: 11px; margin-top: 4px; }
        .btn-primary { padding: 14px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-primary:hover { box-shadow: 0 8px 16px rgba(220,38,38,0.35); transform: translateY(-2px); }
        .btn-secondary { padding: 14px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; background: #f1f5f9; color: #0f172a; border: 1px solid #e2e8f0; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
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
        <div><h1 class="font-display font-black text-2xl tracking-tight text-gray-900">Create Ticket</h1><p class="text-red-600 text-xs font-bold mt-0.5">➕ Create a new support ticket for a client</p></div>
        <div class="flex items-center gap-4">
            <div class="avatar">{{ Auth::user()->name[0] ?? 'T' }}</div>
        </div>
    </header>

    <div class="main-scroll">
        <div class="max-w-3xl">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="font-display font-bold text-xl text-gray-800">➕ New Support Ticket</h2>
                    <p class="text-gray-400 text-xs mt-2">Create a ticket and assign it to yourself</p>
                </div>

                <div style="padding:28px;">
                    <form method="POST" action="{{ route('technician.tickets.store') }}">
                        @csrf

                        <!-- Client Selection -->
                        <div class="form-group">
                            <label class="form-label">Select Client *</label>
                            <select name="client_id" required class="form-input">
                                <option value="">-- Choose a client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')<p class="error-message">{{ $message }}</p>@enderror
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" required placeholder="Brief description of the issue" value="{{ old('subject') }}" class="form-input">
                            @error('subject')<p class="error-message">{{ $message }}</p>@enderror
                        </div>

                        <!-- Priority -->
                        <div class="form-group">
                            <label class="form-label">Priority *</label>
                            <select name="priority" required class="form-input">
                                <option value="">-- Select priority --</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')<p class="error-message">{{ $message }}</p>@enderror
                        </div>

                        <!-- Message -->
                        <div class="form-group">
                            <label class="form-label">Message / Description *</label>
                            <textarea name="message" required rows="6" placeholder="Describe the issue in detail..." class="form-textarea">{{ old('message') }}</textarea>
                            @error('message')<p class="error-message">{{ $message }}</p>@enderror
                        </div>

                        <!-- Action Buttons -->
                        <div style="display:flex;gap:12px;">
                            <button type="submit" class="btn-primary" style="flex:1;">✅ Create Ticket</button>
                            <a href="{{ route('technician.tickets.index') }}" class="btn-secondary" style="flex:1;">← Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
