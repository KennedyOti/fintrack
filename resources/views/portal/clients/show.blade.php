@extends('layouts.portal')

@section('title', 'Client Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
            <span class="text-white fw-bold">{{ substr($client->name, 0, 1) }}</span>
        </div>
        <div>
            <h4 class="mb-0">{{ $client->name }}</h4>
            <p class="text-muted mb-0">{{ $client->company_name ?: 'No company' }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Status Banner -->
@if($client->status === 'inactive')
<div class="alert alert-warning d-flex align-items-center mb-4">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>This client is marked as inactive</strong>
</div>
@endif

<!-- Client Info Cards -->
<div class="row g-4 mb-4">
    <!-- Contact Information -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-address-card me-2 text-primary"></i>Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="text-muted mb-1 small">Email</p>
                    <p class="mb-0">
                        <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                            {{ $client->email }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1 small">Phone</p>
                    <p class="mb-0">{{ $client->phone ?: '-' }}</p>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1 small">Address</p>
                    <p class="mb-0">{{ $client->address ?: '-' }}</p>
                </div>
                <div class="mb-0">
                    <p class="text-muted mb-1 small">Tax ID</p>
                    <p class="mb-0">{{ $client->tax_number ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-lg-8">
        <div class="row g-4 h-100">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-primary-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-folder-open text-primary"></i>
                        </div>
                        <h4 class="mb-0">{{ $client->projects->count() }}</h4>
                        <p class="text-muted mb-0 small">Projects</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-success-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-file-invoice-dollar text-success"></i>
                        </div>
                        <h4 class="mb-0">{{ number_format($client->totalInvoiced(), 2) }}</h4>
                        <p class="text-muted mb-0 small">Total Invoiced</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-info-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-check-circle text-info"></i>
                        </div>
                        <h4 class="mb-0">{{ number_format($client->totalPaid(), 2) }}</h4>
                        <p class="text-muted mb-0 small">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-warning-subtle rounded p-2 mb-2 d-inline-block">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h4 class="mb-0 {{ $client->outstandingBalance() > 0 ? 'text-warning' : 'text-success' }}">
                            {{ number_format($client->outstandingBalance(), 2) }}
                        </h4>
                        <p class="text-muted mb-0 small">Outstanding</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Projects, Invoices, Debts -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="clientTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                    <i class="fas fa-folder-open me-1"></i> Projects ({{ $client->projects->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Invoices ({{ $client->invoices->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="debts-tab" data-bs-toggle="tab" data-bs-target="#debts" type="button" role="tab">
                    <i class="fas fa-hand-holding-usd me-1"></i> Debts Receivable ({{ $client->debtsReceivable->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">
                    <i class="fas fa-sticky-note me-1"></i> Notes
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="clientTabsContent">
            <!-- Projects Tab -->
            <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                @if($client->projects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>Deadline</th>
                                <th>Budget</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-decoration-none">
                                        {{ $project->title }}
                                    </a>
                                </td>
                                <td>
                                    @switch($project->status)
                                        @case('active')
                                            <span class="badge bg-success">Active</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-primary">Completed</span>
                                            @break
                                        @case('on_hold')
                                            <span class="badge bg-warning">On Hold</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($project->status) }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : '-' }}</td>
                                <td>{{ $project->deadline ? $project->deadline->format('M d, Y') : '-' }}</td>
                                <td>{{ number_format($project->budget, 2) }}</td>
                                <td>
                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-outline-primary">
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
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5>No projects yet</h5>
                    <p class="text-muted">Create a project for this client to get started.</p>
                    <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Project
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Invoices Tab -->
            <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                @if($client->invoices->count() > 0)
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
                            @foreach($client->invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ optional($invoice->invoice_date)->format('M d, Y') }}</td>
                                <td>{{ optional($invoice->due_date)->format('M d, Y') }}</td>
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
                    <p class="text-muted">Create an invoice for this client to get paid.</p>
                    <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Invoice
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Debts Tab -->
            <div class="tab-pane fade" id="debts" role="tabpanel" aria-labelledby="debts-tab">
                @if($client->debtsReceivable->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->debtsReceivable as $debt)
                            <tr>
                                <td>{{ $debt->description }}</td>
                                <td>{{ $debt->debt_date->format('M d, Y') }}</td>
                                <td>{{ $debt->due_date->format('M d, Y') }}</td>
                                <td class="fw-bold">{{ number_format($debt->amount, 2) }}</td>
                                <td>
                                    @switch($debt->status)
                                        @case('paid')
                                            <span class="badge bg-success">Paid</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($debt->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('debts.receivable.show', $debt->id) }}" class="btn btn-sm btn-outline-primary">
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
                    <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                    <h5>No debts receivable</h5>
                    <p class="text-muted">Track money owed by this client.</p>
                </div>
                @endif
            </div>
            
            <!-- Notes Tab -->
            <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                @if($client->notes)
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($client->notes)) !!}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                    <h5>No notes</h5>
                    <p class="text-muted">Add notes about this client for your reference.</p>
                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Add Notes
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Client Meta -->
<div class="mt-4 text-muted text-center">
    <small>
        Client ID: {{ $client->id }} | 
        Created: {{ $client->created_at->format('M d, Y') }} | 
        Last Updated: {{ $client->updated_at->format('M d, Y') }}
    </small>
</div>
@endsection
