<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(ScraperFactory::class, function($app){
            return new ScraperFactory();
        });

        $this->app->singleton(JobScrapingService::class, function($app){
            return new JobScrapingService($app->make(ScraperFactory::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
