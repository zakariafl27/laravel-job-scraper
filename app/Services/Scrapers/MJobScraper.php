<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;

class MJobScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.m-job.ma';

    public function getSourceName(): string
    {
        return 'mjob';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        $searchUrl = $this->buildSearchUrl($keyword, $location);
        
        Log::info("Scraping M-job.ma", ['url' => $searchUrl]);

        $html = $this->makeRequest($searchUrl);
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        $crawler->filter('.job-item, article.job')->each(function ($node) use (&$jobs) {
            try {
                $title = $this->extractText($node, 'h2 a, .job-title');
                $company = $this->extractText($node, '.company, .employer');
                $location = $this->extractText($node, '.location, .city');
                $url = $this->extractAttribute($node, 'h2 a, .job-title', 'href');

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
                        'job_type' => null,
                        'salary' => null,
                        'posted_date' => now(),
                        'deadline' => null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Error parsing M-job job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("M-job scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        $crawler = $this->createCrawler($html);
        
        return [
            'description' => $this->extractText($crawler, '.job-details, .description'),
            'job_type' => $this->extractText($crawler, '.contract-type'),
            'salary' => $this->extractText($crawler, '.salary'),
            'deadline' => $this->extractText($crawler, '.deadline'),
        ];
    }

    private function buildSearchUrl(string $keyword, ?string $location): string
    {
        $params = [
            'k' => $keyword,
        ];

        if ($location) {
            $params['l'] = $location;
        }

        return $this->baseUrl . '/recherche?' . http_build_query($params);
    }
}