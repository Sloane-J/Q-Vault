<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuditController extends Controller
{
    /**
     * Get all audit logs with filters
     */
    public function getAllLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'sometimes|integer|min:1|max:100',
            'date_range' => 'sometimes|date_format:Y-m-d,Y-m-d',
            'action_type' => ['sometimes', Rule::in(['create', 'read', 'update', 'delete', 'login', 'system'])],
            'user_id' => 'sometimes|exists:users,id',
            'target_model' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = AuditLog::with(['user:id,name', 'target']);

        $this->applyFilters($query, $request);

        return $query->latest()
            ->paginate($request->input('limit', 25));
    }

    /**
     * Get admin-specific activities
     */
    public function getAdminActivities(Request $request)
    {
        $query = AuditLog::whereIn('action_type', [
            'user_create',
            'user_update',
            'permission_change',
            'system_config'
        ])->with(['user:id,name', 'target']);

        $this->applyFilters($query, $request);

        return $query->latest()
            ->paginate($request->input('limit', 25));
    }

    /**
     * Get paper management activities
     */
    public function getPaperActivities(Request $request)
    {
        $query = AuditLog::where('target_model', 'Paper')
            ->with(['user:id,name', 'target.department:id,name']);

        $this->applyFilters($query, $request);

        return $query->latest()
            ->paginate($request->input('limit', 25));
    }

    /**
     * Get user login/activity logs
     */
    public function getUserActivities(Request $request)
    {
        $query = AuditLog::whereIn('action_type', [
            'login',
            'logout',
            'failed_login',
            'password_change'
        ])->with(['user:id,name,email']);

        $this->applyFilters($query, $request);

        return $query->latest()
            ->paginate($request->input('limit', 25));
    }

    /**
     * Get system events
     */
    public function getSystemEvents(Request $request)
    {
        $query = AuditLog::where('action_type', 'system_event')
            ->with(['user:id,name']);

        $this->applyFilters($query, $request);

        return $query->latest()
            ->paginate($request->input('limit', 25));
    }

    /**
     * Apply common filters to queries
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->date_range) {
            $dates = explode(',', $request->date_range);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        if ($request->action_type) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->target_model) {
            $query->where('target_model', $request->target_model);
        }
    }

    /**
     * Export audit logs as CSV
     */
    public function exportAuditLogs(Request $request)
    {
        $query = AuditLog::with(['user:id,name,email']);

        $this->applyFilters($query, $request);

        $logs = $query->latest()
            ->get([
                'created_at',
                'action_type',
                'target_model',
                'target_id',
                'user_id',
                'ip_address',
                'metadata'
            ]);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=audit_logs_' . Carbon::now()->format('Y-m-d') . '.csv'
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Timestamp',
                'Action Type',
                'Target Model',
                'Target ID',
                'User ID',
                'IP Address',
                'Changes'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at,
                    $log->action_type,
                    $log->target_model,
                    $log->target_id,
                    $log->user_id,
                    $log->ip_address,
                    json_encode($log->metadata)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}