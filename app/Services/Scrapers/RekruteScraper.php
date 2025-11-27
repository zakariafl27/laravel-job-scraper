<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;

class RekruteScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.rekrute.com';

    public function getSourceName(): string
    {
        return 'rekrute';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        $searchUrl = $this->buildSearchUrl($keyword, $location);
        
        Log::info("Scraping Rekrute", ['url' => $searchUrl]);

        $html = $this->makeRequest($searchUrl);
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        $crawler->filter('.post-id')->each(function ($node) use (&$jobs) {
            try {
                $title = $this->extractText($node, 'h2.titreJob a, .job-title a');
                $company = $this->extractText($node, '.company, .companyJob');
                $location = $this->extractText($node, '.location, .villeJob');
                $url = $this->extractAttribute($node, 'h2.titreJob a, .job-title a', 'href');

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
                Log::warning("Error parsing Rekrute job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("Rekrute scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        $crawler = $this->createCrawler($html);
        
        return [
            'description' => $this->extractText($crawler, '.description, .job-description'),
            'job_type' => $this->extractText($crawler, '.job-type, .typeContrat'),
            'salary' => $this->extractText($crawler, '.salary, .salaire'),
            'deadline' => $this->extractText($crawler, '.deadline, .dateExpiration'),
        ];
    }

    private function buildSearchUrl(string $keyword, ?string $location): string
    {
        $params = [
            'keywords' => $keyword,
        ];

        if ($location) {
            $params['l'] = $location;
        }

        return $this->baseUrl . '/offres.html?' . http_build_query($params);
    }
}