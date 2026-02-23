@extends('layouts.portal')

@section('title', 'Create Invoice - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Create Invoice</h4>
        <p class="text-muted mb-0">Create a new invoice for your client</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<!-- Form -->
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
            @csrf
            
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
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                   id="invoice_number" name="invoice_number" 
                                   value="{{ old('invoice_number', App\Models\Invoice::generateInvoiceNumber()) }}" required>
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id">
                                <option value="">Select Project (Optional)</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="quote_id" class="form-label">Quote</label>
                            <select class="form-select @error('quote_id') is-invalid @enderror" 
                                    id="quote_id" name="quote_id">
                                <option value="">Select Quote (Optional)</option>
                                @foreach($quotes as $quote)
                                    <option value="{{ $quote->id }}" {{ old('quote_id') == $quote->id ? 'selected' : '' }}>
                                        {{ $quote->quote_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('quote_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="issue_date" class="form-label">Issue Date *</label>
                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                   id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date *</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
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
                                <!-- Items will be added here dynamically -->
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
                                  placeholder="Add any additional notes or terms...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> Create Invoice
                </button>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    Cancel
                </a>
            </div>
            
            <!-- Hidden inputs for totals - must be inside the form -->
            <input type="hidden" name="subtotal" id="input-subtotal" value="0">
            <input type="hidden" name="tax_amount" id="input-tax-amount" value="0">
            <input type="hidden" name="discount_amount" id="input-discount-amount" value="0">
            <input type="hidden" name="total_amount" id="input-total-amount" value="0">
            
        </form>
    </div>

    <!-- Summary Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold" id="summary-subtotal">{{ $currencySymbol }}0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tax (0%):</span>
                    <span class="fw-semibold" id="summary-tax">{{ $currencySymbol }}0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount:</span>
                    <span class="fw-semibold" id="summary-discount">{{ $currencySymbol }}0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary" id="summary-total">{{ $currencySymbol }}0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get currency symbol from server-side variable
    const currencySymbol = '{{ $currencySymbol }}';
    
    const itemsTbody = document.getElementById('items-tbody');
    const addItemBtn = document.getElementById('add-item');
    const form = document.getElementById('invoice-form');
    let itemCount = 0;

    function addItem() {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" class="form-control item-description" 
                       name="items[${itemCount}][description]" placeholder="Item description" required>
            </td>
            <td>
                <input type="number" class="form-control item-quantity" 
                       name="items[${itemCount}][quantity]" value="1" min="1" step="0.01" required>
            </td>
            <td>
                <input type="number" class="form-control item-unit-price" 
                       name="items[${itemCount}][unit_price]" value="0" min="0" step="0.01" required>
            </td>
            <td>
                <span class="item-total">{{ $currencySymbol }}0.00</span>
                <input type="hidden" class="item-total-input" name="items[${itemCount}][total]" value="0">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        itemsTbody.appendChild(row);
        itemCount++;

        // Add event listeners
        row.querySelector('.item-quantity').addEventListener('input', calculateTotals);
        row.querySelector('.item-unit-price').addEventListener('input', calculateTotals);
        row.querySelector('.remove-item').addEventListener('click', function() {
            row.remove();
            calculateTotals();
        });
        
        // Trigger initial calculation for the new row
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('#items-tbody tr');
        
        if (rows.length === 0) {
            // Reset all values if no items
            document.getElementById('summary-subtotal').textContent = currencySymbol + '0.00';
            document.getElementById('summary-tax').textContent = currencySymbol + '0.00';
            document.getElementById('summary-discount').textContent = currencySymbol + '0.00';
            document.getElementById('summary-total').textContent = currencySymbol + '0.00';
            document.getElementById('input-subtotal').value = 0;
            document.getElementById('input-tax-amount').value = 0;
            document.getElementById('input-discount-amount').value = 0;
            document.getElementById('input-total-amount').value = 0;
            return;
        }
        
        rows.forEach(function(row) {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-unit-price').value) || 0;
            const total = quantity * unitPrice;
            
            row.querySelector('.item-total').textContent = currencySymbol + total.toFixed(2);
            row.querySelector('.item-total-input').value = total;
            
            subtotal += total;
        });

        const taxRate = 0;
        const tax = subtotal * taxRate;
        const discount = 0;
        const total = subtotal + tax - discount;

        document.getElementById('summary-subtotal').textContent = currencySymbol + subtotal.toFixed(2);
        document.getElementById('summary-tax').textContent = currencySymbol + tax.toFixed(2);
        document.getElementById('summary-discount').textContent = currencySymbol + discount.toFixed(2);
        document.getElementById('summary-total').textContent = currencySymbol + total.toFixed(2);

        document.getElementById('input-subtotal').value = subtotal;
        document.getElementById('input-tax-amount').value = tax;
        document.getElementById('input-discount-amount').value = discount;
        document.getElementById('input-total-amount').value = total;
    }

    addItemBtn.addEventListener('click', addItem);

    // Add initial item
    addItem();

    // Form submission - improved validation
    form.addEventListener('submit', function(e) {
        const rowCount = document.querySelectorAll('#items-tbody tr').length;
        console.log('Form submitting with ' + rowCount + ' items');
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Please add at least one item to the invoice.');
            return false;
        }
        
        // Check if all items have valid data
        let hasEmptyFields = false;
        document.querySelectorAll('#items-tbody tr').forEach(function(row) {
            const desc = row.querySelector('.item-description').value.trim();
            const qty = row.querySelector('.item-quantity').value;
            const price = row.querySelector('.item-unit-price').value;
            
            if (!desc || !qty || !price) {
                hasEmptyFields = true;
            }
        });
        
        if (hasEmptyFields) {
            e.preventDefault();
            alert('Please fill in all item fields (description, quantity, unit price).');
            return false;
        }
        
        // Ensure hidden input values are calculated before submission
        calculateTotals();
        
        // Log the values being submitted
        console.log('Subtotal:', document.getElementById('input-subtotal').value);
        console.log('Total Amount:', document.getElementById('input-total-amount').value);
        
        console.log('Form validation passed, submitting...');
        return true;
    });
});
</script>
@endsection
