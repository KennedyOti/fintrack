<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\SavingsAccount;
use App\Models\DebtsReceivable;
use App\Helpers\CurrencyHelper;
// use App\Models\DebtsPayable; // Temporarily disabled - table doesn't exist
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get financial summary - Income now includes actual payments from invoices
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $totalSavings = SavingsAccount::where('user_id', $user->id)->sum('current_balance');
        
        // Only include pending/partial receivables (not draft/sent invoices)
        $totalReceivables = DebtsReceivable::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'partial'])
            ->sum(DB::raw('original_amount - paid_amount'));
            
        $totalPayables = 0; // Temporarily disabled - debts_payables table doesn't exist
        // $totalPayables = DebtsPayable::where('user_id', $user->id)
        //     ->whereIn('status', ['pending', 'partial'])
        //     ->sum(DB::raw('original_amount - paid_amount'));
        
        // Net Position = Income + Savings + Receivables - Payables - Expenses
        // Note: Draft and sent invoices are NOT included - only actual payments
        $netPosition = $totalIncome - $totalExpenses + $totalSavings + $totalReceivables - $totalPayables;
        
        // Get recent transactions
        $recentIncomes = Income::where('user_id', $user->id)
            ->with(['client', 'category'])
            ->latest()
            ->take(5)
            ->get();
            
        $recentExpenses = Expense::where('user_id', $user->id)
            ->with(['category'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get counts
        $activeProjects = Project::where('user_id', $user->id)
            ->whereIn('status', ['planned', 'in_progress'])
            ->count();
            
        $totalClients = Client::where('user_id', $user->id)->count();
        
        $pendingInvoices = Invoice::where('user_id', $user->id)
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->count();
            
        // Get monthly data for charts
        $monthlyIncome = Income::where('user_id', $user->id)
            ->selectRaw('MONTH(income_date) as month, SUM(amount) as total')
            ->whereYear('income_date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
            
        $monthlyExpenses = Expense::where('user_id', $user->id)
            ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        // Prepare chart data
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $incomeData[] = isset($monthlyIncome[$i]) ? $monthlyIncome[$i] : 0;
            $expenseData[] = isset($monthlyExpenses[$i]) ? $monthlyExpenses[$i] : 0;
        }
        
        // Get expense by category - with category names
        $expensesByCategory = Expense::where('user_id', $user->id)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($items, $categoryId) {
                $categoryName = $items->first()->category->name ?? 'Uncategorized';
                return [
                    'name' => $categoryName,
                    'total' => $items->sum('amount')
                ];
            });
        
        // Get user's currency
        $currencyCode   = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);

        // ── Budget Alerts ─────────────────────────────────────────────────
        // Load expense categories that have a monthly budget set
        $budgetYear  = now()->year;
        $budgetMonth = now()->month;

        $categoriesWithBudget = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereNotNull('monthly_budget')
            ->where('monthly_budget', '>', 0)
            ->get();

        if ($categoriesWithBudget->isNotEmpty()) {
            // Single query to get this-month spending per category
            $budgetSpending = Expense::where('user_id', $user->id)
                ->whereIn('category_id', $categoriesWithBudget->pluck('id'))
                ->whereYear('expense_date', $budgetYear)
                ->whereMonth('expense_date', $budgetMonth)
                ->selectRaw('category_id, SUM(amount) as total')
                ->groupBy('category_id')
                ->pluck('total', 'category_id');

            $categoriesWithBudget->transform(function ($cat) use ($budgetSpending) {
                $cat->spent_this_month = (float) ($budgetSpending[$cat->id] ?? 0);
                return $cat;
            });

            // Only surface categories at ≥ 80 % utilisation on the dashboard
            $budgetAlerts = $categoriesWithBudget->filter(function ($cat) {
                return ($cat->spent_this_month / $cat->monthly_budget) >= 0.80;
            })->sortByDesc(function ($cat) {
                return $cat->spent_this_month / $cat->monthly_budget;
            })->values();
        } else {
            $budgetAlerts = collect();
        }

        // ── Onboarding Checklist ──────────────────────────────────────────
        $onboardingCurrencySet = !empty($user->currency_code);
        $onboardingHasClient   = $totalClients > 0;
        $onboardingHasInvoice  = Invoice::where('user_id', $user->id)->exists();
        $showOnboarding = !($onboardingCurrencySet && $onboardingHasClient && $onboardingHasInvoice);

        return view('portal.dashboard', compact(
            'totalIncome',
            'totalExpenses',
            'totalSavings',
            'totalReceivables',
            'totalPayables',
            'netPosition',
            'recentIncomes',
            'recentExpenses',
            'activeProjects',
            'totalClients',
            'pendingInvoices',
            'months',
            'incomeData',
            'expenseData',
            'expensesByCategory',
            'currencyCode',
            'currencySymbol',
            'budgetAlerts',
            'showOnboarding',
            'onboardingCurrencySet',
            'onboardingHasClient',
            'onboardingHasInvoice'
        ));
    }
}
