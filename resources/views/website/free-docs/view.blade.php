@extends('layouts.app')

@section('title', ucfirst($type) . ' — Free Business Docs | FinTrack')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/free-docs.css') }}">
<style>
  /* Reset any inherited body styles from styles.css that break the white doc */
  .fd-view-doc * { box-sizing: border-box; }
  .fd-view-doc table { border-collapse: collapse; }
</style>
@endpush

@section('content')

<div class="fd-view-page">

    {{-- Top action bar --}}
    <div class="fd-view-header">
        <div>
            <div style="font-size:14px; font-weight:700; color:#e6edf3; text-transform:capitalize;">
                <i class="fa-solid fa-{{ $type === 'invoice' ? 'file-invoice-dollar' : ($type === 'quote' ? 'file-lines' : 'receipt') }}"
                   style="color:#22d3ee; margin-right:6px;"></i>
                {{ ucfirst($type) }}
                @if(!empty($docData['details']['number']))
                    &nbsp;·&nbsp; #{{ $docData['details']['number'] }}
                @endif
            </div>
            <div class="fd-view-meta">
                Shared {{ $freeDoc->created_at->diffForHumans() }}
                &nbsp;·&nbsp;
                Expires {{ $freeDoc->expires_at ? $freeDoc->expires_at->format('d M Y') : 'never' }}
                &nbsp;·&nbsp;
                {{ number_format($freeDoc->view_count) }} {{ Str::plural('view', $freeDoc->view_count) }}
            </div>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <a href="{{ route('free-docs.index') }}" class="fd-btn fd-btn-outline" style="font-size:12px;">
                <i class="fa-solid fa-plus"></i> Create New
            </a>
            <a href="{{ route('free-docs.view-pdf', $freeDoc->token) }}" class="fd-btn fd-btn-primary" style="font-size:12px;" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    {{-- Document preview — server-rendered using the shared PDF template --}}
    <div class="fd-view-doc">
        {{-- We include the body content of the PDF template directly.
             The template has its own <style> block which browsers handle fine in body context. --}}
        @include("website.free-docs.pdf.{$template}", ['doc' => $docData, 'type' => $type])
    </div>

    {{-- Footer branding --}}
    <div style="text-align:center; font-size:12px; color:#5c6470; padding:8px 0 20px; line-height:1.8;">
        <i class="fa-solid fa-shield-halved" style="color:#22d3ee; margin-right:4px;"></i>
        Created with
        <a href="{{ route('free-docs.index') }}" style="color:#22d3ee; font-weight:600;">
            FinTrack Free Business Docs
        </a>
        — professional invoices, quotes &amp; receipts for everyone.
        <br>
        <a href="{{ route('register') }}" style="color:#8b949e; font-size:11.5px;">
            Want to track payments &amp; manage clients? Sign up free →
        </a>
    </div>

</div>

@endsection
