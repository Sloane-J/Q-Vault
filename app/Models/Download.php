<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Download extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'paper_id',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'paper_id', 'downloaded_at'])
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Download record created',
                'updated' => 'Download record updated',
                'deleted' => 'Download record deleted',
                default => "Download record {$eventName}"
            });
    }

    /**
     * Get the user that owns the download.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the paper that was downloaded.
     */
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }
}