<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter by action (partial match)
        if ($action = $request->input('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs  = $query->latest()->paginate(50)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        // Action type options derived from existing logs
        $actionTypes = ActivityLog::selectRaw('DISTINCT action')
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'users', 'actionTypes'));
    }

    /**
     * Delete activity logs older than N days.
     */
    public function clearOld(Request $request)
    {
        $days    = max(7, (int) $request->input('days', 90));
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        ActivityLog::log(
            'admin.activity_logs.cleared',
            "Admin cleared {$deleted} activity log entries older than {$days} days."
        );

        return back()->with('success', "{$deleted} log entries older than {$days} days have been deleted.");
    }
}
