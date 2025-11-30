<?php

namespace App\Console\Commands;

use App\Models\JobAlert;
use App\Jobs\ScrapeJobsForAlert;
use Illuminate\Console\Command;

class ScrapeJobs extends Command
{
     
    protected $signature = 'job:score {alert_id?}';

    protected $description = 'Scrape jobs for specific alert';


    public function handle(): int
    {
        $alertId = $this->argument('alert_id');

        if($alertId){

            $alert = JobAlert::find($alertId);
            if(!$alert){
            $this->error("Alert with ID {$alertId} not found");
            return 1;
            } 

        if(!$alert->is_active){
            $this->error("Alert is not active");
            return 1;
        }

        $this->info("Dispatching scrape job for alert {$alert->id}");
        ScrapeJobsForAlert::dispatch($alert);

        $this->info("Job dispatched succeessfully");
        } else{
            $alerts = JobAlert::where('is_active', true)->get();
            if($alerts->isEmpty()){
                $this->warn("No active alerts found");
                return 0;
            }

            $this->info("Found {$alerts->count()} active alerts");
            foreach($alerts as $alert){
                $this->info("Dispatcing scrape job for #{$alert->id} ({$alert->keyword})");
                ScrapeJobsForAlert::dispatch($alert);
            }
            $this->info("All jobs dispatched successfully");
        }
        return 0;
        
    }
}
