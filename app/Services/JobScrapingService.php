<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobAlert;
use App\Services\Scrapers\ScraperFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobScrapingService
{
    private ScraperFactory $scraperFactory;

    public function __construct(ScraperFactory $scraperFactory)
    {
        $this->scraperFactory = $scraperFactory;
    }

    public function scrapeForAlert(JobAlert $alert): array
    {
        $allJobs = [];
        $source = $alert->getSourcesToScrape();

        Log::info("Starting scrape for alert", [
            'alert_id' => $alert->id,
            'keyword' => $alert->keyword,
            'source' => $source,
        ]);

        foreach ($sources as $source){
            try{
                $scraper = $this->scraperFactory->getScraper($source);
                $jobs = $scraper->scrape($alert->keyword, $alert->location);

                $jobs = $this->filterJobs($jobs, $alert);
                $allJobs = array_merge($allJobs, $jobs);
            } catch(\Exception $e){
                Log::error("Error scraping from {$source}",[
                    'error' => $e->getMessage(),
                    'alert_id' => $alert->id,
                ]);
            }
        }

        $newJobs = $this->saveJobs($allJobs, $alert);

        $alert->update(['last_scraped_at' => now()]);
        Log::info("Scrape completed for alert", [
            'alert_id' => $alert->id,
            'total_jobs' => count($allJobs),
            'new_jobs' => count($newJobs),
        ]);

        return $newJobs;
    }

    public function scrapeAllAlerts(): void
    {
        $alerts = JobAlert::where('is_active', true)->get();

        foreach($alerts as $alert){
            try{
                $this->scrapeForAlert($alert);
            } catch(\Exception $e){
                Log::error("Error processing alert", [
                    'alert_id' => $alert->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function filterJobs(array $jobs, JobAlert $alert): array
    {
        return array_filter($jobs, function ($job) use ($alert){
            if(!empty($alert->job_types) && $job['job_type']){
                if(!in_array(strtolower($job['job_type']), array_map('strtolower', $alert->job_types))){
                    return false;
                }
            }
            return true;
        });
    }

    private function saveJobs(array $jobs, JobAlert $alert): array
    {
        $newJobs = []; 
        $maxJobs = config('scraper.max_jobs_per_alert', 50);
        $count = 0;

        foreach ($jobs as $jobData){
            if($count >= $maxJobs){
                break;
            }

            try{
                DB::beginTransaction();

                $job = Job::where('source_id', $jobData['source_id'])->first();
                if(!$job){
                    $job = Job::create($jobData);
                }

                $exists = $alert->jobs()->where('job_id', $job->id)->exists();

                if(!$exists){
                    $alert->jobs()->attach($job->id, [
                        'is_notified' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $newJobs[] = $job;
                    $count++;
                }
                
                DB::commit();
            } catch(\Exception $e){
                DB::rollBack();
                Log::error("Error saving job", [
                    'error' => $e->getMessage(),
                    'job' => $jobData,
                ]);
            }
        }

        return $newJobs;
    }
}
