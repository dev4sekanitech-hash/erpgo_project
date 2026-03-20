<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\CaregiverProfile;
use App\Models\Starrlight\CaregiverLanguage;
use App\Models\Starrlight\CaregiverCertificate;
use App\Models\Starrlight\CaregiverEmploymentRecord;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CaregiverProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get current user's caregiver profile
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            $profile = CaregiverProfile::with([
                'languages',
                'certificates',
                'employmentRecords'
            ])->where('user_id', $user->id)->first();

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            return $this->successResponse($this->formatProfile($profile), 'Profile retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Update contact info (Step 1)
     */
    public function updateContact(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phoneNumber' => 'nullable|string|max:50',
                'email' => 'required|email',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'shortBio' => 'nullable|string',
                'caregiverMotivation' => 'nullable|string',
                'profilePhotoUrl' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = $request->user();
            $profile = CaregiverProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            $profile->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'city' => $request->city,
                'province' => $request->province,
                'short_bio' => $request->shortBio,
                'caregiver_motivation' => $request->caregiverMotivation,
                'profile_photo_url' => $request->profilePhotoUrl,
            ]);

            // Update user name as well
            $user->name = $request->firstName . ' ' . $request->lastName;
            $user->save();

            return $this->successResponse($this->formatProfile($profile), 'Contact info updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Update languages (Step 2)
     */
    public function updateLanguages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'languages' => 'required|array',
                'languages.*' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = $request->user();
            $profile = CaregiverProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            // Delete existing languages and add new ones
            $profile->languages()->delete();
            foreach ($request->languages as $language) {
                CaregiverLanguage::create([
                    'caregiver_profile_id' => $profile->id,
                    'language' => $language,
                ]);
            }

            $profile->load('languages');

            return $this->successResponse($this->formatProfile($profile), 'Languages updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Update certificates (Step 3)
     */
    public function updateCertificates(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'certificates' => 'required|array',
                'certificates.*.type' => 'required|string|max:255',
                'certificates.*.issuingOrganization' => 'required|string|max:255',
                'certificates.*.city' => 'nullable|string|max:100',
                'certificates.*.dateObtained' => 'nullable|date',
                'certificates.*.certificateFileUrl' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = $request->user();
            $profile = CaregiverProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            // Delete existing certificates and add new ones
            $profile->certificates()->delete();
            foreach ($request->certificates as $cert) {
                CaregiverCertificate::create([
                    'caregiver_profile_id' => $profile->id,
                    'type' => $cert['type'],
                    'issuing_organization' => $cert['issuingOrganization'],
                    'city' => $cert['city'] ?? null,
                    'date_obtained' => $cert['dateObtained'] ?? null,
                    'certificate_file_url' => $cert['certificateFileUrl'] ?? null,
                ]);
            }

            $profile->load('certificates');

            return $this->successResponse($this->formatProfile($profile), 'Certificates updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Update work history (Step 4)
     */
    public function updateWorkHistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employmentRecords' => 'required|array',
                'employmentRecords.*.employer' => 'required|string|max:255',
                'employmentRecords.*.startDate' => 'required|date',
                'employmentRecords.*.endDate' => 'nullable|date',
                'employmentRecords.*.isCurrentEmployer' => 'boolean',
                'employmentRecords.*.canBeContacted' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = $request->user();
            $profile = CaregiverProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            // Delete existing employment records and add new ones
            $profile->employmentRecords()->delete();
            foreach ($request->employmentRecords as $record) {
                CaregiverEmploymentRecord::create([
                    'caregiver_profile_id' => $profile->id,
                    'employer' => $record['employer'],
                    'start_date' => $record['startDate'],
                    'end_date' => $record['endDate'] ?? null,
                    'is_current_employer' => $record['isCurrentEmployer'] ?? false,
                    'can_be_contacted' => $record['canBeContacted'] ?? false,
                ]);
            }

            $profile->load('employmentRecords');

            return $this->successResponse($this->formatProfile($profile), 'Work history updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Submit profile for review
     */
    public function submit(Request $request)
    {
        try {
            $user = $request->user();
            $profile = CaregiverProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return $this->errorResponse('Profile not found', 404);
            }

            // Validate that profile has minimum required data
            if (empty($profile->first_name) || empty($profile->last_name) || empty($profile->email)) {
                return $this->errorResponse('Please complete your contact information before submitting.');
            }

            $profile->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            return $this->successResponse($this->formatProfile($profile), 'Profile submitted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Format profile for API response
     */
    private function formatProfile($profile)
    {
        return [
            'id' => $profile->id,
            'userId' => $profile->user_id,
            'firstName' => $profile->first_name,
            'lastName' => $profile->last_name,
            'phoneNumber' => $profile->phone_number,
            'email' => $profile->email,
            'city' => $profile->city,
            'province' => $profile->province,
            'shortBio' => $profile->short_bio,
            'caregiverMotivation' => $profile->caregiver_motivation,
            'profilePhotoUrl' => $profile->profile_photo_url,
            'status' => $profile->status,
            'submittedAt' => $profile->submitted_at,
            'languages' => $profile->languages->map(function ($lang) {
                return ['language' => $lang->language];
            })->toArray(),
            'certificates' => $profile->certificates->map(function ($cert) {
                return [
                    'type' => $cert->type,
                    'issuingOrganization' => $cert->issuing_organization,
                    'city' => $cert->city,
                    'dateObtained' => $cert->date_obtained,
                    'certificateFileUrl' => $cert->certificate_file_url,
                ];
            })->toArray(),
            'employmentRecords' => $profile->employmentRecords->map(function ($record) {
                return [
                    'employer' => $record->employer,
                    'startDate' => $record->start_date,
                    'endDate' => $record->end_date,
                    'isCurrentEmployer' => $record->is_current_employer,
                    'canBeContacted' => $record->can_be_contacted,
                ];
            })->toArray(),
        ];
    }
}
