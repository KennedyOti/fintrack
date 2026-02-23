@extends('layouts.portal')

@section('title', 'Create Quote - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Create Quote</h4>
        <p class="text-muted mb-0">Create a new quote for your client</p>
    </div>
    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<!-- Form -->
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('quotes.store') }}" id="quote-form">
            @csrf
            
            <!-- Quote Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quote Details</h5>
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
                            <label for="quote_number" class="form-label">Quote Number *</label>
                            <input type="text" class="form-control @error('quote_number') is-invalid @enderror" 
                                   id="quote_number" name="quote_number" 
                                   value="{{ old('quote_number', App\Models\Quote::generateQuoteNumber()) }}" required>
                            @error('quote_number')
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
                        <div class="col-md-6">
                            <label for="issue_date" class="form-label">Issue Date *</label>
                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                   id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="valid_until" class="form-label">Valid Until *</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quote Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quote Items</h5>
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
                    <i class="fas fa-save me-2"></i> Create Quote
                </button>
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                    Cancel
                </a>
            </div>
            
            <!-- Hidden inputs for totals -->
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

        <!-- Tax & Discount Settings -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Tax & Discount</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="tax-rate" class="form-label">Tax Rate (%)</label>
                    <input type="number" class="form-control" id="tax-rate" value="0" min="0" step="0.01">
                </div>
                <div class="mb-3">
                    <label for="discount-amount" class="form-label">Discount Amount ({{ $currencySymbol }})</label>
                    <input type="number" class="form-control" id="discount-amount" value="0" min="0" step="0.01">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for dynamic items -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsTbody = document.getElementById('items-tbody');
    const addItemBtn = document.getElementById('add-item');
    const taxRateInput = document.getElementById('tax-rate');
    const discountInput = document.getElementById('discount-amount');
    
    // Add first item row
    addItemRow();
    
    // Add item button click handler
    addItemBtn.addEventListener('click', function() {
        addItemRow();
    });
    
    function addItemRow() {
        const rowId = Date.now();
        const row = document.createElement('tr');
        row.id = 'item-row-' + rowId;
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${rowId}][description]" placeholder="Item description" required>
            </td>
            <td>
                <input type="number" class="form-control item-quantity" name="items[${rowId}][quantity]" value="1" min="1" step="1" required>
            </td>
            <td>
                <input type="number" class="form-control item-price" name="items[${rowId}][unit_price]" value="0" min="0" step="0.01" required>
            </td>
            <td>
                <input type="number" class="form-control item-total" name="items[${rowId}][total]" value="0" min="0" step="0.01" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        itemsTbody.appendChild(row);
        
        // Add event listeners to the new row
        const quantityInput = row.querySelector('.item-quantity');
        const priceInput = row.querySelector('.item-price');
        
        quantityInput.addEventListener('input', calculateTotals);
        priceInput.addEventListener('input', calculateTotals);
    }
    
    window.removeItemRow = function(rowId) {
        const row = document.getElementById('item-row-' + rowId);
        if (row) {
            row.remove();
            calculateTotals();
        }
    };
    
// Get currency symbol from server-side variable
    const currencySymbol = '{{ $currencySymbol }}';
    
    function calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('#items-tbody tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = quantity * price;
            
            row.querySelector('.item-total').value = total.toFixed(2);
            subtotal += total;
        });
        
        const taxRate = parseFloat(taxRateInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        
        const taxAmount = subtotal * (taxRate / 100);
        const totalAmount = subtotal + taxAmount - discount;
        
        // Update summary
        document.getElementById('summary-subtotal').textContent = currencySymbol + subtotal.toFixed(2);
        document.getElementById('summary-tax').textContent = currencySymbol + taxAmount.toFixed(2);
        document.getElementById('summary-discount').textContent = currencySymbol + discount.toFixed(2);
        document.getElementById('summary-total').textContent = currencySymbol + totalAmount.toFixed(2);
        
        // Update hidden inputs
        document.getElementById('input-subtotal').value = subtotal.toFixed(2);
        document.getElementById('input-tax-amount').value = taxAmount.toFixed(2);
        document.getElementById('input-discount-amount').value = discount.toFixed(2);
        document.getElementById('input-total-amount').value = totalAmount.toFixed(2);
    }
    
    // Add event listeners for tax and discount
    taxRateInput.addEventListener('input', calculateTotals);
    discountInput.addEventListener('input', calculateTotals);
});
</script>
@endsection
