<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of jobs
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 15);
        
        $jobs = Job::query()
            ->latest()
            ->paginate($limit);

        return response()->json($jobs);
    }

    /**
     * Get job statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_alerts' => \App\Models\JobAlert::count(),
            'active_alerts' => \App\Models\JobAlert::where('is_active', true)->count(),
            'total_jobs' => Job::count(),
            'new_today' => Job::whereDate('created_at', today())->count(),
            'notifications_sent' => \App\Models\JobAlert::whereNotNull('last_scraped_at')->count(),
            'jobs_by_source' => Job::groupBy('source')
                ->selectRaw('source, count(*) as count')
                ->pluck('count', 'source')
        ];

        return response()->json($stats);
    }

    /**
     * Display the specified job
     */
    public function show(Job $job): JsonResponse
    {
        return response()->json($job);
    }
}
