@extends('layouts.portal')

@section('title', 'Project Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
            <span class="text-white fw-bold">{{ substr($project->title, 0, 1) }}</span>
        </div>
        <div>
            <h4 class="mb-0">{{ $project->title }}</h4>
            <p class="text-muted mb-0">
                @if($project->client)
                    <a href="{{ route('clients.show', $project->client->id) }}" class="text-decoration-none">
                        {{ $project->client->name }}
                    </a>
                @else
                    No client assigned
                @endif
            </p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Status Banner -->
@switch($project->status)
    @case('on_hold')
    <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="fas fa-pause-circle me-2"></i>
        <strong>This project is currently on hold</strong>
    </div>
    @break
    @case('cancelled')
    <div class="alert alert-danger d-flex align-items-center mb-4">
        <i class="fas fa-times-circle me-2"></i>
        <strong>This project has been cancelled</strong>
    </div>
    @break
    @case('completed')
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fas fa-check-circle me-2"></i>
        <strong>This project has been completed</strong>
    </div>
    @break
@endswitch

<!-- Project Overview Cards -->
<div class="row g-4 mb-4">
    <!-- Progress Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Progress</h5>
            </div>
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="12"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#28a745" stroke-width="12"
                                stroke-dasharray="{{ $project->progress_percent * 3.14 }} 314" 
                                stroke-linecap="round" 
                                transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <h3 class="mb-0">{{ $project->progress_percent }}%</h3>
                    </div>
                </div>
                <div class="mt-3">
                    @switch($project->status)
                        @case('planned')
                            <span class="badge bg-secondary">Planned</span>
                            @break
                        @case('in_progress')
                            <span class="badge bg-primary">In Progress</span>
                            @break
                        @case('on_hold')
                            <span class="badge bg-warning">On Hold</span>
                            @break
                        @case('completed')
                            <span class="badge bg-success">Completed</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Overview -->
    <div class="col-lg-8">
        <div class="row g-4 h-100">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-primary-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-wallet text-primary"></i>
                        </div>
                        <h4 class="mb-0">{{ number_format($project->budget, 2) }}</h4>
                        <p class="text-muted mb-0 small">Budget</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-success-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-arrow-up text-success"></i>
                        </div>
                        <h4 class="mb-0">{{ number_format($project->totalIncome(), 2) }}</h4>
                        <p class="text-muted mb-0 small">Income</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-danger-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-arrow-down text-danger"></i>
                        </div>
                        <h4 class="mb-0">{{ number_format($project->totalExpenses(), 2) }}</h4>
                        <p class="text-muted mb-0 small">Expenses</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-info-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                        <h4 class="mb-0 {{ $project->profit() >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($project->profit(), 2) }}
                        </h4>
                        <p class="text-muted mb-0 small">Profit/Loss</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Project Details and Tabs -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="projectTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                    <i class="fas fa-info-circle me-1"></i></button>
             Details
                </li>
            <li class="nav-item">
                <button class="nav-link" id="milestones-tab" data-bs-toggle="tab" data-bs-target="#milestones" type="button" role="tab">
                    <i class="fas fa-flag me-1"></i> Milestones ({{ $project->milestones->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="income-tab" data-bs-toggle="tab" data-bs-target="#income" type="button" role="tab">
                    <i class="fas fa-arrow-up me-1"></i> Income ({{ $project->incomes->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">
                    <i class="fas fa-arrow-down me-1"></i> Expenses ({{ $project->expenses->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Invoices ({{ $project->invoices->count() }})
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="projectTabsContent">
            <!-- Details Tab -->
            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Project Title</p>
                            <p class="mb-0 fw-semibold">{{ $project->title }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Client</p>
                            <p class="mb-0">
                                @if($project->client)
                                    <a href="{{ route('clients.show', $project->client->id) }}" class="text-decoration-none">
                                        {{ $project->client->name }}
                                    </a>
                                @else
                                    <span class="text-muted">No client assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Start Date</p>
                            <p class="mb-0">{{ $project->start_date ? $project->start_date->format('M d, Y') : '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Deadline</p>
                            <p class="mb-0">{{ $project->deadline ? $project->deadline->format('M d, Y') : '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Budget</p>
                            <p class="mb-0 fw-semibold">{{ number_format($project->budget, 2) }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Budget Remaining</p>
                            <p class="mb-0 {{ $project->budgetRemaining() < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($project->budgetRemaining(), 2) }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted mb-1 small">Progress</p>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $project->progress_percent }}%"></div>
                                </div>
                                <span>{{ $project->progress_percent }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-0">
                            <p class="text-muted mb-1 small">Description</p>
                            <p class="mb-0">{{ $project->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Milestones Tab -->
            <div class="tab-pane fade" id="milestones" role="tabpanel" aria-labelledby="milestones-tab">
                @if($project->milestones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->milestones as $milestone)
                            <tr>
                                <td>{{ $milestone->title }}</td>
                                <td>{{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : '-' }}</td>
                                <td>{{ number_format($milestone->amount, 2) }}</td>
                                <td>
                                    @switch($milestone->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('in_progress')
                                            <span class="badge bg-primary">In Progress</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Completed</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($milestone->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                    <h5>No milestones yet</h5>
                    <p class="text-muted">Add milestones to track project phases.</p>
                </div>
                @endif
            </div>
            
            <!-- Income Tab -->
            <div class="tab-pane fade" id="income" role="tabpanel" aria-labelledby="income-tab">
                @if($project->incomes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->incomes as $income)
                            <tr>
                                <td>{{ $income->date ? $income->date->format('M d, Y') : '-' }}</td>
                                <td>{{ $income->description }}</td>
                                <td>{{ $income->category->name ?? '-' }}</td>
                                <td class="text-success fw-bold">{{ number_format($income->amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('income.show', $income->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3">Total Income</td>
                                <td class="text-success">{{ number_format($project->totalIncome(), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                    <h5>No income recorded</h5>
                    <p class="text-muted">Record income for this project.</p>
                    <a href="{{ route('income.create') }}?project_id={{ $project->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Income
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Expenses Tab -->
            <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
                @if($project->expenses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->expenses as $expense)
                            <tr>
                                <td>{{ optional($expense->date)->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ $expense->category->name ?? '-' }}</td>
                                <td class="text-danger fw-bold">{{ number_format($expense->amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3">Total Expenses</td>
                                <td class="text-danger">{{ number_format($project->totalExpenses(), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-arrow-down fa-3x text-muted mb-3"></i>
                    <h5>No expenses recorded</h5>
                    <p class="text-muted">Track project expenses here.</p>
                    <a href="{{ route('expenses.create') }}?project_id={{ $project->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Expense
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Invoices Tab -->
            <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                @if($project->invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ optional($invoice->invoice_date)->format('M d, Y') ?? '-' }}</td>
                                <td>{{ optional($invoice->due_date)->format('M d, Y') ?? '-' }}</td>
                                <td class="fw-bold">{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                <td>
                                    @switch($invoice->status)
                                        @case('paid')
                                            <span class="badge bg-success">Paid</span>
                                            @break
                                        @case('partial')
                                            <span class="badge bg-warning">Partial</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                            @break
                                        @case('draft')
                                            <span class="badge bg-secondary">Draft</span>
                                            @break
                                        @default
                                            <span class="badge bg-info">{{ ucfirst($invoice->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5>No invoices yet</h5>
                    <p class="text-muted">Create invoices for this project.</p>
                    <a href="{{ route('invoices.create') }}?project_id={{ $project->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Invoice
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Project Meta -->
<div class="mt-4 text-muted text-center">
    <small>
        Project ID: {{ $project->id }} | 
        Created: {{ $project->created_at->format('M d, Y') }} | 
        Last Updated: {{ $project->updated_at->format('M d, Y') }}
    </small>
</div>
@endsection
