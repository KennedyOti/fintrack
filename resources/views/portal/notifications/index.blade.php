@extends('layouts.portal')

@section('title', 'Notifications - FinTrack')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Notifications</h1>
        <p class="page-subtitle">Stay on top of invoices, deadlines, and financial alerts</p>
    </div>
    <div class="page-actions">
        @if($unreadCount > 0)
        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-check-double me-1"></i>Mark all read
            </button>
        </form>
        @endif
        <form action="{{ route('notifications.destroyRead') }}" method="POST" class="d-inline"
              onsubmit="return confirm('Clear all read notifications?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-trash-can me-1"></i>Clear read
            </button>
        </form>
        <a href="{{ route('settings.index') }}?tab=notifications" class="btn btn-sm btn-primary">
            <i class="fas fa-gear me-1"></i>Notification Settings
        </a>
    </div>
</div>

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-700" style="font-size:22px;color:var(--ft-teal);">{{ $unreadCount }}</div>
            <div class="text-muted" style="font-size:12px;">Unread</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-700" style="font-size:22px;color:var(--text-h);">{{ $notifications->total() }}</div>
            <div class="text-muted" style="font-size:12px;">Total</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('notifications.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read"   {{ request('status') === 'read'   ? 'selected' : '' }}>Read</option>
            </select>
            <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="invoice_overdue"  {{ request('type') === 'invoice_overdue'  ? 'selected' : '' }}>Invoice Overdue</option>
                <option value="quote_expiring"   {{ request('type') === 'quote_expiring'   ? 'selected' : '' }}>Quote Expiring</option>
                <option value="debt_due"         {{ request('type') === 'debt_due'         ? 'selected' : '' }}>Debt Due</option>
                <option value="savings_goal"     {{ request('type') === 'savings_goal'     ? 'selected' : '' }}>Savings Goal</option>
                <option value="project_deadline" {{ request('type') === 'project_deadline' ? 'selected' : '' }}>Project Deadline</option>
                <option value="budget_alert"     {{ request('type') === 'budget_alert'     ? 'selected' : '' }}>Budget Alert</option>
                <option value="payment_received" {{ request('type') === 'payment_received' ? 'selected' : '' }}>Payment Received</option>
                <option value="system"           {{ request('type') === 'system'           ? 'selected' : '' }}>System</option>
            </select>
            @if(request('status') || request('type'))
            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-xmark me-1"></i>Clear filters
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Notifications list --}}
<div class="card border-0 shadow-sm">
    @forelse($notifications as $notif)
    <div class="notif-row {{ $notif->is_read ? '' : 'unread' }}" id="notif-row-{{ $notif->id }}">

        {{-- Icon --}}
        <div class="notif-row-icon" style="background:{{ $notif->getIconBg() }};">
            <i class="{{ $notif->getIcon() }}" style="color:{{ $notif->getIconColor() }};font-size:20px;"></i>
        </div>

        {{-- Content --}}
        <div class="notif-row-content">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="notif-row-title">{{ $notif->title }}</span>
                <span class="badge notif-type-badge" style="background:{{ $notif->getIconBg() }};color:{{ $notif->getIconColor() }};">
                    {{ $notif->getTypeLabel() }}
                </span>
                @if(!$notif->is_read)
                <span class="badge" style="background:var(--ft-teal);font-size:10px;">New</span>
                @endif
            </div>
            <p class="notif-row-msg mb-1">{{ $notif->message }}</p>
            <span class="notif-row-time">
                <i class="fas fa-clock me-1" style="opacity:.5;"></i>
                {{ $notif->created_at->diffForHumans() }}
                &middot; {{ $notif->created_at->format('M j, Y g:i A') }}
            </span>
        </div>

        {{-- Actions --}}
        <div class="notif-row-actions">
            @if($notif->getLink() !== '#')
            <a href="{{ $notif->getLink() }}" class="btn btn-xs btn-outline-secondary" title="View">
                <i class="fas fa-arrow-up-right-from-square"></i>
            </a>
            @endif

            @if(!$notif->is_read)
            <button class="btn btn-xs btn-outline-primary notif-mark-btn"
                    data-id="{{ $notif->id }}" title="Mark as read">
                <i class="fas fa-check"></i>
            </button>
            @endif

            <form action="{{ route('notifications.destroy', $notif) }}" method="POST" class="d-inline notif-delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-outline-danger btn-delete" title="Delete">
                    <i class="fas fa-trash-can"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div class="mb-3" style="font-size:48px;opacity:.25;">
            <i class="fas fa-bell-slash"></i>
        </div>
        <h6 class="text-muted mb-1">No notifications found</h6>
        <p class="text-muted" style="font-size:13px;">
            @if(request('status') || request('type'))
                Try adjusting your filters.
            @else
                Notifications will appear here when there's something that needs your attention.
            @endif
        </p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($notifications->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $notifications->links() }}
</div>
@endif

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const CSRF = '{{ csrf_token() }}';

    // Mark individual as read
    document.querySelectorAll('.notif-mark-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id  = btn.dataset.id;
            var row = document.getElementById('notif-row-' + id);
            fetch('/notifications/' + id + '/read', {
                method:  'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).then(function () {
                if (row) row.classList.remove('unread');
                btn.remove();
            }).catch(function () {});
        });
    });

    // Delete with AJAX on notification index page
    document.querySelectorAll('.notif-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!confirm('Delete this notification?')) return;
            var row = form.closest('.notif-row');
            fetch(form.action, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).then(function () {
                if (row) {
                    row.style.transition = 'opacity .2s, max-height .3s';
                    row.style.opacity    = '0';
                    row.style.maxHeight  = '0';
                    row.style.overflow   = 'hidden';
                    setTimeout(function () { row.remove(); }, 350);
                }
            }).catch(function () { form.submit(); });
        });
    });
});
</script>
@endsection
