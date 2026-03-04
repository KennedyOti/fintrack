@extends('layouts.portal')
@section('title', 'Blog Management — FinTrack Admin')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-rss me-2" style="color:var(--ft-teal);font-size:20px;"></i>
            Blog Management
        </h1>
        <p class="page-subtitle">Manage all posts, categories & comments</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-folder-tree me-1"></i> Categories
        </a>
        <a href="{{ route('admin.blog.comments.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-comments me-1"></i> Comments
            @if($pendingComments > 0)
            <span class="badge ms-1" style="background:var(--ft-rose);font-size:10px;">{{ $pendingComments }}</span>
            @endif
        </a>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-sm" style="background:var(--ft-navy);color:#fff;">
            <i class="fas fa-plus me-1"></i> New Post
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Stats Row ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-navy">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Posts</div>
                    <div class="stat-value">{{ number_format($totalPosts) }}</div>
                    <div class="stat-sub"><i class="fas fa-circle-check me-1" style="color:var(--ft-emerald);"></i>{{ $publishedCount }} published</div>
                </div>
                <div class="stat-badge"><i class="fas fa-newspaper"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-teal">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Views</div>
                    <div class="stat-value">{{ number_format($totalViews) }}</div>
                    <div class="stat-sub"><i class="fas fa-eye me-1"></i>All time</div>
                </div>
                <div class="stat-badge"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-emerald">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Likes</div>
                    <div class="stat-value">{{ number_format($totalLikes) }}</div>
                    <div class="stat-sub"><i class="fas fa-heart me-1"></i>Across all posts</div>
                </div>
                <div class="stat-badge"><i class="fas fa-heart"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card sc-rose">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Pending Comments</div>
                    <div class="stat-value">{{ number_format($pendingComments) }}</div>
                    <div class="stat-sub"><i class="fas fa-circle-check me-1" style="color:var(--ft-emerald);"></i>{{ $totalComments }} approved</div>
                </div>
                <div class="stat-badge"><i class="fas fa-comments"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Top Posts --}}
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom:1px solid var(--bs-border-color);padding:16px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-trophy me-2" style="color:var(--ft-amber);"></i>Top Performing Posts</h6>
                <a href="{{ route('blog.index') }}" target="_blank" class="btn btn-xs btn-outline-secondary"><i class="fas fa-external-link-alt me-1"></i>View Blog</a>
            </div>
            <div class="card-body p-0">
                @forelse($topPosts as $i => $tp)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="{{ $i < $topPosts->count()-1 ? 'border-bottom:1px solid var(--bs-border-color);' : '' }}">
                    <div style="width:28px;height:28px;border-radius:50%;background:var(--ft-navy);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">{{ $i+1 }}</div>
                    <div class="flex-1" style="min-width:0;">
                        <div class="fw-500 text-truncate" style="font-size:13px;">{{ $tp->title }}</div>
                        <div style="font-size:11px;color:var(--bs-secondary-color);">
                            {{ $tp->published_at?->format('M j, Y') }}
                        </div>
                    </div>
                    <div class="d-flex gap-3" style="font-size:12px;color:var(--bs-secondary-color);flex-shrink:0;">
                        <span><i class="fas fa-eye me-1"></i>{{ number_format($tp->views) }}</span>
                        <span><i class="fas fa-heart me-1" style="color:var(--ft-rose);"></i>{{ $tp->likes_count }}</span>
                    </div>
                    <a href="{{ route('admin.blog.edit', $tp->slug) }}" class="btn btn-xs btn-outline-secondary"><i class="fas fa-pen"></i></a>
                </div>
                @empty
                <div class="p-4 text-center" style="color:var(--bs-secondary-color);font-size:13px;">No published posts yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Monthly chart --}}
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header" style="border-bottom:1px solid var(--bs-border-color);padding:16px 20px;">
                <h6 class="mb-0 fw-600"><i class="fas fa-chart-bar me-2" style="color:var(--ft-teal);"></i>Posts per Month</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="monthlyChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── Posts Table ──────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between" style="border-bottom:1px solid var(--bs-border-color);padding:14px 20px;">
        <div class="d-flex gap-2 flex-wrap">
            @foreach(['all' => 'All', 'published' => 'Published', 'draft' => 'Draft', 'scheduled' => 'Scheduled'] as $val => $label)
            <a href="{{ route('admin.blog.index', ['status' => $val, 'q' => $search]) }}"
               class="btn btn-xs {{ $status === $val ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="{{ $status === $val ? 'background:var(--ft-navy);border-color:var(--ft-navy);' : '' }}">
                {{ $label }}
                @if($val === 'draft') <span class="ms-1">{{ $draftCount }}</span>
                @elseif($val === 'published') <span class="ms-1">{{ $publishedCount }}</span>
                @elseif($val === 'scheduled') <span class="ms-1">{{ $scheduledCount }}</span>
                @endif
            </a>
            @endforeach
        </div>
        <form action="{{ route('admin.blog.index') }}" method="GET" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search posts…"
                   class="form-control form-control-sm" style="width:220px;font-size:13px;">
            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
            <a href="{{ route('admin.blog.index', ['status' => $status]) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-xmark"></i></a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px;">
            <thead>
                <tr style="background:var(--bs-tertiary-bg);">
                    <th class="ps-4 py-3">Post</th>
                    <th class="py-3">Category</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 text-center">Views</th>
                    <th class="py-3 text-center">Likes</th>
                    <th class="py-3 text-center">Comments</th>
                    <th class="py-3">Published</th>
                    <th class="py-3 text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td class="ps-4 py-3" style="max-width:300px;">
                        <div class="d-flex align-items-center gap-2">
                            @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" style="width:40px;height:30px;object-fit:cover;border-radius:4px;flex-shrink:0;" alt="">
                            @else
                            <div style="width:40px;height:30px;border-radius:4px;background:var(--bs-tertiary-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-image" style="color:var(--bs-secondary-color);font-size:11px;"></i></div>
                            @endif
                            <div style="min-width:0;">
                                <div class="fw-500 text-truncate">{{ $post->title }}</div>
                                <div style="color:var(--bs-secondary-color);font-size:11px;">/blog/{{ $post->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        @if($post->category)
                        <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:{{ $post->category->color ?? '#22D3EE' }}22;color:{{ $post->category->color ?? '#22D3EE' }};border:1px solid {{ $post->category->color ?? '#22D3EE' }}44;">
                            {{ $post->category->name }}
                        </span>
                        @else
                        <span style="color:var(--bs-secondary-color);font-size:12px;">—</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <span class="badge bg-{{ $post->status === 'published' ? 'success' : ($post->status === 'scheduled' ? 'warning text-dark' : 'secondary') }}">
                            {{ ucfirst($post->status) }}
                        </span>
                        @if($post->featured)
                        <span class="badge ms-1" style="background:var(--ft-amber);color:#000;"><i class="fas fa-star" style="font-size:9px;"></i></span>
                        @endif
                    </td>
                    <td class="py-3 text-center">{{ number_format($post->views) }}</td>
                    <td class="py-3 text-center">{{ $post->likes_count ?? 0 }}</td>
                    <td class="py-3 text-center">
                        <span>{{ $post->approved_comments_count ?? 0 }}</span>
                        @if(($post->comments_count ?? 0) > ($post->approved_comments_count ?? 0))
                        <span class="badge ms-1" style="background:var(--ft-rose);font-size:10px;">+{{ ($post->comments_count ?? 0) - ($post->approved_comments_count ?? 0) }}</span>
                        @endif
                    </td>
                    <td class="py-3" style="font-size:12px;color:var(--bs-secondary-color);">
                        {{ $post->published_at ? $post->published_at->format('M j, Y') : '—' }}
                    </td>
                    <td class="py-3 text-end pe-4">
                        <div class="d-flex gap-1 justify-content-end">
                            @if($post->status === 'published')
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="View"><i class="fas fa-external-link-alt"></i></a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $post->slug) }}" class="btn btn-xs btn-outline-secondary" title="Edit"><i class="fas fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.blog.destroy', $post->slug) }}" onsubmit="return confirm('Move this post to trash?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5" style="color:var(--bs-secondary-color);">
                        <i class="fas fa-newspaper fa-2x mb-2 d-block" style="opacity:.3;"></i>
                        No posts found. <a href="{{ route('admin.blog.create') }}" style="color:var(--ft-teal);">Create your first post!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center" style="padding:12px 20px;border-top:1px solid var(--bs-border-color);">
        <span style="font-size:12px;color:var(--bs-secondary-color);">Showing {{ $posts->firstItem() }}–{{ $posts->lastItem() }} of {{ $posts->total() }} posts</span>
        {{ $posts->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_column($monthlyData, 'month')),
            datasets: [{
                label: 'Posts',
                data: @json(array_column($monthlyData, 'posts')),
                backgroundColor: 'rgba(14,116,144,0.7)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#8FA5C3', font: { size: 11 } }, grid: { color: 'rgba(99,142,210,0.09)' } },
                x: { ticks: { color: '#8FA5C3', font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
}
</script>
@endsection
