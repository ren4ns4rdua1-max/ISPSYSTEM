<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets Report</title>
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

        .stat-box {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }

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

        .progress-bar {
            height: 8px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="font-display font-black text-4xl text-gray-900">📊 Support Tickets Report</h1>
            <p class="text-gray-600 text-lg mt-2">Analytics and performance metrics for support tickets</p>
        </div>

        <!-- Filter Section -->
        <div class="section-card p-6 mb-8">
            <h3 class="font-display font-bold text-lg text-gray-900 mb-4">Generate Report</h3>
            <form method="GET" action="{{ route('admin.support-tickets.report') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold rounded-lg hover:shadow-lg transition-all">
                        📋 Generate
                    </button>
                    <a href="{{ route('admin.support-tickets.report') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition-all">
                        ↻
                    </a>
                </div>
            </form>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="stat-box p-6">
                <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Tickets</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tickets->count() }}</p>
            </div>
            <div class="stat-box p-6">
                <p class="text-green-600 text-sm font-semibold uppercase tracking-wide">Closed Rate</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $tickets->count() > 0 ? round(($statusStats['closed'] / $tickets->count()) * 100) : 0 }}%</p>
            </div>
            <div class="stat-box p-6">
                <p class="text-blue-600 text-sm font-semibold uppercase tracking-wide">Avg Resolution</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $avgResolutionTime->avg_hours ? number_format($avgResolutionTime->avg_hours, 1) : 0 }}h</p>
            </div>
            <div class="stat-box p-6">
                <p class="text-red-600 text-sm font-semibold uppercase tracking-wide">High Priority</p>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ $priorityStats['high'] }}</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Status Distribution -->
            <div class="section-card p-6">
                <h3 class="font-display font-bold text-lg text-gray-900 mb-6">Status Distribution</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Open</span>
                            <span class="text-sm font-bold text-yellow-600">{{ $statusStats['open'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#fbbf24;width:{{ $tickets->count() > 0 ? ($statusStats['open'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">In Progress</span>
                            <span class="text-sm font-bold text-blue-600">{{ $statusStats['in_progress'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#3b82f6;width:{{ $tickets->count() > 0 ? ($statusStats['in_progress'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Resolved</span>
                            <span class="text-sm font-bold text-green-600">{{ $statusStats['resolved'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#10b981;width:{{ $tickets->count() > 0 ? ($statusStats['resolved'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Closed</span>
                            <span class="text-sm font-bold text-gray-600">{{ $statusStats['closed'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#6b7280;width:{{ $tickets->count() > 0 ? ($statusStats['closed'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="section-card p-6">
                <h3 class="font-display font-bold text-lg text-gray-900 mb-6">Priority Distribution</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Low</span>
                            <span class="text-sm font-bold text-green-600">{{ $priorityStats['low'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#10b981;width:{{ $tickets->count() > 0 ? ($priorityStats['low'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Medium</span>
                            <span class="text-sm font-bold text-yellow-600">{{ $priorityStats['medium'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#fbbf24;width:{{ $tickets->count() > 0 ? ($priorityStats['medium'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">High</span>
                            <span class="text-sm font-bold text-red-600">{{ $priorityStats['high'] }}</span>
                        </div>
                        <div class="progress-bar">
                            <div style="height:100%;background:#ef4444;width:{{ $tickets->count() > 0 ? ($priorityStats['high'] / $tickets->count()) * 100 : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Technicians -->
        <div class="section-card overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-display font-bold text-lg text-gray-900">👥 Top Technicians</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Technician</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Assigned</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Closed</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Close Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($topTechnicians as $tech)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><span class="font-bold text-gray-900">{{ $tech->technician->name }}</span></td>
                                <td class="px-6 py-4 text-gray-700">{{ $tech->ticket_count }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $tech->closed_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        {{ $tech->ticket_count > 0 ? round(($tech->closed_count / $tech->ticket_count) * 100) : 0 }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No technician data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('admin.support-tickets.index') }}" class="btn-back">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Tickets
            </a>
        </div>
    </div>
</div>

</body>
</html>
