<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\CareerApplication;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerApplicationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Submit career application (Public - no auth required)
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
                'resumeUrl' => 'required|string|max:500',
                'additionalInformation' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $application = CareerApplication::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'city' => $request->city,
                'province' => $request->province,
                'resume_url' => $request->resumeUrl,
                'additional_information' => $request->additionalInformation,
                'status' => 'pending',
            ]);

            return $this->successResponse([
                'id' => $application->id,
                'status' => 'pending',
            ], 'Career application submitted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }
}
