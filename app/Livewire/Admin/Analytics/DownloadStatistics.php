<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\DownloadStatistic;
use App\Models\Department;
use App\Models\StudentType;
use App\Models\Level;
use Carbon\Carbon;

class DownloadStatistics extends Component
{
    // Filter properties
    public $startDate;
    public $endDate;
    public $selectedDepartment = '';
    public $selectedStudentType = '';
    public $selectedLevel = '';
    public $selectedExamType = '';
    public $selectedExamYear = '';

    // Data for cards
    public $totalDownloads = 0;
    public $totalDownloadsAllTime = 0;
    public $uniqueUsers = 0;
    public $downloadsByPaper = [];
    public $downloadsByDepartment = [];
    public $downloadsByExamType = [];
    public $downloadsByStudentType = [];
    public $downloadsByLevel = [];
    public $yearlyComparison = [];

    // Available filter options
    public $departments = [];
    public $studentTypes = [];
    public $levels = [];
    public $examTypes = ['Midterm', 'Final', 'Resit', 'Supplementary'];
    public $examYears = [];

    public function mount()
    {
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->loadFilterOptions();
        $this->loadAllStatistics();
    }

    public function loadFilterOptions()
    {
        $this->departments = Department::orderBy('name')->get();
        $this->studentTypes = StudentType::orderBy('name')->get();
        $this->levels = Level::orderBy('level_number')->get();
        
        $this->examYears = DownloadStatistic::distinct()
            ->whereNotNull('exam_year')
            ->orderBy('exam_year', 'desc')
            ->pluck('exam_year')
            ->toArray();
    }

    public function updated()
    {
        $this->loadAllStatistics();
    }

    public function loadAllStatistics()
    {
        $baseQuery = DownloadStatistic::query()
            ->withinDateRange($this->startDate, $this->endDate);

        // Apply filters if selected
        if ($this->selectedDepartment) {
            $baseQuery->byDepartment($this->selectedDepartment);
        }
        if ($this->selectedStudentType) {
            $baseQuery->byStudentType($this->selectedStudentType);
        }
        if ($this->selectedLevel) {
            $baseQuery->byLevel($this->selectedLevel);
        }
        if ($this->selectedExamType) {
            $baseQuery->byExamType($this->selectedExamType);
        }
        if ($this->selectedExamYear) {
            $baseQuery->byExamYear($this->selectedExamYear);
        }

        // Load all statistics
        $this->totalDownloads = $baseQuery->sum('total_downloads');
        $this->totalDownloadsAllTime = DownloadStatistic::sum('total_downloads');
        $this->uniqueUsers = $baseQuery->distinct('user_id')->count('user_id');

        // Load data for individual cards
        $this->loadDownloadsByPaper();
        $this->loadDownloadsByDepartment();
        $this->loadDownloadsByExamType();
        $this->loadDownloadsByStudentType();
        $this->loadDownloadsByLevel();
        
        $this->loadYearlyComparison();
    }

