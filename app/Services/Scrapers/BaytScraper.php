<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;

class BaytScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.bayt.com';

    public function getSourceName(): string
    {
        return 'bayt';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        $searchUrl = $this->buildSearchUrl($keyword, $location);
        
        Log::info("Scraping Bayt.com", ['url' => $searchUrl]);

        $html = $this->makeRequest($searchUrl);
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        // Bayt uses these selectors
        $crawler->filter('li.has-pointer-d, .card-deck li, article.job')->each(function ($node) use (&$jobs) {
            try {
                $title = $this->extractText($node, 'h2 a, .job-title a, .t-default a');
                $company = $this->extractText($node, '.company-name, .t-small');
                $location = $this->extractText($node, '.location, .joblocation');
                $url = $this->extractAttribute($node, 'h2 a, .job-title a, .t-default a', 'href');

                if ($title && $url) {
                    if (!str_starts_with($url, 'http')) {
                        $url = $this->baseUrl . $url;
                    }

                    $jobs[] = [
                        'title' => $title,
                        'company' => $company ?? 'Non spécifié',
                        'location' => $location ?? 'Morocco',
                        'url' => $url,
                        'source' => $this->getSourceName(),
                        'source_id' => $this->generateSourceId($url),
                        'description' => null,
                        'job_type' => $this->extractText($node, '.job-type, .t-mute'),
                        'salary' => null,
                        'posted_date' => now(),
                        'deadline' => null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Error parsing Bayt job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("Bayt scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        $crawler = $this->createCrawler($html);
        
        return [
            'description' => $this->extractText($crawler, '.card-content, .job-description'),
            'job_type' => $this->extractText($crawler, '.job-type'),
            'salary' => $this->extractText($crawler, '.salary'),
            'deadline' => null,
        ];
    }

    private function buildSearchUrl(string $keyword, ?string $location): string
    {
        $location = $location ?: 'morocco';
        $location = strtolower(str_replace(' ', '-', $location));
        $keyword = strtolower(str_replace(' ', '-', $keyword));
        
        return $this->baseUrl . "/en/{$location}/jobs/{$keyword}-jobs/";
    }
}
