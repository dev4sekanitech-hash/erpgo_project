<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StarrlightCareerController extends Controller
{
    /**
     * Starrlight company email
     */
    private const STARRLIGHT_EMAIL = 'starrlight@sekanitech.ca';
    private const TABLE_NAME = 'starrlight_career_applications';

    /**
     * Get Starrlight company ID
     */
    private function getStarrlightCompanyId()
    {
        $user = User::where('email', self::STARRLIGHT_EMAIL)->first();
        return $user ? $user->created_by : null;
    }

    /**
     * Careers Application
     * POST /api/careers/apply
     * Public - no auth required
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'required|email',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:2',
            'resumeUrl' => 'required|url',
            'additionalInformation' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $this->getStarrlightCompanyId();

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        $applicationId = DB::table(self::TABLE_NAME)->insertGetId([
            'company_id' => $companyId,
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'phone_number' => $request->phoneNumber,
            'email' => $request->email,
            'city' => $request->city,
            'province' => $request->province,
            'resume_url' => $request->resumeUrl,
            'additional_information' => $request->additionalInformation,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => [
                'applicationId' => (string) $applicationId,
                'status' => 'submitted'
            ]
        ], 201);
    }
}
