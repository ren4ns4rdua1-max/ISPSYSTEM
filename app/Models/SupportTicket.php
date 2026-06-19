<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'client_id',
        'technician_id',
        'subject',
        'message',
        'status',
        'priority',
        'admin_reply',
        'replied_at',
        'assigned_at',
        'troubleshooting_notes',
        'solution',
        'resolved_at',
        'client_confirmed_at',
        'closed_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'client_confirmed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'        => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
            'resolved'    => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'closed'      => 'bg-gray-100 text-gray-600 border-gray-200',
            default       => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }
}
