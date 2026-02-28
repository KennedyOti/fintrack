<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->latest()
            ->paginate(20);

        // Attach this-month spending to each category (avoid N+1 with a single query)
        $year  = now()->year;
        $month = now()->month;

        $spending = Expense::where('user_id', $user->id)
            ->whereNotNull('category_id')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        // Attach spending totals to each category object so the view can use them
        $categories->getCollection()->transform(function ($cat) use ($spending) {
            $cat->spent_this_month = (float) ($spending[$cat->id] ?? 0);
            return $cat;
        });

        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
        $currentMonth   = now()->format('F Y');

        return view('portal.expenses.category.index', compact(
            'categories',
            'currencySymbol',
            'currentMonth'
        ));
    }

    public function create()
    {
        return view('portal.expenses.category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'color'          => 'nullable|string|max:7',
            'monthly_budget' => 'nullable|numeric|min:0|max:999999999',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['type']    = 'expense';

        // Store null when field is empty string
        if (isset($validated['monthly_budget']) && $validated['monthly_budget'] === '') {
            $validated['monthly_budget'] = null;
        }

        Category::create($validated);

        return redirect()->route('expense.categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    public function edit(Category $category)
    {
        $this->authorizeCategory($category);

        return view('portal.expenses.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'color'          => 'nullable|string|max:7',
            'monthly_budget' => 'nullable|numeric|min:0|max:999999999',
        ]);

        if (isset($validated['monthly_budget']) && $validated['monthly_budget'] === '') {
            $validated['monthly_budget'] = null;
        }

        $category->update($validated);

        return redirect()->route('expense.categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        if ($category->expenses()->count() > 0) {
            return redirect()->route('expense.categories.index')
                ->with('error', 'Cannot delete category that is in use.');
        }

        $category->delete();

        return redirect()->route('expense.categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }

    private function authorizeCategory($category)
    {
        if ($category->user_id !== Auth::id() || $category->type !== 'expense') {
            abort(403);
        }
    }
}
