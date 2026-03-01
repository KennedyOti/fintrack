<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── User Stats ───────────────────────────────────────────────────────
        $totalUsers     = User::count();
        $activeUsers    = User::where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $deletedUsers   = User::onlyTrashed()->count();
        $totalAdmins    = User::where('role', 'admin')->count();

        $newUsersThisMonth = User::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // ── Platform Financial Totals (all users) ────────────────────────────
        $platformIncome   = DB::table('incomes')->sum('amount');
        $platformExpenses = DB::table('expenses')->sum('amount');
        $platformSavings  = DB::table('savings_accounts')->sum('current_balance');
        $totalInvoices    = Invoice::count();
        $paidInvoices     = Invoice::where('status', 'paid')->count();
        $totalRevenue     = DB::table('payments')->sum('amount');

        // ── Monthly User Registrations (current year) ────────────────────────
        $monthlyRegistrations = User::selectRaw(
            'MONTH(created_at) as month, COUNT(*) as count'
        )
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('count', 'month')
            ->toArray();

        $regMonths = [];
        $regCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $regMonths[] = date('M', mktime(0, 0, 0, $m, 1));
            $regCounts[] = $monthlyRegistrations[$m] ?? 0;
        }

        // ── Recent Registrations ─────────────────────────────────────────────
        $recentUsers = User::latest()->take(8)->get();

        // ── Recent Activity Logs ─────────────────────────────────────────────
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(12)
            ->get();

        // ── Top Active Users (by income recorded) ────────────────────────────
        $topUsers = User::select(
            'users.id',
            'users.name',
            'users.email',
            DB::raw('SUM(incomes.amount) as total_income')
        )
            ->leftJoin('incomes', 'incomes.user_id', '=', 'users.id')
            ->whereNull('incomes.deleted_at')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_income')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'deletedUsers',
            'totalAdmins',
            'newUsersThisMonth',
            'platformIncome',
            'platformExpenses',
            'platformSavings',
            'totalInvoices',
            'paidInvoices',
            'totalRevenue',
            'regMonths',
            'regCounts',
            'recentUsers',
            'recentActivity',
            'topUsers'
        ));
    }
}
