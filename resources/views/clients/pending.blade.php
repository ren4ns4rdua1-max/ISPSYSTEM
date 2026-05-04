<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        body { overflow: hidden; height: 100vh; }
        #sidebar { transition: width 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1); background: linear-gradient(180deg, #0a0c18 0%, #0f111e 100%); position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; }
        #main-content { transition: margin-left 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1); margin-left: 260px; width: calc(100% - 260px); height: 100vh; overflow: hidden; display: flex; flex-direction: column; }
        .status-pending_approval { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }
        .main-scroll { overflow-y: auto; scrollbar-width: thin; }
        .main-scroll::-webkit-scrollbar { width: 6px; }
        .main-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-100">

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside id="sidebar" style="width:260px;" class="fixed left-0 top-0 h-full z-50 flex flex-col shadow-2xl">
    <div class="flex items-center gap-3 px-4 py-[18px] border-b border-white/[.08] min-h-[68px]">
        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg shadow-red-900/40" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
            <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
        </div>
        <div><p class="font-display font-bold text-white text-[14px]">ADMIN</p><p class="text-red-400 text-[10px]">ISP Control Center</p></div>
    </div>
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <p class="text-[10px] font-bold text-gray-500 uppercase px-3 py-2">Management</p>
        <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/[.06]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-sm">All Clients</span>
        </a>
        <a href="{{ route('clients.pending') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-red-500/20 text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm">Pending Approval</span>
            @if($pendingCount > 0)
            <span class="ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>
    </nav>
    <div class="border-t border-white/[.08] p-3">
        <div class="flex items-center gap-3 p-2 rounded-xl">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center text-white font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div><p class="text-white text-[12px]">{{ Auth::user()->name }}</p><p class="text-gray-500 text-[10px]">{{ Auth::user()->email }}</p></div>
        </div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div id="main-content" class="flex flex-col">
    <header class="bg-white/90 backdrop-blur border-b border-gray-100 px-7 py-4 flex items-center justify-between">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-xl">Pending Client Approvals</h1>
            <p class="text-gray-500 text-sm">{{ $pendingCount }} client(s) waiting for approval</p>
        </div>
        <a href="{{ route('clients.index') }}" class="text-red-600 hover:underline text-sm">← Back to All Clients</a>
    </header>

    <main class="flex-1 main-scroll p-6">
        @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-green-50 border border-green-200 mb-5">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-green-800 text-sm">{{ session('success') }}</p>
        </div>
        @endif

        @if($pendingClients->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 rounded-2xl bg-purple-50 flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="font-bold text-gray-800 text-lg">No Pending Approvals</p>
            <p class="text-gray-400 text-sm">All client registrations have been reviewed.</p>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead style="background:linear-gradient(90deg,#fdf4ff,#faf5ff);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase">Registered</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-purple-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-purple-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingClients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $client->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $client->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->phone_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->barangay }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold status-pending_approval">Pending Approval</span>
                        </td>
<td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="openAssignModal({{ $client->id}}, '{{ $client->name }}')" class="px-4 py-1.5 text-xs font-semibold text-white bg-indigo-500 hover:bg-indigo-600 rounded-lg">Approve & Assign</button>
                                <form method="POST" action="{{ route('clients.reject', $client->id) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($pendingClients->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $pendingClients->links() }}</div>
            @endif
        </div>
        @endif
</main>
</div>

<!-- Assign Modal -->
<div id="assign-modal" class="fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);display:none;">
    <div class="modal-content bg-white rounded-2xl shadow-2xl p-6 mx-4 w-full max-w-md">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-display font-bold text-gray-900 text-lg">Approve & Assign Task</h3>
                <p class="text-gray-500 text-sm mt-1">Client: <span id="modal-client-name" class="font-semibold text-gray-800"></span></p>
            </div>
        </div>
        
        <form id="assign-form" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Technician</label>
                    <select name="technician_id" id="technician-select" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" required>
                        <option value="">-- Select Technician --</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }} ({{ ucfirst($tech->status) }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Job Type</label>
                    <select name="job_type" id="job-type" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" required>
                        <option value="new_installation">New Installation</option>
                        <option value="repair">Repair</option>
                        <option value="reconnection">Reconnection</option>
                        <option value="upgrade">Upgrade</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Scheduled Date</label>
                    <input type="datetime-local" name="scheduled_date" id="scheduled-date" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="Additional instructions for technician..."></textarea>
                </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:shadow-md hover:-translate-y-0.5" style="background:linear-gradient(135deg,#4f46e5,#6366f1);">
                    Approve & Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Set default scheduled date to current datetime
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('scheduled-date').value = now.toISOString().slice(0, 16);
    });

    function openAssignModal(clientId, clientName) {
        document.getElementById('modal-client-name').textContent = clientName;
        document.getElementById('assign-form').action = '/clients/' + clientId + '/approve-and-assign';
        document.getElementById('assign-modal').style.display = 'flex';
    }
    
    function closeAssignModal() {
        document.getElementById('assign-modal').style.display = 'none';
    }
    
    document.getElementById('assign-modal').addEventListener('click', function(e) {
        if (e.target === this) closeAssignModal();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAssignModal();
    });
</script>
</body>
</html>
