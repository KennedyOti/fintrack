@php
    // ── Doc settings with defaults ──────────────────────────────────────────
    $ds         = $docSettings ?? [];
    $primaryClr = $ds['primary_color']   ?? '#0B2A4A';
    $accentClr  = $ds['accent_color']    ?? '#0E7490';
    $hlClr      = $ds['highlight_color'] ?? '#22D3EE';
    $layout     = $ds['layout']          ?? 'classic';
    $showLogo   = $ds['show_logo']       ?? true;
    $footerNote = $ds['footer_note']     ?? 'Thank you for your business.';

    // Font stack
    $fontMap = [
        'sans'  => "DejaVu Sans, Arial, 'Helvetica Neue', sans-serif",
        'serif' => "DejaVu Serif, Georgia, 'Times New Roman', serif",
        'mono'  => "Courier New, Courier, monospace",
    ];
    $fontStack = $fontMap[$ds['font'] ?? 'sans'] ?? $fontMap['sans'];

    // Derived colour helpers
    // For minimal/modern layouts we flip the header background
    $isMinimal = $layout === 'minimal';
    $isModern  = $layout === 'modern';
    $isClassic = $layout === 'classic';

    $headerBg      = ($isMinimal || $isModern) ? '#FFFFFF' : $primaryClr;
    $headerColor   = ($isMinimal || $isModern) ? '#0F172A' : '#FFFFFF';
    $headerSubClr  = ($isMinimal || $isModern) ? '#64748B' : 'rgba(255,255,255,0.65)';
    $docLabelColor = ($isMinimal)              ? $accentClr : $hlClr;
    $docNumColor   = ($isMinimal || $isModern) ? '#64748B'  : 'rgba(255,255,255,0.7)';
    $headerBorder  = ($isMinimal) ? "border-bottom:2px solid {$accentClr};" : (($isModern) ? "border-bottom:3px solid {$accentClr};" : '');

    $isExpired = $quote->valid_until->isPast() && !in_array($quote->status, ['accepted', 'converted']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 14mm 14mm 14mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: {{ $fontStack }};
            font-size: 11px;
            line-height: 1.45;
            color: #1E293B;
            background: #fff;
        }

        /* ── Header ─────────────────────────────────────────── */
        .doc-header {
            background-color: {{ $headerBg }};
            padding: {{ $isMinimal ? '12px 0 12px 0' : '18px 22px' }};
            {{ $headerBorder }}
        }
        .hdr-table { width: 100%; border-collapse: collapse; }
        .hdr-table td { vertical-align: middle; padding: 0; border: none; background: transparent; }

        @if($isModern && ($logoBase64 ?? null) && $showLogo)
        .hdr-logo-cell { width: 70px; vertical-align: middle; }
        .hdr-logo { max-height: 52px; max-width: 120px; }
        @endif

        .biz-name {
            font-size: {{ $isMinimal ? '17px' : '18px' }};
            font-weight: bold;
            color: {{ $headerColor }};
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }
        .biz-detail {
            font-size: 9.5px;
            color: {{ $headerSubClr }};
            line-height: 1.7;
        }
        .doc-type-label {
            font-size: 26px;
            font-weight: bold;
            color: {{ $docLabelColor }};
            text-transform: uppercase;
            letter-spacing: 3px;
            text-align: right;
            line-height: 1;
            margin-bottom: 4px;
        }
        .doc-number {
            font-size: 12px;
            color: {{ $docNumColor }};
            text-align: right;
            margin-bottom: 7px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-draft     { background-color: #64748B; color: #fff; }
        .status-sent      { background-color: {{ $accentClr }}; color: #fff; }
        .status-accepted  { background-color: #22C55E; color: #fff; }
        .status-rejected  { background-color: #F43F5E; color: #fff; }
        .status-expired   { background-color: #F59E0B; color: #fff; }
        .status-converted { background-color: #8B5CF6; color: #fff; }

        /* ── Accent bar ──────────────────────────────────────── */
        .accent-bar {
            height: 3px;
            background-color: {{ $accentClr }};
            margin-bottom: 14px;
        }

        /* ── 3-column info row: Prepared By | Prepared For | Details ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .info-table td { vertical-align: top; border: none; }

        .info-cell {
            background-color: #F8FAFC;
            border-radius: 5px;
            padding: 10px 12px;
        }
        .info-cell-from    { border-left: 3px solid {{ $primaryClr }}; }
        .info-cell-to      { border-left: 3px solid {{ $accentClr }}; }
        .info-cell-details { border-left: 3px solid {{ $primaryClr }}; }

        .info-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .client-name {
            font-size: 12px;
            font-weight: bold;
            color: #0F172A;
            margin-bottom: 3px;
        }
        .client-detail { font-size: 9.5px; color: #64748B; line-height: 1.6; }

        .meta-row { width: 100%; margin-bottom: 3px; }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-l { display: inline-block; width: 50%; color: #64748B; font-size: 9.5px; }
        .meta-v { font-weight: 600; color: #1E293B; font-size: 9.5px; }
        .meta-v-expired { color: #F43F5E; }

        /* ── Items table ─────────────────────────────────────── */
        .section-label {
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .items-table thead tr { background-color: {{ $primaryClr }}; }
        .items-table th {
            padding: 9px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.85);
            text-align: left;
        }
        .items-table tbody td {
            padding: 8px 10px;
            font-size: 10.5px;
            color: #334155;
            border-bottom: 1px solid #E2E8F0;
        }
        .items-table tbody tr:nth-child(even) td { background-color: #F8FAFC; }
        .items-table tbody tr:last-child td { border-bottom: none; }

        /* ── Bottom area ─────────────────────────────────────── */
        .bottom-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .bottom-table td { vertical-align: top; border: none; }

        .notes-box {
            background-color: #FEFCE8;
            border-left: 3px solid #F59E0B;
            border-radius: 5px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }
        .notes-title { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #92400E; margin-bottom: 5px; }
        .notes-text  { font-size: 10px; color: #78350F; line-height: 1.5; }

        .terms-box {
            background-color: #F1F5F9;
            border-left: 3px solid {{ $accentClr }};
            border-radius: 5px;
            padding: 10px 12px;
        }
        .terms-title { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #334155; margin-bottom: 5px; }
        .terms-item  { font-size: 9.5px; color: #64748B; line-height: 1.6; margin-bottom: 2px; }

        .summary-box { background-color: #F8FAFC; border-radius: 5px; padding: 11px 13px; }
        .sum-row { width: 100%; border-bottom: 1px solid #E2E8F0; padding: 4px 0; }
        .sum-row:last-child { border-bottom: none; }
        .sum-l { display: inline-block; width: 55%; color: #64748B; font-size: 10.5px; }
        .sum-v { font-weight: 600; font-size: 10.5px; color: #1E293B; }
        .sum-divider { border-top: 2px solid {{ $primaryClr }}; padding-top: 6px; margin-top: 2px; border-bottom: none; }
        .sum-l-total { display: inline-block; width: 55%; font-weight: bold; font-size: 11px; color: #0F172A; }
        .sum-v-total { font-weight: bold; font-size: 14px; color: {{ $primaryClr }}; }

        /* ── Validity banner ─────────────────────────────────── */
        .validity-bar {
            background-color: {{ $accentClr }};
            color: #fff;
            text-align: center;
            padding: 7px 14px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 14px;
            letter-spacing: 0.3px;
        }
        .validity-bar.expired { background-color: #F43F5E; }

        /* ── Footer ──────────────────────────────────────────── */
        .doc-footer { border-top: 1px solid #E2E8F0; padding-top: 9px; text-align: center; }
        .footer-biz { font-size: 10px; font-weight: 600; color: #475569; margin-bottom: 2px; }
        .footer-sub { font-size: 8.5px; color: #94A3B8; }
    </style>
</head>
<body>

{{-- ── Header ───────────────────────────────────────────────────── --}}
<div class="doc-header">
    <table class="hdr-table">
        <tr>
            @if(($isModern || $isClassic) && ($logoBase64 ?? null) && $showLogo)
            <td style="width:{{ $isModern ? '70px' : '65px' }}; padding-right:12px; vertical-align:middle;">
                <img src="{{ $logoBase64 }}" alt="Logo" style="max-height:{{ $isModern ? '52px' : '46px' }};max-width:{{ $isModern ? '120px' : '110px' }};">
            </td>
            @endif

            <td>
                <div class="biz-name">{{ $businessInfo['business_name'] }}</div>
                <div class="biz-detail">
                    @if($businessInfo['business_address'])
                        {{ str_replace(["\r\n", "\n", "\r"], ' · ', trim($businessInfo['business_address'])) }}&nbsp;
                    @endif
                    @if($businessInfo['email']) {{ $businessInfo['email'] }} @endif
                    @if($businessInfo['phone']) &nbsp;·&nbsp; {{ $businessInfo['phone'] }} @endif
                    @if($businessInfo['tax_number']) &nbsp;·&nbsp; Tax ID: {{ $businessInfo['tax_number'] }} @endif
                </div>
            </td>

            <td style="width:200px; vertical-align:top; text-align:right;">
                @if($isMinimal && ($logoBase64 ?? null) && $showLogo)
                <div style="text-align:right; margin-bottom:6px;">
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height:40px;max-width:100px;">
                </div>
                @endif
                <div class="doc-type-label">Quote</div>
                <div class="doc-number"># {{ $quote->quote_number }}</div>
                <span class="status-badge status-{{ $quote->status }}">{{ ucfirst($quote->status) }}</span>
            </td>
        </tr>
    </table>
</div>

@if(!$isMinimal && !$isModern)
<div class="accent-bar"></div>
@else
<div style="height:3px;background:{{ $accentClr }};margin-bottom:14px;"></div>
@endif

{{-- ── Validity banner ─────────────────────────────────────────── --}}
<div class="validity-bar {{ $isExpired ? 'expired' : '' }}">
    @if($isExpired)
        &#9888;&nbsp; This quote expired on {{ $quote->valid_until->format('M d, Y') }}. Please contact us for an updated quote.
    @else
        This quote is valid until &nbsp;<strong>{{ $quote->valid_until->format('M d, Y') }}</strong>
        @if(in_array($quote->status, ['accepted', 'converted']))
            &nbsp;&mdash;&nbsp; Accepted
        @endif
    @endif
</div>

{{-- ── 3-column info: Prepared By | Prepared For | Quote Details ── --}}
<table class="info-table">
    <tr>
        {{-- Prepared By --}}
        <td style="width:31%;">
            <div class="info-cell info-cell-from">
                <div class="info-label">Prepared By</div>
                <div class="client-name">{{ $businessInfo['business_name'] }}</div>
                <div class="client-detail">
                    @if($businessInfo['email'])        {{ $businessInfo['email'] }}<br>        @endif
                    @if($businessInfo['phone'])        {{ $businessInfo['phone'] }}<br>        @endif
                    @if($businessInfo['business_address'])
                        {{ str_replace(["\r\n", "\n", "\r"], ', ', trim($businessInfo['business_address'])) }}
                    @endif
                </div>
            </div>
        </td>
        <td style="width:2%;"></td>
        {{-- Prepared For --}}
        <td style="width:31%;">
            <div class="info-cell info-cell-to">
                <div class="info-label">Prepared For</div>
                @if($quote->client)
                    <div class="client-name">{{ $quote->client->name }}</div>
                    <div class="client-detail">
                        @if($quote->client->company_name) {{ $quote->client->company_name }}<br> @endif
                        @if($quote->client->email)        {{ $quote->client->email }}<br>        @endif
                        @if($quote->client->phone)        {{ $quote->client->phone }}<br>        @endif
                        @if($quote->client->address)      {{ $quote->client->address }}          @endif
                    </div>
                @else
                    <div class="client-detail" style="color:#94A3B8;">No client assigned</div>
                @endif
            </div>
        </td>
        <td style="width:2%;"></td>
        {{-- Quote Details --}}
        <td style="width:34%;">
            <div class="info-cell info-cell-details">
                <div class="info-label">Quote Details</div>
                <div class="meta-row">
                    <span class="meta-l">Quote #</span>
                    <span class="meta-v">{{ $quote->quote_number }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-l">Issue Date</span>
                    <span class="meta-v">{{ $quote->issue_date->format('M d, Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-l">Valid Until</span>
                    <span class="meta-v {{ $isExpired ? 'meta-v-expired' : '' }}">
                        {{ $quote->valid_until->format('M d, Y') }}@if($isExpired) &#9888;@endif
                    </span>
                </div>
                @if($quote->project)
                <div class="meta-row">
                    <span class="meta-l">Project</span>
                    <span class="meta-v">{{ $quote->project->title }}</span>
                </div>
                @endif
                <div class="meta-row">
                    <span class="meta-l">Currency</span>
                    <span class="meta-v">{{ $currencyCode }}</span>
                </div>
            </div>
        </td>
    </tr>
</table>

{{-- ── Scope of Work ────────────────────────────────────────────── --}}
<div class="section-label">Scope of Work</div>
<table class="items-table">
    <thead>
        <tr>
            <th style="width:47%;">Description</th>
            <th style="width:11%; text-align:center;">Qty</th>
            <th style="width:21%; text-align:right;">Unit Price</th>
            <th style="width:21%; text-align:right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($quote->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td style="text-align:center; color:#64748B;">{{ number_format($item->quantity, 2) }}</td>
            <td style="text-align:right; color:#64748B;">{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align:right; font-weight:600; color:#0F172A;">{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align:center; color:#94A3B8; padding:16px 10px;">No items</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ── Notes + Summary ─────────────────────────────────────────── --}}
<table class="bottom-table">
    <tr>
        <td>
            @if($quote->notes)
            <div class="notes-box">
                <div class="notes-title">Notes</div>
                <div class="notes-text">{{ $quote->notes }}</div>
            </div>
            @endif

            <div class="terms-box">
                <div class="terms-title">Terms &amp; Conditions</div>
                <div class="terms-item">&#8226; Valid until {{ $quote->valid_until->format('M d, Y') }}. Prices subject to change after validity period.</div>
                <div class="terms-item">&#8226; To accept, reply to this quote or contact us directly.</div>
                <div class="terms-item">&#8226; Payment terms will be confirmed upon acceptance.</div>
            </div>
        </td>
        <td style="width:14px;"></td>
        <td style="width:215px; vertical-align:top;">
            <div class="summary-box">
                <div class="sum-row">
                    <span class="sum-l">Subtotal</span>
                    <span class="sum-v" style="float:right;">{{ $currencySymbol }}{{ number_format($quote->subtotal, 2) }}</span>
                </div>
                @if($quote->tax_amount > 0)
                <div class="sum-row">
                    <span class="sum-l">Tax</span>
                    <span class="sum-v" style="float:right;">{{ $currencySymbol }}{{ number_format($quote->tax_amount, 2) }}</span>
                </div>
                @endif
                @if($quote->discount_amount > 0)
                <div class="sum-row">
                    <span class="sum-l">Discount</span>
                    <span class="sum-v" style="float:right; color:#22C55E;">-{{ $currencySymbol }}{{ number_format($quote->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="sum-divider">
                    <span class="sum-l-total">Total ({{ $currencyCode }})</span>
                    <span class="sum-v-total" style="float:right;">{{ $currencySymbol }}{{ number_format($quote->total_amount, 2) }}</span>
                </div>
            </div>
        </td>
    </tr>
</table>

{{-- ── Footer ───────────────────────────────────────────────────── --}}
<div class="doc-footer">
    <div class="footer-biz">{{ $businessInfo['business_name'] }}</div>
    <div class="footer-sub">
        {{ $footerNote }} &nbsp;&middot;&nbsp;
        Generated by FinTrack &nbsp;&middot;&nbsp;
        {{ now()->format('M d, Y') }}
    </div>
</div>

</body>
</html>
