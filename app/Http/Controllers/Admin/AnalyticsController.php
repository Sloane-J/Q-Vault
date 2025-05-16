<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Models\Download;
use App\Models\Paper;
use App\Models\SearchHistory;
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
     * Get paper download data for charts
     */
    public function getDownloadData(Request $request)
    {
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);
        
        $downloads = Download::select(
            DB::raw('DATE(downloaded_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('downloaded_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return response()->json($downloads);
    }
    
    /**
     * Get popular papers data
     */
    public function getPopularPapers(Request $request)
    {
        $limit = $request->input('limit', 10);
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);
        
        $popularPapers = Download::select('paper_id', DB::raw('COUNT(*) as download_count'))
            ->with(['paper:id,title,department_id,exam_year', 'paper.department:id,name'])
            ->where('downloaded_at', '>=', $startDate)
            ->groupBy('paper_id')
            ->orderByDesc('download_count')
            ->limit($limit)
            ->get();
            
        return response()->json($popularPapers);
    }
    
    /**
     * Get department activity data
     */
    public function getDepartmentData()
    {
        $departments = Department::withCount(['papers', 'downloads'])
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
     * Get user activity data
     */
    public function getUserActivity(Request $request)
    {
        $limit = $request->input('limit', 10);
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);
        
        $activeUsers = Download::select('user_id', DB::raw('COUNT(*) as download_count'))
            ->with('user:id,name,email')
            ->where('downloaded_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('download_count')
            ->limit($limit)
            ->get();
            
        return response()->json($activeUsers);
    }
    
    /**
     * Get search trends data
     */
    public function getSearchTrends(Request $request)
    {
        $limit = $request->input('limit', 10);
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);
        
        $searchTrends = SearchHistory::select('query_string', DB::raw('COUNT(*) as search_count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('query_string')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
            
        return response()->json($searchTrends);
    }
    
    /**
     * Get dashboard summary statistics
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
     * Export analytics data as PDF
     */
    public function exportPdf(Request $request)
    {
        $timeRange = $request->input('timeRange', 'month');
        $startDate = $this->getStartDate($timeRange);
        
        $data = [
            'downloads' => $this->getDownloadData($request)->original,
            'popularPapers' => $this->getPopularPapers($request)->original,
            'departments' => $this->getDepartmentData()->original,
            'userActivity' => $this->getUserActivity($request)->original,
            'searchTrends' => $this->getSearchTrends($request)->original,
            'stats' => $this->getDashboardStats()->original,
            'timeRange' => $timeRange,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => Carbon::now()->format('Y-m-d'),
        ];
        
        $pdf = SnappyPdf::loadView('admin.analytics.pdf', $data);
        
        return $pdf->download('analytics-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export analytics data as Excel
     */
    public function exportExcel(Request $request)
    {
        $timeRange = $request->input('timeRange', 'month');
        
        return Excel::download(
            new AnalyticsExport(
                $this->getDownloadData($request)->original,
                $this->getPopularPapers($request)->original,
                $this->getDepartmentData()->original,
                $timeRange
            ),
            'analytics-report-' . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    /**
     * Get start date based on time range
     */
    private function getStartDate($timeRange)
    {
        switch ($timeRange) {
            case 'week':
                return Carbon::now()->subWeek();
            case 'month':
                return Carbon::now()->subMonth();
            case 'quarter':
                return Carbon::now()->subMonths(3);
            case 'year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subMonth();
        }
    }
}