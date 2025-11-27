<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Job::query();

        if($request->has('keyword')){
            $query->byKeyword($request->keyword);
        }

        if($request->has('location')){
            $query->byLocation($request->location);
        }

        if($request->has('sources')){
            $sources = is_array($request->sources) ? $request->sources : explode(',', $request->sources);
            $query->bySource($sources);
        }

        if($request->has('job_types')){
            $types = is_array($request->job_types) ? $request->job_types : expload(',', $request->job_types);
            $query->byJobType($types);
        }

        if($request->has('from_date')){
            $query->where('created_at', '>=', $request->from_date);
        }

        if($request->has('to_date')){
            $query->where('created_at', '<=', $request->to_date);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $jobs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    public function show(Job $job): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $job,
        ]);
    }

    public function statisctics(): JsonResponse
    {
        $stats = [
            'total_jobs' => Job::count(),
            'jobs_today' => Job::whereDate('created_at', today())->count(),
            'jobs_this_week' => Job::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'jobs_by_source' => Job::selectRaw('source, COUNT(*) as count')->groupBy('source')
                ->pluck('count', 'source'),
            'recent_jobs' => Job::latest()->limit(10)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
