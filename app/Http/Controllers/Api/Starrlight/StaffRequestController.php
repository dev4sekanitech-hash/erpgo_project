<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\StaffRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffRequestController extends Controller
{
    use ApiResponseTrait;

    /**
     * Create a new staff request (Public - no auth required)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phoneNumber' => 'required|string|max:50',
                'email' => 'required|email',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'additionalInformation' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $staffRequest = StaffRequest::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'city' => $request->city,
                'province' => $request->province,
                'additional_information' => $request->additionalInformation,
                'status' => 'pending',
            ]);

            return $this->successResponse([
                'id' => $staffRequest->id,
                'status' => 'pending',
            ], 'Staff request submitted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }
}
