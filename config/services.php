<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Scraper Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the job scraping system.
    |
    */

    'timeout' => env('SCRAPE_TIMEOUT', 30),

    'user_agent' => env('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'),

    'delay_ms' => env('SCRAPE_DELAY_MS', 2000),

    'max_jobs_per_alert' => env('MAX_JOBS_PER_ALERT', 50),

    'max_concurrent_scrapes' => env('MAX_CONCURRENT_SCRAPES', 3),

    'scrape_interval' => env('SCRAPE_INTERVAL', 'hourly'),
];