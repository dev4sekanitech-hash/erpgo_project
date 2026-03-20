<?php

namespace App\Models\Starrlight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $table = 'starrlight_job_applications';

    protected $fillable = [
        'job_id',
        'caregiver_profile_id',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function caregiverProfile(): BelongsTo
    {
        return $this->belongsTo(CaregiverProfile::class, 'caregiver_profile_id');
    }
}
