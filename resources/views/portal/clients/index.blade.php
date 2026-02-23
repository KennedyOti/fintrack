@extends('layouts.portal')

@section('title', 'Clients - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Clients</h4>
        <p class="text-muted mb-0">Manage your client relationships</p>
    </div>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add Client
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name, company, or email..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            @if($search || request('status'))
            <div class="col-md-2">
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Clients Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($clients->count() > 0)
        <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Projects</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                    <tr>
                        <td>
                            <a href="{{ route('clients.show', $client->id) }}" class="text-decoration-none fw-semibold">
                                {{ $client->name }}
                            </a>
                        </td>
                        <td>{{ $client->company_name ?: '-' }}</td>
                        <td>
                            <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                {{ $client->email }}
                            </a>
                        </td>
                        <td>{{ $client->phone ?: '-' }}</td>
                        <td>
                            @if($client->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $client->projects->count() }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('clients.show', $client->id) }}">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger btn-delete">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5>No clients found</h5>
            <p class="text-muted">Start building your client base by adding your first client.</p>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Client
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($clients->hasPages())
<div class="mt-4">
    {{ $clients->links() }}
</div>
@endif

@push('scripts')
<script>
    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection
