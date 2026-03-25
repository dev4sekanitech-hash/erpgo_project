<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StarrlightContactController extends Controller
{
    /**
     * Starrlight company email
     */
    private const STARRLIGHT_EMAIL = 'starrlight@sekanitech.ca';
    private const TABLE_NAME = 'starrlight_contact_submissions';

    /**
     * Get Starrlight company ID
     */
    private function getStarrlightCompanyId()
    {
        $user = User::where('email', self::STARRLIGHT_EMAIL)->first();
        return $user ? $user->created_by : null;
    }

    /**
     * Contact Form
     * POST /api/contact
     * Public - no auth required
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
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

        $contactId = DB::table(self::TABLE_NAME)->insertGetId([
            'company_id' => $companyId,
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'phone_number' => $request->phoneNumber,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'unread',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'contactId' => (string) $contactId,
                'status' => 'submitted'
            ]
        ], 201);
    }
}
