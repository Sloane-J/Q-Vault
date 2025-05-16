<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Exports\AuditLogsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Maatwebsite\Excel\Facades\Excel;

class AuditController extends Controller
{
    /**
     * Display audit logs page
     */
    public function index()
    {
        return view('admin.audit.index');
    }
    
    /**
     * Get filtered audit logs
     */
    public function getLogs(Request $request)
    {
        $query = AuditLog::with('user');
        
        // Apply date filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        // Apply action type filter
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        
        // Apply user filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Apply object type filter
        if ($request->filled('object_type')) {
            $query->where('object_type', $request->object_type);
        }
        
        // Apply severity filter
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        
        // Apply search term
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('additional_context', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($qu) use ($searchTerm) {
                      $qu->where('name', 'like', "%{$searchTerm}%")
                         ->orWhere('email', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Get paginated results
        $perPage = $request->input('per_page', 15);
        $logs = $query->orderByDesc('created_at')->paginate($perPage);
        
        if ($request->wantsJson()) {
            return response()->json($logs);
        }
        
        return $logs;
    }
    
    /**
     * Get audit log statistics
     */
    public function getStats()
    {
        $stats = [
            'total_logs' => AuditLog::count(),
            'logs_today' => AuditLog::whereDate('created_at', Carbon::today())->count(),
            'logs_this_week' => AuditLog::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'action_types' => AuditLog::select('action_type', DB::raw('count(*) as count'))
                ->groupBy('action_type')
                ->orderByDesc('count')
                ->get(),
            'severity_distribution' => AuditLog::select('severity', DB::raw('count(*) as count'))
                ->groupBy('severity')
                ->orderByDesc('count')
                ->get(),
            'top_users' => AuditLog::select('user_id', DB::raw('count(*) as count'))
                ->with('user:id,name,email')
                ->groupBy('user_id')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Show details for a specific audit log entry
     */
    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        
        return view('admin.audit.show', compact('log'));
    }
    
    /**
     * Export audit logs as PDF
     */
    public function exportPdf(Request $request)
    {
        $logs = $this->getLogs($request);
        
        $data = [
            'logs' => $logs,
            'filters' => $request->all(),
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
        
        $pdf = SnappyPdf::loadView('admin.audit.pdf', $data);
        
        return $pdf->download('audit-logs-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export audit logs as Excel
     */
    public function exportExcel(Request $request)
    {
        $logs = $this->getLogs($request);
        
        return Excel::download(
            new AuditLogsExport($logs), 
            'audit-logs-' . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    /**
     * Export audit logs as CSV
     */
    public function exportCsv(Request $request)
    {
        $logs = $this->getLogs($request);
        
        return Excel::download(
            new AuditLogsExport($logs), 
            'audit-logs-' . Carbon::now()->format('Y-m-d') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
    
    /**
     * Clear old audit logs based on retention policy
     */
    public function clearOldLogs(Request $request)
    {
        $this->validate($request, [
            'retention_days' => 'required|integer|min:30',
        ]);
        
        $cutoffDate = Carbon::now()->subDays($request->retention_days);
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        // Log this action
        $this->logAuditAction(
            auth()->user()->id,
            'delete',
            'audit_logs',
            null,
            'Cleared ' . $deletedCount . ' logs older than ' . $cutoffDate->format('Y-m-d'),
            null,
            null,
            'info'
        );
        
        return redirect()->back()->with('success', $deletedCount . ' old audit logs have been cleared.');
    }
    
    /**
     * Log an audit action
     */
    private function logAuditAction($userId, $actionType, $objectType, $objectId, $description, $previousState, $newState, $severity)
    {
        AuditLog::create([
            'user_id' => $userId,
            'action_type' => $actionType,
            'object_type' => $objectType,
            'object_id' => $objectId,
            'description' => $description,
            'previous_state' => $previousState ? json_encode($previousState) : null,
            'new_state' => $newState ? json_encode($newState) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'severity' => $severity,
        ]);
    }
}