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

        


        
    }
}
