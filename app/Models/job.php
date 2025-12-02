<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    protected $fillable = [
        'title',
        'company',
        'description',
        'location',
        'job_type',
        'source',
        'source_id',
        'url',
        'salary',
        'posted_date',
        'deadline',
    ];

    protected $casts = [
        'posted_date' => 'date',
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function alerts(): BelongsToMany
    {
        return $this->belongsToMany(JobAlert::class, 'alert_job')
            ->withPivot('is_notified', 'notified_at')
            ->withTimestamps();
    }

    /**
     * Scope for keyword search - PostgreSQL ILIKE for case-insensitive
     */
    public function scopeByKeyword($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'ILIKE', "%{$keyword}%")
                ->orWhere('description', 'ILIKE', "%{$keyword}%")
                ->orWhere('company', 'ILIKE', "%{$keyword}%");
        });
    }

    /**
     * Scope for location search - PostgreSQL ILIKE for case-insensitive
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'ILIKE', "%{$location}%");
    }

    /**
     * Scope for filtering by job types
     */
    public function scopeByJobType($query, array $types)
    {
        return $query->whereIn('job_type', $types);
    }

    /**
     * Scope for filtering by sources
     */
    public function scopeBySource($query, array $sources)
    {
        return $query->whereIn('source', $sources);
    }

    /**
     * Scope for jobs created today - PostgreSQL compatible
     */
    public function scopeCreatedToday($query)
    {
        return $query->whereRaw("created_at::date = CURRENT_DATE");
    }

    /**
     * Scope for recent jobs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Format job for WhatsApp message
     */
    public function toWhatsappFormat(): string
    {
        $message = "*New Job Alert*\n\n";
        $message .= "*{$this->title}*\n";
        $message .= "{$this->company}\n";
        $message .= "$this->location}\n";

        if ($this->job_type) {
            $message .= "{$this->job_type}\n";
        }

        if ($this->salary) {
            $message .= "{$this->salary}\n";
        }

        if ($this->deadline) {
            $message .= "Deadline: {$this->deadline->format('d/m/Y')}\n";
        }

        $message .= "\n{$this->url}\n";
        $message .= "\n_Source: {$this->source}_";

        return $message;
    }
}