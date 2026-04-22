<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\InstallationJob;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TechnicianController extends Controller
{
    /**
     * Display a listing of technicians.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $technicians = Technician::withCount(['jobs' => function ($query) {
            $query->whereIn('status', ['pending', 'assigned', 'in_progress']);
        }])
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->latest()
        ->paginate(10);

        // Stats
        $stats = [
            'total_technicians' => Technician::count(),
            'available' => Technician::where('status', 'available')->count(),
            'busy' => Technician::where('status', 'busy')->count(),
            'pending_jobs' => InstallationJob::whereIn('status', ['pending', 'assigned'])->count(),
        ];

        return view('technicians.index', compact('technicians', 'search', 'status', 'stats'));
    }

    /**
     * Show the form for creating a new technician.
     */
    public function create(): View
    {
        return view('technicians.create');
    }

    /**
     * Store a newly created technician.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:technicians'],
            'phone_number' => ['required', 'string', 'max:20'],
            'specialization' => ['nullable', 'in:installation,repair,both'],
            'area_coverage' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:available,busy,offduty'],
            'notes' => ['nullable', 'string'],
        ]);

        // Generate temp password
        $tempPassword = Str::random(12);

        // Create user account first
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'technician',
        ]);

        // Create technician linked to user
        $technician = Technician::create(array_merge($validated, ['user_id' => $user->id]));

        return redirect()->route('technicians.index')->with('success', "Technician '{$validated['name']}' created successfully. Temp password: {$tempPassword}. Share with technician to login.");
    }

    /**
     * Display the specified technician.
     */
    public function show(Technician $technician): View
    {
        $technician->load(['jobs' => function ($query) {
            $query->latest()->limit(10);
        }, 'jobs.client', 'jobs.assignedBy']);

        $pendingJobs = $technician->jobs()->whereIn('status', ['pending', 'assigned', 'in_progress'])->get();
        $completedJobs = $technician->jobs()->where('status', 'completed')->count();

        return view('technicians.show', compact('technician', 'pendingJobs', 'completedJobs'));
    }

    /**
     * Show the form for editing the specified technician.
     */
    public function edit(Technician $technician): View
    {
        return view('technicians.edit', compact('technician'));
    }

    /**
     * Update the specified technician.
     */
    public function update(Request $request, Technician $technician): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:technicians,email,' . $technician->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'specialization' => ['nullable', 'in:installation,repair,both'],
            'area_coverage' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:available,busy,offduty'],
            'notes' => ['nullable', 'string'],
        ]);

        $technician->update($validated);

        return redirect()->route('technicians.show', $technician->id)->with('success', 'Technician updated successfully.');
    }

    /**
     * Remove the specified technician.
     */
    public function destroy(Technician $technician): RedirectResponse
    {
        $technician->delete();
        return redirect()->route('technicians.index')->with('success', 'Technician deleted successfully.');
    }

    /**
     * Display all installation jobs.
     */
    public function jobs(Request $request): View
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $technicianId = $request->get('technician_id', '');

        $jobs = InstallationJob::with(['client', 'technician', 'assignedBy'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($technicianId, function ($query) use ($technicianId) {
                $query->where('technician_id', $technicianId);
            })
            ->latest()
            ->paginate(10);

        $technicians = Technician::where('status', 'available')->get();

        $stats = [
            'pending' => InstallationJob::where('status', 'pending')->count(),
            'assigned' => InstallationJob::where('status', 'assigned')->count(),
            'in_progress' => InstallationJob::where('status', 'in_progress')->count(),
            'completed' => InstallationJob::where('status', 'completed')->count(),
        ];

        return view('technicians.jobs', compact('jobs', 'technicians', 'search', 'status', 'technicianId', 'stats'));
    }

    /**
     * Create a new installation job.
     */
    public function createJob(Request $request): View
    {
        $clientId = $request->get('client_id');
        $clients = Client::whereIn('status', ['active', 'inactive', 'suspended'])->get();
        $technicians = Technician::where('status', 'available')->get();

        $selectedClient = null;
        if ($clientId) {
            $selectedClient = Client::find($clientId);
        }

        return view('technicians.create-job', compact('clients', 'technicians', 'selectedClient'));
    }

    /**
     * Store a new installation job.
     */
    public function storeJob(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'technician_id' => ['nullable', 'exists:technicians,id'],
            'job_type' => ['required', 'in:new_installation,repair,reconnection,upgrade,transfer'],
            'scheduled_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $job = InstallationJob::create([
            'client_id' => $validated['client_id'],
            'technician_id' => $validated['technician_id'],
            'job_type' => $validated['job_type'],
            'status' => $validated['technician_id'] ? 'assigned' : 'pending',
            'scheduled_date' => $validated['scheduled_date'],
            'assigned_by' => Auth::id(),
            'notes' => $validated['notes'],
        ]);

        // Update client installation status
        $client = Client::find($validated['client_id']);
        $client->update([
            'installation_status' => $validated['technician_id'] ? 'scheduled' : 'pending',
            'technician_id' => $validated['technician_id'],
        ]);

        return redirect()->route('technicians.jobs')->with('success', 'Installation job created successfully.');
    }

    /**
     * Assign technician to a job.
     */
    public function assignJob(Request $request, InstallationJob $job): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'exists:technicians,id'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        $job->update([
            'technician_id' => $validated['technician_id'],
            'scheduled_date' => $validated['scheduled_date'],
            'status' => 'assigned',
            'assigned_by' => Auth::id(),
        ]);

        // Update client
        $job->client->update([
            'technician_id' => $validated['technician_id'],
            'installation_status' => 'scheduled',
        ]);

        return redirect()->back()->with('success', 'Job assigned successfully.');
    }

    /**
     * Start a job (technician marks as in progress).
     */
    public function startJob(InstallationJob $job): RedirectResponse
    {
        $job->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $job->client->update(['installation_status' => 'in_progress']);

        return redirect()->back()->with('success', 'Job started.');
    }

    /**
     * Complete a job (technician marks as completed).
     */
    public function completeJob(Request $request, InstallationJob $job): RedirectResponse
    {
        $validated = $request->validate([
            'completion_notes' => ['nullable', 'string'],
        ]);

        $job->update([
            'status' => 'completed',
            'completed_by' => Auth::id(),
            'completed_at' => now(),
            'completion_notes' => $validated['completion_notes'],
        ]);

        // Update client - activate after installation
        $job->client->update([
            'installation_status' => 'completed',
            'installation_date' => now(),
            'status' => 'active',
        ]);

        // Make technician available again
        if ($job->technician) {
            $job->technician->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Job completed! Client has been activated.');
    }

    /**
     * Cancel a job.
     */
    public function cancelJob(InstallationJob $job): RedirectResponse
    {
        $job->update(['status' => 'cancelled']);
        
        $job->client->update(['installation_status' => 'cancelled']);

        return redirect()->back()->with('success', 'Job cancelled.');
    }
}
