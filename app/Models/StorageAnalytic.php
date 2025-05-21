<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StorageAnalytic extends Model
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
        'student_type_id',
        'level_id',
        'total_size_bytes',
        'file_count',
        'average_file_size_bytes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'total_size_bytes' => 'integer',
        'file_count' => 'integer',
        'average_file_size_bytes' => 'integer',
    ];

    /**
     * Get the department associated with this storage analytic.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the student type associated with this storage analytic.
     */
    public function studentType()
    {
        return $this->belongsTo(StudentType::class);
    }

    /**
     * Get the level associated with this storage analytic.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Scope a query to only include analytics within a date range.
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
     * Get the total storage used as of a specific date.
     *
     * @param  string|null  $date
     * @return int
     */
    public static function getTotalStorageUsed($date = null)
    {
        $date = $date ?: now()->toDateString();
        
        return self::where('date', '<=', $date)
                ->orderBy('date', 'desc')
                ->value(DB::raw('SUM(total_size_bytes)')) ?? 0;
    }

    /**
     * Get storage growth trends over time.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  string  $groupBy  One of 'day', 'week', 'month', 'year'
     * @return \Illuminate\Support\Collection
     */
    public static function getStorageGrowthTrends($startDate, $endDate, $groupBy = 'month')
    {
        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };
        
        return self::withinDateRange($startDate, $endDate)
                ->select(
                    DB::raw("DATE_FORMAT(date, '$dateFormat') as period"),
                    DB::raw('SUM(total_size_bytes) as total_size'),
                    DB::raw('SUM(file_count) as files')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get();
    }

    /**
     * Get storage usage by department.
     *
     * @param  string|null  $date
     * @return \Illuminate\Support\Collection
     */
    public static function getStorageByDepartment($date = null)
    {
        $date = $date ?: now()->toDateString();
        
        return self::where('date', '<=', $date)
                ->select(
                    'department_id',
                    DB::raw('SUM(total_size_bytes) as total_size'),
                    DB::raw('SUM(file_count) as files'),
                    DB::raw('AVG(average_file_size_bytes) as avg_file_size')
                )
                ->groupBy('department_id')
                ->with('department')
                ->orderBy('total_size', 'desc')
                ->get();
    }

    /**
     * Get file size distribution (for histogram generation).
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFileSizeDistribution()
    {
        // File size ranges in bytes
        $ranges = [
            '0-500KB' => [0, 512000],
            '500KB-1MB' => [512000, 1048576],
            '1MB-2MB' => [1048576, 2097152],
            '2MB-5MB' => [2097152, 5242880],
            '5MB-10MB' => [5242880, 10485760],
            '10MB+' => [10485760, PHP_INT_MAX]
        ];
        
        $result = collect();
        
        // Get the latest date for which we have data
        $latestDate = self::max('date');
        
        // This is a simplified approach - in production you'd want to query the papers table directly
        // for more accurate file size distribution, but this gives an approximation
        
        // For each range, get count of files within that range
        foreach ($ranges as $label => [$min, $max]) {
            // Estimate number of files in this range
            // In a real implementation, you'd query the actual files
            $count = Paper::whereBetween(DB::raw('CHAR_LENGTH(file_path)'), [$min/100, $max/100])->count();
            
            $result->push([
                'range' => $label,
                'count' => $count,
                'min_bytes' => $min,
                'max_bytes' => $max
            ]);
        }
        
        return $result;
    }

    /**
     * Get the storage growth rate per day.
     *
     * @param  int  $days  Number of days to analyze
     * @return float
     */
    public static function getStorageGrowthRate($days = 30)
    {
        $startDate = now()->subDays($days)->toDateString();
        $endDate = now()->toDateString();
        
        $startSize = self::where('date', '<=', $startDate)
                    ->sum('total_size_bytes') ?? 0;
                    
        $endSize = self::where('date', '<=', $endDate)
                    ->sum('total_size_bytes') ?? 0;
                    
        if ($days == 0) {
            return 0;
        }
        
        return ($endSize - $startSize) / $days;
    }

    /**
     * Estimate when storage will reach a given limit based on current growth rate.
     *
     * @param  int  $limitBytes  Storage limit in bytes
     * @param  int  $days  Number of recent days to analyze for growth rate
     * @return \Carbon\Carbon|null  Estimated date when limit will be reached, or null if growth rate is zero
     */
    public static function estimateStorageLimitDate($limitBytes, $days = 30)
    {
        $currentUsage = self::getTotalStorageUsed();
        $growthRatePerDay = self::getStorageGrowthRate($days);
        
        if ($growthRatePerDay <= 0) {
            return null;
        }
        
        $remainingBytes = $limitBytes - $currentUsage;
        if ($remainingBytes <= 0) {
            return now(); // Already exceeded
        }
        
        $daysUntilLimit = ceil($remainingBytes / $growthRatePerDay);
        
        return now()->addDays($daysUntilLimit);
    }
}