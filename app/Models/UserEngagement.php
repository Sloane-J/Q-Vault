<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserEngagement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'login_at',
        'logout_at',
        'last_activity_at',
        'ip_address',
        'user_agent',
        'pages_visited',
        'actions_performed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'pages_visited' => 'array',
        'actions_performed' => 'array',
    ];

    /**
     * Get the user that owns the engagement record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session duration in seconds.
     *
     * @return int|null
     */
    public function getSessionDurationAttribute()
    {
        if ($this->logout_at && $this->login_at) {
            return $this->logout_at->diffInSeconds($this->login_at);
        }
        
        if ($this->last_activity_at && $this->login_at) {
            return $this->last_activity_at->diffInSeconds($this->login_at);
        }
        
        return null;
    }

    /**
     * Scope a query to only include engagements from a specific period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $period  daily, weekly, monthly
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPeriod($query, $period = 'daily')
    {
        $today = Carbon::today();
        
        return match($period) {
            'daily' => $query->whereDate('login_at', $today),
            'weekly' => $query->whereBetween('login_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]),
            'monthly' => $query->whereBetween('login_at', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()]),
            'yearly' => $query->whereBetween('login_at', [$today->copy()->startOfYear(), $today->copy()->endOfYear()]),
            default => $query,
        };
    }

    /**
     * Get active users count for a specific period.
     *
     * @param  string  $period  daily, weekly, monthly
     * @return int
     */
    public static function getActiveUsersCount($period = 'daily')
    {
        return static::forPeriod($period)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get average session duration for a specific period.
     *
     * @param  string  $period  daily, weekly, monthly
     * @return float|null
     */
    public static function getAverageSessionDuration($period = 'daily')
    {
        $sessions = static::forPeriod($period)
            ->whereNotNull('logout_at')
            ->get();
            
        if ($sessions->isEmpty()) {
            return null;
        }
        
        return $sessions->avg(function ($session) {
            return $session->session_duration;
        });
    }

    /**
     * Calculate return visit frequency (average days between visits).
     *
     * @param  int  $userId
     * @param  int  $days  Number of days to analyze
     * @return float|null
     */
    public static function getReturnVisitFrequency($userId, $days = 30)
    {
        $visits = static::where('user_id', $userId)
            ->whereDate('login_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('login_at')
            ->pluck('login_at')
            ->toArray();
            
        $count = count($visits);
        
        if ($count <= 1) {
            return null;
        }
        
        $intervals = [];
        for ($i = 1; $i < $count; $i++) {
            $intervals[] = Carbon::parse($visits[$i])->diffInHours(Carbon::parse($visits[$i-1])) / 24;
        }
        
        return array_sum($intervals) / count($intervals);
    }

    /**
     * Get most active users for a specific period.
     *
     * @param  string  $period  daily, weekly, monthly
     * @param  int  $limit
     * @return \Illuminate\Support\Collection
     */
    public static function getMostActiveUsers($period = 'monthly', $limit = 10)
    {
        return static::forPeriod($period)
            ->select('user_id', \DB::raw('COUNT(*) as login_count'))
            ->groupBy('user_id')
            ->orderByDesc('login_count')
            ->limit($limit)
            ->with('user:id,name,email')
            ->get();
    }

    /**
     * Track a new page visit.
     *
     * @param  string  $page
     * @return bool
     */
    public function trackPageVisit($page)
    {
        $pages = $this->pages_visited ?? [];
        $pages[] = [
            'page' => $page,
            'timestamp' => Carbon::now()->toDateTimeString()
        ];
        
        $this->pages_visited = $pages;
        $this->last_activity_at = Carbon::now();
        
        return $this->save();
    }

    /**
     * Track a user action.
     *
     * @param  string  $action
     * @param  array  $metadata
     * @return bool
     */
    public function trackAction($action, array $metadata = [])
    {
        $actions = $this->actions_performed ?? [];
        $actions[] = [
            'action' => $action,
            'metadata' => $metadata,
            'timestamp' => Carbon::now()->toDateTimeString()
        ];
        
        $this->actions_performed = $actions;
        $this->last_activity_at = Carbon::now();
        
        return $this->save();
    }
}