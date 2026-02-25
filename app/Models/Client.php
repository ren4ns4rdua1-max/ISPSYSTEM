<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'pppoe_name',
        'barangay',
        'nap_box',
        'start_date',
        'plan_description',
        'due_date_time',
        'subscription_rate_id',
        'user_id',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date_time' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the subscription rate associated with the client.
     */
    public function subscriptionRate(): BelongsTo
    {
        return $this->belongsTo(SubscriptionRate::class);
    }

    /**
     * Get the user who created this client.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the billings for this client.
     */
    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class)->latest();
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'inactive' => 'bg-gray-100 text-gray-700 border-gray-200',
            'suspended' => 'bg-amber-100 text-amber-700 border-amber-200',
            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Check if the client is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the client has overdue payment.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->due_date_time->isPast();
    }
}
