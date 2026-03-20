<?php

namespace App\Models\Starrlight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaregiverCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_profile_id',
        'type',
        'issuing_organization',
        'city',
        'date_obtained',
        'certificate_file_url',
    ];

    protected $casts = [
        'date_obtained' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(CaregiverProfile::class, 'caregiver_profile_id');
    }
}
