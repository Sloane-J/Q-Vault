<?php

namespace App\Livewire\Admin\Analytics;

use App\Models\Department;
use App\Models\Download;
use App\Models\Paper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Url;

class Dashboard extends Component
{
    #[Url]
    public $timeRange = 'month';
    
    #[Url]
    public $departmentId = null;
    
    #[Url] 
    public $studentType = null;
    
    #[Url]
    public $level = null;

    public $readyToLoad = false;

    public function mount()
    {
        // Initialize component
    }

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.admin.analytics.dashboard', [
            'metrics' => $this->readyToLoad ? $this->getMetrics() : [],
            'departments' => Department::orderBy('name')->get(),
            'studentTypes' => ['HND', 'B-Tech', 'Top-up'],
            'levels' => [100, 200, 300, 400],
            'isLoading' => !$this->readyToLoad,
        ]);
    }

    protected function getMetrics()
    {
        $startDate = $this->getStartDate();
        $previousStartDate = $this->getPreviousPeriodStartDate();

        return [
            // Core metrics
            'summary' => $this->getSummaryMetrics($startDate, $previousStartDate),
            
            // Download analytics
            'downloadTrends' => $this->getDownloadTrends($startDate),
            'downloadsByDepartment' => $this->getDownloadsByDepartment($startDate),
            'downloadsByStudentType' => $this->getDownloadsByStudentType($startDate),
            'downloadsByLevel' => $this->getDownloadsByLevel($startDate),
            
            // User engagement
            'activeUsers' => $this->getActiveUsers($startDate),
            'userRetention' => $this->getUserRetention($startDate),
            
            // Year-over-year comparison
            'yearOverYear' => $this->getYearOverYearComparison(),
        ];
    }

    protected function getSummaryMetrics($startDate, $previousStartDate)
    {
        return [
            'total_downloads' => [
                'current' => $this->getDownloadCount($startDate),
                'previous' => $this->getDownloadCount($previousStartDate, $startDate),
            ],
            'total_papers' => [
                'current' => $this->getPaperCount($startDate),
                'previous' => $this->getPaperCount($previousStartDate, $startDate),
            ],
            'active_users' => [
                'current' => $this->getActiveUserCount($startDate),
                'previous' => $this->getActiveUserCount($previousStartDate, $startDate),
            ],
        ];
    }

    protected function getDownloadTrends($startDate)
    {
        return Download::query()
            ->selectRaw('DATE(downloaded_at) as date, COUNT(*) as count')
            ->when($startDate, fn($q) => $q->where('downloaded_at', '>=', $startDate))
            ->applyFilters($this->getFilters())
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getDownloadsByDepartment($startDate)
    {
        return Department::query()
            ->withCount([
                'downloads as current_period' => fn($q) => $q->where('downloaded_at', '>=', $startDate)
                    ->applyFilters($this->getFilters(['department'])),
                'downloads as previous_period' => fn($q) => $q->whereBetween('downloaded_at', 
                    [$this->getPreviousPeriodStartDate(), $startDate])
                    ->applyFilters($this->getFilters(['department']))
            ])
            ->orderByDesc('current_period')
            ->limit(5)
            ->get();
    }

    protected function getActiveUsers($startDate)
    {
        return User::query()
            ->whereHas('downloads', fn($q) => $q->where('downloaded_at', '>=', $startDate))
            ->withCount(['downloads' => fn($q) => $q->where('downloaded_at', '>=', $startDate)])
            ->orderByDesc('downloads_count')
            ->limit(10)
            ->get();
    }

    protected function getStartDate()
    {
        return match($this->timeRange) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subQuarter(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::create(1970), // All time
        };
    }

    protected function getPreviousPeriodStartDate()
    {
        return match($this->timeRange) {
            'week' => Carbon::now()->subWeeks(2),
            'month' => Carbon::now()->subMonths(2),
            'quarter' => Carbon::now()->subQuarters(2),
            'year' => Carbon::now()->subYears(2),
            default => Carbon::create(1970),
        };
    }

    protected function getFilters($exclude = [])
    {
        return array_filter([
            'department_id' => in_array('department', $exclude) ? null : $this->departmentId,
            'student_type' => in_array('studentType', $exclude) ? null : $this->studentType,
            'level' => in_array('level', $exclude) ? null : $this->level,
        ]);
    }

    public function updatedTimeRange()
    {
        // Reset filters when time range changes
        $this->reset(['departmentId', 'studentType', 'level']);
    }

    public function exportData($format)
    {
        // Implement export logic
    }
}