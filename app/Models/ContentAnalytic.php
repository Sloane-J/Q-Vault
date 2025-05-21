<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContentAnalytic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'paper_id',
        'department_id',
        'student_type_id',
        'level_id',
        'course_name',
        'exam_type',
        'exam_year',
        'view_count',
        'download_count',
        'search_impression_count',
        'unique_viewers',
        'unique_downloaders',
        'trending_score',
        'last_calculated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Get the paper associated with this analytic.
     */
    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    /**
     * Get the department associated with this analytic.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the student type associated with this analytic.
     */
    public function studentType()
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the level associated with this analytic.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Scope a query to only include trending papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrending($query, $limit = 10)
    {
        return $query->orderBy('trending_score', 'desc')
                    ->take($limit);
    }

    /**
     * Scope a query to only include most downloaded papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostDownloaded($query, $limit = 10)
    {
        return $query->orderBy('download_count', 'desc')
                    ->take($limit);
    }

    /**
     * Scope a query to only include most viewed papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostViewed($query, $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')
                    ->take($limit);
    }

    /**
     * Scope a query to only include least accessed papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeastAccessed($query, $limit = 10)
    {
        return $query->orderBy(DB::raw('view_count + download_count'), 'asc')
                    ->take($limit);
    }

    /**
     * Scope a query to only include analytics for a specific department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include analytics for a specific student type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $studentTypeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStudentType($query, $studentTypeId)
    {
        return $query->where('student_type_id', $studentTypeId);
    }

    /**
     * Scope a query to only include analytics for a specific level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $levelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Scope a query to only include analytics for a specific exam type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $examType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByExamType($query, $examType)
    {
        return $query->where('exam_type', $examType);
    }

    /**
     * Scope a query to only include analytics for a specific exam year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $examYear
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByExamYear($query, $examYear)
    {
        return $query->where('exam_year', $examYear);
    }

    /**
     * Scope a query to only include analytics for a specific course.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $courseName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCourse($query, $courseName)
    {
        return $query->where('course_name', $courseName);
    }

    /**
     * Calculate and update the trending score for this paper.
     * 
     * @return void
     */
    public function calculateTrendingScore()
    {
        // Simple trending algorithm: 
        // (downloads in last 7 days * 2) + views in last 7 days
        $recentDownloads = Download::where('paper_id', $this->paper_id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
            
        $recentViews = PaperView::where('paper_id', $this->paper_id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
            
        $this->trending_score = ($recentDownloads * 2) + $recentViews;
        $this->last_calculated_at = now();
        $this->save();
    }
}