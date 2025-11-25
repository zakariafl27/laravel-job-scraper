<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class JobAlertController extends Controller
{

    /**
     * Display job alerts
     */
    public function index(Request $request)
    {
        $query = JobAlert::query();

        if($request->has('is_active')){
            $query->where('is_active', $request->boolean('is_active'));
        }

        if($request->has('user_email')){
            $query->where('user_email', $request->user_email);
        }

        $alerts = $query->with(['jobs' => function($q){
            $q->latest()->limit(5);
        }])->paginate(10);

        return response()->json($alerts);
    }

    /**
     * Store a new job alert
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:255',
            'keyword' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'job_types' => 'nullable|array',
            'sources' => 'nullable|array'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $alert = JobAlert::create($validator->validated());

        //hna ghadi n3ayat 3la job 7ta nsawbha ou ndirha

        return response()->json([
            'success' => true,
            'message' => 'Job is created',
            'data' => $alert,
        ], 200);
    }

    /**
     * Display a job alert specific
     */
    public function show(JobAlert $alert)
    {
        $alert->load(['jobs' => function ($q) {
            $q->latest()->limit(15);
        }]);

        return response()->json([
            'success' => true,
            'data' => $alert,
        ]);
    }

    /**
     * Update a job alert specific
     */
    public function update(Request $request, JobAlert $alert)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:255',
            'keyword' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'job_types' => 'nullable|array',
            'sources' => 'nullable|array', 
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $alert->updated($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Job updated',
            'data' => $alert,
        ]);
    }

    /**
     * Delete a job alert specific
     */
    public function destroy(JobAlert $alert)
    {
        $alert->delete();
        return response()->json([
            'success' => true,
            'message' => 'Job deleted',
        ]);
    }

    /**
     * Scrape jobs for a specific alert
     */
    public function scrape(JobAlert $alert)
    {
        if(!$alert->is_active){
            return response()->json([
                'success' => false,
                'message' => 'Cannot scrape inactive alert',
            ], 400);
        }

        //hna ghadi n3ayat 3la job 7ta nsawbha ou ndirha
        //ghadi ndire response json dyale dispatch job
    }

    /**
     * Get new jobs for a specific alert
     */
    public function newJobs(JobAlert $alert)
    {
        $newJobs = $alert->newJobs()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $newJobs,
        ]);
    }

}
