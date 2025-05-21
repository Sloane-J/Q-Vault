<?php

namespace App\Services;

use App\Models\UserEngagement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class UserEngagementService
{
    /**
     * Start a new user session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\UserEngagement|null
     */
    public function startSession(Request $request)
    {
        if (!Auth::check()) {
            return null;
        }

        $engagement = UserEngagement::create([
            'user_id' => Auth::id(),
            'session_id' => Session::getId(),
            'login_at' => Carbon::now(),
            'last_activity_at' => Carbon::now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'pages_visited' => [
                [
                    'page' => $request->path(),
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]
            ],
            'actions_performed' => []
        ]);

        Session::put('engagement_id', $engagement->id);

        return $engagement;
    }

    /**
     * End the current user session.
     *
     * @return bool
     */
    public function endSession()
    {
        if (!Auth::check() || !Session::has('engagement_id')) {
            return false;
        }

        $engagement = UserEngagement::find(Session::get('engagement_id'));
        
        if (!$engagement) {
            return false;
        }

        $engagement->logout_at = Carbon::now();
        $result = $engagement->save();

        Session::forget('engagement_id');

        return $result;
    }

    /**
     * Track a page visit in the current session.
     *
     * @param  string  $page
     * @return bool
     */
    public function trackPageVisit($page)
    {
        if (!Auth::check() || !Session::has('engagement_id')) {
            return false;
        }

        $engagement = UserEngagement::find(Session::get('engagement_id'));
        
        if (!$engagement) {
            return false;
        }

        return $engagement->trackPageVisit($page);
    }

    /**
     * Track a user action in the current session.
     *
     * @param  string  $action
     * @param  array  $metadata
     * @return bool
     */
    public function trackAction($action, array $metadata = [])
    {
        if (!Auth::check() || !Session::has('engagement_id')) {
            return false;
        }

        $engagement = UserEngagement::find(Session::get('engagement_id'));
        
        if (!$engagement) {
            return false;
        }

        return $engagement->trackAction($action, $metadata);
    }

    /**
     * Update the last activity timestamp.
     *
     * @return bool
     */
    public function updateActivity()
    {
        if (!Auth::check() || !Session::has('engagement_id')) {
            return false;
        }

        $engagement = UserEngagement::find(Session::get('engagement_id'));
        
        if (!$engagement) {
            return false;
        }

        $engagement->last_activity_at = Carbon::now();
        return $engagement->save();
    }

    /**
     * Get analytics for user engagement over a period.
     *
     * @param  string  $period  daily, weekly, monthly, yearly
     * @return array
     */
    public function getAnalytics($period = 'monthly')
    {
        return [
            'active_users' => UserEngagement::getActiveUsersCount($period),
            'average_session_duration' => UserEngagement::getAverageSessionDuration($period),
            'most_active_users' => UserEngagement::getMostActiveUsers($period),
            'total_sessions' => UserEngagement::forPeriod($period)->count(),
        ];
    }

    /**
     * Get daily active users for the past N days.
     *
     * @param  int  $days
     * @return array
     */
    public function getDailyActiveUsersChart($days = 30)
    {
        $result = [];
        $startDate = Carbon::today()->subDays($days - 1);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $count = UserEngagement::whereDate('login_at', $date)
                ->distinct('user_id')
                ->count('user_id');
                
            $result[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count
            ];
        }

        return $result;
    }

    /**
     * Get most visited pages.
     *
     * @param  string  $period  daily, weekly, monthly, yearly
     * @param  int  $limit
     * @return array
     */
    public function getMostVisitedPages($period = 'monthly', $limit = 10)
    {
        $engagements = UserEngagement::forPeriod($period)->get();
        
        $pageVisits = [];
        
        foreach ($engagements as $engagement) {
            if (empty($engagement->pages_visited)) {
                continue;
            }
            
            foreach ($engagement->pages_visited as $visit) {
                $page = $visit['page'] ?? null;
                
                if ($page) {
                    if (!isset($pageVisits[$page])) {
                        $pageVisits[$page] = 0;
                    }
                    
                    $pageVisits[$page]++;
                }
            }
        }
        
        arsort($pageVisits);
        
        return array_slice($pageVisits, 0, $limit, true);
    }

    /**
     * Get most common user actions.
     *
     * @param  string  $period  daily, weekly, monthly, yearly
     * @param  int  $limit
     * @return array
     */
    public function getMostCommonActions($period = 'monthly', $limit = 10)
    {
        $engagements = UserEngagement::forPeriod($period)->get();
        
        $actions = [];
        
        foreach ($engagements as $engagement) {
            if (empty($engagement->actions_performed)) {
                continue;
            }
            
            foreach ($engagement->actions_performed as $action) {
                $actionName = $action['action'] ?? null;
                
                if ($actionName) {
                    if (!isset($actions[$actionName])) {
                        $actions[$actionName] = 0;
                    }
                    
                    $actions[$actionName]++;
                }
            }
        }
        
        arsort($actions);
        
        return array_slice($actions, 0, $limit, true);
    }
}