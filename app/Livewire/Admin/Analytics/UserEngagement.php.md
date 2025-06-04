<?php
/*
namespace App\Livewire\Admin\Analytics;

use Livewire\Component;
use App\Services\UserEngagementService;
use Carbon\Carbon;

class UserEngagementStats extends Component
{
    public $period = 'monthly';
    public $periods = ['daily', 'weekly', 'monthly', 'yearly'];
    public $stats = [];
    public $dailyActiveUsersChart = [];
    public $mostVisitedPages = [];
    public $mostCommonActions = [];
    public $chartDays = 30;

    protected $queryString = ['period'];

    /**
     * Mount the component.
     * 
     * @param UserEngagementService $engagementService
     * @return void
     */
    public function mount(UserEngagementService $engagementService)
    {
        $this->loadStats($engagementService);
    }

    /**
     * Update the period and refresh the stats.
     * 
     * @param string $newPeriod
     * @return void
     */
    public function updatePeriod($newPeriod)
    {
        $this->period = $newPeriod;
        $this->loadStats(app(UserEngagementService::class));
    }

    /**
     * Update the chart days and refresh the chart data.
     * 
     * @param int $days
     * @return void
     */
    public function updateChartDays($days)
    {
        $this->chartDays = $days;
        $this->dailyActiveUsersChart = app(UserEngagementService::class)->getDailyActiveUsersChart($days);
    }

    /**
     * Load all stats data.
     * 
     * @param UserEngagementService $engagementService
     * @return void
     */
    protected function loadStats(UserEngagementService $engagementService)
    {
        $this->stats = $engagementService->getAnalytics($this->period);
        $this->dailyActiveUsersChart = $engagementService->getDailyActiveUsersChart($this->chartDays);
        $this->mostVisitedPages = $engagementService->getMostVisitedPages($this->period);
        $this->mostCommonActions = $engagementService->getMostCommonActions($this->period);
    }

    /**
     * Format seconds to human readable time.
     * 
     * @param int|null $seconds
     * @return string
     */
    public function formatDuration($seconds)
    {
        if ($seconds === null) {
            return 'N/A';
        }
        
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }
        
        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return $minutes . ' min ' . $remainingSeconds . ' sec';
        }
        
        $hours = floor($seconds / 3600);
        $remainingSeconds = $seconds % 3600;
        $minutes = floor($remainingSeconds / 60);
        $remainingSeconds = $remainingSeconds % 60;
        
        return $hours . ' hr ' . $minutes . ' min ' . $remainingSeconds . ' sec';
    }

    /**
     * Format date for display.
     * 
     * @param string $date
     * @return string
     */
    public function formatDate($date)
    {
        return Carbon::parse($date)->format('M d, Y');
    }

    /**
     * Render the component.
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.analytics.user-engagement');
    }
}