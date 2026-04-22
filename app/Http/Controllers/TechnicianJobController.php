<?php

namespace App\Http\Controllers;

use App\Models\InstallationJob;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TechnicianJobController extends Controller
{
    /**
     * Display list of assigned tasks for technician.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        if (!$technician) {
            abort(404, 'No technician profile found.');
        }

        $status = $request->get('status', '');
        $search = $request->get('search', '');

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

        $tasks = $query->latest()->paginate(15);

        $stats = [
            'pending' => InstallationJob::where('technician_id', $technician->id)->where('status', 'assigned')->count(),
            'in_progress' => InstallationJob::where('technician_id', $technician->id)->where('status', 'in_progress')->count(),
            'completed' => InstallationJob::where('technician_id', $technician->id)->where('status', 'completed')->count(),
        ];

        return view('technician.tasks', compact('tasks', 'stats', 'status', 'search'));
    }

    /**
     * Display job details and update form.
     */
    public function show(InstallationJob $job): View
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        if ($job->technician_id !== $technician->id) {
            abort(403);
        }

        $job->load(['client.subscriptionRate']);

        return view('technician.job-show', compact('job'));
    }

    /**
     * Start job (mark in_progress).
     */
    public function start(InstallationJob $job): RedirectResponse
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        if ($job->technician_id !== $technician->id || $job->status !== 'assigned') {
            abort(403);
        }

        $job->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Mark technician busy
        $technician->update(['status' => 'busy']);

        return redirect()->back()->with('success', 'Job started successfully.');
    }

    /**
     * Complete job with report.
     */
    public function complete(Request $request, InstallationJob $job): RedirectResponse
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        if ($job->technician_id !== $technician->id || $job->status !== 'in_progress') {
            abort(403);
        }

        $request->validate([
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|mac_address',
            'router_ssid' => 'nullable|string|max:32',
            'router_password' => 'nullable|string|max:64',
            'speed_test_result' => 'nullable|json',
            'materials_used' => 'nullable|string',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'completion_notes' => 'required|string',
        ]);

        // Handle file upload
        $proofImage = null;
        if ($request->hasFile('proof_image')) {
            $proofImage = $request->file('proof_image')->store('proofs', 'public');
        }

        $job->update([
            'status' => 'completed',
            'completed_by' => $user->id,
            'completed_at' => now(),
            'ip_address' => $request->ip_address,
            'mac_address' => $request->mac_address,
            'router_ssid' => $request->router_ssid,
            'router_password' => $request->router_password,
            'speed_test_result' => $request->speed_test_result ? json_decode($request->speed_test_result, true) : null,
            'materials_used' => $request->materials_used,
            'proof_image' => $proofImage,
            'completion_notes' => $request->completion_notes,
        ]);

        // Activate client
        $job->client->update(['status' => 'active']);

        // Mark technician available
        $technician->update(['status' => 'available']);

        return redirect()->route('technician.tasks')->with('success', 'Job completed and report submitted!');
    }

    /**
     * Update job status (reschedule, failed, etc.).
     */
    public function updateStatus(Request $request, InstallationJob $job): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:rescheduled,failed',
            'notes' => 'required|string',
        ]);

        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        if ($job->technician_id !== $technician->id) {
            abort(403);
        }

        $job->update([
            'status' => $request->status,
            'completion_notes' => $request->notes,
        ]);

        $technician->update(['status' => 'available']);

        return redirect()->back()->with('success', 'Status updated.');
    }

    /**
     * Display work history.
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        $technician = $user->technicians()->first();
        
        $query = InstallationJob::with(['client'])
            ->where('technician_id', $technician->id)
            ->where('status', 'completed');

        $search = $request->get('search', '');
        if ($search) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $history = $query->latest()->paginate(20);

        return view('technician.history', compact('history', 'search'));
    }
}

