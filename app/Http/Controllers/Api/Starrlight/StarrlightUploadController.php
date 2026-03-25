<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StarrlightUploadController extends Controller
{
    /**
     * Profile Photo Upload
     * POST /api/uploads/profile-photo
     */
    public function profilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png|max:2048', // 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $filename = 'profile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store in public disk under starrlight folder
        $path = $file->storeAs('starrlight/profiles', $filename, 'public');

        return response()->json([
            'success' => true,
            'message' => 'Profile photo uploaded successfully',
            'data' => [
                'url' => Storage::disk('public')->url($path)
            ]
        ]);
    }

    /**
     * Certificate Upload
     * POST /api/uploads/certificate
     */
    public function certificate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,jpeg,png|max:2048', // 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $filename = 'cert_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('starrlight/certificates', $filename, 'public');

        return response()->json([
            'success' => true,
            'message' => 'Certificate uploaded successfully',
            'data' => [
                'url' => Storage::disk('public')->url($path)
            ]
        ]);
    }

    /**
     * Resume Upload
     * POST /api/uploads/resume
     */
    public function resume(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,doc,docx|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $filename = 'resume_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('starrlight/resumes', $filename, 'public');

        return response()->json([
            'success' => true,
            'message' => 'Resume uploaded successfully',
            'data' => [
                'url' => Storage::disk('public')->url($path)
            ]
        ]);
    }
}
