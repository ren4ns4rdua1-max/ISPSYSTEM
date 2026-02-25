<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $paymentMethod = $request->get('payment_method', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        $payments = Payment::with(['client', 'billing', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('receipt_number', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhere('payment_reference', 'like', "%{$search}%");
                });
            })
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('payment_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('payment_date', '<=', $dateTo);
            })
            ->latest()
            ->paginate(10);

        // Calculate summary stats
        $stats = [
            'total_collected' => Payment::sum('amount'),
            'today_collection' => Payment::whereDate('payment_date', today())->sum('amount'),
            'this_month' => Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
            'transactions_count' => Payment::count(),
        ];

        return view('payments.index', compact('payments', 'search', 'paymentMethod', 'dateFrom', 'dateTo', 'stats'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request): View
    {
        $clients = Client::with('subscriptionRate')->where('status', 'active')->orderBy('name', 'asc')->get();
        
        // Get pending/overdue bills that can be paid
        $pendingBills = Billing::with('client')
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();
        
        $prefillClientId = $request->get('client_id');
        $prefillBillingId = $request->get('billing_id');
        
        return view('payments.create', compact('clients', 'pendingBills', 'prefillClientId', 'prefillBillingId'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'billing_id' => ['nullable', 'exists:billings,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'total_paid' => ['required', 'numeric', 'min:0'],
            'change_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,bank_transfer,gcash,paymaya,cheque,other'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        // Generate receipt number
        $validated['receipt_number'] = Payment::generateReceiptNumber();
        
        // Set user_id
        $validated['user_id'] = Auth::id();

        // Create payment
        $payment = Payment::create($validated);

        // If billing_id is provided, update the billing status
        if (!empty($validated['billing_id'])) {
            $billing = Billing::find($validated['billing_id']);
            if ($billing) {
                // Check if fully paid
                $totalPaid = Payment::where('billing_id', $billing->id)->sum('amount');
                if ($totalPaid >= $billing->total_amount) {
                    $billing->update([
                        'status' => 'paid',
                        'paid_date' => now()->toDateString(),
                    ]);
                } elseif ($totalPaid > 0) {
                    $billing->update(['status' => 'partial']);
                }
            }
        }

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['client', 'billing', 'user']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment): View
    {
        $clients = Client::orderBy('name', 'asc')->get();
        $pendingBills = Billing::with('client')
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->orWhere('id', $payment->billing_id)
            ->orderBy('due_date', 'asc')
            ->get();
        
        return view('payments.edit', compact('payment', 'clients', 'pendingBills'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $oldBillingId = $payment->billing_id;

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'billing_id' => ['nullable', 'exists:billings,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'total_paid' => ['required', 'numeric', 'min:0'],
            'change_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,bank_transfer,gcash,paymaya,cheque,other'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment->update($validated);

        // Recalculate billing status if billing was changed or payment amount changed
        $this->updateBillingStatus($oldBillingId);
        $this->updateBillingStatus($payment->billing_id);

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $billingId = $payment->billing_id;
        $payment->delete();
        
        // Update billing status after deletion
        $this->updateBillingStatus($billingId);
        
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    /**
     * Helper to update billing status based on payments.
     */
    private function updateBillingStatus(?int $billingId): void
    {
        if (!$billingId) return;
        
        $billing = Billing::find($billingId);
        if (!$billing) return;
        
        $totalPaid = Payment::where('billing_id', $billingId)->sum('amount');
        
        if ($totalPaid >= $billing->total_amount) {
            $billing->update([
                'status' => 'paid',
                'paid_date' => now()->toDateString(),
            ]);
        } elseif ($totalPaid > 0) {
            $billing->update(['status' => 'partial']);
        } else {
            $billing->update(['status' => 'pending', 'paid_date' => null]);
        }
    }

    /**
     * Get client's pending bills for AJAX.
     */
    public function getClientBills(Client $client): array
    {
        $pendingBills = Billing::where('client_id', $client->id)
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();
        
        $totalOutstanding = $pendingBills->sum('total_amount');
        
        return [
            'client' => $client,
            'pending_bills' => $pendingBills,
            'total_outstanding' => $totalOutstanding,
        ];
    }
}
