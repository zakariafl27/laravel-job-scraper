<?php

namespace App\Services\Scrapers;

class ScraperFactory
{
    public static function make(string $source): ?ScraperInterface
    {
        return match ($source) {
            'adzuna' => new AdzunaScraper(),
            'marocannonces' => new MarocannoncesScraper(),
            'anapec' => new AnapecScraper(),
            'rekrute' => new RekruteScraper(),
            'emploi' => new EmploiScraper(),
            'mjob' => new MJobScraper(),
            'bayt' => new BaytScraper(),
            'novojob' => new NovoJobScraper(),
            default => null,
        };
    }

    public static function getAvailableSources(): array
    {
        return [
            'adzuna',
            'marocannonces',
            'anapec',
        ];
    }
}
