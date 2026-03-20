<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\CaregiverProfile;
use Illuminate\Http\Request;

class CaregiverController extends Controller
{
    public function index()
    {
        $caregivers = CaregiverProfile::with('user')->orderBy('created_at', 'desc')->get();
        return view('starrlight.caregivers.index', compact('caregivers'));
    }

    public function show($id)
    {
        $profile = CaregiverProfile::with(['languages', 'certificates', 'employmentRecords', 'user'])->findOrFail($id);
        return view('starrlight.caregivers.show', compact('profile'));
    }

    public function updateStatus(Request $request, $id)
    {
        $profile = CaregiverProfile::findOrFail($id);
        $profile->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function destroy($id)
    {
        $profile = CaregiverProfile::findOrFail($id);
        $profile->delete();
        return redirect()->route('starrlight.caregivers.index')->with('success', 'Caregiver deleted successfully.');
    }
}
