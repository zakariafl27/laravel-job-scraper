<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class job extends Model
{
    use HasFactory;

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
    ];

    public function alerts(): BelongsToMany
    {
        return $this->belongsToMany(JobAlert::class, 'alert_job')
            ->withPivot('is_notified', 'notified_at')
            ->withTimestamps();
    }

    public function scopeByKeyword($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
                ->orWhere('description', 'LIKE', "%{$keyword}%")
                ->orWhere('company', 'LIKE', "%{$keyword}%");
        });
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'LIKE', "%{$location}%");
    }

    public function scopeByJobType($query, array $types)
    {
        return $query->whereIn('job_type', $types);
    }

    public function scopeBySource($query, array $sources)
    {
        return $query->whereIn('source', $sources);
    } 

    public function toWhatsappFormat(): string
    {
        $message = "{$this->title}\n";
        $message .= "{$this->company}\n";
        $message .= "{$this->location}\n";

        if($this->job_type){
            $message .= "{$this->job_type}\n";
        }

        if($this->salary){
            $message .= "{$this->salary}\n";
        }

        if($this->deadline){
            $message .= "{$this->deadline->format('d/m/Y')}\n";
        }

        $message .= "{$this->url}\n";

        return $message;
    }
}
