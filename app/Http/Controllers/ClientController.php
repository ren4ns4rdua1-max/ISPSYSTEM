<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SubscriptionRate;
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

        return view('clients.index', compact('clients', 'search', 'status'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        $subscriptionRates = SubscriptionRate::where('is_active', true)->orderBy('monthly_fee', 'asc')->get();
        return view('clients.create', compact('subscriptionRates'));
    }

    /**
     * Store a newly created client in storage.
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
            'status' => ['required', 'in:active,inactive,suspended,cancelled'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
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
