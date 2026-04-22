<?php

namespace App\Http\Controllers;

use App\Models\InstallationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Find technician record
        $technician = $user->technicians()->first();
        
        $stats = [
            'todayJobs' => InstallationJob::where('technician_id', $technician?->id)
                ->whereDate('created_at', today())
                ->count(),
            'inProgressJobs' => InstallationJob::where('technician_id', $technician?->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
            'completedJobs' => InstallationJob::where('technician_id', $technician?->id)
                ->where('status', 'completed')
                ->count(),
            'totalJobs' => InstallationJob::where('technician_id', $technician?->id)->count(),
        ];

        $tasks = InstallationJob::with('client')
            ->where('technician_id', $technician?->id)
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->latest()
            ->limit(10)
            ->get();

        return view('technician.dashboard', compact('stats', 'tasks', 'technician'));
    }
}

