@extends('layouts.portal')

@section('title', 'New Recurring Transaction - FinTrack')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">New Recurring Transaction</h4>
        <p class="page-subtitle">Set up an auto-generating expense or income</p>
    </div>
    <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<form method="POST" action="{{ route('recurring.store') }}" id="recurring-form">
    @csrf
    @include('portal.recurring._form', ['mode' => 'create'])

    <div class="d-flex gap-2 mt-2 mb-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i> Create Recurring Transaction
        </button>
        <a href="{{ route('recurring.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
