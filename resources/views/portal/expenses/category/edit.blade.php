@extends('layouts.portal')

@section('title', 'Edit Expense Category — FinTrack')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Expense Category</h1>
        <p class="page-subtitle">Update category details and monthly budget</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('expense.categories.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Categories
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body" style="padding:28px;">
                <form method="POST" action="{{ route('expense.categories.update', $category->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name"
                               required maxlength="150"
                               value="{{ old('name', $category->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Category Colour</label>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <input type="color"
                                   class="form-control form-control-color"
                                   id="color" name="color"
                                   value="{{ old('color', $category->color ?: '#0E7490') }}"
                                   title="Pick a colour"
                                   style="width:46px;height:38px;padding:3px;border-radius:var(--r-sm);">
                            <code id="colorHex" style="font-size:12px;color:var(--text-muted);">
                                {{ strtoupper(old('color', $category->color ?: '#0E7490')) }}
                            </code>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach(['#0B2A4A','#0E7490','#22C55E','#F43F5E','#F59E0B','#8B5CF6','#0891B2','#6B7280'] as $sw)
                                <button type="button"
                                        class="color-swatch"
                                        data-color="{{ $sw }}"
                                        style="width:22px;height:22px;border-radius:50%;background:{{ $sw }};cursor:pointer;flex-shrink:0;"
                                        @if(strtolower(old('color', $category->color)) === strtolower($sw))
                                            data-preselected="1"
                                        @endif
                                        ></button>
                                @endforeach
                            </div>
                        </div>
                        @error('color')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Monthly Budget --}}
                    <div class="mb-4">
                        <label for="monthly_budget" class="form-label fw-semibold">
                            Monthly Budget
                            <span class="badge bg-secondary ms-1" style="font-size:10px;font-weight:500;vertical-align:middle;">Optional</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-coins" style="color:var(--ft-amber);"></i>
                            </span>
                            <input type="number"
                                   class="form-control @error('monthly_budget') is-invalid @enderror"
                                   id="monthly_budget" name="monthly_budget"
                                   min="0" step="0.01"
                                   placeholder="0.00 — leave blank for no budget"
                                   value="{{ old('monthly_budget', $category->monthly_budget) }}">
                            @error('monthly_budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($category->monthly_budget)
                        <div class="form-text mt-1">
                            <i class="fas fa-circle-check me-1" style="color:var(--ft-emerald);"></i>
                            Current budget: <strong>{{ number_format($category->monthly_budget, 2) }}</strong> / month.
                            Clear the field and save to remove the budget.
                        </div>
                        @else
                        <div class="form-text mt-1">
                            <i class="fas fa-circle-info me-1" style="color:var(--ft-teal);"></i>
                            Set a monthly spending limit. Progress bars and alerts appear when you're nearing or over this amount.
                        </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('expense.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    const colorInput = document.getElementById('color');
    const hexLabel   = document.getElementById('colorHex');

    colorInput.addEventListener('input', function () {
        hexLabel.textContent = this.value.toUpperCase();
    });

    /* Mark pre-selected swatch on page load */
    document.querySelectorAll('.color-swatch[data-preselected]').forEach(s => s.classList.add('swatch-selected'));

    document.querySelectorAll('.color-swatch').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const c = this.dataset.color;
            colorInput.value     = c;
            hexLabel.textContent = c.toUpperCase();
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('swatch-selected'));
            this.classList.add('swatch-selected');
        });
    });
})();
</script>
@endsection
