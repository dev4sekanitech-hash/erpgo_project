<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StarrlightLookupController extends Controller
{
    /**
     * Get all languages
     * GET /api/lookup/languages
     */
    public function languages()
    {
        // Common languages used in Canada
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'zh', 'name' => 'Chinese'],
            ['code' => 'ar', 'name' => 'Arabic'],
            ['code' => 'pt', 'name' => 'Portuguese'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'de', 'name' => 'German'],
            ['code' => 'nl', 'name' => 'Dutch'],
            ['code' => 'ru', 'name' => 'Russian'],
            ['code' => 'ja', 'name' => 'Japanese'],
            ['code' => 'ko', 'name' => 'Korean'],
            ['code' => 'hi', 'name' => 'Hindi'],
            ['code' => 'pa', 'name' => 'Punjabi'],
            ['code' => 'vi', 'name' => 'Vietnamese'],
            ['code' => 'tl', 'name' => 'Tagalog'],
            ['code' => 'fa', 'name' => 'Persian'],
            ['code' => 'ur', 'name' => 'Urdu'],
            ['code' => 'bn', 'name' => 'Bengali'],
            ['code' => 'ta', 'name' => 'Tamil'],
        ];

        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    }

    /**
     * Get all provinces
     * GET /api/lookup/provinces
     */
    public function provinces()
    {
        // Canadian provinces and territories
        $provinces = [
            ['code' => 'AB', 'name' => 'Alberta'],
            ['code' => 'BC', 'name' => 'British Columbia'],
            ['code' => 'MB', 'name' => 'Manitoba'],
            ['code' => 'NB', 'name' => 'New Brunswick'],
            ['code' => 'NL', 'name' => 'Newfoundland and Labrador'],
            ['code' => 'NS', 'name' => 'Nova Scotia'],
            ['code' => 'NT', 'name' => 'Northwest Territories'],
            ['code' => 'NU', 'name' => 'Nunavut'],
            ['code' => 'ON', 'name' => 'Ontario'],
            ['code' => 'PE', 'name' => 'Prince Edward Island'],
            ['code' => 'QC', 'name' => 'Quebec'],
            ['code' => 'SK', 'name' => 'Saskatchewan'],
            ['code' => 'YT', 'name' => 'Yukon'],
        ];

        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }

    /**
     * Get all job types
     * GET /api/lookup/job-types
     */
    public function jobTypes()
    {
        $jobTypes = [
            ['id' => 'full-time', 'name' => 'Full Time'],
            ['id' => 'part-time', 'name' => 'Part Time'],
            ['id' => 'contract', 'name' => 'Contract'],
            ['id' => 'temporary', 'name' => 'Temporary'],
            ['id' => 'seasonal', 'name' => 'Seasonal'],
            ['id' => 'internship', 'name' => 'Internship'],
            ['id' => 'casual', 'name' => 'Casual'],
            ['id' => 'permanent', 'name' => 'Permanent'],
        ];

        return response()->json([
            'success' => true,
            'data' => $jobTypes
        ]);
    }

    /**
     * Get all shift patterns
     * GET /api/lookup/shift-patterns
     */
    public function shiftPatterns()
    {
        $shiftPatterns = [
            ['id' => 'day-shift', 'name' => 'Day Shift'],
            ['id' => 'night-shift', 'name' => 'Night Shift'],
            ['id' => 'rotating-shift', 'name' => 'Rotating Shift'],
            ['id' => 'split-shift', 'name' => 'Split Shift'],
            ['id' => 'on-call', 'name' => 'On-Call'],
            ['id' => 'flexible', 'name' => 'Flexible'],
            ['id' => 'weekend-only', 'name' => 'Weekend Only'],
            ['id' => '12-hour-shift', 'name' => '12-Hour Shift'],
            ['id' => '8-hour-shift', 'name' => '8-Hour Shift'],
        ];

        return response()->json([
            'success' => true,
            'data' => $shiftPatterns
        ]);
    }
}
