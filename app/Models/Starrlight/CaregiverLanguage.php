<?php

namespace App\Models\Starrlight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaregiverLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_profile_id',
        'language',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(CaregiverProfile::class, 'caregiver_profile_id');
    }
}
