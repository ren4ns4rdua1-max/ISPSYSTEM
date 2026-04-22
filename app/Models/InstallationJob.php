<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallationJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'technician_id',
        'assigned_by',
        'completed_by',
        'job_type',
        'status',
        'scheduled_date',
        'started_at',
        'completed_at',
        'notes',
        'completion_notes',
        'ip_address',
        'mac_address',
        'router_ssid',
        'router_password',
        'speed_test_result',
        'materials_used',
        'proof_image',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'speed_test_result' => 'array',
    ];

    /**
     * Get the client for this job.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the technician for this job.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Get the user who assigned this job.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who completed this job.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'assigned' => 'bg-blue-100 text-blue-700 border-blue-200',
            'in_progress' => 'bg-amber-100 text-amber-700 border-amber-200',
            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-gray-100 text-gray-700 border-gray-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Get job type label.
     */
    public function getJobTypeLabelAttribute(): string
    {
        return match($this->job_type) {
            'new_installation' => 'New Installation',
            'repair' => 'Repair',
            'reconnection' => 'Reconnection',
            'upgrade' => 'Upgrade',
            'transfer' => 'Transfer',
            default => 'Unknown',
        };
    }

    /**
     * Check if job is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark job as completed.
     */
    public function markAsCompleted(int $userId, string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_by' => $userId,
            'completed_at' => now(),
            'completion_notes' => $notes,
        ]);

        // Update client installation status
        $this->client->update([
            'installation_status' => 'completed',
            'installation_date' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Start the job.
     */
    public function startJob(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }
}
