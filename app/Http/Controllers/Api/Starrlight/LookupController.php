<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get list of languages
     */
    public function languages()
    {
        $languages = [
            'English',
            'French',
            'Spanish',
            'Mandarin',
            'Cantonese',
            'Punjabi',
            'Tagalog',
            'Arabic',
            'Portuguese',
            'Italian',
            'German',
            'Hindi',
            'Urdu',
            'Vietnamese',
            'Korean',
            'Japanese',
            'Russian',
            'Polish',
            'Dutch',
            'Greek',
        ];

        return $this->successResponse($languages, 'Languages retrieved successfully.');
    }

    /**
     * Get list of Canadian provinces
     */
    public function provinces()
    {
        $provinces = [
            'Alberta',
            'British Columbia',
            'Manitoba',
            'New Brunswick',
            'Newfoundland and Labrador',
            'Nova Scotia',
            'Ontario',
            'Prince Edward Island',
            'Quebec',
            'Saskatchewan',
        ];

        return $this->successResponse($provinces, 'Provinces retrieved successfully.');
    }

    /**
     * Get list of job types
     */
    public function jobTypes()
    {
        $jobTypes = [
            'Full-time',
            'Part-time',
            'Contract',
            'Casual',
            'Temporary',
            'Permanent',
        ];

        return $this->successResponse($jobTypes, 'Job types retrieved successfully.');
    }

    /**
     * Get list of shift patterns
     */
    public function shiftPatterns()
    {
        $shiftPatterns = [
            'Day Shift',
            'Night Shift',
            'Rotating Shift',
            '12-Hour Shift',
            '8-Hour Shift',
            'Split Shift',
            'On-Call',
        ];

        return $this->successResponse($shiftPatterns, 'Shift patterns retrieved successfully.');
    }
}
