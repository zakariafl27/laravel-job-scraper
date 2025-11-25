<?php

namespace App\Services\Scrapers;

use InvalidArgumentException;

class ScraperFactory
{
    private array $scrapers = [];

    public function __construct()
    {
        $this->registerScrapers();
    }

    private function registerScrapers(): void
    {
        $this->scrapers = [
            'rekrute' => new RekruteScraper(),
            'emploi' => new EmploiScraper(),
            'mjob' => new MJobScraper(),
        ];
    }

    public function getScraper(string $name): ScraperInterface
    {
        if (!isset($this->scrapers[$name])){
            throw new InvalidArgumentExeption("Scraper '{$name}' not found");
        }
        return $this->scrapers[$name];
    }

    public function getAllScrapers(): array
    {
        return $this->scrapers;
    }

    public function getScrapers(array $name): array
    {
        return array_filter(
            $this->scrapers,
            fn($key) => in_array($key, $names),
        );
    }

    
}
