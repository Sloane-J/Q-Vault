<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserActivity extends Model
{
    use HasFactory;

    /**
     * Activity type constants
     */
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_VIEW_PAPER = 'view_paper';
    const TYPE_DOWNLOAD_PAPER = 'download_paper';
    const TYPE_SEARCH = 'search';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_REGISTRATION = 'registration';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'details',
        'ip_address',
        'user_agent',
        'session_id',
        'paper_id',
        'department_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user associated with this activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the paper associated with this activity (if applicable).
     */
    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    /**
     * Get the department associated with this activity (if applicable).
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope a query to only include activities of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope a query to only include activities from a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include activities within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include activities related to a specific paper.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $paperId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPaper($query, $paperId)
    {
        return $query->where('paper_id', $paperId);
    }

    /**
     * Scope a query to only include activities related to a specific department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include search activities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearches($query)
    {
        return $query->where('activity_type', self::TYPE_SEARCH);
    }

    /**
     * Scope a query to only include paper view activities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaperViews($query)
    {
        return $query->where('activity_type', self::TYPE_VIEW_PAPER);
    }

    /**
     * Scope a query to only include paper download activities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaperDownloads($query)
    {
        return $query->where('activity_type', self::TYPE_DOWNLOAD_PAPER);
    }

    /**
     * Log a user login activity.
     *
     * @param  int  $userId
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logLogin($userId, $metadata = [])
    {
        return self::createActivity(
            $userId,
            self::TYPE_LOGIN,
            'User logged in',
            null,
            null,
            $metadata
        );
    }

    /**
     * Log a user logout activity.
     *
     * @param  int  $userId
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logLogout($userId, $metadata = [])
    {
        return self::createActivity(
            $userId,
            self::TYPE_LOGOUT,
            'User logged out',
            null,
            null,
            $metadata
        );
    }

    /**
     * Log a paper view activity.
     *
     * @param  int  $userId
     * @param  int  $paperId
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logPaperView($userId, $paperId, $metadata = [])
    {
        // Get department ID from paper if available
        $paper = Paper::find($paperId);
        $departmentId = $paper ? $paper->department_id : null;
        
        return self::createActivity(
            $userId,
            self::TYPE_VIEW_PAPER,
            'User viewed paper',
            $paperId,
            $departmentId,
            $metadata
        );
    }

    /**
     * Log a paper download activity.
     *
     * @param  int  $userId
     * @param  int  $paperId
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logPaperDownload($userId, $paperId, $metadata = [])
    {
        // Get department ID from paper if available
        $paper = Paper::find($paperId);
        $departmentId = $paper ? $paper->department_id : null;
        
        return self::createActivity(
            $userId,
            self::TYPE_DOWNLOAD_PAPER,
            'User downloaded paper',
            $paperId,
            $departmentId,
            $metadata
        );
    }

    /**
     * Log a search activity.
     *
     * @param  int  $userId
     * @param  string  $query
     * @param  array  $filters
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logSearch($userId, $query, $filters = [], $metadata = [])
    {
        $details = "User searched for: $query";
        
        $metadata = array_merge([
            'query' => $query,
            'filters' => $filters
        ], $metadata);
        
        $departmentId = $filters['department_id'] ?? null;
        
        return self::createActivity(
            $userId,
            self::TYPE_SEARCH,
            $details,
            null,
            $departmentId,
            $metadata
        );
    }

    /**
     * Log a profile update activity.
     *
     * @param  int  $userId
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logProfileUpdate($userId, $metadata = [])
    {
        return self::createActivity(
            $userId,
            self::TYPE_PROFILE_UPDATE,
            'User updated their profile',
            null,
            null,
            $metadata
        );
    }

    /**
     * Log a registration activity.
     *
     * @param  int  $userId
     * @param  array  $metadata
     * @return UserActivity
     */
    public static function logRegistration($userId, $metadata = [])
    {
        return self::createActivity(
            $userId,
            self::TYPE_REGISTRATION,
            'User registered',
            null,
            null,
            $metadata
        );
    }

    /**
     * Create a new user activity.
     *
     * @param  int  $userId
     * @param  string  $activityType
     * @param  string  $details
     * @param  int|null  $paperId
     * @param  int|null  $departmentId
     * @param  array  $metadata
     * @return UserActivity
     */
    protected static function createActivity(
        $userId,
        $activityType,
        $details,
        $paperId = null,
        $departmentId = null,
        $metadata = []
    ) {
        // Get the IP address and user agent
        $request = request();
        $ipAddress = $request ? $request->ip() : null;
        $userAgent = $request ? $request->userAgent() : null;
        $sessionId = $request ? $request->session()->getId() : null;

        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'details' => $details,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'session_id' => $sessionId,
            'paper_id' => $paperId,
            'department_id' => $departmentId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get active users per day/week/month.
     *
     * @param  string  $period  'day', 'week', or 'month'
     * @param  int  $limit  Number of periods to return
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveUsersByPeriod($period = 'day', $limit = 30)
    {
        $dateFormat = match($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };
        
        return self::select(
                DB::raw("DATE_FORMAT(created_at, '$dateFormat') as period"),
                DB::raw('COUNT(DISTINCT user_id) as active_users')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get average session duration.
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return float  Average session duration in minutes
     */
    public static function getAverageSessionDuration($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate && $endDate) {
            $query->withinDateRange($startDate, $endDate);
        }
        
        // Group activities by user and session
        $sessions = $query->select(
                'user_id',
                'session_id',
                DB::raw('MIN(created_at) as session_start'),
                DB::raw('MAX(created_at) as session_end')
            )
            ->whereNotNull('session_id')
            ->groupBy('user_id', 'session_id')
            ->get();
            
        if ($sessions->isEmpty()) {
            return 0;
        }
            
        // Calculate durations
        $totalMinutes = 0;
        $sessionCount = 0;
        
        foreach ($sessions as $session) {
            $start = strtotime($session->session_start);
            $end = strtotime($session->session_end);
            $durationMinutes = ($end - $start) / 60;
            
            // Only count sessions with reasonable duration (more than a minute, less than a day)
            if ($durationMinutes > 1 && $durationMinutes < 24 * 60) {
                $totalMinutes += $durationMinutes;
                $sessionCount++;
            }
        }
        
        return $sessionCount > 0 ? $totalMinutes / $sessionCount : 0;
    }

    /**
     * Get return visit frequency (average days between visits).
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @return float  Average days between visits
     */
    public static function getReturnVisitFrequency($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate && $endDate) {
            $query->withinDateRange($startDate, $endDate);
        }
        
        // Get login dates for each user
        $userLogins = $query->select(
                'user_id',
                DB::raw('DATE(created_at) as login_date')
            )
            ->where('activity_type', self::TYPE_LOGIN)
            ->orderBy('user_id')
            ->orderBy('login_date')
            ->get()
            ->groupBy('user_id');
            
        // Calculate average days between visits for each user
        $totalDaysBetweenVisits = 0;
        $totalIntervals = 0;
        
        foreach ($userLogins as $userId => $logins) {
            $loginDates = $logins->pluck('login_date')->toArray();
            $uniqueDates = array_values(array_unique($loginDates));
            
            if (count($uniqueDates) > 1) {
                $daysDifferences = [];
                
                for ($i = 1; $i < count($uniqueDates); $i++) {
                    $daysDiff = (strtotime($uniqueDates[$i]) - strtotime($uniqueDates[$i-1])) / (60 * 60 * 24);
                    
                    // Only count reasonable intervals (1-90 days)
                    if ($daysDiff >= 1 && $daysDiff <= 90) {
                        $daysDifferences[] = $daysDiff;
                    }
                }
                
                if (!empty($daysDifferences)) {
                    $totalDaysBetweenVisits += array_sum($daysDifferences);
                    $totalIntervals += count($daysDifferences);
                }
            }
        }
        
        return $totalIntervals > 0 ? $totalDaysBetweenVisits / $totalIntervals : 0;
    }
}