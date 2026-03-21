<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\JobApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = JobApplication::with(['job', 'caregiverProfile'])
            ->orderBy('applied_at', 'desc')
            ->paginate(10);

        return Inertia::render('starrlight/applications/index', [
            'applications' => $applications,
        ]);
    }

    public function show($id)
    {
        $application = JobApplication::with([
            'job',
            'caregiverProfile',
            'caregiverProfile.languages',
            'caregiverProfile.certificates',
            'caregiverProfile.employmentRecords'
        ])->findOrFail($id);

        return Inertia::render('starrlight/applications/show', [
            'application' => $application,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);
        $application->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
