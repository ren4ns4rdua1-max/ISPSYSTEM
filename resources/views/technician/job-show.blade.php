<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Sidebar and topbar same as dashboard -->

    <div id="main-content" class="ml-64 p-8">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    {{ $job->job_type_label[0] }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $job->job_type_label }}</h1>
                    <p class="text-xl font-semibold text-gray-600 mt-1">Client: {{ $job->client->name }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>ID: #{{ $job->id }}</span>
                        <span>Scheduled: {{ $job->scheduled_date?->format('M d, Y h:i A') }}</span>
                        <span>Status: <span class="font-semibold {{ $job->status_badge }}">{{ ucfirst($job->status) }}</span></span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('technician.tasks') }}" class="p-6 bg-white rounded-2xl border shadow-sm hover:shadow-md transition-all">
                    ← Back to Tasks
                </a>
                @if($job->status == 'in_progress')
                    <button onclick="openCompleteModal()" class="p-6 bg-emerald-600 text-white rounded-2xl shadow-sm hover:shadow-md hover:bg-emerald-700 transition-all font-semibold">
                        Complete Job & Submit Report
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Client Details -->
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Client Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Name</label>
                        <p class="text-lg font-semibold mt-1">{{ $job->client->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">PPPoE</label>
                        <p class="text-lg font-mono bg-gray-50 px-3 py-2 rounded-xl mt-1">{{ $job->client->pppoe_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Plan</label>
                        <p class="text-lg mt-1">{{ $job->client->subscriptionRate->name ?? 'N/A' }} - ₱{{ number_format($job->client->subscriptionRate->monthly_fee ?? 0) }}/mo</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Location</label>
                        <p class="text-lg mt-1">{{ $job->client->barangay }}, {{ $job->client->nap_box }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Contact</label>
                        <p class="text-lg mt-1">{{ $job->client->phone_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Job Timeline -->
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Job Timeline</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Job Created</p>
                            <p class="text-sm text-gray-500">{{ $job->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @if($job->assigned_by)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Assigned</p>
                                <p class="text-sm text-gray-500">{{ $job->assignedBy->name ?? 'Admin' }} • {{ $job->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->started_at)
                        <div class="flex items-center gap-4 p-4 bg-amber-50 rounded-xl">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Started</p>
                                <p class="text-sm text-gray-500">{{ $job->started_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Completion Modal -->
        <div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Complete Job</h2>
                    <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('technician.tasks.complete', $job) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">IP Address</label>
                            <input type="text" name="ip_address" placeholder="192.168.1.100" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">MAC Address</label>
                            <input type="text" name="mac_address" placeholder="AA:BB:CC:DD:EE:FF" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Router SSID</label>
                            <input type="text" name="router_ssid" placeholder="MyHomeWiFi" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Router Password</label>
                            <input type="password" name="router_password" placeholder="********" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Speed Test Result</label>
                        <textarea name="speed_test_result" rows="2" placeholder='{"download": 95.2, "upload": 25.1, "ping": 12}' class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Materials Used</label>
                        <textarea name="materials_used" rows="3" placeholder="Cable 50m, Connectors x4, Router config..." class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Proof Photo (Optional)</label>
                            <input type="file" name="proof_image" accept="image/*" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Completion Notes *</label>
                        <textarea name="completion_notes" rows="4" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Describe work performed, issues resolved..."></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-emerald-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-emerald-700 shadow-lg transition-all">
                            Submit Report & Complete
                        </button>
                        <button type="button" onclick="closeCompleteModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 px-6 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openCompleteModal() {
                document.getElementById('completeModal').classList.remove('hidden');
            }
            function closeCompleteModal() {
                document.getElementById('completeModal').classList.add('hidden');
            }
        </script>
    </div>
</body>
</html>
