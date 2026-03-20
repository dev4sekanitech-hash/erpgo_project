<?php

namespace App\Models\Starrlight;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'city',
        'province',
        'resume_url',
        'additional_information',
        'status',
    ];
}
