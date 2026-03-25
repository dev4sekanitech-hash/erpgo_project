<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StarrlightCaregiverController extends Controller
{
    /**
     * Starrlight company email
     */
    private const STARRLIGHT_EMAIL = 'starrlight@sekanitech.ca';

    /**
     * Table name for caregiver profiles
     */
    private const TABLE_PROFILE = 'starrlight_caregiver_profiles';
    private const TABLE_LANGUAGES = 'starrlight_caregiver_languages';
    private const TABLE_CERTIFICATES = 'starrlight_caregiver_certificates';
    private const TABLE_WORK_HISTORY = 'starrlight_caregiver_work_history';

    /**
     * Get Starrlight company ID
     */
    private function getStarrlightCompanyId()
    {
        $user = User::where('email', self::STARRLIGHT_EMAIL)->first();
        return $user ? $user->created_by : null;
    }

    /**
     * Step 1 - Contact Info
     * POST /api/caregiver/profile/contact
     */
    public function storeContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'required|email',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:2',
            'shortBio' => 'nullable|string|max:500',
            'caregiverMotivation' => 'nullable|string|max:1000',
            'profilePhotoUrl' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $companyId = $this->getStarrlightCompanyId();

        // Check if profile exists, create or update
        $profile = DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->first();

        if ($profile) {
            DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'city' => $request->city,
                'province' => $request->province,
                'short_bio' => $request->shortBio,
                'caregiver_motivation' => $request->caregiverMotivation,
                'profile_photo_url' => $request->profilePhotoUrl,
                'step_completed' => max($profile->step_completed ?? 0, 1),
                'updated_at' => now(),
            ]);
        } else {
            DB::table(self::TABLE_PROFILE)->insert([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'city' => $request->city,
                'province' => $request->province,
                'short_bio' => $request->shortBio,
                'caregiver_motivation' => $request->caregiverMotivation,
                'profile_photo_url' => $request->profilePhotoUrl,
                'step_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact information saved successfully',
            'data' => [
                'step' => 1,
                'completed' => true
            ]
        ]);
    }

    /**
     * Step 2 - Languages
     * POST /api/caregiver/profile/languages
     */
    public function storeLanguages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'languages' => 'required|array|min:1',
            'languages.*' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check profile exists
        $profile = DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete step 1 (Contact Info) first'
            ], 400);
        }

        // Delete existing languages and insert new ones
        DB::table(self::TABLE_LANGUAGES)->where('profile_id', $profile->id)->delete();

        foreach ($request->languages as $language) {
            DB::table(self::TABLE_LANGUAGES)->insert([
                'profile_id' => $profile->id,
                'language' => $language,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update step completed
        DB::table(self::TABLE_PROFILE)->where('id', $profile->id)->update([
            'step_completed' => max($profile->step_completed, 2),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Languages saved successfully',
            'data' => [
                'step' => 2,
                'completed' => true
            ]
        ]);
    }

    /**
     * Step 3 - Certificates
     * POST /api/caregiver/profile/certificates
     */
    public function storeCertificates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'certificates' => 'required|array',
            'certificates.*.type' => 'required|string|max:255',
            'certificates.*.issuingOrganization' => 'required|string|max:255',
            'certificates.*.city' => 'required|string|max:255',
            'certificates.*.dateObtained' => 'required|date',
            'certificates.*.certificateFileUrl' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $profile = DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete step 1 (Contact Info) first'
            ], 400);
        }

        // Delete existing certificates and insert new ones
        DB::table(self::TABLE_CERTIFICATES)->where('profile_id', $profile->id)->delete();

        foreach ($request->certificates as $cert) {
            DB::table(self::TABLE_CERTIFICATES)->insert([
                'profile_id' => $profile->id,
                'type' => $cert['type'],
                'issuing_organization' => $cert['issuingOrganization'],
                'city' => $cert['city'],
                'date_obtained' => $cert['dateObtained'],
                'certificate_file_url' => $cert['certificateFileUrl'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table(self::TABLE_PROFILE)->where('id', $profile->id)->update([
            'step_completed' => max($profile->step_completed, 3),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificates saved successfully',
            'data' => [
                'step' => 3,
                'completed' => true
            ]
        ]);
    }

    /**
     * Step 4 - Work History
     * POST /api/caregiver/profile/work-history
     */
    public function storeWorkHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employmentRecords' => 'required|array',
            'employmentRecords.*.employer' => 'required|string|max:255',
            'employmentRecords.*.startDate' => 'required|date',
            'employmentRecords.*.endDate' => 'nullable|date|after:employmentRecords.*.startDate',
            'employmentRecords.*.isCurrentEmployer' => 'boolean',
            'employmentRecords.*.canBeContacted' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $profile = DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete step 1 (Contact Info) first'
            ], 400);
        }

        // Delete existing work history and insert new ones
        DB::table(self::TABLE_WORK_HISTORY)->where('profile_id', $profile->id)->delete();

        foreach ($request->employmentRecords as $record) {
            DB::table(self::TABLE_WORK_HISTORY)->insert([
                'profile_id' => $profile->id,
                'employer' => $record['employer'],
                'start_date' => $record['startDate'],
                'end_date' => $record['endDate'],
                'is_current_employer' => $record['isCurrentEmployer'] ?? false,
                'can_be_contacted' => $record['canBeContacted'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table(self::TABLE_PROFILE)->where('id', $profile->id)->update([
            'step_completed' => max($profile->step_completed, 4),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Work history saved successfully',
            'data' => [
                'step' => 4,
                'completed' => true
            ]
        ]);
    }

    /**
     * Get Full Profile
     * GET /api/caregiver/profile
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $profile = DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);
        }

        $languages = DB::table(self::TABLE_LANGUAGES)->where('profile_id', $profile->id)->get();
        $certificates = DB::table(self::TABLE_CERTIFICATES)->where('profile_id', $profile->id)->get();
        $workHistory = DB::table(self::TABLE_WORK_HISTORY)->where('profile_id', $profile->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $profile->id,
                'stepCompleted' => $profile->step_completed,
                'contact' => [
                    'firstName' => $profile->first_name,
                    'lastName' => $profile->last_name,
                    'phoneNumber' => $profile->phone_number,
                    'email' => $profile->email,
                    'city' => $profile->city,
                    'province' => $profile->province,
                    'shortBio' => $profile->short_bio,
                    'caregiverMotivation' => $profile->caregiver_motivation,
                    'profilePhotoUrl' => $profile->profile_photo_url,
                ],
                'languages' => $languages->map(fn($l) => $l->language)->toArray(),
                'certificates' => $certificates->map(fn($c) => [
                    'type' => $c->type,
                    'issuingOrganization' => $c->issuing_organization,
                    'city' => $c->city,
                    'dateObtained' => $c->date_obtained,
                    'certificateFileUrl' => $c->certificate_file_url,
                ])->toArray(),
                'workHistory' => $workHistory->map(fn($w) => [
                    'employer' => $w->employer,
                    'startDate' => $w->start_date,
                    'endDate' => $w->end_date,
                    'isCurrentEmployer' => $w->is_current_employer,
                    'canBeContacted' => $w->can_be_contacted,
                ])->toArray(),
            ]
        ]);
    }

    /**
     * Final Submit
     * POST /api/caregiver/profile/submit
     */
    public function submit(Request $request)
    {
        $user = $request->user();

        $profile = DB::table(self::TABLE_PROFILE)->where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);
        }

        if ($profile->step_completed < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete all steps before submitting'
            ], 400);
        }

        // Mark as submitted
        DB::table(self::TABLE_PROFILE)->where('id', $profile->id)->update([
            'is_submitted' => true,
            'submitted_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile submitted successfully',
            'data' => [
                'profileId' => $profile->id,
                'status' => 'submitted'
            ]
        ]);
    }
}
