<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Category;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $category_id = $request->get('category_id');
        $income_id = $request->get('income_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        
        $expenses = Expense::where('user_id', $user->id)
            ->with(['project', 'income', 'category'])
            ->when($search, function ($query) use ($search) {
                return $query->where('notes', 'like', "%{$search}%")
                    ->orWhere('vendor_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            })
            ->when($category_id, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($income_id, function ($query) use ($income_id) {
                return $query->where('income_id', $income_id);
            })
            ->when($date_from, function ($query) use ($date_from) {
                return $query->whereDate('expense_date', '>=', $date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                return $query->whereDate('expense_date', '<=', $date_to);
            })
            ->latest()
            ->paginate(10);
            
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
            
        // Get all incomes with their spent amounts
        $incomes = Income::where('user_id', $user->id)
            ->withSum('expenses', 'amount')
            ->get()
            ->filter(function ($income) {
                // Only include incomes that have not been fully spent
                return $income->amount > ($income->expenses_sum_amount ?? 0);
            });
            
        return view('portal.expenses.index', compact('expenses', 'categories', 'incomes', 'search', 'category_id', 'income_id', 'date_from', 'date_to'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
            
        // Get all incomes with their spent amounts - only unspent incomes
        $incomes = Income::where('user_id', $user->id)
            ->withSum('expenses', 'amount')
            ->get()
            ->filter(function ($income) {
                // Only include incomes that have not been fully spent
                return $income->amount > ($income->expenses_sum_amount ?? 0);
            });
            
        return view('portal.expenses.create', compact('categories', 'incomes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_id' => 'nullable|exists:incomes,id',
            'category_id' => 'nullable|exists:categories,id',
            'vendor_name' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,card,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        
        Expense::create($validated);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorizeExpense($expense);
        
        $expense->load(['project', 'income', 'category']);
        
        return view('portal.expenses.show', compact('expense'));
    }
    
    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);
        
        $user = Auth::user();
        
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
            
        // Get all incomes with their spent amounts - only unspent incomes
        // Also include the current expense's income (in case it was partially spent)
        $incomes = Income::where('user_id', $user->id)
            ->withSum('expenses', 'amount')
            ->get()
            ->filter(function ($income) use ($expense) {
                // Include if not fully spent OR if this is the income linked to the current expense
                $isFullySpent = $income->amount <= ($income->expenses_sum_amount ?? 0);
                $isCurrentIncome = $expense->income_id == $income->id;
                return !$isFullySpent || $isCurrentIncome;
            });
            
        return view('portal.expenses.edit', compact('expense', 'categories', 'incomes'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);
        
        $validated = $request->validate([
            'income_id' => 'nullable|exists:incomes,id',
            'category_id' => 'nullable|exists:categories,id',
            'vendor_name' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,card,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);
        
        $expense->delete();
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    private function authorizeExpense($expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
