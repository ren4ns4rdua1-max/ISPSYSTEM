<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'subscription_rate_id',
        'invoice_number',
        'amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'billing_type',
        'status',
        'billing_date',
        'due_date',
        'paid_date',
        'payment_method',
        'payment_reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the client that owns the billing.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the subscription rate associated with the billing.
     */
    public function subscriptionRate(): BelongsTo
    {
        return $this->belongsTo(SubscriptionRate::class, 'subscription_rate_id');
    }

    /**
     * Get the user who created the billing.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the payments for this billing.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $year = date('Y');
        $month = date('m');
        
        $lastBilling = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastBilling ? (int)substr($lastBilling->invoice_number, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): array
    {
        return match($this->status) {
            'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Paid'],
            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Pending'],
            'overdue' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Overdue'],
            'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Cancelled'],
            'partial' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Partial'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Unknown'],
        };
    }

    /**
     * Check if the billing is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    /**
     * Calculate the total amount.
     */
    public function calculateTotal(): float
    {
        return ($this->amount + $this->tax_amount) - $this->discount_amount;
    }
}
