<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Paper extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['department_id', 'course_id', 'level_id', 'student_type_id', 'user_id', 'title', 'file_path', 'exam_type', 'exam_year', 'semester', 'description', 'is_visible', 'current_version_id', 'uploaded_by'];

    protected $casts = [
        'exam_year' => 'integer',
        'is_visible' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'department_id' => 'integer',
        'course_id' => 'integer',
        'level_id' => 'integer', // Add this
        'student_type_id' => 'integer', // Add this if not already there
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'department.name', 'semester', 'exam_type', 'course.name', 'exam_year'])
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['view_count', 'download_count', 'updated_at'])
            ->setDescriptionForEvent(
                fn(string $eventName) => match ($eventName) {
                    'created' => 'Paper uploaded',
                    'updated' => 'Paper updated',
                    'deleted' => 'Paper deleted',
                    default => "Paper {$eventName}",
                },
            );
    }

    // Accessor for formatted semester
    /**
     * Get the semester as a human-readable string.
     *
     * @return string
     */
    public function getFormattedSemesterAttribute(): string
    {
        // Corrected from Attributes to Attribute
        return match ($this->semester) {
            '1' => 'First Semester',
            '2' => 'Second Semester',
            default => 'N/A', // Fallback for unexpected values or null
        };
    }

    // Custom logging methods for specific events
    public function logDownload($user = null)
    {
        $user = $user ?? auth()->user();

        activity()
            ->performedOn($this)
            ->causedBy($user)
            ->withProperties([
                'paper_title' => $this->title,
                'department' => $this->department->name ?? 'Unknown',
                'course' => $this->course->name ?? 'Unknown',
                'exam_type' => $this->exam_type,
                'exam_year' => $this->exam_year,
                'semester' => $this->semester, // Log the raw semester value
                'formatted_semester' => $this->formatted_semester, // Log the formatted value too for easier reading in logs
                'user_name' => $user->name ?? 'Guest',
                'user_email' => $user->email ?? 'N/A',
                'download_time' => now()->toDateTimeString(),
            ])
            ->log('Paper downloaded');
    }

    public function logFileReplaced($oldFilePath, $newFilePath, $user = null)
    {
        $user = $user ?? auth()->user();

        activity()
            ->performedOn($this)
            ->causedBy($user)
            ->withProperties([
                'paper_title' => $this->title,
                'old_file_path' => $oldFilePath,
                'new_file_path' => $newFilePath,
                'department' => $this->department->name ?? 'Unknown',
                'course' => $this->course->name ?? 'Unknown',
                'replaced_by' => $user->name ?? 'System',
                'replacement_time' => now()->toDateTimeString(),
            ])
            ->log('File replaced/updated');
    }

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function studentType(): BelongsTo
    {
        return $this->belongsTo(StudentType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Alias for backward compatibility
    public function uploader(): BelongsTo
    {
        return $this->user();
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

    public function scopeByStudentType($query, $studentTypeId)
    {
        return $query->where('student_type_id', $studentTypeId);
    }

    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }
}