    protected function loadDownloadsByPaper()
    {
        $this->downloadsByPaper = DownloadStatistic::query()
            ->with('paper')
            ->selectRaw('paper_id, SUM(total_downloads) as downloads')
            ->groupBy('paper_id')
            ->orderByDesc('downloads')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'paper' => $item->paper->title ?? 'Unknown',
                    'downloads' => $item->downloads
                ];
            })->toArray();
    }

    protected function loadDownloadsByDepartment()
    {
        $this->downloadsByDepartment = DownloadStatistic::query()
            ->with('department')
            ->selectRaw('department_id, SUM(total_downloads) as downloads')
            ->groupBy('department_id')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->department->name ?? 'Unknown',
                    'downloads' => $item->downloads
                ];
            })->toArray();
    }

    protected function loadDownloadsByExamType()
    {
        $this->downloadsByExamType = DownloadStatistic::query()
            ->selectRaw('exam_type, SUM(total_downloads) as downloads')
            ->groupBy('exam_type')
            ->get()
            ->map(function ($item) {
                return [
                    'exam_type' => $item->exam_type ?? 'Unknown',
                    'downloads' => $item->downloads
                ];
            })->toArray();
    }

    protected function loadDownloadsByStudentType()
    {
        $this->downloadsByStudentType = DownloadStatistic::query()
            ->with('studentType')
            ->selectRaw('student_type_id, SUM(total_downloads) as downloads')
            ->groupBy('student_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'student_type' => $item->studentType->name ?? 'Unknown',
                    'downloads' => $item->downloads
                ];
            })->toArray();
    }

    protected function loadDownloadsByLevel()
    {
        $this->downloadsByLevel = DownloadStatistic::query()
            ->with('level')
            ->selectRaw('level_id, SUM(total_downloads) as downloads')
            ->groupBy('level_id')
            ->get()
            ->map(function ($item) {
                return [
                    'level' => $item->level->name ?? 'Unknown',
                    'downloads' => $item->downloads
                ];
            })->toArray();
    }

    protected function loadYearlyComparison()
    {
        $this->yearlyComparison = DownloadStatistic::query()
            ->selectRaw('YEAR(date) as year, SUM(total_downloads) as downloads')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'downloads' => $item->downloads
                ];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.admin.analytics.download-statistics', [
            'totalDownloadsChartData' => $this->getTotalDownloadsChartData(),
            'departmentChartData' => $this->getDepartmentChartData(),
            'examTypeChartData' => $this->getExamTypeChartData(),
            'studentTypeChartData' => $this->getStudentTypeChartData(),
            'levelChartData' => $this->getLevelChartData(),
            'yearlyChartData' => $this->getYearlyChartData(),
        ]);
    }

    // Chart data preparation methods
    protected function getTotalDownloadsChartData()
    {
        $dailyData = DownloadStatistic::query()
            ->selectRaw('date, SUM(total_downloads) as downloads')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return json_encode([
            'labels' => $dailyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d')),
            'datasets' => [[
                'label' => 'Daily Downloads',
                'data' => $dailyData->pluck('downloads'),
                'borderColor' => '#3B82F6',
                'fill' => false
            ]]
        ]);
    }

    protected function getDepartmentChartData()
    {
        return json_encode([
            'labels' => collect($this->downloadsByDepartment)->pluck('department'),
            'datasets' => [[
                'data' => collect($this->downloadsByDepartment)->pluck('downloads'),
                'backgroundColor' => ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6']
            ]]
        ]);
    }

    protected function getExamTypeChartData()
    {
        return json_encode([
            'labels' => collect($this->downloadsByExamType)->pluck('exam_type'),
            'datasets' => [[
                'data' => collect($this->downloadsByExamType)->pluck('downloads'),
                'backgroundColor' => ['#3B82F6', '#EF4444', '#10B981', '#F59E0B']
            ]]
        ]);
    }

    protected function getStudentTypeChartData()
    {
        return json_encode([
            'labels' => collect($this->downloadsByStudentType)->pluck('student_type'),
            'datasets' => [[
                'data' => collect($this->downloadsByStudentType)->pluck('downloads'),
                'backgroundColor' => ['#3B82F6', '#EF4444', '#10B981']
            ]]
        ]);
    }

    protected function getLevelChartData()
    {
        return json_encode([
            'labels' => collect($this->downloadsByLevel)->pluck('level'),
            'datasets' => [[
                'data' => collect($this->downloadsByLevel)->pluck('downloads'),
                'backgroundColor' => ['#3B82F6', '#EF4444', '#10B981', '#F59E0B']
            ]]
        ]);
    }

    protected function getYearlyChartData()
    {
        return json_encode([
            'labels' => collect($this->yearlyComparison)->pluck('year'),
            'datasets' => [[
                'label' => 'Downloads by Year',
                'data' => collect($this->yearlyComparison)->pluck('downloads'),
                'borderColor' => '#3B82F6',
                'fill' => false
            ]]
        ]);
    }
}