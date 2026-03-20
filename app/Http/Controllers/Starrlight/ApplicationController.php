<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\JobApplication;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = JobApplication::with(['job', 'caregiverProfile'])->orderBy('applied_at', 'desc')->get();
        return view('starrlight.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = JobApplication::with(['job', 'caregiverProfile', 'caregiverProfile.languages', 'caregiverProfile.certificates', 'caregiverProfile.employmentRecords'])->findOrFail($id);
        return view('starrlight.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);
        $application->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
