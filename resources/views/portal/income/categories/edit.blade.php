@extends('layouts.portal')

@section('title', 'Edit Income Category - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Edit Income Category</h4>
        <p class="text-muted mb-0">Update the income category details</p>
    </div>
    <a href="{{ route('income.categories.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<!-- Form -->
<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('income.categories.update', $category->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="150" value="{{ old('name', $category->name) }}">
                        @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="color" class="form-label">Color</label>
                        <div class="row g-2">
                            <div class="col-auto">
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', $category->color ?: '#6c757d') }}" title="Choose a color">
                            </div>
                            <div class="col-auto">
                                <span class="text-muted small">Select a color for this category</span>
                            </div>
                        </div>
                        @error('color')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Category
                        </button>
                        <a href="{{ route('income.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
