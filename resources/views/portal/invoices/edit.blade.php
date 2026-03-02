@extends('layouts.portal')

@section('title', 'Edit Invoice - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Edit Invoice</h4>
        <p class="text-muted mb-0">Update invoice #{{ $invoice->invoice_number }}</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<!-- Form -->
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" id="invoice-form">
            @csrf
            @method('PUT')

            <!-- Invoice Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Invoice Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client *</label>
                            <select class="form-select @error('client_id') is-invalid @enderror"
                                    id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} @if($client->company_name)({{ $client->company_name }})@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="invoice_number" class="form-label">Invoice Number *</label>
                            <input type="text" class="form-control"
                                   id="invoice_number" name="invoice_number"
                                   value="{{ old('invoice_number', $invoice->invoice_number) }}" readonly>
                            <small class="text-muted">Invoice number cannot be changed</small>
                        </div>
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project</label>
                            <select class="form-select @error('project_id') is-invalid @enderror"
                                    id="project_id" name="project_id">
                                <option value="">Select Project (Optional)</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $invoice->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ old('status', $invoice->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="partial" {{ old('status', $invoice->status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ old('status', $invoice->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="issue_date" class="form-label">Issue Date *</label>
                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                                   id="issue_date" name="issue_date" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date *</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                   id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items (Editable) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Invoice Items</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="add-item">
                        <i class="fas fa-plus me-1"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%">Description</th>
                                    <th style="width: 15%">Quantity</th>
                                    <th style="width: 20%">Unit Price</th>
                                    <th style="width: 20%">Total</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                    @error('items')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="4"
                                  placeholder="Add any additional notes or terms...">{{ old('notes', $invoice->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> Update Invoice
                </button>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    Cancel
                </a>
            </div>

            <!-- Hidden inputs for calculated totals (updated by JS) -->
            <input type="hidden" name="subtotal" id="input-subtotal" value="{{ $invoice->subtotal }}">
            <input type="hidden" name="tax_amount" id="input-tax-amount" value="{{ $invoice->tax_amount }}">
            <input type="hidden" name="discount_amount" id="input-discount-amount" value="{{ $invoice->discount_amount }}">
            <input type="hidden" name="total_amount" id="input-total-amount" value="{{ $invoice->total_amount }}">

        </form>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">

        <!-- Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold" id="summary-subtotal">{{ $currencySymbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tax:</span>
                    <span class="fw-semibold" id="summary-tax">{{ $currencySymbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount:</span>
                    <span class="fw-semibold" id="summary-discount">{{ $currencySymbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary" id="summary-total">{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Tax & Discount -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Tax & Discount</h5>
            </div>
            <div class="card-body">
                @php
                    $taxRate = 0;
                    if ($invoice->subtotal > 0) {
                        $taxRate = ($invoice->tax_amount / $invoice->subtotal) * 100;
                    }
                @endphp
                <div class="mb-3">
                    <label for="tax-rate" class="form-label">Tax Rate (%)</label>
                    <input type="number" class="form-control" id="tax-rate" value="{{ number_format($taxRate, 2) }}" min="0" step="0.01">
                </div>
                <div class="mb-3">
                    <label for="discount-input" class="form-label">Discount Amount ({{ $currencySymbol }})</label>
                    <input type="number" class="form-control" id="discount-input" value="{{ number_format($invoice->discount_amount, 2) }}" min="0" step="0.01">
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        @if($invoice->payments()->sum('amount') > 0)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Payment Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Paid:</span>
                    <span class="fw-semibold text-success">{{ $currencySymbol }}{{ number_format($invoice->payments()->sum('amount'), 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Outstanding:</span>
                    <span class="fw-bold text-danger">{{ $currencySymbol }}{{ number_format($invoice->total_amount - $invoice->payments()->sum('amount'), 2) }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Invoice Actions -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-eye me-2"></i> View Invoice
                </a>
                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline w-100">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this invoice?')">
                        <i class="fas fa-trash me-2"></i> Delete Invoice
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- JavaScript for dynamic items -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsTbody = document.getElementById('items-tbody');
    const addItemBtn  = document.getElementById('add-item');
    const taxRateInput  = document.getElementById('tax-rate');
    const discountInput = document.getElementById('discount-input');
    const currencySymbol = @json($currencySymbol);

    // Load existing items from server
    @foreach($invoice->items as $item)
    addItemRow({{ $item->id }}, @json($item->description), {{ $item->quantity }}, {{ $item->unit_price }});
    @endforeach

    addItemBtn.addEventListener('click', function () {
        addItemRow();
    });

    function addItemRow(id = null, description = '', quantity = 1, price = 0) {
        const rowId = id || Date.now();
        const row   = document.createElement('tr');
        row.id = 'item-row-' + rowId;

        row.innerHTML = `
            <td><input type="text"   class="form-control item-desc"     name="items[${rowId}][description]" placeholder="Item description" required></td>
            <td><input type="number" class="form-control item-quantity"  name="items[${rowId}][quantity]"    value="${quantity}" min="1"  step="1"    required></td>
            <td><input type="number" class="form-control item-price"     name="items[${rowId}][unit_price]"  value="${price}"    min="0"  step="0.01" required></td>
            <td><input type="number" class="form-control item-total"     name="items[${rowId}][total]"       value="${(quantity * price).toFixed(2)}" min="0" step="0.01" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-times"></i></button></td>
        `;

        // Set description safely via DOM property (avoids HTML-escaping issues)
        row.querySelector('.item-desc').value = description;

        row.querySelector('.remove-item').addEventListener('click', function () {
            row.remove();
            calculateTotals();
        });

        row.querySelector('.item-quantity').addEventListener('input', calculateTotals);
        row.querySelector('.item-price').addEventListener('input', calculateTotals);

        itemsTbody.appendChild(row);
    }

    function calculateTotals() {
        let subtotal = 0;

        itemsTbody.querySelectorAll('tr').forEach(function (row) {
            const qty   = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value)    || 0;
            const total = qty * price;
            row.querySelector('.item-total').value = total.toFixed(2);
            subtotal += total;
        });

        const taxRate  = parseFloat(taxRateInput.value)  || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const taxAmt   = subtotal * (taxRate / 100);
        const totalAmt = subtotal + taxAmt - discount;

        document.getElementById('summary-subtotal').textContent = currencySymbol + subtotal.toFixed(2);
        document.getElementById('summary-tax').textContent      = currencySymbol + taxAmt.toFixed(2);
        document.getElementById('summary-discount').textContent = currencySymbol + discount.toFixed(2);
        document.getElementById('summary-total').textContent    = currencySymbol + totalAmt.toFixed(2);

        document.getElementById('input-subtotal').value         = subtotal.toFixed(2);
        document.getElementById('input-tax-amount').value       = taxAmt.toFixed(2);
        document.getElementById('input-discount-amount').value  = discount.toFixed(2);
        document.getElementById('input-total-amount').value     = totalAmt.toFixed(2);
    }

    taxRateInput.addEventListener('input', calculateTotals);
    discountInput.addEventListener('input', calculateTotals);
});
</script>
@endsection
