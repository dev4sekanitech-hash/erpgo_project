<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\StaffRequest;
use Illuminate\Http\Request;

class StarrlightStaffRequestController extends Controller
{
    public function index()
    {
        $requests = StaffRequest::orderBy('created_at', 'desc')->get();
        return view('starrlight.staff-requests.index', compact('requests'));
    }

    public function show($id)
    {
        $request = StaffRequest::findOrFail($id);
        return view('starrlight.staff-requests.show', compact('request'));
    }

    public function updateStatus(Request $request, $id)
    {
        $staffRequest = StaffRequest::findOrFail($id);
        $staffRequest->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
