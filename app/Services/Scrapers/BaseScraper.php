<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseScraper implements ScraperInterface
{
    protected int $timeout;
    protected string $userAgent;
    protected int $delayMs;

    public function __construct()
    {
        $this->timeout = config('scraper.timeout', 30);
        $this->userAgent = config('scraper.user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $this->delayMs = config('scraper.delay_ms', 2000);
    }


    protected function makeRequest(string $url, array $options = []): ?string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                ])
                ->get($url, $options);

            if ($response->successful()) {
                usleep($this->delayMs * 1000);
                return $response->body();
            }

            Log::warning("Failed to fetch URL: {$url}", [
                'status' => $response->status(),
                'source' => $this->getSourceName(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching URL: {$url}", [
                'error' => $e->getMessage(),
                'source' => $this->getSourceName(),
            ]);
            return null;
        }
    }
 

    protected function createCrawler(string $html): Crawler
    {
        return new Crawler($html);
    }


    protected function cleanText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', $text));
    }


    protected function generateSourceId(string $url): string
    {
        return $this->getSourceName() . '_' . md5($url);
    }

    protected function extractText(Crawler $crawler, string $selector, ?string $default = null): ?string
    {
        try {
            $node = $crawler->filter($selector);
            if ($node->count() > 0) {
                return $this->cleanText($node->text());
            }
        } catch (\Exception $e) {
            Log::debug("Failed to extract text with selector: {$selector}", [
                'error' => $e->getMessage(),
            ]);
        }

        return $default;
    }

    protected function extractAttribute(Crawler $crawler, string $selector, string $attribute, ?string $default = null): ?string
    {
        try {
            $node = $crawler->filter($selector);
            if ($node->count() > 0) {
                return $node->attr($attribute);
            }
        } catch (\Exception $e) {
            Log::debug("Failed to extract attribute with selector: {$selector}", [
                'error' => $e->getMessage(),
            ]);
        }

        return $default;
    }
}