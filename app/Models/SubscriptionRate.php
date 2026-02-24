<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'plan_type',
        'speed',
        'monthly_fee',
        'installation_fee',
        'activation_fee',
        'router_fee',
        'billing_cycle',
        'lock_in_period',
        'late_penalty',
        'reconnection_fee',
        'data_limit',
        'is_active',
    ];

    protected $casts = [
        'monthly_fee' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'activation_fee' => 'decimal:2',
        'router_fee' => 'decimal:2',
        'late_penalty' => 'decimal:2',
        'reconnection_fee' => 'decimal:2',
        'lock_in_period' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function planTypes()
    {
        return ['Residential', 'Business', 'Prepaid', 'Postpaid'];
    }

    public static function billingCycles()
    {
        return ['Monthly', 'Quarterly', 'Yearly'];
    }

    public static function dataLimits()
    {
        return ['Unlimited', '10GB', '50GB', '100GB', '200GB', '500GB'];
    }
}
