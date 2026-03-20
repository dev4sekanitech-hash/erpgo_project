<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::orderBy('created_at', 'desc')->get();
        return view('starrlight.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('starrlight.jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type' => 'required|string|max:100',
            'shift_pattern' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
        ]);

        $validated['created_by'] = auth()->id();
        Job::create($validated);

        return redirect()->route('starrlight.jobs.index')->with('success', 'Job created successfully.');
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        return view('starrlight.jobs.show', compact('job'));
    }

    public function edit($id)
    {
        $job = Job::findOrFail($id);
        return view('starrlight.jobs.edit', compact('job'));
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type' => 'required|string|max:100',
            'shift_pattern' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $job->update($validated);
        return redirect()->route('starrlight.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();
        return redirect()->route('starrlight.jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);
        $job->update($validated);
        return redirect()->route('starrlight.jobs.index')->with('success', 'Job status updated successfully.');
    }
}
