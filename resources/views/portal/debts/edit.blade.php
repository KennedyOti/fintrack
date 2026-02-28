@extends('layouts.portal')

@php
    $isReceivable = request()->routeIs('debts.receivable.*');
    $type         = $isReceivable ? 'receivable' : 'payable';
    $color        = $isReceivable ? 'success' : 'danger';
@endphp

@section('title', 'Edit ' . ($isReceivable ? 'Receivable' : 'Payable') . ' - FinTrack')

@section('styles')
<style>
.form-card { border-radius:14px; border:none; }
.form-card .card-header { border-radius:14px 14px 0 0; border-bottom:1px solid #f0f0f0; background:#fff; padding:1.2rem 1.5rem; }
.form-label { font-weight:600; font-size:.85rem; color:#495057; margin-bottom:.35rem; }
.form-control, .form-select { border-radius:8px; padding:.55rem .9rem; font-size:.92rem; border-color:#dee2e6; }
.form-control:focus, .form-select:focus { border-color: {{ $isReceivable ? '#198754' : '#dc3545' }}; box-shadow:0 0 0 .2rem rgba({{ $isReceivable ? '25,135,84' : '220,53,69' }},.15); }
.section-header { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#6c757d; }
.info-box { border-radius:10px; border:1px solid; padding:1rem 1.25rem; }
.current-val { background:#f8f9fa; border-radius:8px; padding:.6rem 1rem; font-size:.88rem; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="{{ route('debts.'.$type.'.index') }}" class="text-decoration-none text-muted">
                        {{ $isReceivable ? 'Receivables' : 'Payables' }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('debts.'.$type.'.show', $debt) }}" class="text-decoration-none text-muted">
                        #{{ $debt->id }}
                    </a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h4 class="mb-0">
            <i class="fas fa-{{ $isReceivable ? 'hand-holding-usd text-success' : 'file-invoice-dollar text-danger' }} me-2"></i>
            Edit {{ $isReceivable ? 'Receivable' : 'Payable' }}
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('debts.'.$type.'.show', $debt) }}" class="btn btn-outline-secondary">
            <i class="fas fa-eye me-1"></i> View
        </a>
        <a href="{{ route('debts.'.$type.'.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Form --}}
    <div class="col-lg-8">
        <form method="POST"
              action="{{ $isReceivable ? route('debts.receivable.update', $debt) : route('debts.payable.update', $debt) }}"
              class="needs-validation" novalidate>
            @csrf
            @method('PUT')

            <div class="card form-card shadow-sm mb-4">
                <div class="card-header">
                    <span class="section-header">
                        <i class="fas fa-pencil-alt me-1"></i>
                        {{ $isReceivable ? 'Receivable' : 'Payable' }} Details
                    </span>
                </div>
                <div class="card-body p-4">
                    @if($isReceivable)
                    {{-- Receivable: Client + Invoice --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror"
                                    id="client_id" name="client_id" required>
                                <option value="">— Select Client —</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                            {{ old('client_id', $debt->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                        @if($client->company_name) ({{ $client->company_name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="invoice_id" class="form-label">Linked Invoice</label>
                            <select class="form-select @error('invoice_id') is-invalid @enderror"
                                    id="invoice_id" name="invoice_id">
                                <option value="">— No Invoice (Manual) —</option>
                                @if($debt->invoice)
                                    <option value="{{ $debt->invoice->id }}" selected>
                                        {{ $debt->invoice->invoice_number }}
                                    </option>
                                @endif
                            </select>
                            @error('invoice_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional — link to an existing invoice</div>
                        </div>
                    </div>
                    @else
                    {{-- Payable: Vendor Name --}}
                    <div class="mb-3">
                        <label for="vendor_name" class="form-label">Vendor / Creditor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                               id="vendor_name" name="vendor_name"
                               placeholder="e.g. Supplier Co., Bank Loan, Landlord…"
                               value="{{ old('vendor_name', $debt->vendor_name) }}" required>
                        @error('vendor_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    {{-- Amount --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="original_amount" class="form-label">
                                Total Amount <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-semibold">
                                    {{ $isReceivable ? 'Owed to you' : 'You owe' }}
                                </span>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('original_amount') is-invalid @enderror"
                                       id="original_amount" name="original_amount"
                                       placeholder="0.00"
                                       value="{{ old('original_amount', $debt->original_amount) }}" required>
                                @error('original_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                   id="due_date" name="due_date"
                                   value="{{ old('due_date', $debt->due_date->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Paid Amount (read-only info) --}}
                    <div class="mb-3">
                        <label class="form-label">Amount Already Paid</label>
                        <div class="current-val d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-{{ $color }}"></i>
                            <span class="fw-semibold text-{{ $color }}">
                                {{ number_format($debt->paid_amount, 2) }}
                            </span>
                            <span class="text-muted small">
                                — {{ $debt->paid_amount > 0 ? number_format(($debt->paid_amount / $debt->original_amount) * 100, 1) . '% paid' : 'nothing paid yet' }}
                            </span>
                        </div>
                        <div class="form-text">Paid amount is updated automatically via payments — not editable here.</div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach(['pending' => ['warning', 'clock', 'Pending'],
                                      'partial'  => ['info',    'adjust', 'Partially Paid'],
                                      'paid'     => ['success', 'check-circle', 'Paid'],
                                      'overdue'  => ['danger',  'exclamation-circle', 'Overdue']] as $val => $opt)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                       id="status_{{ $val }}" value="{{ $val }}"
                                       {{ old('status', $debt->status) == $val ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_{{ $val }}">
                                    <span class="badge bg-{{ $opt[0] }}-subtle text-{{ $opt[0] }} border border-{{ $opt[0] }}-subtle px-2">
                                        <i class="fas fa-{{ $opt[1] }} me-1"></i>{{ $opt[2] }}
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-0">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Any additional details…">{{ old('notes', $debt->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-{{ $color }} px-4">
                    <i class="fas fa-save me-2"></i> Update {{ $isReceivable ? 'Receivable' : 'Payable' }}
                </button>
                <a href="{{ route('debts.'.$type.'.show', $debt) }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Side Panel --}}
    <div class="col-lg-4">
        {{-- Current summary --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-chart-pie me-2 text-{{ $color }}"></i>Current Summary
                </h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Original Amount</span>
                    <span class="fw-semibold">{{ number_format($debt->original_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Paid So Far</span>
                    <span class="fw-semibold text-success">{{ number_format($debt->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Outstanding</span>
                    <span class="fw-semibold text-{{ $color }}">
                        {{ number_format($debt->original_amount - $debt->paid_amount, 2) }}
                    </span>
                </div>
                @php $pct = $debt->original_amount > 0 ? ($debt->paid_amount / $debt->original_amount) * 100 : 0; @endphp
                <div class="progress mt-3" style="height:6px;">
                    <div class="progress-bar bg-{{ $color }}" style="width:{{ min(100,$pct) }}%"></div>
                </div>
                <div class="text-muted small mt-1 text-end">{{ number_format($pct,1) }}% settled</div>
            </div>
        </div>

        {{-- Status Guide --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="fas fa-tag me-2 text-secondary"></i>Status Guide</h6>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="width:100px;text-align:center">Pending</span>
                        <small class="text-muted">Not yet paid at all</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info-subtle text-info border border-info-subtle" style="width:100px;text-align:center">Partial</span>
                        <small class="text-muted">Partially paid</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="width:100px;text-align:center">Paid</span>
                        <small class="text-muted">Fully settled</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="width:100px;text-align:center">Overdue</span>
                        <small class="text-muted">Past due, unpaid</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
