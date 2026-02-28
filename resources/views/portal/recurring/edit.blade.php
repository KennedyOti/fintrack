@extends('layouts.portal')

@section('title', 'Edit Recurring Transaction - FinTrack')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">Edit Recurring Transaction</h4>
        <p class="page-subtitle">{{ $recurring->name }}</p>
    </div>
    <a href="{{ route('recurring.show', $recurring) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('recurring.update', $recurring) }}" id="recurring-form">
    @csrf
    @method('PUT')
    @include('portal.recurring._form', ['mode' => 'edit'])

    <div class="d-flex gap-2 mt-2 mb-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i> Save Changes
        </button>
        <a href="{{ route('recurring.show', $recurring) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
