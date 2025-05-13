<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paper extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'course_id',
        'title',
        'file_path',
        'exam_type',
        'exam_year',
        'semester',
        'student_type',
        'level',
        'description',
        'is_visible',
        'uploaded_by',
        'current_version_id',
    ];

    protected $casts = [
        'exam_year' => 'integer',
        'level' => 'integer',
        'is_visible' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PaperVersion::class);
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(PaperVersion::class, 'current_version_id');
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function viewLogs(): HasMany
    {
        return $this->hasMany(ViewLog::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    // Scopes
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByExamType($query, $examType)
    {
        return $query->where('exam_type', $examType);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('exam_year', $year);
    }

    public function scopeByStudentType($query, $studentType)
    {
        return $query->where('student_type', $studentType);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}