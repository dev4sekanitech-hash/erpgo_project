<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\CareerApplication;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index()
    {
        $applications = CareerApplication::orderBy('created_at', 'desc')->get();
        return view('starrlight.careers.index', compact('applications'));
    }

    public function show($id)
    {
        $application = CareerApplication::findOrFail($id);
        return view('starrlight.careers.show', compact('application'));
    }

    public function updateStatus(Request $request, $id)
    {
        $application = CareerApplication::findOrFail($id);
        $application->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
