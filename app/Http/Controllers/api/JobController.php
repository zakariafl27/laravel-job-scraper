<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobAlert;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        
        $jobs = Job::with('alerts')
            ->latest()
            ->paginate($perPage);

        return response()->json($jobs);
    }

    public function statistics()
    {
        $totalJobs = Job::count();
        $newToday = Job::whereDate('created_at', today())->count();
        $totalAlerts = JobAlert::where('is_active', true)->count();
        
        $notificationsSent = Job::whereHas('alerts', function($query) {
            $query->where('is_notified', true);
        })->count();

        $jobsBySource = [
            'adzuna' => Job::where('source', 'adzuna')->count(),
            'marocannonces' => Job::where('source', 'marocannonces')->count(),
            'anapec' => Job::where('source', 'anapec')->count(),
        ];

        // Jobs today timeline (by hour)
        $jobsTodayTimeline = Job::selectRaw('EXTRACT(HOUR FROM created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function($item) {
                return [
                    'hour' => str_pad($item->hour, 2, '0', STR_PAD_LEFT),
                    'count' => $item->count
                ];
            });

        return response()->json([
            'total_jobs' => $totalJobs,
            'new_today' => $newToday,
            'total_alerts' => $totalAlerts,
            'notifications_sent' => $notificationsSent,
            'jobs_by_source' => $jobsBySource,
            'jobs_today_timeline' => $jobsTodayTimeline
        ]);
    }
}
