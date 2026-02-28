@extends('layouts.portal')

@section('title', 'Expense Details - FinTrack')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Expense Details</h1>
        <p class="page-subtitle">View expense information and receipt</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-pen me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- ── Left: Expense Info ───────────────────────── --}}
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon" style="background:rgba(244,63,94,.10);">
                        <i class="fas fa-receipt" style="color:var(--ft-rose);"></i>
                    </span>
                    Expense Information
                </h6>
            </div>
            <div class="card-body">
                {{-- Amount + Date hero row --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:4px;">Amount</div>
                        <div style="font-size:28px;font-weight:800;color:var(--ft-rose);line-height:1.1;">
                            {{ number_format($expense->amount, 2) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:4px;">Expense Date</div>
                        <div style="font-size:20px;font-weight:700;color:var(--text-h);">
                            {{ $expense->expense_date->format('M d, Y') }}
                        </div>
                        <div style="font-size:12px;color:var(--text-faint);">{{ $expense->expense_date->format('l') }}</div>
                    </div>
                </div>

                <hr style="margin:0 0 20px;">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Category</div>
                        @if($expense->category)
                        <span class="badge" style="background-color:{{ $expense->category->color ?? '#6B7280' }};color:#fff;font-size:12.5px;padding:5px 10px;">
                            {{ $expense->category->name }}
                        </span>
                        @else
                        <span style="color:var(--text-faint);">—</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Funded From Income</div>
                        @if($expense->income)
                        <span style="font-size:13px;color:var(--text-h);">
                            {{ number_format($expense->income->amount, 2) }} — {{ $expense->income->income_date->format('M d, Y') }}
                        </span>
                        @else
                        <span style="color:var(--text-faint);">—</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Vendor Name</div>
                        <span style="font-size:13px;color:var(--text-h);">{{ $expense->vendor_name ?: '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Reference Number</div>
                        <span style="font-size:13px;color:var(--text-h);">{{ $expense->reference_number ?: '—' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Payment Method</div>
                        @switch($expense->payment_method)
                        @case('cash')
                            <span class="badge" style="background:rgba(14,116,144,.12);color:var(--ft-teal);font-size:12px;">Cash</span>
                            @break
                        @case('bank_transfer')
                            <span class="badge" style="background:rgba(11,42,74,.10);color:var(--ft-navy);font-size:12px;">Bank Transfer</span>
                            @break
                        @case('mpesa')
                            <span class="badge" style="background:rgba(34,197,94,.12);color:#15803D;font-size:12px;">M-Pesa</span>
                            @break
                        @case('card')
                            <span class="badge" style="background:rgba(139,92,246,.12);color:#7C3AED;font-size:12px;">Card</span>
                            @break
                        @default
                            <span class="badge" style="background:var(--bg-page);color:var(--text-muted);font-size:12px;">{{ ucfirst($expense->payment_method) }}</span>
                        @endswitch
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Recorded At</div>
                        <span style="font-size:13px;color:var(--text-h);">{{ $expense->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                </div>

                @if($expense->notes)
                <div class="mt-2">
                    <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:6px;">Notes</div>
                    <p style="font-size:13px;color:var(--text-h);white-space:pre-wrap;margin-bottom:0;background:var(--bg-page);padding:12px 14px;border-radius:var(--r-md);border:1px solid var(--border);">{{ $expense->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-0 shadow-sm" style="border-top:2px solid var(--ft-rose) !important;">
            <div class="card-body">
                <h6 class="mb-1" style="color:var(--ft-rose);">Danger Zone</h6>
                <p class="mb-3" style="font-size:12.5px;color:var(--text-muted);">This will permanently delete the expense record and its receipt.</p>
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm btn-delete"
                            data-confirm="Permanently delete this expense and its receipt?">
                        <i class="fas fa-trash me-1"></i> Delete Expense
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Right: Receipt Card ──────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="card-header-title">
                    <span class="card-header-icon" style="background:rgba(14,116,144,.10);">
                        <i class="fas fa-paperclip" style="color:var(--ft-teal);"></i>
                    </span>
                    Receipt
                    @if($expense->receipt_path)
                    <span class="badge ms-1" style="background:rgba(34,197,94,.15);color:#15803D;font-size:10px;">Attached</span>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                @if($expense->receipt_path)
                    @if($expense->receiptIsImage())
                    {{-- ── Image Receipt ──────────────────── --}}
                    <div id="receiptImgWrap" style="position:relative;cursor:zoom-in;" onclick="openLightbox()">
                        <img src="{{ route('expenses.receipt.view', $expense->id) }}"
                             alt="Receipt"
                             id="receiptThumb"
                             style="width:100%;border-radius:var(--r-md);border:1px solid var(--border);object-fit:contain;max-height:280px;background:#f9fafb;">
                        <div style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.45);color:#fff;border-radius:6px;padding:3px 8px;font-size:11px;pointer-events:none;">
                            <i class="fas fa-magnifying-glass-plus me-1"></i>Zoom
                        </div>
                    </div>

                    {{-- Lightbox --}}
                    <div id="receiptLightbox"
                         onclick="closeLightbox()"
                         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;cursor:zoom-out;">
                        <img src="{{ route('expenses.receipt.view', $expense->id) }}"
                             style="max-width:90vw;max-height:90vh;border-radius:10px;object-fit:contain;box-shadow:0 20px 60px rgba(0,0,0,.6);">
                        <button onclick="closeLightbox()" style="position:fixed;top:20px;right:24px;background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:50%;width:36px;height:36px;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);">&times;</button>
                    </div>

                    @else
                    {{-- ── PDF Receipt ─────────────────────── --}}
                    <a href="{{ route('expenses.receipt.view', $expense->id) }}" target="_blank"
                       class="d-flex align-items-center gap-3 p-3 mb-1"
                       style="border-radius:var(--r-md);border:1px solid var(--border);background:var(--bg-page);text-decoration:none;color:inherit;transition:background .15s;">
                        <i class="fas fa-file-pdf" style="font-size:36px;color:var(--ft-rose);flex-shrink:0;"></i>
                        <div style="min-width:0;">
                            <div style="font-size:13px;font-weight:600;color:var(--text-h);word-break:break-all;">
                                {{ basename($expense->receipt_path) }}
                            </div>
                            <div style="font-size:11.5px;color:var(--ft-teal);margin-top:2px;">
                                <i class="fas fa-arrow-up-right-from-square me-1"></i>Open PDF
                            </div>
                        </div>
                    </a>
                    @endif

                    {{-- Actions --}}
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('expenses.receipt.view', $expense->id) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-secondary flex-fill text-center">
                            <i class="fas fa-{{ $expense->receiptIsPdf() ? 'file-pdf' : 'image' }} me-1"></i>
                            {{ $expense->receiptIsPdf() ? 'Open PDF' : 'Full Size' }}
                        </a>
                        <a href="{{ route('expenses.receipt.view', $expense->id) }}"
                           download
                           class="btn btn-sm btn-outline-primary flex-fill text-center">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>

                    <div class="mt-2">
                        <form action="{{ route('expenses.receipt.delete', $expense->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger w-100 btn-delete"
                                    data-confirm="Remove this receipt? This cannot be undone.">
                                <i class="fas fa-trash me-1"></i> Remove Receipt
                            </button>
                        </form>
                    </div>

                @else
                {{-- ── No Receipt ─────────────────────── --}}
                <div class="text-center py-4">
                    <div style="width:60px;height:60px;border-radius:50%;background:rgba(14,116,144,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                        <i class="fas fa-receipt" style="font-size:22px;color:var(--ft-teal);"></i>
                    </div>
                    <p style="font-size:13px;font-weight:600;color:var(--text-h);margin-bottom:4px;">No receipt attached</p>
                    <p style="font-size:12.5px;color:var(--text-muted);margin-bottom:16px;">
                        Attach a photo or PDF for tax audit purposes.
                    </p>
                    <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-paperclip me-1"></i> Attach Receipt
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openLightbox() {
    const lb = document.getElementById('receiptLightbox');
    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('receiptLightbox').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush
@endsection
