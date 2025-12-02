<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of jobs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Job::query();

        // Filter by keyword
        if ($request->has('keyword')) {
            $query->byKeyword($request->keyword);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->byLocation($request->location);
        }

        // Filter by source
        if ($request->has('sources')) {
            $sources = is_array($request->sources) ? $request->sources : explode(',', $request->sources);
            $query->bySource($sources);
        }

        // Filter by job type
        if ($request->has('job_types')) {
            $types = is_array($request->job_types) ? $request->job_types : explode(',', $request->job_types);
            $query->byJobType($types);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $jobs = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $job,
        ]);
    }

    /**
     * Get job statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalAlerts = \App\Models\JobAlert::count();
        $activeAlerts = \App\Models\JobAlert::where('is_active', true)->count();
        $totalJobs = Job::count();
        // Count jobs created today - using Carbon to avoid PostgreSQL casting issues
        $newToday = Job::where('created_at', '>=', now()->startOfDay())
            ->where('created_at', '<=', now()->endOfDay())
            ->count();
        
        // Count notifications sent (jobs that have been notified)
        $notificationsSent = \DB::table('alert_job')
            ->where('is_notified', true)
            ->count();

        return response()->json([
            'total_alerts' => $totalAlerts,
            'active_alerts' => $activeAlerts,
            'total_jobs' => $totalJobs,
            'new_today' => $newToday,
            'notifications_sent' => $notificationsSent,
            'jobs_by_source' => Job::selectRaw('source, COUNT(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source'),
        ]);
    }
}