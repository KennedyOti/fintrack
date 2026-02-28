@extends('layouts.portal')

@section('title', 'Edit Expense - FinTrack')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Expense</h1>
        <p class="page-subtitle">Update the expense details</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Form -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon" style="background:rgba(244,63,94,.10);">
                        <i class="fas fa-receipt" style="color:var(--ft-rose);"></i>
                    </span>
                    Expense Details
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data" id="expenseForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                           id="amount" name="amount" required min="0" step="0.01"
                                           value="{{ old('amount', $expense->amount) }}">
                                    @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense_date" class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror"
                                       id="expense_date" name="expense_date" required
                                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}">
                                @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="income_id" class="form-label fw-semibold">Funded From Income</label>
                                <select class="form-select @error('income_id') is-invalid @enderror" id="income_id" name="income_id">
                                    <option value="">Select Income Source</option>
                                    @foreach($incomes as $income)
                                    <option value="{{ $income->id }}" {{ old('income_id', $expense->income_id) == $income->id ? 'selected' : '' }}>
                                        {{ number_format($income->amount, 2) }} — {{ $income->income_date->format('M d, Y') }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Only incomes with available balance shown</div>
                                @error('income_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="vendor_name" class="form-label fw-semibold">Vendor Name</label>
                        <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                               id="vendor_name" name="vendor_name" maxlength="150"
                               placeholder="e.g., Office Supplies Inc."
                               value="{{ old('vendor_name', $expense->vendor_name) }}">
                        @error('vendor_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash"          {{ old('payment_method', $expense->payment_method) == 'cash'          ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="mpesa"         {{ old('payment_method', $expense->payment_method) == 'mpesa'         ? 'selected' : '' }}>M-Pesa</option>
                                    <option value="card"          {{ old('payment_method', $expense->payment_method) == 'card'          ? 'selected' : '' }}>Card</option>
                                    <option value="other"         {{ old('payment_method', $expense->payment_method) == 'other'         ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference_number" class="form-label fw-semibold">Reference Number</label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                                       id="reference_number" name="reference_number" maxlength="100"
                                       placeholder="e.g., INV-001"
                                       value="{{ old('reference_number', $expense->reference_number) }}">
                                @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Add any additional details…">{{ old('notes', $expense->notes) }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Expense
                        </button>
                        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Receipt Card ─────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon" style="background:rgba(14,116,144,.10);">
                        <i class="fas fa-paperclip" style="color:var(--ft-teal);"></i>
                    </span>
                    Receipt Attachment
                </h6>
            </div>
            <div class="card-body">

                @if($expense->receipt_path)
                {{-- ── Existing Receipt ──────────────────── --}}
                <div class="existing-receipt mb-3" id="existingReceiptBlock">
                    <p style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                        Current Receipt
                    </p>

                    @if($expense->receiptIsImage())
                    <div style="border-radius:var(--r-md);overflow:hidden;border:1px solid var(--border);">
                        <a href="{{ route('expenses.receipt.view', $expense->id) }}" target="_blank">
                            <img src="{{ route('expenses.receipt.view', $expense->id) }}"
                                 alt="Receipt" style="width:100%;max-height:220px;object-fit:contain;background:#f9fafb;">
                        </a>
                    </div>
                    @else
                    <a href="{{ route('expenses.receipt.view', $expense->id) }}" target="_blank"
                       class="d-flex align-items-center gap-3 p-3"
                       style="border-radius:var(--r-md);border:1px solid var(--border);background:var(--bg-page);text-decoration:none;color:inherit;">
                        <i class="fas fa-file-pdf" style="font-size:28px;color:var(--ft-rose);flex-shrink:0;"></i>
                        <div style="min-width:0;">
                            <div style="font-size:13px;font-weight:600;color:var(--text-h);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ basename($expense->receipt_path) }}
                            </div>
                            <div style="font-size:11.5px;color:var(--ft-teal);">Click to open PDF</div>
                        </div>
                        <i class="fas fa-arrow-up-right-from-square ms-auto" style="color:var(--text-faint);font-size:12px;flex-shrink:0;"></i>
                    </a>
                    @endif

                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('expenses.receipt.view', $expense->id) }}" target="_blank"
                           class="btn btn-xs btn-outline-secondary flex-fill text-center">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                        <form action="{{ route('expenses.receipt.delete', $expense->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger w-100 btn-delete"
                                    data-confirm="Remove this receipt? This cannot be undone.">
                                <i class="fas fa-trash me-1"></i> Remove
                            </button>
                        </form>
                    </div>
                    <hr style="margin:16px 0 12px;">
                    <p style="font-size:12px;color:var(--text-muted);margin-bottom:10px;">
                        Replace with a new file:
                    </p>
                </div>
                @else
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                    Attach a photo or PDF of your receipt. Invaluable for tax audits and record-keeping.
                </p>
                @endif

                {{-- Drop zone --}}
                <div id="receiptDropZone" class="receipt-drop-zone" onclick="document.getElementById('receiptInput').click()">
                    <input type="file" id="receiptInput" name="receipt" form="expenseForm"
                           accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                           style="display:none;" onchange="handleReceiptChange(this)">

                    <div id="dropZonePrompt">
                        <div class="receipt-drop-icon">
                            <i class="fas fa-cloud-arrow-up"></i>
                        </div>
                        <p class="receipt-drop-label">
                            {{ $expense->receipt_path ? 'Drop new receipt or' : 'Drop receipt here or' }}
                            <span class="text-primary">browse</span>
                        </p>
                        <p class="receipt-drop-hint">JPEG, PNG, GIF, WEBP, PDF &bull; max 5 MB</p>
                    </div>

                    <div id="receiptPreview" style="display:none;">
                        <img id="previewImg" src="" alt="Receipt preview"
                             style="max-width:100%;max-height:200px;border-radius:8px;object-fit:contain;display:none;">
                        <div id="previewPdf" style="display:none;text-align:center;">
                            <i class="fas fa-file-pdf" style="font-size:48px;color:var(--ft-rose);"></i>
                        </div>
                        <p id="previewName" style="font-size:12px;color:var(--text-muted);margin-top:8px;word-break:break-all;"></p>
                        <button type="button" class="btn btn-xs btn-outline-danger mt-1" onclick="clearReceipt(event)">
                            <i class="fas fa-xmark me-1"></i> Remove
                        </button>
                    </div>
                </div>

                @error('receipt')
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror

                {{-- Hidden remove flag for when user has no existing file but submits with none --}}
                <input type="hidden" name="remove_receipt" id="removeReceiptFlag" value="0" form="expenseForm">
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.receipt-drop-zone {
    border: 2px dashed var(--border);
    border-radius: var(--r-md);
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: var(--bg-page);
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.receipt-drop-zone:hover,
.receipt-drop-zone.drag-over {
    border-color: var(--ft-teal);
    background: rgba(14,116,144,.04);
}
.receipt-drop-icon {
    font-size: 32px;
    color: var(--ft-teal);
    margin-bottom: 8px;
    line-height: 1;
}
.receipt-drop-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-h);
    margin-bottom: 4px;
}
.receipt-drop-hint {
    font-size: 11.5px;
    color: var(--text-faint);
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
function handleReceiptChange(input) {
    const file = input.files[0];
    if (!file) return;

    const prompt  = document.getElementById('dropZonePrompt');
    const preview = document.getElementById('receiptPreview');
    const img     = document.getElementById('previewImg');
    const pdf     = document.getElementById('previewPdf');
    const name    = document.getElementById('previewName');

    prompt.style.display  = 'none';
    preview.style.display = 'block';
    name.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        reader.readAsDataURL(file);
        img.style.display = 'block';
        pdf.style.display = 'none';
    } else {
        img.style.display = 'none';
        pdf.style.display = 'block';
    }
}

function clearReceipt(e) {
    e.stopPropagation();
    document.getElementById('receiptInput').value = '';
    document.getElementById('dropZonePrompt').style.display  = 'block';
    document.getElementById('receiptPreview').style.display  = 'none';
    document.getElementById('previewImg').src = '';
}

// Drag-and-drop
const dz = document.getElementById('receiptDropZone');
dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('drag-over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('drag-over');
    const input = document.getElementById('receiptInput');
    input.files = e.dataTransfer.files;
    handleReceiptChange(input);
});
</script>
@endpush
@endsection
