<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\Technician;
use App\Models\InstallationJob;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request)
    {
        // Get date filters
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Client Statistics
        $clientStats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'active')->count(),
            'inactive' => Client::where('status', 'inactive')->count(),
            'suspended' => Client::where('status', 'suspended')->count(),
            'cancelled' => Client::where('status', 'cancelled')->count(),
            'new_this_month' => Client::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count(),
        ];

        // Billing Statistics
        $billingStats = [
            'total_invoices' => Billing::count(),
            'total_amount' => Billing::sum('total_amount'),
            'paid' => Billing::where('status', 'paid')->count(),
            'paid_amount' => Billing::where('status', 'paid')->sum('total_amount'),
            'pending' => Billing::where('status', 'pending')->count(),
            'pending_amount' => Billing::where('status', 'pending')->sum('total_amount'),
            'overdue' => Billing::where('status', 'overdue')->count(),
            'overdue_amount' => Billing::where('status', 'overdue')->sum('total_amount'),
            'period_invoices' => Billing::whereBetween('billing_date', [$startDate, $endDate])->count(),
            'period_amount' => Billing::whereBetween('billing_date', [$startDate, $endDate])->sum('total_amount'),
        ];

        // Payment Statistics
        $paymentStats = [
            'total_payments' => Payment::count(),
            'total_collected' => Payment::sum('amount'),
            'period_payments' => Payment::whereBetween('payment_date', [$startDate, $endDate])->count(),
            'period_collected' => Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'by_method' => [
                'cash' => Payment::where('payment_method', 'cash')->sum('amount'),
                'bank_transfer' => Payment::where('payment_method', 'bank_transfer')->sum('amount'),
                'gcash' => Payment::where('payment_method', 'gcash')->sum('amount'),
                'paymaya' => Payment::where('payment_method', 'paymaya')->sum('amount'),
                'cheque' => Payment::where('payment_method', 'cheque')->sum('amount'),
                'other' => Payment::where('payment_method', 'other')->sum('amount'),
            ],
        ];

        // Technician Statistics
        $technicianStats = [
            'total' => Technician::count(),
            'available' => Technician::where('status', 'available')->count(),
            'busy' => Technician::where('status', 'busy')->count(),
            'offduty' => Technician::where('status', 'offduty')->count(),
        ];

        // Installation Job Statistics
        $jobStats = [
            'total' => InstallationJob::count(),
            'pending' => InstallationJob::where('status', 'pending')->count(),
            'assigned' => InstallationJob::where('status', 'assigned')->count(),
            'in_progress' => InstallationJob::where('status', 'in_progress')->count(),
            'completed' => InstallationJob::where('status', 'completed')->count(),
            'cancelled' => InstallationJob::where('status', 'cancelled')->count(),
            'period_completed' => InstallationJob::where('status', 'completed')
                ->whereBetween('completed_at', [$startDate, $endDate])->count(),
        ];

        // Recent Activity
        $recentClients = Client::latest()->take(5)->get();
        $recentPayments = Payment::with('client')->latest()->take(5)->get();
        $recentJobs = InstallationJob::with(['client', 'technician'])->latest()->take(5)->get();

        // Monthly Income Chart Data (last 12 months)
        $monthlyIncome = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyIncome[] = Payment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');
        }

        // Client Growth Chart Data (last 12 months)
        $monthlyClients = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyClients[] = Client::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return view('reports.index', compact(
            'clientStats',
            'billingStats',
            'paymentStats',
            'technicianStats',
            'jobStats',
            'recentClients',
            'recentPayments',
            'recentJobs',
            'monthlyIncome',
            'monthlyLabels',
            'monthlyClients',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export reports data.
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date',   Carbon::now()->endOfMonth()->format('Y-m-d'));

        $filename = 'report_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($startDate, $endDate) {
            $out = fopen('php://output', 'w');

            // ── CLIENT STATS ──────────────────────────────────────────────
            fputcsv($out, ['=== CLIENT OVERVIEW ===']);
            fputcsv($out, ['Metric', 'Value']);
            fputcsv($out, ['Total Clients',      Client::count()]);
            fputcsv($out, ['Active',             Client::where('status','active')->count()]);
            fputcsv($out, ['Inactive',           Client::where('status','inactive')->count()]);
            fputcsv($out, ['Suspended',          Client::where('status','suspended')->count()]);
            fputcsv($out, ['Cancelled',          Client::where('status','cancelled')->count()]);
            fputcsv($out, ['New This Month',     Client::whereBetween('created_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])->count()]);
            fputcsv($out, []);

            // ── BILLING STATS ─────────────────────────────────────────────
            fputcsv($out, ['=== BILLING SUMMARY ===']);
            fputcsv($out, ['Metric', 'Count', 'Amount (PHP)']);
            fputcsv($out, ['Total Invoices', Billing::count(), number_format(Billing::sum('total_amount'),2)]);
            fputcsv($out, ['Paid',    Billing::where('status','paid')->count(),    number_format(Billing::where('status','paid')->sum('total_amount'),2)]);
            fputcsv($out, ['Pending', Billing::where('status','pending')->count(), number_format(Billing::where('status','pending')->sum('total_amount'),2)]);
            fputcsv($out, ['Overdue', Billing::where('status','overdue')->count(), number_format(Billing::where('status','overdue')->sum('total_amount'),2)]);
            fputcsv($out, ['Period Invoices ('.$startDate.' – '.$endDate.')', Billing::whereBetween('billing_date',[$startDate,$endDate])->count(), number_format(Billing::whereBetween('billing_date',[$startDate,$endDate])->sum('total_amount'),2)]);
            fputcsv($out, []);

            // ── PAYMENT STATS ─────────────────────────────────────────────
            fputcsv($out, ['=== PAYMENT COLLECTION ===']);
            fputcsv($out, ['Metric', 'Count', 'Amount (PHP)']);
            fputcsv($out, ['Total Payments',   Payment::count(), number_format(Payment::sum('amount'),2)]);
            fputcsv($out, ['Period Payments',  Payment::whereBetween('payment_date',[$startDate,$endDate])->count(), number_format(Payment::whereBetween('payment_date',[$startDate,$endDate])->sum('amount'),2)]);
            fputcsv($out, []);
            fputcsv($out, ['=== PAYMENT BY METHOD ===']);
            fputcsv($out, ['Method', 'Amount (PHP)']);
            foreach (['cash','bank_transfer','gcash','paymaya','cheque','other'] as $method) {
                fputcsv($out, [ucfirst(str_replace('_',' ',$method)), number_format(Payment::where('payment_method',$method)->sum('amount'),2)]);
            }
            fputcsv($out, []);

            // ── TECHNICIAN STATS ──────────────────────────────────────────
            fputcsv($out, ['=== TECHNICIAN OVERVIEW ===']);
            fputcsv($out, ['Metric', 'Value']);
            fputcsv($out, ['Total Technicians', Technician::count()]);
            fputcsv($out, ['Available',         Technician::where('status','available')->count()]);
            fputcsv($out, ['Busy',              Technician::where('status','busy')->count()]);
            fputcsv($out, ['Off Duty',          Technician::where('status','offduty')->count()]);
            fputcsv($out, []);

            // ── JOB STATS ─────────────────────────────────────────────────
            fputcsv($out, ['=== INSTALLATION JOBS ===']);
            fputcsv($out, ['Metric', 'Value']);
            fputcsv($out, ['Total',       InstallationJob::count()]);
            fputcsv($out, ['Pending',     InstallationJob::where('status','pending')->count()]);
            fputcsv($out, ['Assigned',    InstallationJob::where('status','assigned')->count()]);
            fputcsv($out, ['In Progress', InstallationJob::where('status','in_progress')->count()]);
            fputcsv($out, ['Completed',   InstallationJob::where('status','completed')->count()]);
            fputcsv($out, ['Cancelled',   InstallationJob::where('status','cancelled')->count()]);
            fputcsv($out, ['Period Completed ('.$startDate.' – '.$endDate.')', InstallationJob::where('status','completed')->whereBetween('completed_at',[$startDate,$endDate])->count()]);
            fputcsv($out, []);

            // ── RECENT PAYMENTS ───────────────────────────────────────────
            fputcsv($out, ['=== RECENT PAYMENTS (Last 20) ===']);
            fputcsv($out, ['Date','Client','Amount (PHP)','Method','Reference']);
            Payment::with('client')->latest()->take(20)->get()->each(function ($p) use ($out) {
                fputcsv($out, [
                    $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d') : '',
                    $p->client->name ?? 'N/A',
                    number_format($p->amount, 2),
                    ucfirst(str_replace('_',' ', $p->payment_method ?? '')),
                    $p->reference_number ?? '',
                ]);
            });

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
