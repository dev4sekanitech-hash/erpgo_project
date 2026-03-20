<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    use ApiResponseTrait;

    /**
     * Upload profile photo (JPG, PNG, max 2MB)
     */
    public function profilePhoto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB = 2048KB
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $file = $request->file('file');
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in public/uploads/profile-photos
            $path = $file->storeAs('uploads/profile-photos', $filename, 'public');

            $url = Storage::disk('public')->url($path);

            return $this->successResponse([
                'url' => $url,
                'path' => $path,
            ], 'Profile photo uploaded successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Upload certificate (PDF, JPG, PNG, max 2MB)
     */
    public function certificate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048', // 2MB
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $file = $request->file('file');
            $filename = 'cert_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in public/uploads/certificates
            $path = $file->storeAs('uploads/certificates', $filename, 'public');

            $url = Storage::disk('public')->url($path);

            return $this->successResponse([
                'url' => $url,
                'path' => $path,
            ], 'Certificate uploaded successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Upload resume (PDF, DOC, DOCX, max 5MB)
     */
    public function resume(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:pdf,doc,docx|max:5120', // 5MB = 5120KB
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $file = $request->file('file');
            $filename = 'resume_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store in public/uploads/resumes
            $path = $file->storeAs('uploads/resumes', $filename, 'public');

            $url = Storage::disk('public')->url($path);

            return $this->successResponse([
                'url' => $url,
                'path' => $path,
            ], 'Resume uploaded successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }
}
