<?php

namespace App\Http\Controllers\Api\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Workdo\Recruitment\Models\Job;
use Workdo\Recruitment\Models\JobApplication;

class StarrlightJobController extends Controller
{
    /**
     * Starrlight company email
     */
    private const STARRLIGHT_EMAIL = 'starrlight@sekanitech.ca';

    /**
     * Get Starrlight company ID
     */
    private function getStarrlightCompanyId()
    {
        $user = User::where('email', self::STARRLIGHT_EMAIL)->first();
        return $user ? $user->created_by : null;
    }

    /**
     * Get all jobs
     * GET /api/jobs
     */
    public function index(Request $request)
    {
        $companyId = $this->getStarrlightCompanyId();

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        $query = Job::where('created_by', $companyId)
            ->where('status', 'active');

        // Filter by mustInclude (search)
        if ($request->has('mustInclude') && $request->mustInclude) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->mustInclude . '%')
                    ->orWhere('description', 'like', '%' . $request->mustInclude . '%');
            });
        }

        // Filter by jobType
        if ($request->has('jobType') && $request->jobType) {
            $query->where('type', $request->jobType);
        }

        // Filter by shiftPattern
        if ($request->has('shiftPattern') && $request->shiftPattern) {
            $query->where('shift', $request->shiftPattern);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 9);

        $jobs = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $jobsData = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'description' => strip_tags($job->description),
                'jobType' => $job->type,
                'shiftPattern' => $job->shift,
                'location' => $job->job_location ? $job->job_location->name : null,
                'branch' => $job->branch ? $job->branch->name : null,
                'salaryFrom' => $job->salary_from,
                'salaryTo' => $job->salary_to,
                'salaryType' => $job->salary_type,
                'requiredAt' => $job->required_at,
                'createdAt' => $job->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'jobs' => $jobsData,
                'totalPages' => $jobs->lastPage(),
                'currentPage' => $jobs->currentPage(),
                'total' => $jobs->total(),
            ]
        ]);
    }

    /**
     * Get single job detail
     * GET /api/jobs/:id
     */
    public function show($id)
    {
        $companyId = $this->getStarrlightCompanyId();

        $job = Job::where('id', $id)
            ->where('created_by', $companyId)
            ->where('status', 'active')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'jobType' => $job->type,
                'shiftPattern' => $job->shift,
                'location' => $job->job_location ? $job->job_location->name : null,
                'branch' => $job->branch ? $job->branch->name : null,
                'salaryFrom' => $job->salary_from,
                'salaryTo' => $job->salary_to,
                'salaryType' => $job->salary_type,
                'requiredAt' => $job->required_at,
                'skill' => $job->skill,
                'description' => $job->description,
                'createdAt' => $job->created_at->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Apply for a job
     * POST /api/jobs/:id/apply
     */
    public function apply(Request $request, $id)
    {
        $companyId = $this->getStarrlightCompanyId();

        $job = Job::where('id', $id)
            ->where('created_by', $companyId)
            ->where('status', 'active')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Get the authenticated user (candidate)
        $user = $request->user();

        // Check if already applied
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('candidate_id', $user->id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job'
            ], 400);
        }

        // Create application
        $application = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $user->id,
            'status' => 'pending',
            'created_by' => $companyId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => [
                'applicationId' => (string) $application->id,
                'status' => 'submitted'
            ]
        ], 201);
    }

    /**
     * Apply for job + Create account in one step
     * POST /api/jobs/apply-with-account
     */
    public function applyWithAccount(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'job_id' => 'required|integer',
            'phoneNumber' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:2',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'additionalInformation' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if email already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'An account with this email already exists. Please login to apply for jobs.',
                'data' => [
                    'existing_account' => true
                ]
            ], 400);
        }

        $companyId = $this->getStarrlightCompanyId();

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ], 404);
        }

        // Verify job exists and is active
        $job = Job::where('id', $request->job_id)
            ->where('created_by', $companyId)
            ->where('status', 'active')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Upload resume file
        $resumeFile = $request->file('resume');
        $filename = 'resume_' . time() . '_' . uniqid() . '.' . $resumeFile->getClientOriginalExtension();
        $resumePath = $resumeFile->storeAs('starrlight/resumes', $filename, 'public');
        $resumeUrl = storage_path('app/public/' . $resumePath);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'type' => 'candidate',
            'created_by' => $companyId,
            'creator_id' => $companyId,
        ]);

        // Create job application
        $application = JobApplication::create([
            'job_id' => $job->id,
            'candidate_id' => $user->id,
            'status' => 'pending',
            'created_by' => $companyId,
            'resume' => $resumeUrl,
        ]);

        // Generate authentication token
        $token = $user->createToken('starrlight-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Application submitted and account created successfully',
            'data' => [
                'user_id' => $user->id,
                'application_id' => (string) $application->id,
                'resume_url' => $resumeUrl,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }
}
