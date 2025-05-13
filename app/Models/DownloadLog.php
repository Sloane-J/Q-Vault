<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    // Relationships
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByPaper($query, $paperId)
    {
        return $query->where('paper_id', $paperId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeLastWeek($query)
    {
        return $query->where('created_at', '>=', now()->subWeek());
    }

    public function scopeLastMonth($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }
}