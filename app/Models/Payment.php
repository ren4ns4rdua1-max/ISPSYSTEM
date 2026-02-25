<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'billing_id',
        'user_id',
        'receipt_number',
        'amount',
        'change_amount',
        'total_paid',
        'payment_method',
        'payment_reference',
        'payment_date',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the client that owns the payment.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the billing associated with the payment.
     */
    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }

    /**
     * Get the user who recorded the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP-';
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ? (int)substr($lastPayment->receipt_number, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya',
            'cheque' => 'Cheque',
            'other' => 'Other',
            default => 'Unknown',
        };
    }

    /**
     * Calculate total revenue from all payments.
     */
    public static function getTotalRevenue(): float
    {
        return self::sum('amount');
    }

    /**
     * Calculate total revenue for the current month.
     */
    public static function getMonthlyRevenue(): float
    {
        return self::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
    }

    /**
     * Calculate total revenue for the current year.
     */
    public static function getYearlyRevenue(): float
    {
        return self::whereYear('payment_date', now()->year)
            ->sum('amount');
    }
}
