<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class JobScrapingService
{
    private string $appId;
    private string $appKey;

    public function __construct()
    {
        $this->appId = env('ADZUNA_APP_ID', '');
        $this->appKey = env('ADZUNA_APP_KEY', '');
    }

    public function scrapeForAlert(JobAlert $alert): array
    {
        $newJobs = [];
        
        Log::info("Starting job search", [
            'alert_id' => $alert->id, 
            'keyword' => $alert->keyword,
        ]);

        if (empty($this->appId) || empty($this->appKey)) {
            Log::error('Adzuna credentials missing');
            return [];
        }

        try {
            $jobs = $this->searchAdzuna($alert->keyword);
            
            Log::info("Adzuna returned jobs", ['count' => count($jobs)]);
            
            foreach ($jobs as $jobData) {
                try {
                    $job = Job::updateOrCreate(
                        ['source_id' => $jobData['source_id']],
                        $jobData
                    );
                    
                    if ($job->wasRecentlyCreated) {
                        $newJobs[] = $job;
                        $alert->jobs()->attach($job->id, [
                            'is_notified' => false,
                            'notified_at' => null
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to save job', [
                        'job' => $jobData['title'],
                        'error' => substr($e->getMessage(), 0, 100)
                    ]);
                    continue;
                }
            }

        } catch (\Exception $e) {
            Log::error("Search failed", ['error' => $e->getMessage()]);
        }

        $alert->update(['last_scraped_at' => now()]);
        
        Log::info("Search complete", ['new_jobs' => count($newJobs)]);
        
        return $newJobs;
    }

    private function searchAdzuna(string $keyword): array
    {
        $jobs = [];
        
        try {
            $url = "https://api.adzuna.com/v1/api/jobs/gb/search/1";

            $params = [
                'app_id' => $this->appId,
                'app_key' => $this->appKey,
                'what' => $keyword,
                'results_per_page' => 20,
            ];

            Log::info('Calling Adzuna API', ['keyword' => $keyword, 'country' => 'GB']);

            $response = Http::timeout(30)->get($url, $params);

            if (!$response->successful()) {
                Log::error('API failed', ['status' => $response->status()]);
                return [];
            }

            $data = $response->json();
            
            if (empty($data['results'])) {
                Log::warning('No results from API');
                return [];
            }

            Log::info('API results received', [
                'total' => $data['count'], 
                'returned' => count($data['results'])
            ]);

            foreach ($data['results'] as $item) {
                // Clean description - remove invalid UTF-8
                $description = $item['description'] ?? '';
                $description = mb_convert_encoding($description, 'UTF-8', 'UTF-8');
                $description = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $description);
                $description = substr(strip_tags($description), 0, 500);

                // Clean title
                $title = mb_convert_encoding($item['title'] ?? 'Job', 'UTF-8', 'UTF-8');
                $title = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $title);

                $jobs[] = [
                    'title' => $title,
                    'company' => $item['company']['display_name'] ?? 'Company',
                    'description' => $description,
                    'location' => $item['location']['display_name'] ?? 'Remote',
                    'job_type' => 'CDI',
                    'source' => 'adzuna',
                    'source_id' => 'adzuna-' . $item['id'],
                    'url' => $item['redirect_url'] ?? '#',
                    'salary' => isset($item['salary_min']) ? number_format($item['salary_min'], 0, '', '') . ' - ' . number_format($item['salary_max'], 0, '', '') : null,
                    'posted_date' => isset($item['created']) ? Carbon::parse($item['created']) : now(),
                ];
            }

        } catch (\Exception $e) {
            Log::error('API exception', ['error' => $e->getMessage()]);
        }

        return $jobs;
    }
}
