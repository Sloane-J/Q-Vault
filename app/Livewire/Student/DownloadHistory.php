<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\Download;
use App\Models\Paper;
// Note: App\Models\Course is not directly used here but its existence is implied by paper->course relationship
use App\Models\Course; // Added for clarity, as we're grouping by course name
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For potential complex queries if needed, though not used in this version
use Illuminate\Support\Carbon;

class DownloadHistory extends Component
{
    use WithPagination;

    // Graph data properties
    public array $popularPapersChart = [];
    public array $downloadTrendsChart = [];
    public int $totalDownloadsThisSemester = 0;

    protected $listeners = ['refreshCharts' => '$refresh'];

    public function mount()
    {
        $this->prepareChartData();
    }

    protected function prepareChartData()
    {
        // 1. Popular Papers Chart Data (This Week) - ALL USERS (Original logic maintained)
        // This section remains unchanged from your previous version.
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

        // 2. Download Trends Chart (Monthly by Course for Current Year) - ALL USERS (MODIFIED SECTION)
        // Fetch downloads from the beginning of the current year up to now.
        $downloadsCurrentYear = Download::with('paper.course')
            ->where('created_at', '>=', now()->startOfYear()) // Changed to start of current year
            ->where('created_at', '<=', now()) // Ensure we don't get future data if any
            ->get();

        // This array will store download counts: $courseDownloadsByMonth['Course Name']['YYYY-MM'] = count
        $courseDownloadsByMonth = [];

        foreach ($downloadsCurrentYear as $download) {
            // Ensure that the download is associated with a paper, that paper has a course, and the course has a name.
            if ($download->paper && $download->paper->course && $download->paper->course->name) {
                $courseName = $download->paper->course->name;
                // Format the download's creation date to 'YYYY-MM' to group by month.
                $monthKey = Carbon::parse($download->created_at)->format('Y-m');

                // Initialize arrays if they don't exist.
                if (!isset($courseDownloadsByMonth[$courseName])) {
                    $courseDownloadsByMonth[$courseName] = [];
                }
                if (!isset($courseDownloadsByMonth[$courseName][$monthKey])) {
                    $courseDownloadsByMonth[$courseName][$monthKey] = 0;
                }
                // Increment the download count for the specific course and month.
                $courseDownloadsByMonth[$courseName][$monthKey]++;
            }
        }

        // Prepare month keys (e.g., '2025-01') and category labels (e.g., 'Jan 2025')
        // for the chart's x-axis, covering months from January of the current year up to the current month.
        $monthKeysOrdered = [];
        $trendsChartCategories = []; // Categories for the x-axis of the trends chart.
        $startOfMonth = now()->startOfYear(); // Start from January of the current year.
        $currentMonth = now()->month; // Get the current month number (1 for Jan, 12 for Dec).

        for ($i = 0; $i < $currentMonth; $i++) {
            $monthDate = $startOfMonth->copy()->addMonths($i); // Create a Carbon instance for each month.
            $monthKeysOrdered[] = $monthDate->format('Y-m');    // Store 'YYYY-MM' for data lookup.
            $trendsChartCategories[] = $monthDate->format('M Y'); // Store 'Mon YYYY' for display.
        }


        // Prepare the series data for the chart. Each course will be a separate series.
        $trendsSeries = [];
        // Get all distinct course names that had downloads this year to ensure all are represented
        // This is an alternative to iterating $courseDownloadsByMonth keys,
        // useful if you want to ensure even courses with 0 downloads in some months are listed (though current logic handles this with ?? 0)
        // $allCourseNames = $downloadsCurrentYear->pluck('paper.course.name')->filter()->unique()->sort();

        // Iterate through the collected course data
        foreach ($courseDownloadsByMonth as $courseName => $monthlyData) {
            $courseDataPoints = [];
            // For each month in the current year (represented by $monthKeysOrdered),
            // get the download count for the current course. Default to 0 if no downloads.
            foreach ($monthKeysOrdered as $monthKey) {
                $courseDataPoints[] = $monthlyData[$monthKey] ?? 0;
            }
            // Add the course's data series to the list of series for the chart.
            $trendsSeries[] = [
                'name' => $courseName,
                'data' => $courseDataPoints,
            ];
        }
        
        // Optional: Sort series by course name for a consistent order in the chart legend.
        usort($trendsSeries, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        $this->downloadTrendsChart = [
            'series' => $trendsSeries,       // Contains an array of series, each for a course.
            'categories' => $trendsChartCategories, // Contains month labels for the x-axis.
        ];

        // 3. Semester Download Count - ALL USERS (Original logic maintained)
        // Counts total downloads in the last 4 months (approximating a semester).
        $this->totalDownloadsThisSemester = Download::where('created_at', '>=', now()->subMonths(4))
            ->count();
    }

    public function render()
    {
        // Fetches download history for the currently authenticated student, paginated.
        return view('livewire.student.download-history', [
            'downloads' => Download::where('user_id', Auth::id())
                ->with(['paper.department', 'paper.course', 'paper.level']) // Eager load related paper details.
                ->latest('downloaded_at') // Order by the download timestamp.
                ->paginate(10),
        ]);
    }
}
