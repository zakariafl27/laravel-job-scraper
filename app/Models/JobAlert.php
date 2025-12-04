<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAlert extends Model
{
    use HasFactory;

    protected $table = 'job_alerts';

    protected $fillable = [
        'user_name',
        'user_email',
        'user_phone',
        'keyword',
        'location',
        'job_types',
        'sources',
        'is_active',
        'last_scraped_at',
    ];

    protected $casts = [
        'job_types' => 'array',
        'sources' => 'array',
        'is_active' => 'boolean',
        'last_scraped_at' => 'datetime',
    ];

    /**
     * Get jobs for this alert
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'alert_job')
            ->withPivot('is_notified', 'notified_at')
            ->withTimestamps();
    }

    /**
     * Get new jobs that haven't been notified
     */
    public function newJobs()
    {
        return $this->jobs()->wherePivot('is_notified', false);
    }

    /**
     * Get sources to scrape from
     */
    public function getSourcesToScrape(): array
    {
        return $this->sources ?? ['adzuna'];
    }

    /**
     * Check if should scrape from a source
     */
    public function shouldScrapeFrom(string $source): bool
    {
        $sources = $this->getSourcesToScrape();
        return in_array($source, $sources);
    }
}