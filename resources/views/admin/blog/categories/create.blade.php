@extends('layouts.portal')
@section('title', 'New Blog Category — FinTrack Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-folder-plus me-2" style="color:var(--ft-teal);font-size:20px;"></i>New Category</h1>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-body" style="padding:24px;">
                <form action="{{ route('admin.blog.categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-500">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-500">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-500">Color</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" name="color" value="{{ old('color', '#22D3EE') }}" class="form-control form-control-color" style="width:50px;height:38px;">
                            <input type="text" id="colorHex" value="{{ old('color', '#22D3EE') }}" class="form-control" style="font-size:13px;" placeholder="#22D3EE">
                        </div>
                        <div class="mt-2 d-flex gap-1 flex-wrap">
                            @foreach(['#22D3EE','#22C55E','#F59E0B','#8B5CF6','#F43F5E','#0E7490','#0B2A4A','#EC4899','#14B8A6'] as $c)
                            <button type="button" class="color-swatch" data-color="{{ $c }}" style="width:22px;height:22px;border-radius:50%;background:{{ $c }};border:2px solid transparent;cursor:pointer;" title="{{ $c }}"></button>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-500">Icon <span style="font-size:12px;color:var(--bs-secondary-color);">(Font Awesome class)</span></label>
                        <input type="text" name="icon" value="{{ old('icon', 'fa-tag') }}" class="form-control" placeholder="e.g. fa-chart-bar" id="iconInput">
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            @foreach(['fa-tag','fa-chart-bar','fa-dollar-sign','fa-briefcase','fa-lightbulb','fa-graduation-cap','fa-newspaper','fa-rocket','fa-star'] as $ic)
                            <button type="button" class="btn btn-xs btn-outline-secondary icon-pick" data-icon="{{ $ic }}" title="{{ $ic }}">
                                <i class="fa-solid {{ $ic }}"></i>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="mb-3 p-3 rounded" style="background:var(--bs-tertiary-bg);">
                    <div style="font-size:11px;color:var(--bs-secondary-color);margin-bottom:8px;font-weight:600;">PREVIEW</div>
                    <span id="catPreview" style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:13px;background:#22D3EE22;color:#22D3EE;border:1px solid #22D3EE44;">
                        <i class="fa-solid fa-tag" id="previewIcon"></i>
                        <span id="previewName">Category Name</span>
                    </span>
                </div>

                <hr>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-500">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                    </div>
                    <div class="col-sm-6 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active (visible on blog)</label>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="fw-600 mb-3"><i class="fas fa-search-dollar me-2" style="color:var(--ft-emerald);"></i>SEO (optional)</h6>

                <div class="mb-3">
                    <label class="form-label fw-500">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" maxlength="160" placeholder="Leave blank to use category name">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-500">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control" maxlength="320">{{ old('meta_description') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;"><i class="fas fa-save me-1"></i> Create Category</button>
                    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const colorInput  = document.querySelector('input[type="color"]');
const colorHex    = document.getElementById('colorHex');
const nameInput   = document.querySelector('input[name="name"]');
const iconInput   = document.getElementById('iconInput');
const previewEl   = document.getElementById('catPreview');
const previewName = document.getElementById('previewName');
const previewIcon = document.getElementById('previewIcon');

function updatePreview() {
    const color = colorInput.value;
    previewEl.style.background = color + '22';
    previewEl.style.color      = color;
    previewEl.style.borderColor= color + '44';
    previewIcon.className = 'fa-solid ' + (iconInput.value || 'fa-tag');
    previewName.textContent = nameInput.value || 'Category Name';
}

colorInput.addEventListener('input', function() { colorHex.value = this.value; updatePreview(); });
colorHex.addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) { colorInput.value = this.value; updatePreview(); }
});
nameInput.addEventListener('input', updatePreview);
iconInput.addEventListener('input', updatePreview);

document.querySelectorAll('.color-swatch').forEach(btn => {
    btn.addEventListener('click', function() {
        colorInput.value = this.dataset.color;
        colorHex.value   = this.dataset.color;
        updatePreview();
    });
});

document.querySelectorAll('.icon-pick').forEach(btn => {
    btn.addEventListener('click', function() {
        iconInput.value = this.dataset.icon;
        updatePreview();
    });
});
</script>
@endsection
