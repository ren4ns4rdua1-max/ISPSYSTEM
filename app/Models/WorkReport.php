<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkReport extends Model
{
    protected $fillable = [
        'installation_job_id',
        'technician_id',
        'work_performed',
        'materials_used',
        'issues_encountered',
        'proof_image',
        'completion_time',
    ];

    protected $casts = [
        'completion_time' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(InstallationJob::class, 'installation_job_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }
}
