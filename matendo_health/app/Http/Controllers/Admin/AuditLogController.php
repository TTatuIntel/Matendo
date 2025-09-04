<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with(['causer', 'subject'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(50);
        
        return view('admin.audit.index', compact('logs'));
    }

    public function userLogs(User $user)
    {
        $logs = ActivityLog::where('causer_id', $user->id)
                          ->orWhere('subject_id', $user->id)
                          ->orderBy('created_at', 'desc')
                          ->paginate(50);
        
        return view('admin.audit.user-logs', compact('logs', 'user'));
    }

    public function exportLogs(Request $request)
    {
        $logs = ActivityLog::with(['causer', 'subject'])
                          ->orderBy('created_at', 'desc')
                          ->limit(1000)
                          ->get();
        
        return response()->streamDownload(function () use ($logs) {
            echo "Audit Logs Export\n";
            echo "Generated: " . now() . "\n\n";
            echo "Timestamp,User,Action,Subject\n";
            
            foreach ($logs as $log) {
                echo implode(',', [
                    $log->created_at,
                    $log->causer->name ?? 'System',
                    $log->description ?? 'Unknown',
                    $log->subject_type ?? 'N/A'
                ]) . "\n";
            }
        }, 'audit-logs-' . now()->format('Y-m-d') . '.csv');
    }

    public function clearOldLogs(Request $request)
    {
        $days = $request->input('days', 90);
        
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old log entries"
        ]);
    }
}
