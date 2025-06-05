<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\Download;
use App\Models\Paper;
use App\Models\Course;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DownloadHistory extends Component
{
    use WithPagination;

    // Graph data properties
    public array $popularPapersChart = [];
    public array $downloadTrendsChart = [];
    public $recentlyUploadedPapers = [];

    protected $listeners = ['refreshCharts' => '$refresh'];

    public function mount()
    {
        $this->prepareChartData();
    }

    protected function prepareChartData()
    {
        // 1. Popular Papers Chart Data (This Week) - ALL USERS (Original logic maintained)
        $popularPapers = Paper::with(['course'])
            ->withCount(['downloads' => function($query) {
                $query->where('created_at', '>=', now()->subWeek());
            }])
            ->having('downloads_count', '>', 0)
            ->orderByDesc('downloads_count')
            ->limit(5)
            ->get();

        $chartData = [];
        $popularChartCategories = [];
        $chartIds = [];

        foreach ($popularPapers as $paper) {
            $chartData[] = $paper->downloads_count;
            $popularChartCategories[] = $paper->course ? $paper->course->name : ($paper->title ?? 'Unknown Course');
            $chartIds[] = $paper->id;
        }

        $this->popularPapersChart = [
            'series' => [
                [
                    'name' => 'Downloads',
                    'data' => $chartData
                ]
            ],
            'categories' => $popularChartCategories,
            'ids' => $chartIds
        ];

        // 2. Download Trends Chart (Monthly by Course for Current Year) - ALL USERS
        $downloadsCurrentYear = Download::with('paper.course')
            ->where('created_at', '>=', now()->startOfYear())
            ->where('created_at', '<=', now())
            ->get();

        $courseDownloadsByMonth = [];

        foreach ($downloadsCurrentYear as $download) {
            if ($download->paper && $download->paper->course && $download->paper->course->name) {
                $courseName = $download->paper->course->name;
                $monthKey = Carbon::parse($download->created_at)->format('Y-m');

                if (!isset($courseDownloadsByMonth[$courseName])) {
                    $courseDownloadsByMonth[$courseName] = [];
                }
                if (!isset($courseDownloadsByMonth[$courseName][$monthKey])) {
                    $courseDownloadsByMonth[$courseName][$monthKey] = 0;
                }
                $courseDownloadsByMonth[$courseName][$monthKey]++;
            }
        }

        $monthKeysOrdered = [];
        $trendsChartCategories = [];
        $startOfMonth = now()->startOfYear();
        $currentMonth = now()->month;

        for ($i = 0; $i < $currentMonth; $i++) {
            $monthDate = $startOfMonth->copy()->addMonths($i);
            $monthKeysOrdered[] = $monthDate->format('Y-m');
            $trendsChartCategories[] = $monthDate->format('M Y');
        }

        $trendsSeries = [];
        foreach ($courseDownloadsByMonth as $courseName => $monthlyData) {
            $courseDataPoints = [];
            foreach ($monthKeysOrdered as $monthKey) {
                $courseDataPoints[] = $monthlyData[$monthKey] ?? 0;
            }
            $trendsSeries[] = [
                'name' => $courseName,
                'data' => $courseDataPoints,
            ];
        }
        
        usort($trendsSeries, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $this->downloadTrendsChart = [
            'series' => $trendsSeries,
            'categories' => $trendsChartCategories,
        ];

        // 3. Recently Uploaded Papers (Top 5) - NEW FEATURE
        $this->recentlyUploadedPapers = Paper::with(['department', 'course', 'level'])
            ->latest('created_at') // Order by upload date (newest first)
            ->limit(5)
            ->get()
            ->map(function ($paper) {
                return [
                    'id' => $paper->id,
                    'title' => $paper->title ?? 'Untitled Paper',
                    'course_name' => $paper->course ? $paper->course->name : 'Unknown Course',
                    'department_name' => $paper->department ? $paper->department->name : 'Unknown Department',
                    'level' => $paper->level ? $paper->level->name : 'Unknown Level',
                    'exam_type' => $paper->exam_type,
                    'exam_year' => $paper->exam_year,
                    'uploaded_at' => $paper->created_at,
                    'uploaded_at_human' => $paper->created_at->diffForHumans(), // e.g., "2 days ago"
                ];
            });
    }

    public function render()
    {
        return view('livewire.student.download-history', [
            'downloads' => Download::where('user_id', Auth::id())
                ->with(['paper.department', 'paper.course', 'paper.level'])
                ->latest('downloaded_at')
                ->paginate(10),
        ]);
    }
}