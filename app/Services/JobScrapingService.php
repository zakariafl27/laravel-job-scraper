<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobAlert;
use App\Services\Scrapers\ScraperFactory;
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
        $newJobs = [];
        
        // Get sources from alert
        $sources = $alert->getSourcesToScrape();

        Log::info("Starting scrape for alert", [
            'alert_id' => $alert->id,
            'keyword' => $alert->keyword,
            'sources' => $sources,
        ]);

        foreach ($sources as $source) {
            try {
                $scraper = $this->scraperFactory->getScraper($source);
                $jobs = $scraper->scrape($alert->keyword, $alert->location);

                foreach ($jobs as $jobData) {
                    $job = $this->saveJob($jobData);
                    
                    if ($job->wasRecentlyCreated) {
                        $newJobs[] = $job;
                        
                        // Link job to alert
                        $alert->jobs()->syncWithoutDetaching([
                            $job->id => [
                                'is_notified' => false,
                                'notified_at' => null
                            ]
                        ]);
                    }
                }

                Log::info("Scraped jobs from source", [
                    'source' => $source,
                    'count' => count($jobs),
                ]);
            } catch (\Exception $e) {
                Log::error("Error scraping from source", [
                    'source' => $source,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $alert->update(['last_scraped_at' => now()]);

        return $newJobs;
    }

    private function saveJob(array $data): Job
    {
        return Job::updateOrCreate(
            ['source_id' => $data['source_id']],
            [
                'title' => $data['title'],
                'company' => $data['company'],
                'description' => $data['description'] ?? null,
                'location' => $data['location'],
                'job_type' => $data['job_type'] ?? null,
                'source' => $data['source'],
                'url' => $data['url'],
                'salary' => $data['salary'] ?? null,
                'posted_date' => $data['posted_date'] ?? now(),
                'deadline' => $data['deadline'] ?? null,
            ]
        );
    }
}