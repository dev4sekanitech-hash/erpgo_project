<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StarrlightBookingController extends Controller
{
    /**
     * Starrlight company email
     */
    private const STARRLIGHT_EMAIL = 'starrlight@sekanitech.ca';
    private const TABLE_NAME = 'starrlight_staff_requests';

    /**
     * Get Starrlight company ID
     */
    private function getStarrlightCompanyId()
    {
        $user = User::where('email', self::STARRLIGHT_EMAIL)->first();
        return $user ? $user->created_by : null;
    }

    /**
     * Book Healthcare Staff
     * POST /api/bookings/staff-request
     * Public - no auth required
     */
    public function staffRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'required|email',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:2',
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

        $staffRequest = DB::table(self::TABLE_NAME)->insert([
            'company_id' => $companyId,
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'phone_number' => $request->phoneNumber,
            'email' => $request->email,
            'city' => $request->city,
            'province' => $request->province,
            'additional_information' => $request->additionalInformation,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff request submitted successfully',
            'data' => [
                'requestId' => DB::getSequence()->lastId() ?? null,
                'status' => 'pending'
            ]
        ], 201);
    }
}
