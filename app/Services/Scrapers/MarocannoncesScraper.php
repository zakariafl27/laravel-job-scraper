<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;

class MarocannoncesScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.marocannonces.com';

    public function getSourceName(): string
    {
        return 'marocannonces';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        // Marocannonces emploi category - direct to offers
        $searchUrl = $this->baseUrl . "/categorie/309/Offres-emploi.html";
        
        Log::info("Scraping Marocannonces", ['url' => $searchUrl]);

        $html = $this->makeRequest($searchUrl);
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        // Marocannonces uses .cars-list li a structure
        $crawler->filter('.cars-list li a')->each(function ($node) use (&$jobs, $keyword) {
            try {
                $title = $node->attr('title') ?: $this->extractText($node, 'h2, h3, .title');
                $url = $node->attr('href');

                // Filter by keyword (case insensitive)
                if ($title && stripos($title, $keyword) !== false && $url) {
                    if (!str_starts_with($url, 'http')) {
                        $url = $this->baseUrl . '/' . ltrim($url, '/');
                    }

                    // Extract location from title if exists (format: "Job Title - Location")
                    $location = 'Maroc';
                    if (preg_match('/[–-]\s*([^–-]+)$/u', $title, $matches)) {
                        $location = trim($matches[1]);
                    }

                    $jobs[] = [
                        'title' => trim($title),
                        'company' => 'Entreprise',
                        'location' => $location,
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
                Log::warning("Error parsing Marocannonces job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("Marocannonces scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        $crawler = $this->createCrawler($html);
        
        return [
            'description' => $this->extractText($crawler, '.description, .content, #desc'),
            'job_type' => null,
            'salary' => null,
            'deadline' => null,
        ];
    }

    private function detectJobType(string $title): string
    {
        $text = strtolower($title);
        
        if (preg_match('/\b(permanent|cdi)\b/i', $text)) {
            return 'CDI';
        }
        
        if (preg_match('/\b(cdd|temporaire)\b/i', $text)) {
            return 'CDD';
        }
        
        if (preg_match('/\b(freelance|indépendant)\b/i', $text)) {
            return 'Freelance';
        }
        
        if (preg_match('/\b(stage|stagiaire)\b/i', $text)) {
            return 'Stage';
        }
        
        return 'CDI';
    }
}
