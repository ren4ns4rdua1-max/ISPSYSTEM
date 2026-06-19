<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use App\Models\SubscriptionRate;
use App\Models\Payment;
use App\Mail\BillingDueNoticeMail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BillingController extends Controller
{
    /**
     * Display a listing of the billings.
     */
    public function index(Request $request): View
    {
        $search      = $request->get('search', '');
        $status      = $request->get('status', '');
        $billingType = $request->get('billing_type', '');
        $dateFrom    = $request->get('date_from', '');
        $dateTo      = $request->get('date_to', '');

        // Build base query filtered
        $query = Billing::with(['client', 'subscriptionRate'])
            ->when($search, fn($q) => $q->whereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))->orWhere('invoice_number', 'like', "%{$search}%"))
            ->when($status,      fn($q) => $q->where('status', $status))
            ->when($billingType, fn($q) => $q->where('billing_type', $billingType))
            ->when($dateFrom,    fn($q) => $q->whereDate('billing_date', '>=', $dateFrom))
            ->when($dateTo,      fn($q) => $q->whereDate('billing_date', '<=', $dateTo));

        // Group by client: one row per client with aggregated summary
        $clientSummaries = $query->get()
            ->groupBy('client_id')
            ->map(function ($rows) {
                $latest = $rows->sortByDesc('created_at')->first();
                return (object)[
                    'client'         => $latest->client,
                    'total_invoices' => $rows->count(),
                    'total_amount'   => $rows->sum('total_amount'),
                    'paid_amount'    => $rows->where('status', 'paid')->sum('total_amount'),
                    'unpaid_amount'  => $rows->whereIn('status', ['pending','overdue','partial'])->sum('total_amount'),
                    'has_overdue'    => $rows->where('status', 'overdue')->count() > 0,
                    'has_pending'    => $rows->whereIn('status', ['pending','overdue','partial'])->count() > 0,
                    'latest_due'     => $rows->sortByDesc('due_date')->first()->due_date,
                    'latest_status'  => $latest->status,
                    'client_id'      => $latest->client_id,
                ];
            })->values();

        // Paginate manually
        $page     = $request->get('page', 1);
        $perPage  = 10;
        $billings = new \Illuminate\Pagination\LengthAwarePaginator(
            $clientSummaries->forPage($page, $perPage),
            $clientSummaries->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total_revenue'  => Billing::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Billing::where('status', 'pending')->sum('total_amount'),
            'overdue_amount' => Billing::where('status', 'overdue')->sum('total_amount'),
            'pending_count'  => Billing::where('status', 'pending')->count(),
            'overdue_count'  => Billing::where('status', 'overdue')->count(),
        ];

        return view('billings.index', compact('billings', 'search', 'status', 'billingType', 'dateFrom', 'dateTo', 'stats'));
    }

    /**
     * Return all billings for a client as JSON (for history modal).
     */
    public function clientHistory(Client $client): \Illuminate\Http\JsonResponse
    {
        $history = Billing::where('client_id', $client->id)
            ->with('subscriptionRate')
            ->latest()
            ->get()
            ->map(fn($b) => [
                'invoice_number' => $b->invoice_number,
                'billing_type'   => ucfirst($b->billing_type),
                'total_amount'   => number_format($b->total_amount, 2),
                'billing_date'   => $b->billing_date->format('M d, Y'),
                'due_date'       => $b->due_date->format('M d, Y'),
                'status'         => $b->status,
                'id'             => $b->id,
            ]);

        return response()->json(['client' => $client->name, 'email' => $client->email, 'history' => $history]);
    }

    /**
     * Show the form for creating a new billing.
     */
    public function create(Request $request): View
    {
        $clients = Client::with('subscriptionRate')->whereIn('status', ['active', 'pending_installation'])->orderBy('name', 'asc')->get();
        $subscriptionRates = SubscriptionRate::where('is_active', true)->orderBy('monthly_fee', 'asc')->get();
        $prefillClientId = $request->get('client_id');
        
        return view('billings.create', compact('clients', 'subscriptionRates', 'prefillClientId'));
    }

    /**
     * Store a newly created billing in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'subscription_rate_id' => ['nullable', 'exists:subscription_rates,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'billing_type' => ['required', 'in:monthly,quarterly,yearly,installation,reconnection,other'],
            'status' => ['required', 'in:pending,paid,overdue,cancelled,partial'],
            'billing_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:billing_date'],
            'paid_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Calculate total
        $totalAmount = ($validated['amount'] + ($validated['tax_amount'] ?? 0)) - ($validated['discount_amount'] ?? 0);
        $validated['total_amount'] = $totalAmount;
        
        // Generate invoice number
        $validated['invoice_number'] = Billing::generateInvoiceNumber();
        
        // Set created by
        $validated['created_by'] = Auth::id();

        // If status is paid, set paid_date if not provided
        if ($validated['status'] === 'paid' && empty($validated['paid_date'])) {
            $validated['paid_date'] = now()->toDateString();
        }

        Billing::create($validated);

        return redirect()->route('billings.index')->with('success', 'Billing created successfully.');
    }

    /**
     * Display the specified billing.
     */
    public function show(Billing $billing): View
    {
        $billing->load(['client', 'subscriptionRate', 'creator', 'payments']);
        return view('billings.show', compact('billing'));
    }

    /**
     * Show the form for editing the specified billing.
     */
    public function edit(Billing $billing): View
    {
        $clients = Client::orderBy('name', 'asc')->get();
        $subscriptionRates = SubscriptionRate::where('is_active', true)->orderBy('monthly_fee', 'asc')->get();
        return view('billings.edit', compact('billing', 'clients', 'subscriptionRates'));
    }

    /**
     * Update the specified billing in storage.
     */
    public function update(Request $request, Billing $billing): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'subscription_rate_id' => ['nullable', 'exists:subscription_rates,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'billing_type' => ['required', 'in:monthly,quarterly,yearly,installation,reconnection,other'],
            'status' => ['required', 'in:pending,paid,overdue,cancelled,partial'],
            'billing_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:billing_date'],
            'paid_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Calculate total
        $totalAmount = ($validated['amount'] + ($validated['tax_amount'] ?? 0)) - ($validated['discount_amount'] ?? 0);
        $validated['total_amount'] = $totalAmount;

        // If status is paid, set paid_date if not provided
        if ($validated['status'] === 'paid' && empty($validated['paid_date'])) {
            $validated['paid_date'] = now()->toDateString();
        }

        // If status changed from paid to something else, clear paid_date
        if ($billing->status === 'paid' && $validated['status'] !== 'paid') {
            $validated['paid_date'] = null;
        }

        $billing->update($validated);

        return redirect()->route('billings.index')->with('success', 'Billing updated successfully.');
    }

    /**
     * Remove the specified billing from storage.
     */
    public function destroy(Billing $billing): RedirectResponse
    {
        $billing->delete();
        return redirect()->route('billings.index')->with('success', 'Billing deleted successfully.');
    }

    /**
     * Mark billing as paid and create payment record.
     */
    public function markAsPaid(Request $request, Billing $billing): RedirectResponse
    {
        $paidDate = $request->get('paid_date', now()->toDateString());
        $paymentMethod = $request->get('payment_method', 'cash');
        
        // Update billing status
        $billing->update([
            'status' => 'paid',
            'paid_date' => $paidDate,
            'payment_method' => $paymentMethod,
            'payment_reference' => $request->get('payment_reference'),
        ]);

        // Create payment record
        $payment = Payment::create([
            'client_id' => $billing->client_id,
            'billing_id' => $billing->id,
            'user_id' => Auth::id(),
            'receipt_number' => Payment::generateReceiptNumber(),
            'amount' => $billing->total_amount,
            'total_paid' => $billing->total_amount,
            'change_amount' => 0,
            'payment_method' => $paymentMethod,
            'payment_reference' => $request->get('payment_reference'),
            'payment_date' => $paidDate,
            'notes' => 'Payment recorded from billing: ' . $billing->invoice_number,
        ]);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Billing marked as paid. Payment record created.');
    }

    /**
     * Send a due/overdue notice email to the client.
     */
    public function sendDueNotice(Billing $billing): RedirectResponse
    {
        if (!in_array($billing->status, ['pending', 'overdue', 'partial'])) {
            return back()->with('error', 'Notice can only be sent for pending or overdue invoices.');
        }

        Mail::to($billing->client->email)->send(new BillingDueNoticeMail($billing));

        return back()->with('success', "Due notice sent to {$billing->client->name} ({$billing->client->email}).");
    }

    /**
     * Suspend the client account for non-payment.
     */
    public function suspendClient(Billing $billing): RedirectResponse
    {
        if (!in_array($billing->status, ['pending', 'overdue', 'partial'])) {
            return back()->with('error', 'Client can only be suspended for unpaid invoices.');
        }

        $billing->client->update(['status' => 'suspended']);
        $billing->update(['status' => 'overdue']);

        return back()->with('success', "{$billing->client->name}'s account has been suspended due to non-payment.");
    }

    /**
     * Get client details for AJAX.
     */
    public function getClientDetails(Client $client): \Illuminate\Http\JsonResponse
    {
        $client->load('subscriptionRate');

        // Try to find matching subscription rate by plan_description if not directly linked
        $matchedRate = $client->subscriptionRate;
        if (!$matchedRate && $client->plan_description) {
            $matchedRate = SubscriptionRate::where('is_active', true)
                ->get()
                ->first(function ($rate) use ($client) {
                    return str_contains(
                        strtolower($client->plan_description),
                        strtolower($rate->plan_name)
                    );
                });
        }

        return response()->json([
            'id'                   => $client->id,
            'name'                 => $client->name,
            'email'                => $client->email,
            'phone_number'         => $client->phone_number,
            'pppoe_name'           => $client->pppoe_name,
            'barangay'             => $client->barangay,
            'plan_description'     => $client->plan_description,
            'due_date_time'        => $client->due_date_time?->format('Y-m-d'),
            'subscription_rate_id' => $matchedRate?->id ?? $client->subscription_rate_id,
            'monthly_fee'          => $matchedRate?->monthly_fee ?? 0,
            'plan_name'            => $matchedRate?->plan_name ?? $client->plan_description,
            'outstanding'          => Billing::where('client_id', $client->id)
                                        ->whereIn('status', ['pending', 'overdue', 'partial'])
                                        ->count(),
        ]);
    }
}
