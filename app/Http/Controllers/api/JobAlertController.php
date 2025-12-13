<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobAlert;
use App\Jobs\ScrapeJobsForAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobAlertController extends Controller
{
    public function index()
    {
        $alerts = JobAlert::with('jobs')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json($alerts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:20',
            'keyword' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'job_types' => 'nullable|array',
            'job_types.*' => 'in:CDI,CDD,Stage,Freelance,Temps partiel',
            'sources' => 'nullable|array',
            'sources.*' => 'in:rekrute,emploi,mjob,adzuna,bayt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $alert = JobAlert::create([
            'user_name' => $request->user_name,
            'user_email' => $request->user_email,
            'user_phone' => $request->user_phone,
            'keyword' => $request->keyword,
            'location' => $request->location,
            'job_types' => $request->job_types ?? [],
            'sources' => $request->sources ?? ['adzuna'],
            'is_active' => true,
        ]);

        ScrapeJobsForAlert::dispatch($alert);

        return response()->json([
            'success' => true,
            'message' => 'Alert created successfully! Scraping jobs now...',
            'data' => $alert
        ], 201);
    }

    public function show($id)
    {
        $alert = JobAlert::with('jobs')->findOrFail($id);
        return response()->json($alert);
    }

    public function update(Request $request, $id)
    {
        $alert = JobAlert::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'user_name' => 'string|max:255',
            'user_email' => 'email|max:255',
            'user_phone' => 'string|max:20',
            'keyword' => 'string|max:255',
            'location' => 'nullable|string|max:255',
            'job_types' => 'nullable|array',
            'sources' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $alert->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $alert
        ]);
    }

    public function destroy($id)
    {
        $alert = JobAlert::findOrFail($id);
        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert deleted successfully'
        ]);
    }
}