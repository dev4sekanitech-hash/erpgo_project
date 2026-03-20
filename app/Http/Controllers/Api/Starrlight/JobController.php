<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\Job;
use App\Models\Starrlight\JobApplication;
use App\Models\Starrlight\CaregiverProfile;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all jobs with filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Job::active()->with('creator');

            // Filter by must_include
            if ($request->has('mustInclude') && !empty($request->mustInclude)) {
                $query->where('must_include', 'like', '%' . $request->mustInclude . '%');
            }

            // Filter by job_type
            if ($request->has('jobType') && !empty($request->jobType)) {
                $query->where('job_type', $request->jobType);
            }

            // Filter by shift_pattern
            if ($request->has('shiftPattern') && !empty($request->shiftPattern)) {
                $query->where('shift_pattern', $request->shiftPattern);
            }

            // Pagination
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 9);
            $offset = ($page - 1) * $limit;

            $total = $query->count();
            $jobs = $query->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            return $this->successResponse([
                'jobs' => $jobs->map(function ($job) {
                    return $this->formatJob($job);
                })->toArray(),
                'totalPages' => ceil($total / $limit),
                'currentPage' => (int) $page,
                'total' => $total,
            ], 'Jobs retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Get single job details
     */
    public function show($id)
    {
        try {
            $job = Job::with('creator')->find($id);

            if (!$job) {
                return $this->errorResponse('Job not found', 404);
            }

            return $this->successResponse($this->formatJob($job), 'Job retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Apply for a job
     */
    public function apply(Request $request, $id)
    {
        try {
            $user = $request->user();

            $job = Job::find($id);
            if (!$job) {
                return $this->errorResponse('Job not found', 404);
            }

            // Get caregiver profile
            $profile = CaregiverProfile::where('user_id', $user->id)->first();
            if (!$profile) {
                return $this->errorResponse('Caregiver profile not found', 404);
            }

            // Check if already applied
            $existingApplication = JobApplication::where('job_id', $id)
                ->where('caregiver_profile_id', $profile->id)
                ->first();

            if ($existingApplication) {
                return $this->errorResponse('You have already applied for this job');
            }

            // Create application
            $application = JobApplication::create([
                'job_id' => $id,
                'caregiver_profile_id' => $profile->id,
                'status' => 'submitted',
                'applied_at' => now(),
            ]);

            return $this->successResponse([
                'applicationId' => (string) $application->id,
                'status' => 'submitted',
            ], 'Application submitted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Format job for API response
     */
    private function formatJob($job)
    {
        return [
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description,
            'mustInclude' => $job->must_include,
            'jobType' => $job->job_type,
            'shiftPattern' => $job->shift_pattern,
            'city' => $job->city,
            'province' => $job->province,
            'requirements' => $job->requirements,
            'responsibilities' => $job->responsibilities,
            'salaryRange' => $job->salary_range,
            'isActive' => $job->is_active,
            'createdBy' => $job->creator ? [
                'id' => $job->creator->id,
                'name' => $job->creator->name,
            ] : null,
            'createdAt' => $job->created_at,
            'updatedAt' => $job->updated_at,
        ];
    }
}
