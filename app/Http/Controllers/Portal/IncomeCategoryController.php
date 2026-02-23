<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->orderBy('name')
            ->paginate(10);
            
        return view('portal.income.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('portal.income.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'color' => 'nullable|string|max:20',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['type'] = 'income';
        
        Category::create($validated);
        
        return redirect()->route('income.categories.index')
            ->with('success', 'Income category created successfully.');
    }

    public function edit(Category $category)
    {
        $this->authorizeCategory($category);
        
        return view('portal.income.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'color' => 'nullable|string|max:20',
        ]);

        $category->update($validated);
        
        return redirect()->route('income.categories.index')
            ->with('success', 'Income category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);
        
        // Check if category is in use
        if ($category->incomes()->count() > 0) {
            return redirect()->route('income.categories.index')
                ->with('error', 'Cannot delete category that is in use.');
        }
        
        $category->delete();
        
        return redirect()->route('income.categories.index')
            ->with('success', 'Income category deleted successfully.');
    }

    private function authorizeCategory($category)
    {
        if ($category->user_id !== Auth::id() || $category->type !== 'income') {
            abort(403);
        }
    }
}
