<?php

namespace App\Models\Starrlight;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaregiverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'city',
        'province',
        'short_bio',
        'caregiver_motivation',
        'profile_photo_url',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(CaregiverLanguage::class, 'caregiver_profile_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(CaregiverCertificate::class, 'caregiver_profile_id');
    }

    public function employmentRecords(): HasMany
    {
        return $this->hasMany(CaregiverEmploymentRecord::class, 'caregiver_profile_id');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'caregiver_profile_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($profile) {
            // Ensure user has caregiver type
            if ($profile->user && $profile->user->type !== 'caregiver') {
                $profile->user->type = 'caregiver';
                $profile->user->save();
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
