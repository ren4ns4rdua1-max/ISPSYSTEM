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


<!-- ═══════════════════ SIDEBAR ═══════════════════ -->

@include('partials.sidebar')


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
