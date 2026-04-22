<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work History — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Same sidebar with History active -->

    <div id="main-content">
        <!-- Topbar with "Work History" title -->

        <div class="main-scroll p-8">
            <!-- Filters -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Search Client</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Client name..." class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold whitespace-nowrap">Filter</button>
                    @if($search)
                        <a href="{{ route('technician.history') }}" class="px-6 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 font-semibold">Clear</a>
                    @endif
                </form>
            </div>

            <!-- History Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($history as $job)
                    <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all border">
                        <div class="flex items-start justify-between mb-4">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">Completed</span>
                            <span class="text-xs text-gray-500">{{ $job->completed_at->format('MMM dd') }}</span>
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-2 line-clamp-2">{{ $job->client->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $job->job_type_label }}</p>
                        @if($job->proof_image)
                            <img src="{{ Storage::url($job->proof_image) }}" alt="Proof" class="w-full h-32 object-cover rounded-xl mb-4 shadow-md">
                        @endif
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">IP</span>
                                <span class="font-mono font-semibold">{{ $job->ip_address ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">MAC</span>
                                <span class="font-mono font-semibold">{{ $job->mac_address ?? 'N/A' }}</span>
                            </div>
                            @if($job->speed_test_result)
                                <div>
                                    <span class="text-gray-500">Speed Test:</span>
                                    <div class="ml-2 text-sm">
                                        ↓ {{ data_get($job->speed_test_result, 'download') ?? 'N/A' }} Mbps
                                        ↑ {{ data_get($job->speed_test_result, 'upload') ?? 'N/A' }} Mbps
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 pt-4 border-t">
                            <a href="{{ route('technician.tasks.show', $job) }}" class="text-red-600 hover:text-red-700 text-sm font-semibold">View Details →</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <svg class="w-20 h-20 mx-auto mb-6 text-gray-400" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2-2 2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No completed jobs yet</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Your work history will appear here once you complete your first service job.</p>
                    </div>
                @endforelse
            </div>

            {{ $history->appends(request()->query())->links() }}
        </div>
    </div>
</body>
</html>
