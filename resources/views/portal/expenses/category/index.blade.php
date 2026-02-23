@extends('layouts.portal')

@section('title', 'Expense Categories - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Expense Categories</h4>
        <p class="text-muted mb-0">Manage your expense categories</p>
    </div>
    <div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back to Expenses
        </a>
        <a href="{{ route('expense.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Category
        </a>
    </div>
</div>

<!-- Categories Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>
                            @if($category->color)
                            <span class="badge me-2" style="background-color: {{ $category->color }}; width: 12px; height: 12px; display: inline-block;"></span>
                            @endif
                            {{ $category->name }}
                        </td>
                        <td>
                            @if($category->color)
                            <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                {{ $category->color }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('expense.categories.edit', $category->id) }}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('expense.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">No expense categories yet.</p>
            <a href="{{ route('expense.categories.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-1"></i> Create Your First Category
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($categories->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $categories->links() }}
</div>
@endif
@endsection
