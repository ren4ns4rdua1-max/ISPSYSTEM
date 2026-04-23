<?php

namespace App\Http\Controllers;

use App\Models\InstallationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        if (!$technician) {
            $technician = $user->technicians()->create([
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => 'N/A',
                'status' => 'available',
            ]);
        }

        $status = $request->get('status', '');
        $search = $request->get('search', '');

        $stats = [
            'todayJobs' => InstallationJob::where('technician_id', $technician->id)
                ->whereDate('created_at', today())
                ->count(),
            'pendingJobs' => InstallationJob::where('technician_id', $technician->id)
                ->where('status', 'assigned')
                ->count(),
            'inProgressJobs' => InstallationJob::where('technician_id', $technician->id)
                ->where('status', 'in_progress')
                ->count(),
            'completedJobs' => InstallationJob::where('technician_id', $technician->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'totalJobs' => InstallationJob::where('technician_id', $technician->id)->count(),
        ];

        $query = InstallationJob::with(['client.subscriptionRate'])
            ->where('technician_id', $technician->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('pppoe_name', 'like', "%{$search}%");
            });
        }

        $tasks = $query->whereIn('status', ['assigned', 'in_progress'])
            ->latest()
            ->paginate(10);

        return view('technician.dashboard', compact('stats', 'tasks', 'technician', 'status', 'search'));
    }
}

