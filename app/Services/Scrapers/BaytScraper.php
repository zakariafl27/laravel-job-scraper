<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BaytScraper extends BaseScraper
{
    private string $puppeteerUrl = 'http://localhost:3002';

    public function getSourceName(): string
    {
        return 'bayt';
    }

    public function scrape(string $keyword, ?string $location = null): array
    {
        Log::info("Scraping Bayt via Puppeteer", [
            'keyword' => $keyword,
            'location' => $location
        ]);

        try {
            $country = $this->getCountry($location);
            
            // Call Puppeteer service
            $response = Http::timeout(60)->post("{$this->puppeteerUrl}/scrape", [
                'keyword' => $keyword,
                'country' => $country
            ]);
            
            if (!$response->successful()) {
                Log::error('Puppeteer service failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }
            
            $data = $response->json();
            
            if (!$data['success']) {
                Log::error('Bayt scraping failed', ['error' => $data['error'] ?? 'Unknown']);
                return [];
            }
            
            $jobs = [];
            foreach ($data['jobs'] as $jobData) {
                $jobs[] = [
                    'title' => $jobData['title'],
                    'company' => $jobData['company'] ?? 'Company',
                    'location' => $jobData['location'] ?? 'Morocco',
                    'url' => $jobData['url'],
                    'source' => $this->getSourceName(),
                    'source_id' => $this->generateSourceId($jobData['url']),
                    'description' => null,
                    'job_type' => $this->detectJobType($jobData['title']),
                    'salary' => null,
                    'posted_date' => now(),
                    'deadline' => null,
                ];
            }
            
            Log::info("Bayt scraping completed", ['jobs_found' => count($jobs)]);
            
            return $jobs;
            
        } catch (\Exception $e) {
            Log::error('Bayt scraper exception', ['error' => $e->getMessage()]);
            return [];
        }
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

    private function getCountry(?string $location): string
    {
        if (!$location) return 'morocco';
        
        $locationLower = strtolower(trim($location));
        
        // Morocco
        if (str_contains($locationLower, 'morocco') || str_contains($locationLower, 'maroc') ||
            str_contains($locationLower, 'casablanca') || str_contains($locationLower, 'rabat') ||
            str_contains($locationLower, 'tanger') || str_contains($locationLower, 'fes') ||
            str_contains($locationLower, 'marrakech') || str_contains($locationLower, 'agadir')) {
            return 'morocco';
        }
        
        // UAE
        if (str_contains($locationLower, 'dubai') || str_contains($locationLower, 'uae') ||
            str_contains($locationLower, 'emirates') || str_contains($locationLower, 'abu dhabi')) {
            return 'uae';
        }
        
        // Saudi Arabia
        if (str_contains($locationLower, 'saudi') || str_contains($locationLower, 'riyadh') ||
            str_contains($locationLower, 'jeddah') || str_contains($locationLower, 'ksa')) {
            return 'saudi-arabia';
        }
        
        // Egypt
        if (str_contains($locationLower, 'egypt') || str_contains($locationLower, 'cairo')) {
            return 'egypt';
        }
        
        // Lebanon
        if (str_contains($locationLower, 'lebanon') || str_contains($locationLower, 'beirut')) {
            return 'lebanon';
        }
        
        // Jordan
        if (str_contains($locationLower, 'jordan') || str_contains($locationLower, 'amman')) {
            return 'jordan';
        }
        
        // Qatar
        if (str_contains($locationLower, 'qatar') || str_contains($locationLower, 'doha')) {
            return 'qatar';
        }
        
        // Kuwait
        if (str_contains($locationLower, 'kuwait')) {
            return 'kuwait';
        }
        
        // Oman
        if (str_contains($locationLower, 'oman') || str_contains($locationLower, 'muscat')) {
            return 'oman';
        }
        
        // Bahrain
        if (str_contains($locationLower, 'bahrain') || str_contains($locationLower, 'manama')) {
            return 'bahrain';
        }
        
        return 'morocco'; // Default
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
        
        return 'CDI'; // Default
    }
}