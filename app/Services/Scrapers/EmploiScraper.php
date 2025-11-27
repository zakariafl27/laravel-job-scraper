<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;

class EmploiScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.emploi.ma';

    public function getSourceName(): string
    {
        return 'emploi';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        $searchUrl = $this->buildSearchUrl($keyword, $location);
        
        Log::info("Scraping Emploi.ma", ['url' => $searchUrl]);

        $html = $this->makeRequest($searchUrl);
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        $crawler->filter('.job-list-item, .job-description-wrapper')->each(function ($node) use (&$jobs) {
            try {
                $title = $this->extractText($node, 'h5 a, .job-title a, h3.title a');
                $company = $this->extractText($node, '.company-name, .job-company');
                $location = $this->extractText($node, '.job-location, .location');
                $url = $this->extractAttribute($node, 'h5 a, .job-title a, h3.title a', 'href');

                if ($title && $url) {
                    if (!str_starts_with($url, 'http')) {
                        $url = $this->baseUrl . $url;
                    }

                    $jobs[] = [
                        'title' => $title,
                        'company' => $company ?? 'Non spécifié',
                        'location' => $location ?? 'Maroc',
                        'url' => $url,
                        'source' => $this->getSourceName(),
                        'source_id' => $this->generateSourceId($url),
                        'description' => null,
                        'job_type' => $this->extractText($node, '.job-type, .contract-type'),
                        'salary' => null,
                        'posted_date' => now(),
                        'deadline' => null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Error parsing Emploi.ma job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("Emploi.ma scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        $crawler = $this->createCrawler($html);
        
        return [
            'description' => $this->extractText($crawler, '.job-description, .description-wrapper'),
            'job_type' => $this->extractText($crawler, '.contract-type, .job-type'),
            'salary' => $this->extractText($crawler, '.salary'),
            'deadline' => $this->extractText($crawler, '.application-deadline'),
        ];
    }

    private function buildSearchUrl(string $keyword, ?string $location): string
    {
        $params = [
            'q' => $keyword,
        ];

        if ($location) {
            $params['ville'] = $location;
        }

        return $this->baseUrl . '/recherche-jobs-maroc?' . http_build_query($params);
    }
}