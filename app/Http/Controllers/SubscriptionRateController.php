<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionRate;
use Illuminate\Http\Request;

class SubscriptionRateController extends Controller
{
    public function index()
    {
        $subscriptionRates = SubscriptionRate::orderBy('created_at', 'desc')->paginate(10);
        return view('subscription-rates.index', compact('subscriptionRates'));
    }

    public function create()
    {
        $planTypes = SubscriptionRate::planTypes();
        $billingCycles = SubscriptionRate::billingCycles();
        $dataLimits = SubscriptionRate::dataLimits();
        return view('subscription-rates.create', compact('planTypes', 'billingCycles', 'dataLimits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_type' => 'required|string|max:255',
            'speed' => 'required|string|max:255',
            'monthly_fee' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'activation_fee' => 'nullable|numeric|min:0',
            'router_fee' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|string|max:255',
            'lock_in_period' => 'nullable|integer|min:0',
            'late_penalty' => 'nullable|numeric|min:0',
            'reconnection_fee' => 'nullable|numeric|min:0',
            'data_limit' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        SubscriptionRate::create($validated);

        return redirect()->route('subscription-rates.index')
            ->with('success', 'Subscription rate created successfully.');
    }

    public function show(SubscriptionRate $subscriptionRate)
    {
        return view('subscription-rates.show', compact('subscriptionRate'));
    }

    public function edit(SubscriptionRate $subscriptionRate)
    {
        $planTypes = SubscriptionRate::planTypes();
        $billingCycles = SubscriptionRate::billingCycles();
        $dataLimits = SubscriptionRate::dataLimits();
        return view('subscription-rates.edit', compact('subscriptionRate', 'planTypes', 'billingCycles', 'dataLimits'));
    }

    public function update(Request $request, SubscriptionRate $subscriptionRate)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_type' => 'required|string|max:255',
            'speed' => 'required|string|max:255',
            'monthly_fee' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'activation_fee' => 'nullable|numeric|min:0',
            'router_fee' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|string|max:255',
            'lock_in_period' => 'nullable|integer|min:0',
            'late_penalty' => 'nullable|numeric|min:0',
            'reconnection_fee' => 'nullable|numeric|min:0',
            'data_limit' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subscriptionRate->update($validated);

        return redirect()->route('subscription-rates.index')
            ->with('success', 'Subscription rate updated successfully.');
    }

    public function destroy(SubscriptionRate $subscriptionRate)
    {
        $subscriptionRate->delete();

        return redirect()->route('subscription-rates.index')
            ->with('success', 'Subscription rate deleted successfully.');
    }
}
