<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - Technician</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 40px; font-size: 11px; font-weight: 700; }
        .badge-pending { background: #fffbeb; color: #b45309; border: 1px solid #fed7aa; }
        .badge-progress { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .badge-done { background: #ecfdf7; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .job-badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 40px; font-size: 11px; font-weight: 700; }
        .job-install { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .job-repair { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .job-upgrade { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .btn-start { padding: 8px 18px; border-radius: 14px; font-size: 12px; font-weight: 700; background: linear-gradient(105deg, #dc2626, #b91c1c); color: white; border: none; cursor: pointer; }
        .btn-details { padding: 8px 18px; border-radius: 14px; font-size: 12px; font-weight: 700; background: #f2efed; color: #3f2e2a; border: 1px solid #f0dbd6; cursor: pointer; }
        /* Map modal */
        #nav-map { height: 380px; width: 100%; border-radius: 14px; overflow: hidden; }
        .tech-dot { width: 18px; height: 18px; background: #2563eb; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 4px rgba(37,99,235,.35); animation: techPulse 1.8s ease-in-out infinite; }
        @keyframes techPulse { 0%,100%{box-shadow:0 0 0 4px rgba(37,99,235,.35);} 50%{box-shadow:0 0 0 10px rgba(37,99,235,.08);} }
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
        <a href="{{ route('technician.tasks') }}" class="nav-link active">
            <div class="nav-icon"><svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            My Tasks <span style="margin-left:auto;background:#dc2626;color:white;font-size:9px;padding:2px 8px;border-radius:30px;">{{ $stats['pendingJobs'] }}</span>
        </a>
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
        <div><h1 class="font-display font-black text-2xl tracking-tight text-gray-900">My Tasks</h1><p class="text-red-600 text-xs font-bold mt-0.5">⚡ {{ $stats['pendingJobs'] }} pending jobs</p></div>
        <div class="flex items-center gap-4">
            <div class="avatar">{{ $initials }}</div>
        </div>
    </header>

    <div class="main-scroll">
        <div class="section-card">
            <div class="section-header flex justify-between items-center">
                <div><h2 class="font-display font-bold text-xl text-gray-800">📋 Assigned Tasks</h2><p class="text-gray-400 text-xs">Your pending and active jobs</p></div>
                <div class="flex gap-2">
                    <select class="filter-select" onchange="window.location='{{ route('technician.tasks') }}?status='+this.value">
                        <option value="">All Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ $status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>CLIENT</th>
                            <th>TYPE</th>
                            <th>LOCATION</th>
                            <th>STATUS</th>
                            <th>SCHEDULE</th>
                            <th class="text-right">ACTION</th>
                        </tr>
                    </thead>
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
                            <td>
                                @php $hasCoords = $task->client->latitude && $task->client->longitude; @endphp
                                <div class="text-sm">
                                    <p class="text-gray-700 font-semibold">{{ $task->client->barangay }}</p>
                                    @if($hasCoords)
                                        <button type="button"
                                            onclick="openNavMap({{ $task->client->latitude }}, {{ $task->client->longitude }}, '{{ addslashes($task->client->name) }}', '{{ addslashes($task->client->barangay) }}', null)"
                                            class="inline-flex items-center gap-1 text-xs text-blue-600 font-semibold mt-0.5 hover:underline">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            View on map
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">No coordinates</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span id="status-badge-{{ $task->id }}" class="badge badge-{{ $task->status == 'in_progress' ? 'progress' : ($task->status == 'completed' ? 'done' : 'pending') }}">
                                    <span class="badge-dot"></span><span class="badge-label">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
                                </span>
                            </td>
<td class="text-sm font-semibold">{{ $task->scheduled_date ? $task->scheduled_date->format('M d, Y h:i A') : 'Not scheduled' }}</td>
                            <td class="text-right">
                                <div class="flex gap-2 justify-end">
                                    @if($task->status === 'assigned')
                                    @php $hasCoords = $task->client->latitude && $task->client->longitude; @endphp
                                    <div id="action-{{ $task->id }}">
                                        <button type="button" class="btn-start"
                                            onclick="startJobWithMap({{ $task->id }}, {{ $task->client->latitude ?? 'null' }}, {{ $task->client->longitude ?? 'null' }}, '{{ addslashes($task->client->name) }}', '{{ addslashes($task->client->barangay) }}')">Start</button>
                                    </div>
                                    @elseif($task->status === 'in_progress')
                                    <div id="action-{{ $task->id }}">
                                        <button onclick="openCompleteModal({{ $task->id }})" class="btn-start" style="background:linear-gradient(105deg,#059669,#047857);">Complete</button>
                                    </div>
                                    @elseif($task->status === 'completed')
                                    <span class="text-xs font-semibold text-emerald-600">✓ Done</span>
                                    @else
                                    <span class="text-xs font-semibold text-amber-600">Pending</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-500">No tasks found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
@if($tasks->hasPages())
            <div class="p-4 border-t border-red-50 flex justify-between items-center">
                <span class="text-xs text-gray-400">Showing {{ $tasks->count() }} of {{ $tasks->total() }} tasks</span>
                <div class="flex gap-1">{{ $tasks->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Complete Job Modal -->
<dialog id="complete-modal" class="modal backdrop:bg-black/60 rounded-2xl p-6 w-full max-w-md">
    <form method="POST" id="complete-form" action="" enctype="multipart/form-data">
        @csrf
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-gray-900 text-lg">Complete Job</h3>
                <p class="text-gray-500 text-sm mt-1">Add completion notes and upload a photo to prove work is done.</p>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Completion Notes</label>
            <textarea name="completion_notes" rows="3" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none" placeholder="Describe what was done..."></textarea>
        </div>
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Proof Photo <span class="text-gray-400 font-normal">(optional)</span></label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-emerald-300 transition-colors" id="photo-drop-area">
                <input type="file" name="photo" id="photo-input" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                <label for="photo-input" class="cursor-pointer">
                    <div class="mb-2">
                        <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-500" id="photo-label">Click to upload photo</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG (max 5MB)</p>
                </label>
            </div>
            <div id="photo-preview" class="hidden mt-3">
                <img id="preview-img" class="w-full h-40 object-cover rounded-xl" src="" alt="Preview">
                <button type="button" onclick="removePhoto()" class="text-xs text-red-500 mt-1">Remove photo</button>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="document.getElementById('complete-modal').close()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md hover:-translate-y-0.5" style="background:linear-gradient(105deg,#059669,#047857);">Mark Complete</button>
        </div>
    </form>
</dialog>

<!-- ===================== NAV MAP MODAL ===================== -->
<div id="nav-map-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.75);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:24px;width:90%;max-width:720px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,.4);overflow:hidden;">
        <!-- Header -->
        <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:20px;height:20px;color:#1d4ed8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p style="font-weight:700;font-size:15px;color:#0f172a;" id="nav-map-title">Client Location</p>
                    <p style="font-size:12px;color:#64748b;" id="nav-map-subtitle">Loading your position...</p>
                </div>
            </div>
            <button onclick="closeNavMap()" style="width:32px;height:32px;border-radius:10px;background:#f1f5f9;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg style="width:16px;height:16px;color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Map -->
        <div style="padding:16px;flex:1;overflow:hidden;">
            <div id="nav-map" style="height:340px;width:100%;border-radius:14px;"></div>
        </div>
        <!-- Footer buttons -->
        <div style="padding:0 16px 16px;display:flex;gap:10px;flex-shrink:0;">
            <a id="google-directions-btn" href="#" target="_blank"
               style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:white;border-radius:14px;font-size:13px;font-weight:700;text-decoration:none;">
                🧭 Open Google Maps Navigation
            </a>
            <button id="confirm-start-btn" onclick="confirmStartJob()"
                    style="flex:1;padding:12px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border:none;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;">
                ✅ Start Job
            </button>
        </div>
    </div>
</div>

<script>
    let navMap = null;
    let currentStartFormId = null;
    let watchId = null;
    let techMarker = null;

    function startJobWithMap(jobId, clientLat, clientLng, clientName, barangay) {
        currentStartFormId = 'start-form-' + jobId;
        openNavMap(clientLat, clientLng, clientName, barangay, jobId);
    }

    function openNavMap(clientLat, clientLng, clientName, barangay, jobId) {
        if (jobId) currentStartFormId = 'start-form-' + jobId;
        document.getElementById('nav-map-modal').style.display = 'flex';
        document.getElementById('nav-map-title').textContent = clientName;
        document.getElementById('nav-map-subtitle').textContent = barangay + ' — Locating you...';

        // Show/hide start button
        document.getElementById('confirm-start-btn').style.display = jobId ? 'block' : 'none';

        setTimeout(() => initNavMap(clientLat, clientLng, clientName, barangay), 80);
    }

    function initNavMap(clientLat, clientLng, clientName, barangay) {
        if (navMap) { navMap.remove(); navMap = null; }
        if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }

        const hasClient = clientLat && clientLng;
        const center = hasClient ? [clientLat, clientLng] : [12.8797, 121.7740];

        navMap = L.map('nav-map').setView(center, hasClient ? 15 : 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(navMap);

        // Client pin
        if (hasClient) {
            const clientIcon = L.divIcon({
                html: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 32 42">
                    <path d="M16 0C7.163 0 0 7.163 0 16c0 10.667 16 26 16 26S32 26.667 32 16C32 7.163 24.837 0 16 0z" fill="#dc2626"/>
                    <circle cx="16" cy="16" r="7" fill="white"/>
                    <circle cx="16" cy="16" r="4" fill="#dc2626"/>
                </svg>`,
                className: '', iconSize: [32, 42], iconAnchor: [16, 42], popupAnchor: [0, -44]
            });

            const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${clientLat},${clientLng}&travelmode=driving`;
            document.getElementById('google-directions-btn').href = directionsUrl;

            L.marker([clientLat, clientLng], { icon: clientIcon })
                .addTo(navMap)
                .bindPopup(`<b style="font-size:13px;">${clientName}</b><br><span style="color:#64748b;font-size:12px;">📍 ${barangay}</span>`)
                .openPopup();
        } else {
            document.getElementById('google-directions-btn').href = '#';
            document.getElementById('nav-map-subtitle').textContent = barangay + ' — No coordinates saved';
        }

        // Technician live position
        if (navigator.geolocation) {
            const techIcon = L.divIcon({
                html: `<div class="tech-dot"></div>`,
                className: '', iconSize: [18, 18], iconAnchor: [9, 9]
            });

            watchId = navigator.geolocation.watchPosition(pos => {
                const tLat = pos.coords.latitude;
                const tLng = pos.coords.longitude;

                if (techMarker) {
                    techMarker.setLatLng([tLat, tLng]);
                } else {
                    techMarker = L.marker([tLat, tLng], { icon: techIcon })
                        .addTo(navMap)
                        .bindPopup('<b style="font-size:12px;">📍 You are here</b>');
                }

                document.getElementById('nav-map-subtitle').textContent = barangay + ' — Your location found';

                // Update directions URL to include origin
                if (hasClient) {
                    const url = `https://www.google.com/maps/dir/?api=1&origin=${tLat},${tLng}&destination=${clientLat},${clientLng}&travelmode=driving`;
                    document.getElementById('google-directions-btn').href = url;
                }

                // Fit both markers
                if (hasClient) {
                    navMap.fitBounds([[tLat, tLng], [clientLat, clientLng]], { padding: [40, 40] });
                }
            }, null, { enableHighAccuracy: true, maximumAge: 5000 });
        }
    }

    function closeNavMap() {
        document.getElementById('nav-map-modal').style.display = 'none';
        if (navMap) { navMap.remove(); navMap = null; }
        if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        techMarker = null;
        currentStartFormId = null;
    }

    function confirmStartJob() {
        if (!currentStartFormId) return;
        const jobId = currentStartFormId.replace('start-form-', '');
        closeNavMap();

        const actionDiv  = document.getElementById('action-' + jobId);
        const badgeSpan  = document.getElementById('status-badge-' + jobId);

        // Optimistic UI — swap immediately
        if (actionDiv) {
            actionDiv.innerHTML = `<button onclick="openCompleteModal(${jobId})" class="btn-start" style="background:linear-gradient(105deg,#059669,#047857);">Complete</button>`;
        }
        if (badgeSpan) {
            badgeSpan.className = 'badge badge-progress';
            badgeSpan.querySelector('.badge-label').textContent = 'In Progress';
        }

        // AJAX POST to start the job
        fetch('/technician/jobs/' + jobId + '/start', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        }).catch(() => {
            // On failure revert
            if (actionDiv) {
                actionDiv.innerHTML = `<button type="button" class="btn-start" onclick="startJobWithMap(${jobId},null,null,'','');">Start</button>`;
            }
            if (badgeSpan) {
                badgeSpan.className = 'badge badge-pending';
                badgeSpan.querySelector('.badge-label').textContent = 'Assigned';
            }
        });
    }

    document.getElementById('nav-map-modal').addEventListener('click', function(e) {
        if (e.target === this) closeNavMap();
    });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNavMap(); });
</script>

<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('photo-preview').classList.remove('hidden');
                document.getElementById('photo-drop-area').classList.add('hidden');
                document.getElementById('photo-label').textContent = input.files[0].name;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function removePhoto() {
        document.getElementById('photo-input').value = '';
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('photo-drop-area').classList.remove('hidden');
        document.getElementById('photo-label').textContent = 'Click to upload photo';
    }
</script>

<script>
    function openCompleteModal(jobId) {
        const modal = document.getElementById('complete-modal');
        const form = document.getElementById('complete-form');
        form.action = '/technician/jobs/' + jobId + '/complete';
        modal.showModal();
    }
</script>
</body>
</html>
