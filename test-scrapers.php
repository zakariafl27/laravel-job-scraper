<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING MOROCCAN SCRAPERS ===\n\n";

$sources = ['rekrute', 'emploi', 'mjob'];

foreach ($sources as $source) {
    echo "Testing {$source}...\n";
    echo str_repeat("-", 50) . "\n";
    
    try {
        $scraper = \App\Services\Scrapers\ScraperFactory::create($source);
        $jobs = $scraper->scrape('Developer', 'Casablanca');
        
        echo "✅ Jobs found: " . count($jobs) . "\n";
        
        if (count($jobs) > 0) {
            echo "First job:\n";
            echo "  Title: " . $jobs[0]['title'] . "\n";
            echo "  Company: " . $jobs[0]['company'] . "\n";
            echo "  Location: " . $jobs[0]['location'] . "\n";
            echo "  URL: " . $jobs[0]['url'] . "\n";
        }
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "Stack: " . substr($e->getTraceAsString(), 0, 300) . "...\n";
    }
    
    echo "\n";
}

echo "=== END TEST ===\n";
