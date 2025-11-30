<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Scrape jobs hourly for all active alerts
        $schedule->command('job:scrape')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up old jobs older than 30 days
        $schedule->call(function () {
            \App\Models\Job::where('created_at', '<', now()->subDays(30))->delete();
        })->daily();

        // Log scheduler activity
        $schedule->command('schedule:list')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}