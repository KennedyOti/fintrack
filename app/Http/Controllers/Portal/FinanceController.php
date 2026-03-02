<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Expense;
use App\Models\SavingsAccount;
use App\Models\DebtsReceivable;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Category;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');
        
        // Calculate totals from Income table (this now includes payments from invoices)
        $totalIncome = Income::where('user_id', $user->id)
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('income_date', $year);
            })
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('income_date', $month);
            })
            ->sum('amount');
            
        $totalExpenses = Expense::where('user_id', $user->id)
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('expense_date', $year);
            })
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('expense_date', $month);
            })
            ->sum('amount');
            
        $totalSavings = SavingsAccount::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('current_balance');
        
        // Total receivables: Only include invoices that are sent/partial/overdue (not draft, not paid)
        // This represents money owed to the business but not yet received
        $totalReceivables = DebtsReceivable::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'partial'])
            ->sum(DB::raw('original_amount - paid_amount'));
            
        $totalPayables = 0; // Temporarily disabled - debts_payables table doesn't exist
        // $totalPayables = DebtsPayable::where('user_id', $user->id)
        //     ->whereIn('status', ['pending', 'partial'])
        //     ->sum(DB::raw('original_amount - paid_amount'));
        
        // Net Position = Total Income + Total Savings + Total Receivables - Total Payables - Total Expenses
        // Note: Draft and sent invoices are NOT included in net position calculation
        // Only paid invoices (via Income table) and pending receivables are included
        $netPosition = $totalIncome - $totalExpenses + $totalSavings + $totalReceivables - $totalPayables;
        
        // Monthly trend data
        $monthlyIncome = Income::where('user_id', $user->id)
            ->whereYear('income_date', $year)
            ->selectRaw('MONTH(income_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
            
        $monthlyExpenses = Expense::where('user_id', $user->id)
            ->whereYear('expense_date', $year)
            ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $incomeData[] = isset($monthlyIncome[$i]) ? $monthlyIncome[$i] : 0;
            $expenseData[] = isset($monthlyExpenses[$i]) ? $monthlyExpenses[$i] : 0;
        }
        
        // Income by category
        $incomeByCategory = Income::where('user_id', $user->id)
            ->whereYear('income_date', $year)
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('income_date', $month);
            })
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'total' => $items->sum('amount'),
                    'name' => $items->first()->category ? $items->first()->category->name : 'Uncategorized',
                    'color' => $items->first()->category ? $items->first()->category->color : '#6c757d',
                ];
            })
            ->values();
        
        // Expense by category
        $expenseByCategory = Expense::where('user_id', $user->id)
            ->whereYear('expense_date', $year)
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('expense_date', $month);
            })
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'total' => $items->sum('amount'),
                    'name' => $items->first()->category ? $items->first()->category->name : 'Uncategorized',
                    'color' => $items->first()->category ? $items->first()->category->color : '#6c757d',
                ];
            })
            ->values();
        
        // Top clients by revenue: invoice payments (paid_amount) + direct income (not linked to an invoice)
        $topClients = Client::where('user_id', $user->id)
            ->withSum(['invoices as invoice_revenue' => function ($query) use ($year, $month) {
                $query->whereYear('issue_date', $year)
                    ->whereIn('status', ['paid', 'partial']);
                if ($month) {
                    $query->whereMonth('issue_date', $month);
                }
            }], 'paid_amount')
            ->withSum(['incomes as direct_income' => function ($query) use ($year, $month) {
                $query->whereYear('income_date', $year)
                    ->whereNull('invoice_id'); // exclude invoice-generated income to avoid double-counting
                if ($month) {
                    $query->whereMonth('income_date', $month);
                }
            }], 'amount')
            ->get()
            ->map(function ($client) {
                $client->total_revenue = ($client->invoice_revenue ?? 0) + ($client->direct_income ?? 0);
                return $client;
            })
            ->sortByDesc('total_revenue')
            ->take(5);
        
        // Outstanding invoices
        $outstandingInvoices = Invoice::where('user_id', $user->id)
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->with('client')
            ->latest()
            ->take(5)
            ->get();
        
        // Get user's currency
        $currencyCode = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);
        
        return view('portal.finances.index', compact(
            'year',
            'month',
            'totalIncome',
            'totalExpenses',
            'totalSavings',
            'totalReceivables',
            'totalPayables',
            'netPosition',
            'months',
            'incomeData',
            'expenseData',
            'incomeByCategory',
            'expenseByCategory',
            'topClients',
            'outstandingInvoices',
            'currencyCode',
            'currencySymbol'
        ));
    }
}
