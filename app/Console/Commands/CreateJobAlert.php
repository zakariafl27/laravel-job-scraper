<?php

namespace App\Console\Commands;

use App\Models\JobAlert;
use Illuminate\Console\Command;

class CreateJobAlert extends Command
{
   protected $signature = 'job:create-alert
                        {keyword : Job keyword to search for}
                        {location? : Location to filter by}
                        {email? : User email }
                        {phone? : User Whatsapp phone number}';

    protected $description = 'Create a new job alert';

    public function handle(): int
    {
        $keyword = $this->argument('keyword');
        $location = $this->argument('location');
        $email = $this->argument('email') ?? $this->ask('Enter user email');
        $phone = $this->argument('phone') ?? $this->ask('Enter WhatsApp phone number (with country code)');

        $name = $this->ask('Enter user name', 'User');

        $jobTypes = [];
        if ($this->confirm('Do you want to filter by job type?', false)) {
            $availableTypes = ['CDI', 'CDD', 'Stage', 'Freelance', 'Télétravail'];
            $jobTypes = $this->choice(
                'Select job types (comma-separated)',
                $availableTypes,
                null,
                null,
                true
            );
        }

        $sources = [];
        if ($this->confirm('Do you want to select specific job sites?', false)) {
            $availableSources = ['rekrute', 'emploi', 'mjob'];
            $sources = $this->choice(
                'Select sources (comma-separated)',
                $availableSources,
                null,
                null,
                true
            );
        }

        $alert = JobAlert::create([
            'user_name' => $name,
            'user_email' => $email,
            'user_phone' => $phone,
            'keyword' => $keyword,
            'location' => $location,
            'job_types' => !empty($jobTypes) ? $jobTypes : null,
            'sources' => !empty($sources) ? $sources : null,
            'is_active' => true,
        ]);

        $this->info("Job alert created successfully!");
        $this->table(
            ['ID', 'Keyword', 'Location', 'Phone', 'Sources'],
            [[
                $alert->id,
                $alert->keyword,
                $alert->location ?? 'All',
                $alert->user_phone,
                implode(', ', $alert->getSourcesToScrape()),
            ]]
        );

        if ($this->confirm('Do you want to run the scraper now?', true)) {
            $this->call('job:scrape', ['alert_id' => $alert->id]);
        }

        return 0;
    }

}
