<?php

namespace App\Livewire\Admin\Analytics;

use App\Models\Department;
use App\Models\Paper;
use App\Models\Download;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContentAnalytics extends Component
{
    use WithPagination;

    public $timeRange = 'month';
    public $departmentFilter = null;
    public $examTypeFilter = null;
    public $limit = 10;
    public $activeTab = 'popularity';

    protected $queryString = [
        'timeRange' => ['except' => 'month'],
        'departmentFilter' => ['except' => null],
        'examTypeFilter' => ['except' => null],
        'limit' => ['except' => 10],
        'activeTab' => ['except' => 'popularity']
    ];

    public function render()
    {
        return view('livewire.admin.analytics.content-analytics', [
            'analyticsData' => $this->getAnalyticsData(),
            'departments' => Department::orderBy('name')->get(),
            'examTypes' => ['final', 'resit', 'supplementary'] // Assuming these are your exam types
        ]);
    }

    protected function getAnalyticsData()
    {
        $startDate = $this->getStartDate();

        return match ($this->activeTab) {
            'popularity' => $this->getPopularityData($startDate),
            'growth' => $this->getGrowthData($startDate),
            default => $this->getPopularityData($startDate),
        };
    }

    protected function getPopularityData($startDate)
    {
        return [
            'topPapers' => $this->getTopPapers($startDate),
            'popularCourses' => $this->getPopularCourses($startDate),
            'trendingPapers' => $this->getTrendingPapers($startDate),
            'leastAccessed' => $this->getLeastAccessedPapers($startDate),
            'byExamType' => $this->getPopularityByExamType($startDate),
        ];
    }

    protected function getGrowthData($startDate)
    {
        return [
            'papersAdded' => $this->getPapersAddedOverTime(),
            'departmentGrowth' => $this->getDepartmentGrowth(),
            'versionUpdates' => $this->getVersionUpdateFrequency($startDate),
            'freshness' => $this->getContentFreshness(),
        ];
    }

    protected function getTopPapers($startDate)
    {
        return Paper::query()
            ->with(['department:id,name', 'downloads'])
            ->withCount(['downloads' => function($query) use ($startDate) {
                $query->when($startDate, fn($q) => $q->where('downloaded_at', '>=', $startDate));
            }])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->examTypeFilter, fn($q) => $q->where('exam_type', $this->examTypeFilter))
            ->orderByDesc('downloads_count')
            ->limit($this->limit)
            ->get();
    }

    protected function getPopularCourses($startDate)
    {
        return Paper::query()
            ->select('course_name', DB::raw('COUNT(*) as paper_count'), DB::raw('SUM(downloads_count) as total_downloads'))
            ->withCount(['downloads' => function($query) use ($startDate) {
                $query->when($startDate, fn($q) => $q->where('downloaded_at', '>=', $startDate));
            }])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->groupBy('course_name')
            ->orderByDesc('total_downloads')
            ->limit($this->limit)
            ->get();
    }

    protected function getTrendingPapers($startDate)
    {
        $previousPeriodStart = $this->getStartDate($this->timeRange, true);

        return Paper::query()
            ->select('papers.*')
            ->with(['department:id,name'])
            ->join(DB::raw('(SELECT paper_id, COUNT(*) as recent_downloads 
                           FROM downloads 
                           WHERE downloaded_at >= ? 
                           GROUP BY paper_id) as recent'), 
                    function($join) use ($startDate) {
                        $join->on('papers.id', '=', 'recent.paper_id')
                             ->addBinding($startDate, 'select');
                    })
            ->join(DB::raw('(SELECT paper_id, COUNT(*) as previous_downloads 
                           FROM downloads 
                           WHERE downloaded_at >= ? AND downloaded_at < ? 
                           GROUP BY paper_id) as previous'), 
                    function($join) use ($previousPeriodStart, $startDate) {
                        $join->on('papers.id', '=', 'previous.paper_id')
                             ->addBinding([$previousPeriodStart, $startDate], 'select');
                    })
            ->selectRaw('(recent.recent_downloads - previous.previous_downloads) as growth')
            ->orderByDesc('growth')
            ->limit($this->limit)
            ->get();
    }

    protected function getLeastAccessedPapers($startDate)
    {
        return Paper::query()
            ->with(['department:id,name'])
            ->withCount(['downloads' => function($query) use ($startDate) {
                $query->when($startDate, fn($q) => $q->where('downloaded_at', '>=', $startDate));
            }])
            ->having('downloads_count', '>', 0)
            ->orderBy('downloads_count')
            ->limit($this->limit)
            ->get();
    }

    protected function getPopularityByExamType($startDate)
    {
        return Download::query()
            ->select('papers.exam_type', DB::raw('COUNT(*) as download_count'))
            ->join('papers', 'downloads.paper_id', '=', 'papers.id')
            ->when($startDate, fn($q) => $q->where('downloaded_at', '>=', $startDate))
            ->when($this->departmentFilter, fn($q) => $q->where('papers.department_id', $this->departmentFilter))
            ->groupBy('papers.exam_type')
            ->orderByDesc('download_count')
            ->get();
    }

    protected function getPapersAddedOverTime()
    {
        $interval = match ($this->timeRange) {
            'week' => 'DAY',
            'month' => 'WEEK',
            'quarter' => 'MONTH',
            'year' => 'MONTH',
            default => 'MONTH',
        };

        return Paper::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '" . ($interval === 'DAY' ? '%Y-%m-%d' : '%Y-%m') . "') as period"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    protected function getDepartmentGrowth()
    {
        return Department::query()
            ->withCount(['papers as recent_papers' => function($query) {
                $query->where('created_at', '>=', $this->getStartDate());
            }])
            ->withCount('papers as total_papers')
            ->orderByDesc('recent_papers')
            ->get();
    }

    protected function getVersionUpdateFrequency($startDate)
    {
        return Paper::query()
            ->withCount(['versions as recent_versions' => function($query) use ($startDate) {
                $query->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate));
            }])
            ->having('recent_versions', '>', 0)
            ->orderByDesc('recent_versions')
            ->limit($this->limit)
            ->get();
    }

    protected function getContentFreshness()
    {
        return Paper::query()
            ->select('id', 'title', 'exam_year', 'updated_at')
            ->selectRaw('TIMESTAMPDIFF(MONTH, updated_at, NOW()) as months_since_update')
            ->orderByDesc('months_since_update')
            ->limit($this->limit)
            ->get();
    }

    protected function getStartDate($range = null, $previousPeriod = false)
    {
        $range = $range ?: $this->timeRange;
        $multiplier = $previousPeriod ? 2 : 1;

        return match ($range) {
            'week' => Carbon::now()->subWeeks(1 * $multiplier),
            'month' => Carbon::now()->subMonths(1 * $multiplier),
            'quarter' => Carbon::now()->subMonths(3 * $multiplier),
            'year' => Carbon::now()->subYears(1 * $multiplier),
            default => null, // all time
        };
    }

    public function updatedTimeRange()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['departmentFilter', 'examTypeFilter', 'timeRange']);
        $this->resetPage();
    }
}