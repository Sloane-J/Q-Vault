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
    
    // View mode
    public $viewMode = 'overview'; // overview, department, student_type, level, exam_type, year
    
    // Chart data
    public $chartData = [];
    public $totalDownloads = 0;
    public $uniqueUsers = 0;
    
    // Available options for filters
    public $departments = [];
    public $studentTypes = [];
    public $levels = [];
    public $examTypes = ['Midterm', 'Final', 'Resit', 'Supplementary'];
    public $examYears = [];

    public function mount()
    {
        // Set default date range (last 30 days)
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        
        // Load filter options
        $this->loadFilterOptions();
        
        // Load initial data
        $this->loadStatistics();
    }

    public function loadFilterOptions()
    {
        $this->departments = Department::orderBy('name')->get();
        $this->studentTypes = StudentType::orderBy('name')->get();
        $this->levels = Level::with('studentType')->orderBy('level_number')->get();
        
        // Get available exam years from the statistics
        $this->examYears = DownloadStatistic::distinct()
            ->whereNotNull('exam_year')
            ->orderBy('exam_year', 'desc')
            ->pluck('exam_year')
            ->toArray();
    }

    public function updatedStartDate()
    {
        $this->loadStatistics();
    }

    public function updatedEndDate()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedDepartment()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedStudentType()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedLevel()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedExamType()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedExamYear()
    {
        $this->loadStatistics();
    }

    public function updatedViewMode()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        // Build the query with filters
        $query = DownloadStatistic::withinDateRange($this->startDate, $this->endDate);
        
        if ($this->selectedDepartment) {
            $query->byDepartment($this->selectedDepartment);
        }
        
        if ($this->selectedStudentType) {
            $query->byStudentType($this->selectedStudentType);
        }
        
        if ($this->selectedLevel) {
            $query->byLevel($this->selectedLevel);
        }
        
        if ($this->selectedExamType) {
            $query->byExamType($this->selectedExamType);
        }
        
        if ($this->selectedExamYear) {
            $query->byExamYear($this->selectedExamYear);
        }

        // Calculate totals
        $this->totalDownloads = $query->sum('total_downloads');
        $this->uniqueUsers = $query->sum('unique_users');

        // Load chart data based on view mode
        $this->loadChartData();
    }

    public function loadChartData()
    {
        switch ($this->viewMode) {
            case 'department':
                $this->chartData = $this->getDepartmentData();
                break;
            case 'student_type':
                $this->chartData = $this->getStudentTypeData();
                break;
            case 'level':
                $this->chartData = $this->getLevelData();
                break;
            case 'exam_type':
                $this->chartData = $this->getExamTypeData();
                break;
            case 'year':
                $this->chartData = $this->getYearData();
                break;
            default:
                $this->chartData = $this->getOverviewData();
        }
    }

    private function getDepartmentData()
    {
        $data = DownloadStatistic::getDownloadsByDepartment($this->startDate, $this->endDate);
        
        return $data->map(function ($item) {
            return [
                'label' => $item->department->name ?? 'Unknown',
                'value' => $item->downloads,
                'color' => $this->generateColor($item->department_id)
            ];
        })->toArray();
    }

    private function getStudentTypeData()
    {
        $data = DownloadStatistic::getDownloadsByStudentType($this->startDate, $this->endDate);
        
        return $data->map(function ($item) {
            return [
                'label' => $item->studentType->name ?? 'Unknown',
                'value' => $item->downloads,
                'color' => $this->generateColor($item->student_type_id)
            ];
        })->toArray();
    }

    private function getLevelData()
    {
        $data = DownloadStatistic::getDownloadsByLevel($this->startDate, $this->endDate);
        
        return $data->map(function ($item) {
            return [
                'label' => $item->level->name ?? 'Unknown',
                'value' => $item->downloads,
                'color' => $this->generateColor($item->level_id)
            ];
        })->toArray();
    }

    private function getExamTypeData()
    {
        $data = DownloadStatistic::getDownloadsByExamType($this->startDate, $this->endDate);
        
        return $data->map(function ($item) {
            return [
                'label' => $item->exam_type,
                'value' => $item->downloads,
                'color' => $this->generateColor(crc32($item->exam_type))
            ];
        })->toArray();
    }

    private function getYearData()
    {
        $data = DownloadStatistic::getDownloadsByExamYear($this->startDate, $this->endDate);
        
        return $data->map(function ($item) {
            return [
                'label' => (string) $item->exam_year,
                'value' => $item->downloads,
                'color' => $this->generateColor($item->exam_year)
            ];
        })->toArray();
    }

    private function getOverviewData()
    {
        // Get daily downloads for the date range
        $query = DownloadStatistic::withinDateRange($this->startDate, $this->endDate);
        
        // Apply filters
        if ($this->selectedDepartment) {
            $query->byDepartment($this->selectedDepartment);
        }
        
        $data = $query->selectRaw('date, SUM(total_downloads) as downloads')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data->map(function ($item) {
            return [
                'label' => Carbon::parse($item->date)->format('M d'),
                'value' => $item->downloads,
                'date' => $item->date
            ];
        })->toArray();
    }

    private function generateColor($seed)
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
        ];
        
        return $colors[abs($seed) % count($colors)];
    }

    public function resetFilters()
    {
        $this->selectedDepartment = '';
        $this->selectedStudentType = '';
        $this->selectedLevel = '';
        $this->selectedExamType = '';
        $this->selectedExamYear = '';
        
        $this->loadStatistics();
    }

    public function exportData()
    {
        // This method would handle exporting the current data to CSV/Excel
        // Implementation depends on your export requirements
        $this->dispatch('export-requested', [
            'filters' => [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'department' => $this->selectedDepartment,
                'student_type' => $this->selectedStudentType,
                'level' => $this->selectedLevel,
                'exam_type' => $this->selectedExamType,
                'exam_year' => $this->selectedExamYear,
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.admin.download-statistics');
    }
}