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
        // This can be expanded to export PDF/Excel reports
        return redirect()->route('reports.index')->with('success', 'Export feature coming soon!');
    }
}
