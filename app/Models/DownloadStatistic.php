<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DownloadStatistic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'department_id',
        'paper_id',
        'student_type_id',
        'level_id',
        'exam_type',
        'exam_year',
        'total_downloads',
        'unique_users',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the paper associated with this download statistic.
     */
    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    /**
     * Get the department associated with this download statistic.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the student type associated with this download statistic.
     */
    public function studentType()
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the level associated with this download statistic.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Scope a query to only include statistics within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include statistics for a specific department.
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
     * Scope a query to only include statistics for a specific paper.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $paperId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPaper($query, $paperId)
    {
        return $query->where('paper_id', $paperId);
    }

    /**
     * Scope a query to only include statistics for a specific student type.
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
     * Scope a query to only include statistics for a specific level.
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
     * Scope a query to only include statistics for a specific exam type.
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
     * Scope a query to only include statistics for a specific exam year.
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
     * Get total downloads for a specific period.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return int
     */
    public static function getTotalDownloadsForPeriod($startDate, $endDate)
    {
        return self::withinDateRange($startDate, $endDate)
                ->sum('total_downloads');
    }

    /**
     * Get downloads by department for a specific period.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getDownloadsByDepartment($startDate, $endDate)
    {
        return self::withinDateRange($startDate, $endDate)
                ->select('department_id', DB::raw('SUM(total_downloads) as downloads'))
                ->groupBy('department_id')
                ->with('department')
                ->orderBy('downloads', 'desc')
                ->get();
    }

    /**
     * Get downloads by student type for a specific period.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getDownloadsByStudentType($startDate, $endDate)
    {
        return self::withinDateRange($startDate, $endDate)
                ->select('student_type_id', DB::raw('SUM(total_downloads) as downloads'))
                ->groupBy('student_type_id')
                ->with('studentType')
                ->orderBy('downloads', 'desc')
                ->get();
    }

    /**
     * Get downloads by level for a specific period.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getDownloadsByLevel($startDate, $endDate)
    {
        return self::withinDateRange($startDate, $endDate)
                ->select('level_id', DB::raw('SUM(total_downloads) as downloads'))
                ->groupBy('level_id')
                ->with('level')
                ->orderBy('downloads', 'desc')
                ->get();
    }

    /**
     * Get downloads by exam type for a specific period.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getDownloadsByExamType($startDate, $endDate)
    {
        return self::withinDateRange($startDate, $endDate)
                ->select('exam_type', DB::raw('SUM(total_downloads) as downloads'))
                ->groupBy('exam_type')
                ->orderBy('downloads', 'desc')
                ->get();
    }

    /**
     * Get downloads by exam year for a specific period.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getDownloadsByExamYear($startDate, $endDate)
    {
        return self::withinDateRange($startDate, $endDate)
                ->select('exam_year', DB::raw('SUM(total_downloads) as downloads'))
                ->groupBy('exam_year')
                ->orderBy('exam_year', 'desc')
                ->get();
    }

    /**
     * Get year-over-year comparison data.
     *
     * @param  int  $currentYear
     * @param  int  $previousYear
     * @return array
     */
    public static function getYearOverYearComparison($currentYear, $previousYear)
    {
        $currentYearData = self::where('date', '>=', "$currentYear-01-01")
                            ->where('date', '<=', "$currentYear-12-31")
                            ->select(
                                DB::raw('MONTH(date) as month'),
                                DB::raw('SUM(total_downloads) as downloads')
                            )
                            ->groupBy(DB::raw('MONTH(date)'))
                            ->orderBy(DB::raw('MONTH(date)'))
                            ->get()
                            ->pluck('downloads', 'month')
                            ->toArray();

        $previousYearData = self::where('date', '>=', "$previousYear-01-01")
                            ->where('date', '<=', "$previousYear-12-31")
                            ->select(
                                DB::raw('MONTH(date) as month'),
                                DB::raw('SUM(total_downloads) as downloads')
                            )
                            ->groupBy(DB::raw('MONTH(date)'))
                            ->orderBy(DB::raw('MONTH(date)'))
                            ->get()
                            ->pluck('downloads', 'month')
                            ->toArray();

        return [
            'current_year' => $currentYear,
            'previous_year' => $previousYear,
            'current_year_data' => $currentYearData,
            'previous_year_data' => $previousYearData,
        ];
    }
}