@php
    $r = $recurring ?? null; // null on create
    $incomeCategories  = $categories->where('type', 'income');
    $expenseCategories = $categories->where('type', 'expense');
    $currentType = old('type', $r?->type ?? 'expense');
@endphp

<div class="row">
    <!-- Left column: main fields -->
    <div class="col-lg-8">

        <!-- Core Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Transaction Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Type -->
                    <div class="col-md-6">
                        <label for="type" class="form-label">Type *</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="expense" {{ $currentType === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="income"  {{ $currentType === 'income'  ? 'selected' : '' }}>Income</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Name -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" maxlength="150" required
                               placeholder="e.g. Adobe Creative Cloud, Office Rent"
                               value="{{ old('name', $r?->name) }}">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Amount -->
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $currencySymbol }}</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" min="0.01" step="0.01" required
                                   value="{{ old('amount', $r?->amount) }}">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Frequency -->
                    <div class="col-md-6">
                        <label for="frequency" class="form-label">Frequency *</label>
                        <select class="form-select @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                            <option value="daily"     {{ old('frequency', $r?->frequency) === 'daily'     ? 'selected' : '' }}>Daily</option>
                            <option value="weekly"    {{ old('frequency', $r?->frequency) === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly"   {{ old('frequency', $r?->frequency) === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('frequency', $r?->frequency) === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="yearly"    {{ old('frequency', $r?->frequency) === 'yearly'    ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date *</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date" required
                               value="{{ old('start_date', $r?->start_date?->format('Y-m-d') ?? date('Y-m-d')) }}">
                        <div class="form-text">First date a record will be generated.</div>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date <span class="text-muted">(optional)</span></label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date"
                               value="{{ old('end_date', $r?->end_date?->format('Y-m-d')) }}">
                        <div class="form-text">Leave blank to run indefinitely.</div>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Category -->
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            <optgroup label="Expense Categories" id="expense-cats">
                                @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $r?->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Income Categories" id="income-cats">
                                @foreach($incomeCategories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $r?->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">Payment Method *</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="cash"          {{ old('payment_method', $r?->payment_method) === 'cash'          ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method', $r?->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="mpesa"         {{ old('payment_method', $r?->payment_method) === 'mpesa'         ? 'selected' : '' }}>M-Pesa</option>
                            <option value="card"          {{ old('payment_method', $r?->payment_method) === 'card'          ? 'selected' : '' }}>Card</option>
                            <option value="paypal"        {{ old('payment_method', $r?->payment_method) === 'paypal'        ? 'selected' : '' }}>PayPal</option>
                            <option value="other"         {{ old('payment_method', $r?->payment_method ?? 'other') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Vendor Name (expense only) -->
                    <div class="col-md-6" id="vendor-field">
                        <label for="vendor_name" class="form-label">Vendor / Payee</label>
                        <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                               id="vendor_name" name="vendor_name" maxlength="150"
                               placeholder="e.g. Adobe, Landlord"
                               value="{{ old('vendor_name', $r?->vendor_name) }}">
                        @error('vendor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Client (income only) -->
                    <div class="col-md-6" id="client-field" style="display:none">
                        <label for="client_id" class="form-label">Client</label>
                        <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id">
                            <option value="">Select Client (Optional)</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $r?->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} @if($client->company_name)({{ $client->company_name }})@endif
                            </option>
                            @endforeach
                        </select>
                        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Project -->
                    <div class="col-md-6">
                        <label for="project_id" class="form-label">Project <span class="text-muted">(optional)</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $r?->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Reference Number -->
                    <div class="col-md-6">
                        <label for="reference_number" class="form-label">Reference Number <span class="text-muted">(optional)</span></label>
                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                               id="reference_number" name="reference_number" maxlength="100"
                               value="{{ old('reference_number', $r?->reference_number) }}">
                        @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes <span class="text-muted">(optional)</span></label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Optional notes appended to each generated record...">{{ old('notes', $r?->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Right column: status + summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4 sticky-top" style="top:1.5rem">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Settings</h5>
            </div>
            <div class="card-body">

                <!-- Active toggle -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $r?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (auto-generate)</label>
                </div>
                <p class="small text-muted">When active, the scheduler generates a record automatically each period. You can pause at any time.</p>

                <hr>

                <div class="small text-muted mb-1">Preview</div>
                <div id="freq-preview" class="alert alert-light border py-2 px-3 small mb-0">
                    A record will be generated on the start date, then every
                    <strong id="prev-freq">month</strong>.
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect   = document.getElementById('type');
    const vendorField  = document.getElementById('vendor-field');
    const clientField  = document.getElementById('client-field');
    const freqSelect   = document.getElementById('frequency');
    const prevFreq     = document.getElementById('prev-freq');

    const freqLabels = {
        daily:     'day',
        weekly:    'week',
        monthly:   'month',
        quarterly: '3 months',
        yearly:    'year',
    };

    function updateTypeFields() {
        const isExpense = typeSelect.value === 'expense';
        vendorField.style.display = isExpense ? '' : 'none';
        clientField.style.display = isExpense ? 'none' : '';
    }

    function updateFreqPreview() {
        prevFreq.textContent = freqLabels[freqSelect.value] || 'period';
    }

    typeSelect.addEventListener('change', updateTypeFields);
    freqSelect.addEventListener('change', updateFreqPreview);

    // Init
    updateTypeFields();
    updateFreqPreview();
});
</script>
