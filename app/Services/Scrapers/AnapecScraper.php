<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AnapecScraper extends BaseScraper
{
    private string $baseUrl = 'https://www.anapec.org';

    public function getSourceName(): string
    {
        return 'anapec';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        $jobs = [];
        
        $searchUrl = $this->baseUrl . "/";
        
        Log::info("Scraping ANAPEC", ['url' => $searchUrl]);

        // Use Http facade with SSL verification disabled for ANAPEC
        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get($searchUrl);
            
            $html = $response->body();
        } catch (\Exception $e) {
            Log::error('ANAPEC request failed', ['error' => $e->getMessage()]);
            return $jobs;
        }
        
        if (!$html) {
            return $jobs;
        }

        $crawler = $this->createCrawler($html);

        $crawler->filter('tr.grid.element')->each(function ($node) use (&$jobs, $keyword) {
            try {
                $titleNode = $node->filter('a#offreLink')->first();
                $locationNode = $node->filter('.ville')->first();
                
                if ($titleNode->count() === 0) {
                    return;
                }
                
                $title = $titleNode->text();
                $url = $titleNode->attr('href');
                $location = $locationNode->count() > 0 ? $locationNode->text() : 'Maroc';

                if ($title && stripos($title, $keyword) !== false && $url) {
                    if (!str_starts_with($url, 'http')) {
                        $url = $this->baseUrl . $url;
                    }

                    $jobs[] = [
                        'title' => trim($title),
                        'company' => 'ANAPEC',
                        'location' => trim($location),
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
                Log::warning("Error parsing ANAPEC job", ['error' => $e->getMessage()]);
            }
        });

        Log::info("ANAPEC scraping completed", ['jobs_found' => count($jobs)]);

        return $jobs;
    }

    public function parseJobDetails(string $html): array
    {
        return [
            'description' => null,
            'job_type' => null,
            'salary' => null,
            'deadline' => null,
        ];
    }

    private function detectJobType(string $title): string
    {
        $text = strtolower($title);
        
        if (preg_match('/\b(cdd|saisonnier)\b/i', $text)) {
            return 'CDD';
        }
        
        if (preg_match('/\b(stage)\b/i', $text)) {
            return 'Stage';
        }
        
        return 'CDI';
    }
}
