<?php

namespace App\Models\Starrlight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaregiverEmploymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_profile_id',
        'employer',
        'start_date',
        'end_date',
        'is_current_employer',
        'can_be_contacted',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current_employer' => 'boolean',
        'can_be_contacted' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(CaregiverProfile::class, 'caregiver_profile_id');
    }
}
