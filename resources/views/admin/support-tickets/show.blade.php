<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        body { background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%); }

        .section-card {
            background: white;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .badge-open { background: #fef3c7; color: #92400e; }
        .badge-progress { background: #eff6ff; color: #1e3a8a; }
        .badge-resolved { background: #ecfdf7; color: #065f46; }
        .badge-closed { background: #f3f4f6; color: #4b5563; }

        .priority-low { background: #d1fae5; color: #065f46; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-high { background: #fee2e2; color: #991b1b; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }
        .btn-back:hover {
            transform: translateX(-4px);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.3);
        }

        .form-section {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

@include('partials.sidebar')

<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8" style="margin-left:260px;">
    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="section-card p-6 mb-8">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="font-display font-black text-3xl text-gray-900 mb-2">#{{ $ticket->id }} - {{ $ticket->subject }}</h1>
                    <p class="text-gray-600">
                        <span class="font-bold">Client:</span> {{ $ticket->client->name }}<br>
                        <span class="text-sm">Created: {{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap justify-end">
                    <span class="px-4 py-2 rounded-full text-xs font-bold priority-{{ $ticket->priority }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                    <span class="px-4 py-2 rounded-full text-xs font-bold badge-{{ $ticket->status }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Main Content (2 cols) -->
            <div class="lg:col-span-2">
                <!-- Client Message -->
                <div class="form-section">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">📝 Client Message</p>
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-wrap leading-relaxed">{{ $ticket->message }}</p>
                    </div>
                </div>

                @if($ticket->troubleshooting_notes)
                    <div class="form-section">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">🔧 Troubleshooting Notes</p>
                        <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                            <p class="text-blue-900 whitespace-pre-wrap leading-relaxed">{{ $ticket->troubleshooting_notes }}</p>
                        </div>
                    </div>
                @endif

                @if($ticket->solution)
                    <div class="form-section">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">✅ Solution</p>
                        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                            <p class="text-green-900 whitespace-pre-wrap leading-relaxed">{{ $ticket->solution }}</p>
                        </div>
                    </div>
                @endif

                @if($ticket->admin_reply)
                    <div class="form-section">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">💬 Technician Reply</p>
                        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                            <p class="text-xs font-bold text-green-600 mb-2">Sent: {{ $ticket->replied_at?->format('M d, Y h:i A') ?? '-' }}</p>
                            <p class="text-green-900 whitespace-pre-wrap leading-relaxed">{{ $ticket->admin_reply }}</p>
                        </div>
                    </div>
                @endif

                <!-- Update Status Form -->
                <div class="form-section">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-6">📊 Update Status</p>
                    <form method="POST" action="{{ route('admin.support-tickets.updateStatus', $ticket) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                <option value="open" {{ $ticket->status==='open'?'selected':'' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status==='in_progress'?'selected':'' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status==='resolved'?'selected':'' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status==='closed'?'selected':'' }}>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-lg hover:shadow-lg transition-all">
                            💾 Save Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar (1 col) -->
            <div class="lg:col-span-1">
                <!-- Ticket Info Card -->
                <div class="form-section">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">📋 Ticket Info</p>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold">ID</p>
                            <p class="text-sm font-bold text-gray-900">#{{ $ticket->id }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold">Client</p>
                            <p class="text-sm font-bold text-gray-900">{{ $ticket->client->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold">Created</p>
                            <p class="text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($ticket->assigned_at)
                            <div>
                                <p class="text-xs text-gray-500 font-semibold">Assigned</p>
                                <p class="text-sm text-gray-900">{{ $ticket->assigned_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                        @if($ticket->resolved_at)
                            <div>
                                <p class="text-xs text-gray-500 font-semibold">Resolved</p>
                                <p class="text-sm text-gray-900">{{ $ticket->resolved_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assign Technician Card -->
                <div class="form-section bg-orange-50 border-orange-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">👤 Assign Technician</p>
                    <form method="POST" action="{{ route('admin.support-tickets.assignTechnician', $ticket) }}">
                        @csrf
                        <select name="technician_id" class="w-full px-4 py-2.5 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition mb-3" required>
                            <option value="">-- Select Technician --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $ticket->technician_id === $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white font-bold rounded-lg hover:shadow-lg transition-all">
                            Assign
                        </button>
                    </form>

                    @if($ticket->technician)
                        <div class="mt-4 pt-4 border-t border-orange-200">
                            <p class="text-xs text-orange-700 font-bold mb-1">Currently Assigned</p>
                            <p class="text-sm font-bold text-gray-900">{{ $ticket->technician->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back to Tickets Button (Bottom) -->
       
    </div>
</div>

</body>
</html>
