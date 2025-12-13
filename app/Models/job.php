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

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($job) {
            // Remove NULL bytes and fix UTF-8 encoding
            $job->title = self::cleanText($job->title);
            $job->company = self::cleanText($job->company);
            $job->description = self::cleanText($job->description);
            $job->location = self::cleanText($job->location);
            $job->salary = self::cleanText($job->salary);
        });
        
        static::updating(function ($job) {
            // Also clean on update
            $job->title = self::cleanText($job->title);
            $job->company = self::cleanText($job->company);
            $job->description = self::cleanText($job->description);
            $job->location = self::cleanText($job->location);
            $job->salary = self::cleanText($job->salary);
        });
    }
    
    private static function cleanText(?string $text): ?string
    {
        if (!$text) return $text;
        
        // Remove NULL bytes (0x00)
        $text = str_replace("\0", '', $text);
        
        // Remove other problematic characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        
        // Fix UTF-8 encoding
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        return trim($text);
    }

    public function alerts(): BelongsToMany
    {
        return $this->belongsToMany(JobAlert::class, 'alert_job')
            ->withPivot('is_notified', 'notified_at')
            ->withTimestamps();
    }

    public function scopeByKeyword($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'ILIKE', "%{$keyword}%")
                ->orWhere('description', 'ILIKE', "%{$keyword}%")
                ->orWhere('company', 'ILIKE', "%{$keyword}%");
        });
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'ILIKE', "%{$location}%");
    }

    public function scopeByJobType($query, array $types)
    {
        return $query->whereIn('job_type', $types);
    }

    public function scopeBySource($query, array $sources)
    {
        return $query->whereIn('source', $sources);
    }

    public function scopeCreatedToday($query)
    {
        return $query->whereRaw("created_at::date = CURRENT_DATE");
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function toWhatsappFormat(): string
    {
        $message = "*New Job Alert*\n\n";
        $message .= "*{$this->title}*\n";
        $message .= "{$this->company}\n";
        $message .= "{$this->location}\n";

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
