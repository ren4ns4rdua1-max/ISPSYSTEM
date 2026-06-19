<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'specialization',
        'area_coverage',
        'status',
        'notes',
        'user_id',
        'photo',
        'email_verified_at',
        'email_verification_token',
    ];

    protected $casts = [
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Get installation jobs for this technician.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(InstallationJob::class)->latest();
    }

    /**
     * Get support tickets assigned to this technician.
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class)->latest();
    }

    /**
     * Get pending jobs count.
     */
    public function pendingJobsCount(): int
    {
        return $this->jobs()->whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
    }

    /**
     * Get completed jobs count.
     */
    public function completedJobsCount(): int
    {
        return $this->jobs()->where('status', 'completed')->count();
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'available' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'busy' => 'bg-amber-100 text-amber-700 border-amber-200',
            'offduty' => 'bg-gray-100 text-gray-700 border-gray-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Get the user account for this technician.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if technician is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
