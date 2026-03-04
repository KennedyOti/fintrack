<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('sort_order')->get();
        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:500',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:50',
            'meta_title'       => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
        ]);

        $validated['slug']      = BlogCategory::generateSlug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        BlogCategory::create($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    public function update(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:500',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:50',
            'meta_title'       => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
        ]);

        // Regenerate slug only if name changed
        if ($category->name !== $validated['name']) {
            $validated['slug'] = BlogCategory::generateSlug($validated['name'], $category->id);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(BlogCategory $category)
    {
        // Nullify category on posts before deleting
        $category->posts()->update(['category_id' => null]);
        $category->delete();

        return back()->with('success', 'Category deleted. Posts have been uncategorised.');
    }
}
