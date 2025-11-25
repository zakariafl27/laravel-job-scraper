<?php

namespace App\Services\Scrapers;

interface ScraperInterface
{
    /**
     * Scrapes job listings from a specific source.
     */
    public function scrape(string $keyword, ?string $location = null): array;

    /**
     * Get the source name
     */
    public function getSourceName(): string;

    /**
     * Parse job details from the HTML
     */
    public function parseJobDetails(string $html): array;
}
