<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JobAlert extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_name',
        'user_email',
        'user_phone',
        'keyword',
        'location',
        'job_type',
        'sources',
        'is_active',
        'last_scraped_at',
    ];


    /**
     * how the data will be automatically converted
     */
    protected $casts = [
        'job_types' => 'array',
        'sources' => 'array',
        'is_active' => 'boolean',
        'last_scraped_at' => 'datetime',
    ];

    /**
     * many-to-many relationship between JobAlert and Job models
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'alert_job')
        ->withPivot('is_notified', 'notified_at')
        ->withTimestamps();
    }

    /**
     * scope to get the new jobs
     */
    public function newJobs()
    {
        return $this->Jobs()
        ->wherePivot('is_notified', false)
        ->orderBy('jobs.created_at', 'desc');
    }

    /**
     * method to get the sources to scrape
     */
    public function getSourcesToScrape(): array
    {
        return $this->sources ?? ['rekrute', 'emploi', 'mjob', 'marocannonce'];
    }


    /**
     * method to mark the jobs as notified
     */
    public function markJobsAsNotified(array $jobIds): void
    {
        $this->jobs()->whereIn('job_id', $jobIds)
        ->update([
            'is_notified' => true,
            'notified_at' => now(),
        ]);
    }


}
