<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
            ->paginate(10);
            
        return view('portal.expenses.category.index', compact('categories'));
    }

    public function create()
    {
        return view('portal.expenses.category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['type'] = 'expense';
        
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
            'name' => 'required|string|max:150',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($validated);
        
        return redirect()->route('expense.categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);
        
        // Check if category is in use
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
