<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Category;
use App\Models\Income;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            ->paginate(15)
            ->withQueryString();

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

        // ── Budget overview for the current month ────────────────────────
        $budgetYear  = now()->year;
        $budgetMonth = now()->month;

        $budgetCategories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereNotNull('monthly_budget')
            ->where('monthly_budget', '>', 0)
            ->get();

        if ($budgetCategories->isNotEmpty()) {
            $budgetSpending = Expense::where('user_id', $user->id)
                ->whereIn('category_id', $budgetCategories->pluck('id'))
                ->whereYear('expense_date', $budgetYear)
                ->whereMonth('expense_date', $budgetMonth)
                ->selectRaw('category_id, SUM(amount) as total')
                ->groupBy('category_id')
                ->pluck('total', 'category_id');

            $budgetCategories->transform(function ($cat) use ($budgetSpending) {
                $cat->spent_this_month = (float) ($budgetSpending[$cat->id] ?? 0);
                return $cat;
            });
        }

        $currencySymbol  = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
        $currentMonth    = now()->format('F Y');

        return view('portal.expenses.index', compact(
            'expenses', 'categories', 'incomes',
            'search', 'category_id', 'income_id', 'date_from', 'date_to',
            'budgetCategories', 'currencySymbol', 'currentMonth'
        ));
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
            'income_id'        => 'nullable|exists:incomes,id',
            'category_id'      => 'nullable|exists:categories,id',
            'vendor_name'      => 'nullable|string|max:150',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'payment_method'   => 'required|in:cash,bank_transfer,mpesa,card,other',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'receipt'          => 'nullable|file|mimes:jpeg,png,gif,webp,pdf|max:5120',
        ]);

        $validated['user_id'] = Auth::id();
        unset($validated['receipt']);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')
                ->store('receipts/' . Auth::id(), 'private');
        }

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
            'income_id'        => 'nullable|exists:incomes,id',
            'category_id'      => 'nullable|exists:categories,id',
            'vendor_name'      => 'nullable|string|max:150',
            'amount'           => 'required|numeric|min:0',
            'expense_date'     => 'required|date',
            'payment_method'   => 'required|in:cash,bank_transfer,mpesa,card,other',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'receipt'          => 'nullable|file|mimes:jpeg,png,gif,webp,pdf|max:5120',
            'remove_receipt'   => 'nullable|boolean',
        ]);

        unset($validated['receipt'], $validated['remove_receipt']);

        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('local')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')
                ->store('receipts/' . Auth::id(), 'private');
        } elseif ($request->boolean('remove_receipt') && $expense->receipt_path) {
            Storage::disk('local')->delete($expense->receipt_path);
            $validated['receipt_path'] = null;
        }

        $expense->update($validated);

        return redirect()->route('expenses.show', $expense->id)
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);

        // Clean up receipt file
        if ($expense->receipt_path) {
            Storage::disk('local')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Serve the receipt file securely (ownership-checked).
     */
    public function receiptView(Expense $expense)
    {
        $this->authorizeExpense($expense);

        if (!$expense->receipt_path || !Storage::disk('local')->exists($expense->receipt_path)) {
            abort(404, 'Receipt not found.');
        }

        $path     = Storage::disk('local')->path($expense->receipt_path);
        $mimeType = mime_content_type($path);
        $filename = basename($expense->receipt_path);

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Delete just the receipt attachment from an expense.
     */
    public function receiptDelete(Expense $expense)
    {
        $this->authorizeExpense($expense);

        if ($expense->receipt_path) {
            Storage::disk('local')->delete($expense->receipt_path);
            $expense->update(['receipt_path' => null]);
        }

        return back()->with('success', 'Receipt removed successfully.');
    }

    private function authorizeExpense($expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
