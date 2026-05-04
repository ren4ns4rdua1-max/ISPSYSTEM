<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SubscriptionRate;
use App\Models\Technician;
use App\Models\InstallationJob;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $clients = Client::with(['subscriptionRate', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('pppoe_name', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

// Get available technicians for the approve & assign modal
        $technicians = Technician::whereIn('status', ['available', 'busy'])
            ->orderBy('name')
            ->get();

        return view('clients.index', compact('clients', 'search', 'status', 'technicians'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        $subscriptionRates = SubscriptionRate::where('is_active', true)->orderBy('monthly_fee', 'asc')->get();
        $technicians = Technician::whereIn('status', ['available', 'busy'])->orderBy('name')->get();
        return view('clients.create', compact('subscriptionRates', 'technicians'));
    }

/**
     * Store a newly created client from public/guest registration.
     * Called from welcome page "Apply" button.
     * No authentication required - creates pending client for admin approval.
     * 
     * User only fills BASIC info - admin fills the rest when approving.
     */
    public function storeGuest(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:clients,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'barangay'     => ['required', 'string', 'max:255'],
            'plan_selected'=> ['required', 'string', 'max:255'],
            'notes'        => ['nullable', 'string'],
            'photo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $pppoeBase = strtolower(explode('@', $validated['email'])[0]);
        $validated['pppoe_name']      = $pppoeBase . '_' . rand(100, 999);
        $validated['nap_box']         = 'TBD';
        $validated['start_date']      = now()->addDays(7);
        $validated['due_date_time']   = now()->addDays(7)->setTime(12, 0);
        $validated['plan_description']= $validated['plan_selected'];
        $validated['status']          = 'pending_approval';
        $validated['user_id']         = null;

        unset($validated['plan_selected']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $client = Client::create($validated);

        return response()->json([
            'success'   => true,
            'message'   => 'Application submitted successfully! Your application is pending admin approval. You will be notified once reviewed.',
            'client_id' => $client->id,
        ]);
    }

    /**
     * Store a newly created client in storage.
     * New clients are set to pending_approval by default.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'pppoe_name' => ['required', 'string', 'max:255', 'unique:clients,pppoe_name'],
            'barangay' => ['required', 'string', 'max:255'],
            'nap_box' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'plan_description' => ['required', 'string', 'max:255'],
            'due_date_time' => ['required', 'date'],
            'subscription_rate_id' => ['nullable', 'exists:subscription_rates,id'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'technician_id' => ['nullable', 'exists:technicians,id'],
            'job_type' => ['nullable', 'in:new_installation,repair,reconnection,upgrade,transfer'],
            'scheduled_date' => ['nullable', 'date'],
            'job_notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending_approval';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $client = Client::create(array_diff_key($validated, array_flip(['technician_id', 'job_type', 'scheduled_date', 'job_notes'])));

        if ($request->filled('technician_id') && $request->filled('scheduled_date')) {
            InstallationJob::create([
                'client_id' => $client->id,
                'technician_id' => $validated['technician_id'],
                'assigned_by' => auth()->id(),
                'job_type' => $validated['job_type'] ?? 'new_installation',
                'status' => 'assigned',
                'scheduled_date' => $validated['scheduled_date'],
                'notes' => $validated['job_notes'] ?? null,
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Client registered successfully. Pending admin approval.');
    }

/**
     * Approve a pending client registration.
     */
    public function approve(Client $client): RedirectResponse
    {
        $client->approve();
        
        return redirect()->back()->with('success', "Client '{$client->name}' has been approved and is now active.");
    }

    /**
     * Reject a pending client registration.
     */
    public function reject(Client $client): RedirectResponse
    {
        $client->reject();
        
        return redirect()->back()->with('success', "Client '{$client->name}' has been rejected.");
    }

    /**
     * Approve client and assign a task to a technician in one workflow.
     */
    public function approveAndAssign(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'exists:technicians,id'],
            'scheduled_date' => ['required', 'date'],
            'job_type' => ['required', 'in:new_installation,repair,reconnection,upgrade,transfer'],
            'notes' => ['nullable', 'string'],
        ]);

        // Approve the client
        $client->approve();

        // Create installation job
        InstallationJob::create([
            'client_id' => $client->id,
            'technician_id' => $validated['technician_id'],
            'assigned_by' => auth()->id(),
            'job_type' => $validated['job_type'],
            'status' => 'assigned',
            'scheduled_date' => $validated['scheduled_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', "Client '{$client->name}' has been approved and task assigned to technician.");
    }

    /**
     * Get list of available technicians (AJAX).
     */
    public function getTechnicians(): \Illuminate\Http\JsonResponse
    {
        $technicians = Technician::where('status', 'available')
            ->orWhere('status', 'busy')
            ->orderBy('name')
            ->get(['id', 'name', 'status', 'specialization']);

        return response()->json($technicians);
    }

/**
     * Display a listing of clients pending approval.
     */
    public function pending(Request $request): View
    {
        $search = $request->get('search', '');

        $pendingClients = Client::with(['subscriptionRate', 'user'])
            ->where('status', 'pending_approval')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        $pendingCount = Client::where('status', 'pending_approval')->count();
        
        // Get available technicians for the approve & assign modal
        $technicians = Technician::whereIn('status', ['available', 'busy'])
            ->orderBy('name')
            ->get();

        return view('clients.pending', compact('pendingClients', 'search', 'pendingCount', 'technicians'));
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): View
    {
        $client->load(['subscriptionRate', 'user']);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View
    {
        $subscriptionRates = SubscriptionRate::where('is_active', true)->orderBy('monthly_fee', 'asc')->get();
        return view('clients.edit', compact('client', 'subscriptionRates'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email,' . $client->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'pppoe_name' => ['required', 'string', 'max:255', 'unique:clients,pppoe_name,' . $client->id],
            'barangay' => ['required', 'string', 'max:255'],
            'nap_box' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'plan_description' => ['required', 'string', 'max:255'],
            'due_date_time' => ['required', 'date'],
            'subscription_rate_id' => ['nullable', 'exists:subscription_rates,id'],
            'status' => ['required', 'in:active,inactive,suspended,cancelled'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($client->photo) {
                Storage::disk('public')->delete($client->photo);
            }
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
