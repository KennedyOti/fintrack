@extends('layouts.portal')
@section('title', 'Blog Categories — FinTrack Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-folder-tree me-2" style="color:var(--ft-teal);font-size:20px;"></i>Blog Categories</h1>
        <p class="page-subtitle">Organise your blog content into categories</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Posts</a>
        <a href="{{ route('admin.blog.categories.create') }}" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;"><i class="fas fa-plus me-1"></i> New Category</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px;">
            <thead>
                <tr style="background:var(--bs-tertiary-bg);">
                    <th class="ps-4 py-3">Category</th>
                    <th class="py-3">Slug</th>
                    <th class="py-3 text-center">Posts</th>
                    <th class="py-3 text-center">Status</th>
                    <th class="py-3 text-center">Order</th>
                    <th class="py-3 text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:8px;background:{{ $cat->color }}22;border:1px solid {{ $cat->color }}44;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fa-solid {{ $cat->icon }}" style="color:{{ $cat->color }};font-size:13px;"></i>
                            </div>
                            <div>
                                <div class="fw-500">{{ $cat->name }}</div>
                                @if($cat->description)
                                <div style="font-size:11px;color:var(--bs-secondary-color);">{{ Str::limit($cat->description, 60) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="py-3"><code style="font-size:11px;color:var(--ft-teal);">{{ $cat->slug }}</code></td>
                    <td class="py-3 text-center">{{ $cat->posts_count }}</td>
                    <td class="py-3 text-center">
                        <span class="badge bg-{{ $cat->is_active ? 'success' : 'secondary' }}">{{ $cat->is_active ? 'Active' : 'Hidden' }}</span>
                    </td>
                    <td class="py-3 text-center">{{ $cat->sort_order }}</td>
                    <td class="py-3 text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.blog.categories.edit', $cat->id) }}" class="btn btn-xs btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.blog.categories.destroy', $cat->id) }}" onsubmit="return confirm('Delete category? Posts will be uncategorised.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:var(--bs-secondary-color);">
                        <i class="fas fa-folder-open fa-2x mb-2 d-block" style="opacity:.3;"></i>
                        No categories yet. <a href="{{ route('admin.blog.categories.create') }}" style="color:var(--ft-teal);">Create one!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
