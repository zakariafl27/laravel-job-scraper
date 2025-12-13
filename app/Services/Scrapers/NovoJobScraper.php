<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;

class NovoJobScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.novojob.com';

    public function getSourceName(): string
    {
        return 'novojob';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        $searchUrl = $this->buildSearchUrl($keyword, $location);
        
        Log::info("Scraping NovoJob", ['url' => $searchUrl]);

        $html = $this->makeRequest($searchUrl);
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        // NovoJob selectors
        $crawler->filter('.job-item, .offre-item, article.job, .listing-item')->each(function ($node) use (&$jobs) {
            try {
                $title = $this->extractText($node, 'h2 a, h3 a, .job-title a, .title a');
                $company = $this->extractText($node, '.company, .entreprise, .company-name');
                $location = $this->extractText($node, '.location, .lieu, .localisation');
                $url = $this->extractAttribute($node, 'h2 a, h3 a, .job-title a', 'href');

                if ($title && $url) {
                    if (!str_starts_with($url, 'http')) {
                        $url = $this->baseUrl . '/' . ltrim($url, '/');
                    }

                    $jobs[] = [
                        'title' => $title,
                        'company' => $company ?? 'Entreprise',
                        'location' => $location ?? 'Maroc',
                        'url' => $url,
                        'source' => $this->getSourceName(),
                        'source_id' => $this->generateSourceId($url),
                        'description' => null,
                        'job_type' => $this->detectJobType($title),
                        'salary' => null,
                        'posted_date' => now(),
                        'deadline' => null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Error parsing NovoJob job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("NovoJob scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        $crawler = $this->createCrawler($html);
        
        return [
            'description' => $this->extractText($crawler, '.job-description, .description'),
            'job_type' => $this->extractText($crawler, '.job-type, .type-contrat'),
            'salary' => $this->extractText($crawler, '.salary, .salaire'),
            'deadline' => null,
        ];
    }

    private function buildSearchUrl(string $keyword, ?string $location): string
    {
        $keyword = urlencode($keyword);
        $url = $this->baseUrl . "/chercher-emploi?mot_cle={$keyword}";
        
        if ($location) {
            $url .= "&lieu=" . urlencode($location);
        }
        
        return $url;
    }

    private function detectJobType(string $title): string
    {
        $text = strtolower($title);
        
        if (preg_match('/\b(permanent|full.?time|cdi)\b/i', $text)) {
            return 'CDI';
        }
        
        if (preg_match('/\b(contract|temporary|cdd)\b/i', $text)) {
            return 'CDD';
        }
        
        if (preg_match('/\b(freelance|contractor)\b/i', $text)) {
            return 'Freelance';
        }
        
        if (preg_match('/\b(intern|internship|stage)\b/i', $text)) {
            return 'Stage';
        }
        
        return 'CDI';
    }
}
