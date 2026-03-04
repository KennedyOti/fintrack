@extends('layouts.portal')
@section('title', 'Edit Category — FinTrack Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-folder-pen me-2" style="color:var(--ft-teal);font-size:20px;"></i>Edit Category</h1>
        <p class="page-subtitle">{{ $category->name }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
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
                <form action="{{ route('admin.blog.categories.update', $category->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-500">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required id="nameInput">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-500">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-500">Color</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="color" name="color" value="{{ old('color', $category->color) }}" class="form-control form-control-color" id="colorInput" style="width:50px;height:38px;">
                            <input type="text" id="colorHex" value="{{ old('color', $category->color) }}" class="form-control" style="font-size:13px;">
                        </div>
                        <div class="mt-2 d-flex gap-1 flex-wrap">
                            @foreach(['#22D3EE','#22C55E','#F59E0B','#8B5CF6','#F43F5E','#0E7490','#0B2A4A','#EC4899','#14B8A6'] as $c)
                            <button type="button" class="color-swatch" data-color="{{ $c }}" style="width:22px;height:22px;border-radius:50%;background:{{ $c }};border:2px solid transparent;cursor:pointer;"></button>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-500">Icon</label>
                        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" class="form-control" id="iconInput">
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            @foreach(['fa-tag','fa-chart-bar','fa-dollar-sign','fa-briefcase','fa-lightbulb','fa-graduation-cap','fa-newspaper','fa-rocket','fa-star'] as $ic)
                            <button type="button" class="btn btn-xs btn-outline-secondary icon-pick" data-icon="{{ $ic }}"><i class="fa-solid {{ $ic }}"></i></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-3 p-3 rounded" style="background:var(--bs-tertiary-bg);">
                    <div style="font-size:11px;color:var(--bs-secondary-color);margin-bottom:8px;font-weight:600;">PREVIEW</div>
                    <span id="catPreview" style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:13px;background:{{ $category->color }}22;color:{{ $category->color }};border:1px solid {{ $category->color }}44;">
                        <i class="fa-solid {{ $category->icon }}" id="previewIcon"></i>
                        <span id="previewName">{{ $category->name }}</span>
                    </span>
                </div>

                <hr>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-500">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="form-control" min="0">
                    </div>
                    <div class="col-sm-6 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="fw-600 mb-3"><i class="fas fa-search-dollar me-2" style="color:var(--ft-emerald);"></i>SEO</h6>

                <div class="mb-3">
                    <label class="form-label fw-500">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" class="form-control" maxlength="160">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-500">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control" maxlength="320">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;"><i class="fas fa-save me-1"></i> Update Category</button>
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
const colorInput  = document.getElementById('colorInput');
const colorHex    = document.getElementById('colorHex');
const nameInput   = document.getElementById('nameInput');
const iconInput   = document.getElementById('iconInput');
const previewEl   = document.getElementById('catPreview');
const previewName = document.getElementById('previewName');
const previewIcon = document.getElementById('previewIcon');

function updatePreview() {
    const color = colorInput.value;
    previewEl.style.background  = color + '22';
    previewEl.style.color       = color;
    previewEl.style.borderColor = color + '44';
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
    btn.addEventListener('click', function() { colorInput.value = colorHex.value = this.dataset.color; updatePreview(); });
});
document.querySelectorAll('.icon-pick').forEach(btn => {
    btn.addEventListener('click', function() { iconInput.value = this.dataset.icon; updatePreview(); });
});
</script>
@endsection
