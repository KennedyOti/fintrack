@extends('layouts.portal')
@section('title', 'Blog Comments — FinTrack Admin')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-comments me-2" style="color:var(--ft-teal);font-size:20px;"></i>Blog Comments</h1>
        <p class="page-subtitle">Moderate reader comments</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Posts</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card stat-card sc-rose">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $pendingCount }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card stat-card sc-emerald">
            <div class="stat-label">Approved</div>
            <div class="stat-value">{{ $approvedCount }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card stat-card sc-teal">
            <div class="stat-label">Spam</div>
            <div class="stat-value">{{ $spamCount }}</div>
        </div>
    </div>
</div>

{{-- Filter bar + search --}}
<div class="card mb-3" style="padding:14px 20px;">
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="d-flex gap-2 flex-wrap">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'spam' => 'Spam', 'all' => 'All'] as $val => $label)
            <a href="{{ route('admin.blog.comments.index', ['status' => $val]) }}"
               class="btn btn-xs {{ $status === $val ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="{{ $status === $val ? 'background:var(--ft-navy);border-color:var(--ft-navy);' : '' }}">
                {{ $label }}
                @if($val === 'pending' && $pendingCount > 0)<span class="badge ms-1" style="background:var(--ft-rose);font-size:10px;">{{ $pendingCount }}</span>@endif
            </a>
            @endforeach
        </div>
        <form action="{{ route('admin.blog.comments.index') }}" method="GET" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search comments…" class="form-control form-control-sm" style="width:200px;font-size:13px;">
            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

{{-- Bulk action form --}}
<form method="POST" action="{{ route('admin.blog.comments.bulk') }}" id="bulkForm">
@csrf

<div class="card">
    @if($comments->count())
    <div class="px-4 py-2 d-flex align-items-center gap-2" style="border-bottom:1px solid var(--bs-border-color);background:var(--bs-tertiary-bg);">
        <input type="checkbox" id="selectAll" class="form-check-input">
        <label for="selectAll" style="font-size:12px;margin-bottom:0;">Select All</label>
        <span class="ms-auto d-flex gap-2">
            <button type="submit" name="action" value="approve" class="btn btn-xs" style="background:var(--ft-emerald);color:#fff;" onclick="return confirmBulk()">
                <i class="fas fa-check me-1"></i> Approve
            </button>
            <button type="submit" name="action" value="spam" class="btn btn-xs btn-outline-warning" onclick="return confirmBulk()">
                <i class="fas fa-ban me-1"></i> Spam
            </button>
            <button type="submit" name="action" value="delete" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete selected comments?')">
                <i class="fas fa-trash me-1"></i> Delete
            </button>
        </span>
    </div>
    @endif

    <div class="comment-mod-list">
        @forelse($comments as $comment)
        <div class="px-4 py-3 d-flex gap-3" style="{{ !$loop->last ? 'border-bottom:1px solid var(--bs-border-color);' : '' }}">
            <input type="checkbox" name="ids[]" value="{{ $comment->id }}" class="form-check-input mt-1 comment-check">
            <div class="flex-1" style="min-width:0;">
                <div class="d-flex flex-wrap gap-2 align-items-start justify-content-between mb-1">
                    <div>
                        <span class="fw-500" style="font-size:13px;">
                            @if($comment->user)
                            {{ $comment->user->name }}
                            <span class="badge ms-1" style="background:var(--ft-teal);font-size:10px;">User</span>
                            @else
                            {{ $comment->guest_name ?? 'Anonymous' }}
                            @if($comment->guest_email)
                            <span style="color:var(--bs-secondary-color);font-size:12px;">&lt;{{ $comment->guest_email }}&gt;</span>
                            @endif
                            <span class="badge ms-1 bg-secondary" style="font-size:10px;">Guest</span>
                            @endif
                        </span>
                        <span style="font-size:11px;color:var(--bs-secondary-color);margin-left:8px;">{{ $comment->created_at->diffForHumans() }}</span>
                        @if($comment->parent_id)
                        <span class="badge ms-1 bg-secondary" style="font-size:10px;"><i class="fas fa-reply me-1"></i>Reply</span>
                        @endif
                    </div>
                    <div class="d-flex gap-1">
                        <span class="badge bg-{{ $comment->status === 'approved' ? 'success' : ($comment->status === 'spam' ? 'danger' : 'warning text-dark') }}">
                            {{ ucfirst($comment->status) }}
                        </span>
                    </div>
                </div>

                <p style="font-size:13px;margin:4px 0 8px;color:var(--bs-body-color);">{{ $comment->content }}</p>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('blog.show', $comment->post->slug) }}#comment-{{ $comment->id }}" target="_blank"
                       style="font-size:12px;color:var(--ft-teal);">
                        <i class="fas fa-external-link-alt me-1"></i>{{ Str::limit($comment->post->title, 40) }}
                    </a>
                    <div class="ms-auto d-flex gap-1">
                        @if($comment->status !== 'approved')
                        <form method="POST" action="{{ route('admin.blog.comments.approve', $comment->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-xs" style="background:var(--ft-emerald);color:#fff;"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                        @if($comment->status !== 'spam')
                        <form method="POST" action="{{ route('admin.blog.comments.spam', $comment->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline-warning"><i class="fas fa-ban"></i></button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.blog.comments.destroy', $comment->id) }}" onsubmit="return confirm('Delete this comment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5" style="color:var(--bs-secondary-color);">
            <i class="fas fa-comments fa-2x mb-2 d-block" style="opacity:.3;"></i>
            No comments in this filter.
        </div>
        @endforelse
    </div>

    @if($comments->hasPages())
    <div class="card-footer" style="padding:12px 20px;border-top:1px solid var(--bs-border-color);">
        {{ $comments->withQueryString()->links() }}
    </div>
    @endif
</div>

</form>

@endsection

@section('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.comment-check').forEach(cb => cb.checked = this.checked);
});
function confirmBulk() {
    const checked = document.querySelectorAll('.comment-check:checked');
    if (checked.length === 0) { alert('Please select at least one comment.'); return false; }
    return true;
}
</script>
@endsection
