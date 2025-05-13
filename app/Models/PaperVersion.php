<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'file_path',
        'version_number',
        'change_notes',
        'uploaded_by',
    ];

    // Relationships
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    // Current version papers
    public function currentVersionPapers()
    {
        return $this->hasMany(Paper::class, 'current_version_id');
    }
}