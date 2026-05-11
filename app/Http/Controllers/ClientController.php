<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SubscriptionRate;
use App\Models\Technician;
use App\Models\InstallationJob;
use App\Models\Billing;
use App\Models\AdminNotification;
use App\Mail\ClientApprovedMail;
use App\Mail\ClientVerifyEmailMail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $pppoeBase = strtolower(explode('@', $validated['email'])[0]);
        $validated['pppoe_name']      = $pppoeBase . '_' . rand(100, 999);
        $validated['nap_box']         = 'TBD';
        $validated['start_date']      = now()->addDays(7);
        $validated['due_date_time']   = now()->addDays(7)->setTime(12, 0);
        $validated['plan_description']= $validated['plan_selected'];
        $validated['status']          = 'pending_approval';
        $validated['user_id']         = null;
        $validated['email_verification_token'] = Str::random(64);
        $validated['email_verified_at']        = null;

        unset($validated['plan_selected']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $client = Client::create($validated);

        try {
            Mail::to($client->email)->send(new ClientVerifyEmailMail($client));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email to ' . $client->email . ': ' . $e->getMessage());
        }

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

    public function mapData(): \Illuminate\Http\JsonResponse
    {
        $clients = Client::whereNotNull('latitude')->whereNotNull('longitude')
            ->get(['id', 'name', 'barangay', 'nap_box', 'phone_number', 'status', 'latitude', 'longitude', 'plan_description']);
        return response()->json($clients);
    }

    public function emailPreview(Client $client)
    {
        $mailable = new ClientApprovedMail($client);
        return response($mailable->render());
    }

    /**
     * Verify client email via token link.
     */
    public function verifyEmail(string $token)
    {
        $client = Client::where('email_verification_token', $token)->first();

        if (!$client) {
            return view('clients.verify-email-result', ['success' => false, 'message' => 'Invalid or expired verification link.']);
        }

        if ($client->email_verified_at) {
            return view('clients.verify-email-result', ['success' => true, 'message' => 'Your email is already verified. Please wait for admin approval.']);
        }

        $client->update([
            'email_verified_at'        => now(),
            'email_verification_token' => null,
        ]);

        return view('clients.verify-email-result', ['success' => true, 'message' => 'Your email has been verified! Your application is now pending admin review. We will notify you once approved.']);
    }

/**
     * Approve a pending client registration.
     */
    public function approve(Client $client): RedirectResponse
    {
        if (!$client->email_verified_at) {
            return redirect()->back()->with('error', "Cannot approve '{$client->name}' — email not yet verified by the client.");
        }
        $client->approve();

        // Auto-create initial billing on approval
        $this->createInitialBilling($client);

        // Notify admins
        AdminNotification::notifyAdmins(
            AdminNotification::TYPE_CLIENT_APPROVED,
            'Client Approved',
            "Client '{$client->name}' has been approved. Initial billing has been generated.",
            ['client_id' => $client->id]
        );

        try {
            Mail::to($client->email)->send(new ClientApprovedMail($client));
            \Log::info('Approval email sent to ' . $client->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email to ' . $client->email . ': ' . $e->getMessage());
        }

        return redirect()->route('billings.index')->with('success', "Client '{$client->name}' approved. Initial billing created — please review.");
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
        if (!$client->email_verified_at) {
            return redirect()->back()->with('error', "Cannot assign '{$client->name}' — email not yet verified by the client.");
        }
        $validated = $request->validate([
            'technician_id'  => ['required', 'exists:technicians,id'],
            'scheduled_date' => ['required', 'date'],
            'job_type'       => ['required', 'in:new_installation,repair,reconnection,upgrade,transfer'],
            'notes'          => ['nullable', 'string'],
            'override_email' => ['nullable', 'email'],
            'custom_message' => ['nullable', 'string'],
        ]);

        $client->update(['status' => 'pending_installation']);

        $job = InstallationJob::create([
            'client_id'      => $client->id,
            'technician_id'  => $validated['technician_id'],
            'assigned_by'    => auth()->id(),
            'job_type'       => $validated['job_type'],
            'status'         => 'assigned',
            'scheduled_date' => $validated['scheduled_date'],
            'notes'          => $validated['notes'] ?? null,
        ]);

        $technician = Technician::find($validated['technician_id']);

        // Billing is NOT created here — it will be created when the technician completes the job

        // Notify admins
        AdminNotification::notifyAdmins(
            AdminNotification::TYPE_CLIENT_APPROVED,
            'Client Approved & Assigned',
            "Client '{$client->name}' approved and assigned to {$technician->name}. Initial billing generated.",
            ['client_id' => $client->id, 'technician_id' => $technician->id]
        );

        $sendTo = !empty($validated['override_email']) ? $validated['override_email'] : $client->email;

        try {
            Mail::to($sendTo)->send(new ClientApprovedMail($client, $technician, $job, $validated['custom_message'] ?? null));
            \Log::info('Approval+assign email sent to ' . $sendTo . ' | Technician: ' . $technician->name);
        } catch (\Exception $e) {
            \Log::error('Failed to send approval+assign email to ' . $sendTo . ': ' . $e->getMessage());
        }

        return redirect()->route('clients.index')->with('success', "Client '{$client->name}' assigned to {$technician->name}. Waiting for technician to complete the job.");
    }

    /**
     * Auto-create initial billing when a client is approved.
     * Called publicly by technician controllers on job completion.
     */
    public function createBillingForClient(Client $client): void
    {
        $this->createInitialBilling($client);
    }

    /**
     * Auto-create initial billing when a client is approved.
     */
    private function createInitialBilling(Client $client): void
    {
        // Skip if billing already exists
        if ($client->billings()->exists()) {
            return;
        }

        $amount = 0;
        if ($client->subscriptionRate) {
            $amount = $client->subscriptionRate->monthly_fee;
        }

        $tax      = 0;
        $discount = 0;
        $total    = $amount;

        Billing::create([
            'client_id'           => $client->id,
            'subscription_rate_id'=> $client->subscription_rate_id,
            'invoice_number'      => Billing::generateInvoiceNumber(),
            'amount'              => $amount,
            'tax_amount'          => $tax,
            'discount_amount'     => $discount,
            'total_amount'        => $total,
            'billing_type'        => 'monthly',
            'status'              => 'pending',
            'billing_date'        => now()->toDateString(),
            'due_date'            => $client->due_date_time
                                        ? $client->due_date_time->toDateString()
                                        : now()->addMonth()->toDateString(),
            'notes'               => 'Initial billing — auto-generated on client approval.',
            'created_by'          => auth()->id(),
        ]);
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
            'status' => ['required', 'in:active,inactive,suspended,cancelled,pending_approval'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($client->photo) {
                Storage::disk('public')->delete($client->photo);
            }
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        } elseif ($request->input('remove_photo') == '1') {
            if ($client->photo) {
                Storage::disk('public')->delete($client->photo);
            }
            $validated['photo'] = null;
        } else {
            unset($validated['photo']);
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

