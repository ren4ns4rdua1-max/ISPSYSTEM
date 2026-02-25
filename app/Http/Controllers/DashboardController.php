<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Billing;
use App\Models\SubscriptionRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real data.
     */
    public function index()
    {
        // Total Clients Count
        $totalClients = Client::count();

        // Active Clients Count (clients with status = 'active')
        $activeClients = Client::where('status', 'active')->count();

        // Total Revenue (sum of all paid billings)
        $totalRevenue = Billing::where('status', 'paid')
            ->sum('total_amount');

        // This Month's Revenue
        $thisMonthRevenue = Billing::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('total_amount');

        // Last Month Revenue (for comparison)
        $lastMonthRevenue = Billing::where('status', 'paid')
            ->whereMonth('paid_date', now()->subMonth()->month)
            ->whereYear('paid_date', now()->subMonth()->year)
            ->sum('total_amount');

        // Calculate revenue growth percentage
        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        // Get monthly revenue for the last 6 months
        $sixMonthRevenue = $this->getMonthlyRevenue(6);
        $twelveMonthRevenue = $this->getMonthlyRevenue(12);

        // Get plan distribution (clients grouped by subscription rate)
        $planDistribution = $this->getPlanDistribution();

        // Get billing status distribution
        $billingStatus = $this->getBillingStatusDistribution();

        // Recent clients
        $recentClients = Client::latest()->take(5)->get();

        // Recent billings
        $recentBillings = Billing::with('client')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalClients',
            'activeClients',
            'totalRevenue',
            'thisMonthRevenue',
            'revenueGrowth',
            'sixMonthRevenue',
            'twelveMonthRevenue',
            'planDistribution',
            'billingStatus',
            'recentClients',
            'recentBillings'
        ));
    }

    /**
     * Get monthly revenue for the last N months.
     */
    private function getMonthlyRevenue(int $months): array
    {
        $revenueData = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Billing::where('status', 'paid')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('total_amount');
            
            $revenueData[] = [
                'month' => $date->format('M'),
                'revenue' => $revenue,
            ];
        }

        return $revenueData;
    }

    /**
     * Get plan distribution (clients grouped by subscription rate).
     */
    private function getPlanDistribution(): array
    {
        $distribution = SubscriptionRate::withCount('clients')
            ->where('is_active', true)
            ->get()
            ->map(function ($plan) {
                return [
                    'name' => $plan->plan_name,
                    'count' => $plan->clients_count,
                    'color' => $this->getPlanColor($plan->plan_type),
                ];
            });

        // Calculate total for percentages
        $total = $distribution->sum('count');
        
        return $distribution->map(function ($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100) : 0;
            return $item;
        })->toArray();
    }

    /**
     * Get color for plan type.
     */
    private function getPlanColor(string $planType): string
    {
        return match($planType) {
            'Basic' => '#dc2626',
            'Standard' => '#f87171',
            'Premium' => '#8b5cf6',
            'Enterprise' => '#f59e0b',
            default => '#6b7280',
        };
    }

    /**
     * Get billing status distribution.
     */
    private function getBillingStatusDistribution(): array
    {
        $statuses = ['paid', 'pending', 'overdue', 'partial', 'cancelled'];
        $distribution = [];

        foreach ($statuses as $status) {
            $count = Billing::where('status', $status)->count();
            $distribution[$status] = $count;
        }

        return $distribution;
    }
}
