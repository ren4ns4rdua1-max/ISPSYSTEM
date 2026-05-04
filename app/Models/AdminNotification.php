<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    /**
     * Notification types
     */
    const TYPE_JOB_COMPLETED = 'job_completed';
    const TYPE_CLIENT_APPROVED = 'client_approved';
    const TYPE_CLIENT_REJECTED = 'client_rejected';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_JOB_ASSIGNED = 'job_assigned';
    const TYPE_PAYMENT_DUE_SOON = 'payment_due_soon';
    const TYPE_PAYMENT_OVERDUE = 'payment_overdue';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user (admin) that owns this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get recent notifications.
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get unread count for a user.
     */
    public static function unreadCount(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

/**
     * Create a notification for all admin users.
     * Falls back to all users if no admin role exists.
     */
    public static function notifyAdmins(string $type, string $title, string $message, array $data = []): void
    {
        // Get all admin users
        $admins = User::where('role', 'admin')->get();
        
        // Fallback: if no admins found, get all users (for development)
        if ($admins->isEmpty()) {
            $admins = User::all();
        }

        foreach ($admins as $admin) {
            static::create([
                'user_id' => $admin->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }

/**
     * Create job completed notification.
     */
    public static function notifyJobCompleted(InstallationJob $job): void
    {
        $technicianName = $job->technician?->name ?? 'Unknown Technician';
        $clientName = $job->client?->name ?? 'Unknown Client';

        $data = [
            'job_id' => $job->id,
            'client_id' => $job->client_id,
            'technician_id' => $job->technician_id,
            'job_type' => $job->job_type,
        ];

        // Include photo if uploaded
        if ($job->photo) {
            $data['photo'] = $job->photo;
        }

        // Include completion notes if available
        if ($job->completion_notes) {
            $data['completion_notes'] = $job->completion_notes;
        }

        static::notifyAdmins(
            self::TYPE_JOB_COMPLETED,
            'Job Completed',
            "{$technicianName} has completed the {$job->job_type_label} for {$clientName}.",
            $data
        );
    }
}
