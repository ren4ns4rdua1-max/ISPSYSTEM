<?php

namespace App\Http\Controllers;

use App\Models\InstallationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    /**
     * Get technician data for views.
     */
    private function getTechnicianData()
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        return [
            'user' => $user,
            'technician' => $technician,
            'techName' => $technician?->name ?? $user->name,
            'techEmail' => $technician?->email ?? $user->email,
            'initials' => $this->getInitials($technician?->name ?? $user->name),
        ];
    }
    
    /**
     * Get initials from name.
     */
    private function getInitials($name)
    {
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            if (strlen($initials) < 2) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        return $initials ?: 'T';
    }
    
    /**
     * Get jobs stats.
     */
    private function getStats($technicianId)
    {
        return [
            'todayJobs' => InstallationJob::where('technician_id', $technicianId)
                ->whereDate('created_at', today())
                ->count(),
            'inProgressJobs' => InstallationJob::where('technician_id', $technicianId)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
            'completedJobs' => InstallationJob::where('technician_id', $technicianId)
                ->where('status', 'completed')
                ->count(),
            'totalJobs' => InstallationJob::where('technician_id', $technicianId)->count(),
            'pendingJobs' => InstallationJob::where('technician_id', $technicianId)
                ->whereIn('status', ['pending', 'assigned', 'in_progress'])
                ->count(),
        ];
    }

    public function dashboard()
    {
        $techData = $this->getTechnicianData();
        $stats = $this->getStats($techData['technician']?->id);
        
        $tasks = InstallationJob::with('client')
            ->where('technician_id', $techData['technician']?->id)
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->latest()
            ->limit(10)
            ->get();

        return view('technician.dashboard', compact('stats', 'tasks') + $techData);
    }
    
    /**
     * Display technician's tasks/jobs.
     */
    public function tasks(Request $request)
    {
        $techData = $this->getTechnicianData();
        $stats = $this->getStats($techData['technician']?->id);
        
        $status = $request->get('status', '');
        
        $tasks = InstallationJob::with('client')
            ->where('technician_id', $techData['technician']?->id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            }, function ($query) {
                $query->whereIn('status', ['pending', 'assigned', 'in_progress']);
            })
            ->latest()
            ->paginate(10);

        return view('technician.tasks', compact('tasks', 'stats', 'status') + $techData);
    }
    
/**
     * Display technician's job history.
     */
    public function history(Request $request)
    {
        $techData = $this->getTechnicianData();
        $stats = $this->getStats($techData['technician']?->id);
        
        $status = $request->get('status', 'completed');
        
        $history = InstallationJob::with('client')
            ->where('technician_id', $techData['technician']?->id)
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return view('technician.history', compact('history', 'stats', 'status') + $techData);
    }
    
    /**
     * Start a job (mark as in_progress).
     */
    public function startJob(InstallationJob $job): \Illuminate\Http\RedirectResponse
    {
        $techData = $this->getTechnicianData();
        
        // Verify the job belongs to this technician
        if ($job->technician_id !== $techData['technician']?->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        $job->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Job started! Good luck.');
    }
    
/**
     * Complete a job.
     */
    public function completeJob(Request $request, InstallationJob $job): \Illuminate\Http\RedirectResponse
    {
        $techData = $this->getTechnicianData();
        
        // Verify the job belongs to this technician
        if ($job->technician_id !== $techData['technician']?->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        $request->validate([
            'completion_notes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);
        
        $photoPath = null;
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName = 'job-' . $job->id . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('job-photos', $fileName, 'public');
        }
        
$job->update([
            'status' => 'completed',
            'completed_by' => $techData['user']->id,
            'completed_at' => now(),
            'completion_notes' => $request->input('completion_notes'),
            'photo' => $photoPath,
        ]);
        
        // Refresh to get updated photo value
        $job->refresh();
        
        // Update client status to active if this was a new installation
        if ($job->job_type === 'new_installation') {
            $job->client->update(['status' => 'active']);
        }
        
        // Notify admins about job completion (with fresh job data including photo)
        \App\Models\AdminNotification::notifyJobCompleted($job);
        
        return redirect()->back()->with('success', 'Job completed! Great work.');
    }
}

