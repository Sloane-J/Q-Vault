<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Models\Download;
use App\Models\Paper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        return view('admin.analytics.index');
    }

    /**
     * Get paper download trends
     */
    public function getDownloadData(Request $request)
    {
        $validated = $request->validate([
            'timeRange' => 'sometimes|in:week,month,quarter,year,all-time',
        ]);

        $timeRange = $validated['timeRange'] ?? 'month';
        $startDate = $this->getStartDate($timeRange);

        $downloads = Download::select(
            DB::raw('DATE(downloaded_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->when($startDate, fn($query) => $query->where('downloaded_at', '>=', $startDate))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($downloads);
    }

    /**
     * Get top downloaded papers
     */
    public function getPopularPapers(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'timeRange' => 'sometimes|in:week,month,quarter,year,all-time',
        ]);

        $limit = $validated['limit'] ?? 10;
        $timeRange = $validated['timeRange'] ?? 'month';
        $startDate = $this->getStartDate($timeRange);

        $popularPapers = Download::select('paper_id', DB::raw('COUNT(*) as download_count'))
            ->with(['paper:id,title,department_id,exam_year', 'paper.department:id,name'])
            ->when($startDate, fn($query) => $query->where('downloaded_at', '>=', $startDate))
            ->groupBy('paper_id')
            ->orderByDesc('download_count')
            ->limit($limit)
            ->get();

        return response()->json($popularPapers);
    }

    /**
     * Get department-level analytics
     */
    public function getDepartmentData(Request $request)
    {
        $validated = $request->validate([
            'timeRange' => 'sometimes|in:week,month,quarter,year,all-time',
        ]);

        $timeRange = $validated['timeRange'] ?? 'month';
        $startDate = $this->getStartDate($timeRange);

        $departments = Department::withCount([
                'papers',
                'downloads' => fn($query) => $query->when($startDate, fn($q) => $q->where('downloaded_at', '>=', $startDate)),
            ])
            ->get()
            ->map(function ($department) {
                $department->average_downloads = $department->papers_count > 0 
                    ? round($department->downloads_count / $department->papers_count, 2) 
                    : 0;
                return $department;
            });

        return response()->json($departments);
    }

    /**
     * Get active user statistics
     */
    public function getUserActivity(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'timeRange' => 'sometimes|in:week,month,quarter,year,all-time',
        ]);

        $limit = $validated['limit'] ?? 10;
        $timeRange = $validated['timeRange'] ?? 'month';
        $startDate = $this->getStartDate($timeRange);

        $activeUsers = Download::select('user_id', DB::raw('COUNT(*) as download_count'))
            ->with('user:id,name,email')
            ->when($startDate, fn($query) => $query->where('downloaded_at', '>=', $startDate))
            ->groupBy('user_id')
            ->orderByDesc('download_count')
            ->limit($limit)
            ->get();

        return response()->json($activeUsers);
    }

    /**
     * Get dashboard summary stats
     */
    public function getDashboardStats()
    {
        $stats = [
            'total_papers' => Paper::count(),
            'total_downloads' => Download::count(),
            'total_users' => User::count(),
            'papers_this_month' => Paper::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'downloads_this_month' => Download::where('downloaded_at', '>=', Carbon::now()->startOfMonth())->count(),
            'departments' => Department::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export analytics as PDF
     */
    public function exportPdf(Request $request)
    {
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);

        $data = [
            'downloads' => Download::where('downloaded_at', '>=', $startDate)
                ->select(DB::raw('DATE(downloaded_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->get(),
            'popularPapers' => $this->getPopularPapers($request)->original,
            'departments' => $this->getDepartmentData($request)->original,
            'userActivity' => $this->getUserActivity($request)->original,
            'stats' => $this->getDashboardStats()->original,
            'timeRange' => $timeRange,
            'startDate' => $startDate?->format('Y-m-d') ?? 'All time',
            'endDate' => Carbon::now()->format('Y-m-d'),
        ];

        $pdf = SnappyPdf::loadView('admin.analytics.pdf', $data);
        return $pdf->download('analytics-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export analytics as Excel
     */
    public function exportExcel(Request $request)
    {
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);

        return Excel::download(
            new AnalyticsExport(
                $this->getDownloadData($request)->original,
                $this->getPopularPapers($request)->original,
                $this->getDepartmentData($request)->original,
                $this->getUserActivity($request)->original,
                $timeRange
            ),
            'analytics-report-' . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Calculate start date for time ranges
     */
    private function getStartDate(?string $timeRange): ?Carbon
    {
        return match ($timeRange) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subMonths(3),
            'year' => Carbon::now()->subYear(),
            'all-time' => null, // No date filter
            default => Carbon::now()->subMonth(),
        };
    }
}