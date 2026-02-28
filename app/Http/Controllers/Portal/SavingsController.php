<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Expense;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SavingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status');
        
        $accounts = SavingsAccount::where('user_id', $user->id)
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(10);
            
        // Calculate net income (total income - total expenses)
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $netIncome = $totalIncome - $totalExpenses;
        
        // Get available income for savings (income with remaining amounts)
        $availableIncome = Income::where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->orderBy('income_date', 'desc')
            ->get();
        
        // Get user's currency symbol
        $currencySymbol = CurrencyHelper::getSymbol();
        
        return view('portal.savings.index', compact('accounts', 'search', 'status', 'netIncome', 'availableIncome', 'currencySymbol'));
    }

    public function create()
    {
        return view('portal.savings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,archived',
        ]);

        $validated['user_id'] = Auth::id();
        
        // Set default current_balance to 0 if not provided
        $validated['current_balance'] = $validated['current_balance'] ?? 0;
        
        SavingsAccount::create($validated);
        
        return redirect()->route('savings.index')
            ->with('success', 'Savings account created successfully.');
    }

    public function show(SavingsAccount $saving)
    {
        $savings = $saving;

        // If savings account is null due to route model binding (not found or not owned by user)
        // This will now return 404 because resolveRouteBinding scopes to current user
        if (!$savings) {
            abort(404);
        }

        $savings->load(['transactions' => function ($query) {
            $query->latest();
        }]);
        
        // Get available income for deposits
        $user = Auth::user();
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalSavings = SavingsAccount::where('user_id', $user->id)->sum('current_balance');
        $netIncome = $totalIncome - $totalExpenses;
        
        $availableIncome = Income::where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->orderBy('income_date', 'desc')
            ->get();
        
        // Get user's currency symbol
        $currencySymbol = CurrencyHelper::getSymbol();
        $currencyCode = CurrencyHelper::getUserCurrency();
        
        return view('portal.savings.show', compact('savings', 'netIncome', 'availableIncome', 'currencySymbol', 'currencyCode'));
    }

    public function edit(SavingsAccount $saving)
    {
        $savings = $saving;
        $this->authorizeSavings($savings);

        return view('portal.savings.edit', compact('savings'));
    }

    public function update(Request $request, SavingsAccount $saving)
    {
        $savings = $saving;
        $this->authorizeSavings($savings);
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,archived',
        ]);

        $savings->update($validated);
        
        return redirect()->route('savings.index')
            ->with('success', 'Savings account updated successfully.');
    }

    public function destroy(SavingsAccount $saving)
    {
        $savings = $saving;
        $this->authorizeSavings($savings);

        $savings->delete();
        
        return redirect()->route('savings.index')
            ->with('success', 'Savings account deleted successfully.');
    }

    /**
     * Quick add savings from available income
     * This allows users to quickly save from their positive net income
     */
    public function quickSave(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'savings_account_id' => 'required|exists:savings_accounts,id',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // Calculate net income
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $netIncome = $totalIncome - $totalExpenses;
        
        // Get total current savings
        $totalSavings = SavingsAccount::where('user_id', $user->id)->sum('current_balance');
        
        // Available for savings = Net Income - Already Saved
        $availableForSavings = $netIncome - $totalSavings;
        
        if ($availableForSavings <= 0) {
            return redirect()->back()
                ->with('error', 'You need positive net income to save. Current net income: $' . number_format($netIncome, 2));
        }
        
        if ($validated['amount'] > $availableForSavings) {
            return redirect()->back()
                ->with('error', 'Maximum available for savings is $' . number_format($availableForSavings, 2) . '. You cannot save more than your positive net income.');
        }
        
        $savingsAccount = SavingsAccount::where('id', $validated['savings_account_id'])
            ->where('user_id', $user->id)
            ->first();
            
        if (!$savingsAccount) {
            return redirect()->back()
                ->with('error', 'Savings account not found.');
        }
        
        DB::transaction(function () use ($savingsAccount, $validated, $user) {
            // Create savings transaction
            SavingsTransaction::create([
                'savings_account_id' => $savingsAccount->id,
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $validated['amount'],
                'transaction_date' => now()->format('Y-m-d'),
                'notes' => $validated['notes'] ?? 'Quick savings deposit',
            ]);

            // Update savings account balance
            $savingsAccount->update([
                'current_balance' => $savingsAccount->current_balance + $validated['amount'],
            ]);
        });

        return redirect()->route('savings.show', $savingsAccount->id)
            ->with('success', 'Quick savings added successfully! You saved $' . number_format($validated['amount'], 2));
    }

    public function deposit(Request $request, SavingsAccount $savings)
    {
        $this->authorizeSavings($savings);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'income_id' => 'required|exists:incomes,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Verify the income belongs to the user
        $income = Income::where('id', $validated['income_id'])
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$income) {
            return redirect()->back()
                ->with('error', 'Invalid income source selected.');
        }
        
        // Calculate available amount from this income
        $totalSavedFromIncome = SavingsTransaction::whereHas('savingsAccount', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('income_id', $validated['income_id'])
            ->sum('amount');
            
        $availableFromIncome = $income->amount - $totalSavedFromIncome;
        
        if ($validated['amount'] > $availableFromIncome) {
            return redirect()->back()
                ->with('error', 'Cannot deposit more than available from this income. Available: $' . number_format($availableFromIncome, 2));
        }
        
        DB::transaction(function () use ($savings, $validated) {
            SavingsTransaction::create([
                'savings_account_id' => $savings->id,
                'user_id' => Auth::id(),
                'type' => 'deposit',
                'amount' => $validated['amount'],
                'income_id' => $validated['income_id'],
                'transaction_date' => $validated['transaction_date'],
                'notes' => $validated['notes'],
            ]);

            $savings->update([
                'current_balance' => $savings->current_balance + $validated['amount'],
            ]);
        });

        return redirect()->route('savings.show', $savings->id)
            ->with('success', 'Deposit recorded successfully.');
    }

    public function withdraw(Request $request, SavingsAccount $savings)
    {
        $this->authorizeSavings($savings);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $savings->current_balance,
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($savings, $validated) {
            SavingsTransaction::create([
                'savings_account_id' => $savings->id,
                'user_id' => Auth::id(),
                'type' => 'withdrawal',
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'notes' => $validated['notes'],
            ]);

            $savings->update([
                'current_balance' => $savings->current_balance - $validated['amount'],
            ]);
        });

        return redirect()->route('savings.show', $savings->id)
            ->with('success', 'Withdrawal recorded successfully.');
    }

    private function authorizeSavings($savings)
    {
        // If savings is null due to route model binding, abort 404
        // Otherwise, the route binding already ensures it belongs to the current user
        if (!$savings) {
            abort(404);
        }
    }
}
