<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Reuse dashboard styles */
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        :root { --red-primary: #dc2626; --red-glow: rgba(220,38,38,0.35); }
        /* ... include sidebar and topbar styles from dashboard ... */
        /* (To save space, assuming same layout as dashboard.blade.php) */
    </style>
</head>
<body>
    <!-- Same sidebar as dashboard, with 'My Tasks' active -->

    <div id="main-content">
        <!-- Same topbar -->

        <div class="main-scroll p-8">
            <!-- Stats row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Pending</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['pendingJobs'] ?? 0 }}</p>
                    <a href="?status=assigned" class="text-xs text-red-600 hover:underline mt-1 inline-block">View all</a>
                </div>
                <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">In Progress</h3>
                    <p class="text-3xl font-bold text-amber-600">{{ $stats['inProgressJobs'] ?? 0 }}</p>
                    <a href="?status=in_progress" class="text-xs text-amber-600 hover:underline mt-1 inline-block">View all</a>
                </div>
                <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Completed Today</h3>
                    <p class="text-3xl font-bold text-emerald-600">{{ $stats['completedJobs'] ?? 0 }}</p>
                    <a href="{{ route('technician.history') }}" class="text-xs text-emerald-600 hover:underline mt-1 inline-block">View history</a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Search Client</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Name or PPPoE..." class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div class="w-48">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="assigned" {{ $status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold">Filter</button>
                    @if($search || $status)
                        <a href="{{ route('technician.tasks') }}" class="px-6 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 font-semibold">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Tasks Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-lg text-gray-900">
                    My Tasks ({{ $tasks->total() }})
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Job Type</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Scheduled</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($tasks as $task)
                                <tr class="hover:bg-red-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr($task->client->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm">{{ $task->client->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $task->client->pppoe_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $task->job_type_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ $task->scheduled_date?->format('M d, Y h:i A') ?? 'ASAP' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $task->status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($task->status == 'assigned')
                                            <form method="POST" action="{{ route('technician.tasks.start', $task) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 mr-2">Start</button>
                                            </form>
                                        @elseif($task->status == 'in_progress')
                                            <a href="{{ route('technician.tasks.show', $task) }}" class="px-4 py-2 bg-amber-600 text-white text-xs font-semibold rounded-lg hover:bg-amber-700 mr-2">Complete</a>
                                        @endif
                                        <a href="{{ route('technician.tasks.show', $task) }}" class="px-4 py-2 border text-xs font-semibold border-gray-300 rounded-lg hover:bg-gray-50">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No tasks assigned</h3>
                                        <p class="text-sm">You're all caught up! Check back later for new assignments.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $tasks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <script>
        // Live date, animations (same as dashboard)
    </script>
</body>
</html>
