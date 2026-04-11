<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Billing;
use App\Models\SubscriptionRate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    /**
     * Display a listing of sales/activations.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        // Get clients that have at least one billing (sales history)
        $clients = Client::with(['subscriptionRate', 'billings', 'user'])
            ->whereHas('billings')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('pppoe_name', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('start_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('start_date', '<=', $dateTo);
            })
            ->latest()
            ->paginate(10);

// Calculate summary stats for dashboard cards
        $totalSales = Client::whereHas('billings')->count();
        $activeSales = Client::whereHas('billings')->where('status', 'active')->count();
        $monthlySales = Client::whereHas('billings')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->count();
        $totalRevenue = Billing::where('status', 'paid')->sum('total_amount');

        return view('sales.index', compact('clients', 'search', 'status', 'dateFrom', 'dateTo', 'totalSales', 'activeSales', 'monthlySales', 'totalRevenue'));
    }

    /**
     * Show the form for creating a new sale (new client + subscription).
     */
    public function create(): View
    {
        $subscriptionRates = SubscriptionRate::where('is_active', true)->orderBy('monthly_fee', 'asc')->get();
        return view('sales.create', compact('subscriptionRates'));
    }

    /**
     * Store a newly created sale - creates client AND generates initial billing.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate client data
        $clientValidated = $request->validate([
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
        ]);

        // Validate billing data
        $billingValidated = $request->validate([
            'billing_type' => ['required', 'in:monthly,quarterly,yearly,installation,reconnection,other'],
            'billing_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:billing_date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        // Get subscription rate details if provided
        $subscriptionRate = null;
        $planDescription = $clientValidated['plan_description'];
        $amount = 0;

        if (!empty($clientValidated['subscription_rate_id'])) {
            $subscriptionRate = SubscriptionRate::find($clientValidated['subscription_rate_id']);
            if ($subscriptionRate) {
                $amount = $subscriptionRate->monthly_fee;
                // If billing type is quarterly/yearly, calculate accordingly
                if ($billingValidated['billing_type'] === 'quarterly') {
                    $amount = $subscriptionRate->monthly_fee * 3;
                } elseif ($billingValidated['billing_type'] === 'yearly') {
                    $amount = $subscriptionRate->monthly_fee * 12;
                } elseif ($billingValidated['billing_type'] === 'installation') {
                    $amount = $subscriptionRate->installation_fee ?? 0;
                }
            }
        }

        // Calculate total
        $taxAmount = $billingValidated['tax_amount'] ?? 0;
        $discountAmount = $billingValidated['discount_amount'] ?? 0;
        $totalAmount = ($amount + $taxAmount) - $discountAmount;

        // Set default status to active for new sales
        $clientValidated['status'] = 'active';
        $clientValidated['user_id'] = Auth::id();

        // Create the client
        $client = Client::create($clientValidated);

        // Generate billing invoice
        $billingData = [
            'client_id' => $client->id,
            'subscription_rate_id' => $clientValidated['subscription_rate_id'],
            'invoice_number' => Billing::generateInvoiceNumber(),
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'billing_type' => $billingValidated['billing_type'],
            'status' => 'pending',
            'billing_date' => $billingValidated['billing_date'],
            'due_date' => $billingValidated['due_date'],
            'notes' => $billingValidated['notes'] ?? 'Initial billing for new client subscription',
            'created_by' => Auth::id(),
        ];

        $billing = Billing::create($billingData);

        // Redirect based on action
        if ($request->has('create_payment')) {
            return redirect()->route('payments.create', ['client_id' => $client->id, 'billing_id' => $billing->id])
                ->with('success', 'Client created successfully! Please record the payment.');
        }

        return redirect()->route('sales.index')
            ->with('success', "Sale completed! Client '{$client->name}' has been registered with invoice #{$billing->invoice_number}.");
    }

    /**
     * Display the sales details for a client.
     */
    public function show(Client $client): View
    {
        $client->load(['subscriptionRate', 'user', 'billings.payments', 'billings.creator']);
        return view('sales.show', compact('client'));
    }

    /**
     * Quick activation - create client and mark billing as paid immediately.
     */
    public function quickActivate(Request $request): RedirectResponse
    {
        // Validate all required data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'pppoe_name' => ['required', 'string', 'max:255', 'unique:clients,pppoe_name'],
            'barangay' => ['required', 'string', 'max:255'],
            'nap_box' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'plan_description' => ['required', 'string', 'max:255'],
            'subscription_rate_id' => ['nullable', 'exists:subscription_rates,id'],
            'payment_method' => ['required', 'in:cash,bank_transfer,gcash,paymaya,cheque,other'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        // Calculate due date (default 7 days from now)
        $dueDate = now()->addDays(7);

        // Get subscription rate
        $subscriptionRate = null;
        $amount = 0;
        if ($request->subscription_rate_id) {
            $subscriptionRate = SubscriptionRate::find($request->subscription_rate_id);
            if ($subscriptionRate) {
                $amount = $subscriptionRate->monthly_fee;
            }
        }

        // Create client
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'pppoe_name' => $request->pppoe_name,
            'barangay' => $request->barangay,
            'nap_box' => $request->nap_box,
            'start_date' => $request->start_date,
            'plan_description' => $request->plan_description,
            'due_date_time' => $dueDate,
            'subscription_rate_id' => $request->subscription_rate_id,
            'status' => 'active',
            'user_id' => Auth::id(),
        ]);

        // Create billing
        $billing = Billing::create([
            'client_id' => $client->id,
            'subscription_rate_id' => $request->subscription_rate_id,
            'invoice_number' => Billing::generateInvoiceNumber(),
            'amount' => $amount,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $amount,
            'billing_type' => 'monthly',
            'status' => 'paid',
            'billing_date' => now()->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'paid_date' => now()->toDateString(),
            'payment_method' => $request->payment_method,
            'notes' => 'Quick activation - payment received',
            'created_by' => Auth::id(),
        ]);

        // Create payment record
        $payment = \App\Models\Payment::create([
            'client_id' => $client->id,
            'billing_id' => $billing->id,
            'user_id' => Auth::id(),
            'receipt_number' => \App\Models\Payment::generateReceiptNumber(),
            'amount' => $request->amount_paid,
            'total_paid' => $request->amount_paid,
            'change_amount' => max(0, $request->amount_paid - $amount),
            'payment_method' => $request->payment_method,
            'payment_date' => now()->toDateString(),
            'notes' => 'Payment received during quick activation',
        ]);

        return redirect()->route('sales.show', $client->id)
            ->with('success', "Client '{$client->name}' has been activated successfully!");
    }
}
