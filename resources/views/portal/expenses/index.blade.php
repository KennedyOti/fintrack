@extends('layouts.portal')

@section('title', 'Expenses - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Expenses</h4>
        <p class="text-muted mb-0">Track all your expenses</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('expense.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-tags me-1"></i> Categories
        </a>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Expense
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="income_id" class="form-select">
                    <option value="">All Income Sources</option>
                    @foreach($incomes as $income)
                    <option value="{{ $income->id }}" {{ $income_id == $income->id ? 'selected' : '' }}>
                        {{ number_format($income->amount, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ $date_from }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ $date_to }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($expenses->count() > 0)
        <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td>
                            @if($expense->category)
                            <span class="badge" style="background-color: {{ $expense->category->color ?? '#6c757d' }};">
                                {{ $expense->category->name }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $expense->vendor_name ?: '-' }}</td>
                        <td class="fw-bold text-danger">{{ number_format($expense->amount, 2) }}</td>
                        <td>
                            @switch($expense->payment_method)
                            @case('cash')
                            <span class="badge bg-info">Cash</span>
                            @break
                            @case('bank_transfer')
                            <span class="badge bg-primary">Bank Transfer</span>
                            @break
                            @case('mpesa')
                            <span class="badge bg-success">M-Pesa</span>
                            @break
                            @case('card')
                            <span class="badge bg-warning">Card</span>
                            @break
                            @default
                            <span class="badge bg-secondary">{{ ucfirst($expense->payment_method) }}</span>
                            @endswitch
                        </td>
                        <td>{{ $expense->reference_number ?: '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('expenses.show', $expense->id) }}">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('expenses.edit', $expense->id) }}">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger btn-delete">
                                                <i class="fas fa-trash me-2"></i>Delete
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
        @else
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <h5>No expense records found</h5>
            <p class="text-muted">Start tracking your expenses by adding your first entry.</p>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Expense
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($expenses->hasPages())
<div class="mt-4">
    {{ $expenses->links() }}
</div>
@endif
@endsection
