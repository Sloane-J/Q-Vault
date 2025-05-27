<?php

namespace App\Livewire\Admin\Analytics;

use Livewire\Component;
use App\Models\Download;
use App\Models\Department;
use App\Models\StudentType;
use App\Models\Level;
use App\Models\Paper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DownloadStatistics extends Component
{
    public $startDate;
    public $endDate;
    public $filterPeriod = '30_days';

    public $totalDownloadsPeriod = 0;
    public $totalDownloadsAllTime = 0;

    public $downloadsPerPaper = [];
    public $downloadsByDepartment = [];
    public $downloadsByExamType = [];
    public $downloadsByStudentType = [];
    public $downloadsByLevel = [];
    public $yearlyComparison = [];

    public function mount()
    {
        \Log::debug('Statistics loaded', [
    'downloadsPeriod' => $this->totalDownloadsPeriod,
    'allTime' => $this->totalDownloadsAllTime
]);
        $this->setInitialDateRange();
        $this->loadStatistics();
    }

    public function updatedFilterPeriod()
    {
        $this->setInitialDateRange();
        $this->loadStatistics();
    }

    private function setInitialDateRange()
    {
        switch ($this->filterPeriod) {
            case '7_days':
                $this->endDate = Carbon::now()->format('Y-m-d');
                $this->startDate = Carbon::now()->subDays(7)->format('Y-m-d');
                break;
            case '30_days':
                $this->endDate = Carbon::now()->format('Y-m-d');
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
                break;
            case '90_days':
                $this->endDate = Carbon::now()->format('Y-m-d');
                $this->startDate = Carbon::now()->subDays(90)->format('Y-m-d');
                break;
            case 'all_time':
                $this->startDate = null;
                $this->endDate = Carbon::now()->format('Y-m-d');
                break;
            default:
                $this->endDate = Carbon::now()->format('Y-m-d');
                $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
                break;
        }
    }
    

    public function loadStatistics()
    {
        $baseQuery = Download::query();

        if ($this->startDate && $this->endDate) {
            $baseQuery->whereBetween('downloaded_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        } elseif ($this->endDate) {
            $baseQuery->where('downloaded_at', '<=', $this->endDate . ' 23:59:59');
        }

        $this->totalDownloadsPeriod = (clone $baseQuery)->count();
        $this->totalDownloadsAllTime = Download::count();

        $this->downloadsPerPaper = (clone $baseQuery)
            ->join('papers', 'downloads.paper_id', '=', 'papers.id')
            ->select('papers.title as paper_title', 'downloads.paper_id', DB::raw('COUNT(*) as downloads_count'))
            ->groupBy('downloads.paper_id', 'papers.title')
            ->orderByDesc('downloads_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'paper_title' => $item->paper_title,
                    'downloads' => $item->downloads_count,
                    'paper_id' => $item->paper_id
                ];
            })
            ->toArray();

        $this->downloadsByDepartment = (clone $baseQuery)
            ->join('papers', 'downloads.paper_id', '=', 'papers.id')
            ->join('departments', 'papers.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department_name, COUNT(*) as downloads_count')
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('downloads_count')
            ->get()
            ->map(function ($item) {
                return [
                    'department_name' => $item->department_name,
                    'downloads' => $item->downloads_count
                ];
            })
            ->toArray();

        $this->downloadsByExamType = (clone $baseQuery)
            ->join('papers', 'downloads.paper_id', '=', 'papers.id')
            ->selectRaw('papers.exam_type, COUNT(*) as downloads_count')
            ->groupBy('papers.exam_type')
            ->orderByDesc('downloads_count')
            ->get()
            ->map(function ($item) {
                return [
                    'exam_type' => $item->exam_type ?? 'Unknown',
                    'downloads' => $item->downloads_count
                ];
            })
            ->toArray();

        $this->downloadsByStudentType = (clone $baseQuery)
            ->join('papers', 'downloads.paper_id', '=', 'papers.id')
            ->join('student_types', 'papers.student_type_id', '=', 'student_types.id')
            ->selectRaw('student_types.name as student_type_name, COUNT(*) as downloads_count')
            ->groupBy('student_types.id', 'student_types.name')
            ->orderByDesc('downloads_count')
            ->get()
            ->map(function ($item) {
                return [
                    'student_type_name' => $item->student_type_name,
                    'downloads' => $item->downloads_count
                ];
            })
            ->toArray();

        $this->downloadsByLevel = (clone $baseQuery)
            ->join('papers', 'downloads.paper_id', '=', 'papers.id')
            ->join('levels', 'papers.level_id', '=', 'levels.id')
            ->selectRaw('levels.name as level_name, COUNT(*) as downloads_count')
            ->groupBy('levels.id', 'levels.name')
            ->orderByDesc('downloads_count')
            ->get()
            ->map(function ($item) {
                return [
                    'level_name' => $item->level_name,
                    'downloads' => $item->downloads_count
                ];
            })
            ->toArray();

        $this->yearlyComparison = Download::select(DB::raw('YEAR(downloaded_at) as year'), DB::raw('COUNT(*) as downloads_count'))
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'downloads' => $item->downloads_count
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.analytics.download-statistics');
    }
}
