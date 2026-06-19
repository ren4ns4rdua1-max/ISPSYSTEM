<?php

namespace App\Http\Controllers;

use App\Models\Payment;

use App\Models\Client;
use App\Models\Billing;
use App\Models\AdminNotification;
use App\Mail\PaymentApprovedMail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(Request $request): View
    {
        $search        = $request->get('search', '');
        $paymentMethod = $request->get('payment_method', '');
        $dateFrom      = $request->get('date_from', '');
        $dateTo        = $request->get('date_to', '');

        $query = Payment::with(['client', 'billing'])
            ->when($search, fn($q) => $q->whereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))->orWhere('receipt_number', 'like', "%{$search}%"))
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($dateFrom,      fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo,        fn($q) => $q->whereDate('payment_date', '<=', $dateTo));

        $hasApprovalColumn = Schema::hasColumn('payments', 'approval_status');

        // Group by client: one row per client
        $clientSummaries = $query->get()
            ->groupBy('client_id')
            ->map(function ($rows) use ($hasApprovalColumn) {
                $latest = $rows->sortByDesc('created_at')->first();

                return (object) [
                    'client'                => $latest->client,
                    'client_id'             => $latest->client_id,
                    'total_payments'        => $rows->count(),
                    'total_paid'            => $rows->sum('amount'),
                    'latest_method'         => $latest->payment_method,
                    'latest_method_label'   => $latest->payment_method_label,
                    'latest_date'           => $latest->payment_date,
                    'pending_count'         => $hasApprovalColumn
                        ? $rows->where('approval_status', 'pending')->count()
                        : 0,
                    'latest_approval'       => $hasApprovalColumn ? $latest->approval_status : null,
                    'latest_id'             => $latest->id,
                ];
            })
            ->values();

        $page     = $request->get('page', 1);
        $perPage  = 10;
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $clientSummaries->forPage($page, $perPage),
            $clientSummaries->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total_collected'   => $hasApprovalColumn
                ? Payment::where('approval_status', 'approved')->sum('amount')
                : Payment::sum('amount'),
            'today_collection'  => $hasApprovalColumn
                ? Payment::where('approval_status', 'approved')->whereDate('payment_date', today())->sum('amount')
                : Payment::whereDate('payment_date', today())->sum('amount'),
            'this_month'        => $hasApprovalColumn
                ? Payment::where('approval_status', 'approved')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount')
                : Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
            'transactions_count'=> Payment::count(),
        ];

        $pendingApprovalCount = $hasApprovalColumn
            ? Payment::where('approval_status', 'pending')->count()
            : 0;

        return view('payments.index', compact(
            'payments', 'search', 'paymentMethod', 'dateFrom', 'dateTo', 'stats', 'pendingApprovalCount'
        ));
    }

    /**
     * Return all payments for a client as JSON (for history modal).
     */
    public function clientHistory(Client $client): \Illuminate\Http\JsonResponse
    {
        $history = Payment::where('client_id', $client->id)
            ->with('billing')
            ->latest()
            ->get()
            ->map(fn($p) => [
                'receipt_number'   => $p->receipt_number,
                'invoice_number'   => $p->billing?->invoice_number ?? '-',
                'amount'           => number_format($p->amount, 2),
                'payment_method'   => $p->payment_method_label,
                'payment_date'     => \Carbon\Carbon::parse($p->payment_date)->format('M d, Y'),
                'approval_status'  => $p->approval_status,
                'id'               => $p->id,
            ]);

        return response()->json(['client' => $client->name, 'email' => $client->email, 'history' => $history]);
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
     * Approve a client-submitted payment proof.
     */
    public function approvePayment(Payment $payment): RedirectResponse
    {
        if ($payment->approval_status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $payment->update([
            'approval_status' => 'approved',
            'approved_at'     => now(),
        ]);

        // Only mark billing as paid AFTER admin approves
        if ($payment->billing_id) {
            $totalApproved = Payment::where('billing_id', $payment->billing_id)
                ->where('approval_status', 'approved')
                ->sum('amount');
            $billing = $payment->billing;
            if ($totalApproved >= $billing->total_amount) {
                $billing->update(['status' => 'paid', 'paid_date' => $payment->payment_date]);
            } else {
                $billing->update(['status' => 'partial']);
            }
        }

        // Send Gmail confirmation to client
        Mail::to($payment->client->email)->send(new PaymentApprovedMail($payment));

        return back()->with('success', "Payment from {$payment->client->name} has been approved and confirmation sent via email.");
    }

    /**
     * Reject a client-submitted payment proof.
     */
    public function rejectPayment(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->approval_status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $payment->update([
            'approval_status'  => 'rejected',
            'rejection_reason' => $request->get('reason', 'Payment proof could not be verified.'),
        ]);

        return back()->with('success', "Payment from {$payment->client->name} has been rejected.");
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
