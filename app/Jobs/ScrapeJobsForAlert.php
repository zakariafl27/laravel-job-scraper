<?php

namespace App\Jobs;

use App\Models\JobAlert;
use App\Services\JobScrapingService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeJobsForAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    private JobAlert $alert;

    public function __construct(JobAlert $alert)
    {
        $this->alert = $alert;
    }

    public function handle(
        JobScrapingService $scrapingService,
        WhatsAppService $whatsappService
    ): void {
        Log::info("Processing scrape job for alert", ['alert_id' => $this->alert->id]);

        try {
            $newJobs = $scrapingService->scrapeForAlert($this->alert);

            if (!empty($newJobs)) {
                $jobsCollection = collect($newJobs);
                $whatsappService->sendJobNotifications($this->alert, $jobsCollection);
            } else {
                Log::info("No new jobs found for alert", ['alert_id' => $this->alert->id]);
            }
        } catch (\Exception $e) {
            Log::error("Error in scrape job", [
                'alert_id' => $this->alert->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e; 
        }
    }


    public function failed(\Throwable $exception): void
    {
        Log::error("Scrape job failed permanently", [
            'alert_id' => $this->alert->id,
            'error' => $exception->getMessage(),
        ]);
    }
}