<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\ContactMessage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    use ApiResponseTrait;

    /**
     * Submit contact message (Public - no auth required)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phoneNumber' => 'required|string|max:50',
                'email' => 'required|email',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $contact = ContactMessage::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'is_read' => false,
            ]);

            return $this->successResponse([
                'id' => $contact->id,
            ], 'Message sent successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }
}
