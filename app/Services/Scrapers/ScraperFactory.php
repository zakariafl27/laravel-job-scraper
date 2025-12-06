<?php

namespace App\Services\Scrapers;

use InvalidArgumentException;

class ScraperFactory
{
    public static function create(string $name): ScraperInterface
    {
        return match(strtolower($name)) {
            'rekrute' => new RekruteScraper(),
            'emploi' => new EmploiScraper(),
            'mjob' => new MJobScraper(),
            'bayt' => new BaytScraper(),
            default => throw new InvalidArgumentException("Scraper '{$name}' not found")
        };
    }

    public static function getAllScrapers(): array
    {
        return [
            'rekrute' => self::create('rekrute'),
            'emploi' => self::create('emploi'),
            'mjob' => self::create('mjob'),
            'bayt' => self::create('bayt'),
        ];
    }

    public static function getScrapers(array $names): array
    {
        $scrapers = [];
        foreach ($names as $name) {
            try {
                $scrapers[$name] = self::create($name);
            } catch (InvalidArgumentException $e) {
                // Skip
            }
        }
        return $scrapers;
    }
}
