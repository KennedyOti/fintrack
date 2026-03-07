<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: {{ $doc['font'] === 'georgia' ? 'Georgia, serif' : ($doc['font'] === 'courier' ? '"Courier New", monospace' : ($doc['font'] === 'verdana' ? 'Verdana, sans-serif' : ($doc['font'] === 'trebuchet' ? '"Trebuchet MS", sans-serif' : 'Helvetica, Arial, sans-serif'))) }};
    color: #1a1a2e;
    background: #fff;
    font-size: 13px;
}
.accent-bar { position: fixed; left: 0; top: 0; width: 6px; height: 100%; background: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; }
.page { padding: 36px 36px 36px 48px; }
table { border-collapse: collapse; width: 100%; }
.header-table td { vertical-align: top; }
.doc-title { font-size: 30px; font-weight: 900; color: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; letter-spacing: -1px; }
.doc-num { font-size: 12px; color: #666; margin-top: 4px; }
.biz-name { font-size: 18px; font-weight: 800; color: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; }
.biz-sub { font-size: 11px; color: #555; margin-top: 4px; line-height: 1.5; }
.logo-img { max-height: 60px; max-width: 140px; }
.meta-bar { background: #f8f9fa; border-radius: 8px; padding: 14px 18px; margin: 20px 0 22px; }
.meta-cell { vertical-align: top; padding: 0 16px; }
.meta-cell:first-child { padding-left: 0; }
.meta-cell + .meta-cell { border-left: 1px solid #e5e7eb; }
.meta-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #888; margin-bottom: 4px; }
.meta-val { font-size: 12.5px; font-weight: 600; color: #1a1a2e; }
.meta-val.due { color: #dc2626; }
.items-table { margin-top: 0; }
.items-table th {
    background: {{ $doc['colors']['primary'] ?? '#0B2A4A' }};
    color: #fff;
    padding: 10px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.items-table th.right { text-align: right; }
.items-table td { padding: 9px 10px; border-bottom: 1px solid #f0f0f0; font-size: 12.5px; }
.items-table td.right { text-align: right; }
.items-table td.bold { font-weight: 600; }
.totals-row td { padding: 6px 10px; font-size: 12px; }
.totals-row td.label { text-align: right; color: #555; }
.totals-row td.value { text-align: right; font-weight: 500; }
.grand-total td { padding: 10px 10px; border-top: 2px solid {{ $doc['colors']['accent'] ?? '#22D3EE' }}; }
.grand-total td.label { text-align: right; font-size: 13.5px; font-weight: 700; }
.grand-total td.value { text-align: right; font-size: 13.5px; font-weight: 800; color: {{ $doc['colors']['accent'] ?? '#22D3EE' }}; }
.footer-area { margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 16px; }
.footer-label { font-weight: 700; color: #222; font-size: 12px; }
.footer-text { font-size: 12px; color: #444; margin-bottom: 8px; line-height: 1.5; }
.brand-line { margin-top: 28px; text-align: center; font-size: 10px; color: #ccc; }
</style>
</head>
<body>
<div class="accent-bar"></div>
<div class="page">

@php
    $primary   = $doc['colors']['primary']   ?? '#0B2A4A';
    $accent    = $doc['colors']['accent']    ?? '#22D3EE';
    $from      = $doc['from']    ?? [];
    $to        = $doc['to']      ?? [];
    $details   = $doc['details'] ?? [];
    $items     = $doc['items']   ?? [];
    $tax       = $doc['tax']     ?? ['enabled'=>false,'label'=>'Tax','rate'=>0];
    $discount  = $doc['discount']?? ['enabled'=>false,'type'=>'percentage','value'=>0];
    $shipping  = $doc['shipping']?? ['enabled'=>false,'amount'=>0];
    $paid      = $doc['paid']    ?? ['enabled'=>false,'amount'=>0];
    $notes     = $doc['notes']   ?? '';
    $terms     = $doc['terms']   ?? '';
    $payInfo   = $doc['payment_info'] ?? '';

    $subtotal  = collect($items)->sum(fn($i) => ($i['qty']??0) * ($i['rate']??0));
    $discAmt   = 0;
    if ($discount['enabled'] ?? false) {
        $discAmt = ($discount['type'] ?? 'percentage') === 'percentage'
            ? $subtotal * (($discount['value']??0) / 100)
            : ($discount['value'] ?? 0);
    }
    $afterDisc = $subtotal - $discAmt;
    $taxAmt    = ($tax['enabled']      ?? false) ? $afterDisc * (($tax['rate']??0) / 100) : 0;
    $shipAmt   = ($shipping['enabled'] ?? false) ? ($shipping['amount']??0) : 0;
    $total     = $afterDisc + $taxAmt + $shipAmt;
    $paidAmt   = ($paid['enabled']     ?? false) ? ($paid['amount']??0) : 0;
    $due       = $total - $paidAmt;

    $currency  = $doc['currency'] ?? ['symbol'=>'$','pos'=>'before'];
    function fmtPdf($amt, $cur) {
        $num = number_format((float)$amt, 2);
        return ($cur['pos']??'before')==='after' ? $num.($cur['symbol']??'$') : ($cur['symbol']??'$').$num;
    }
    function fmtDatePdf($d) {
        if (!$d) return '—';
        try { return (new DateTime($d))->format('d M Y'); } catch(\Exception $e) { return $d; }
    }
    $docLabels = ['invoice'=>'INVOICE','quote'=>'QUOTATION','receipt'=>'RECEIPT'];
    $title = $docLabels[$type ?? 'invoice'] ?? 'DOCUMENT';
    $dueLbl = ($type==='quote') ? 'Valid Until' : (($type==='receipt') ? 'Receipt Date' : 'Due Date');
    function addrPdf($obj) {
        $parts = array_filter([
            $obj['address'] ?? '',
            trim(($obj['city']??'').' '.($obj['state']??'')),
            $obj['zip'] ?? '',
            $obj['country'] ?? '',
        ]);
        return implode(', ', $parts);
    }
@endphp

<!-- Header -->
<table class="header-table" style="margin-bottom:8px;">
    <tr>
        <td>
            @if(!empty($from['logo']))
                <img src="{{ $from['logo'] }}" class="logo-img" alt="Logo">
            @endif
            <div class="biz-name" style="margin-top:{{ !empty($from['logo']) ? '8' : '0' }}px;">{{ $from['name'] ?? 'Your Business' }}</div>
            <div class="biz-sub">
                @if(!empty($from['email'])){{ $from['email'] }}@endif
                @if(!empty($from['email']) && !empty($from['phone'])) &nbsp;·&nbsp; @endif
                @if(!empty($from['phone'])){{ $from['phone'] }}@endif
            </div>
        </td>
        <td style="text-align:right;">
            <div class="doc-title">{{ $title }}</div>
            <div class="doc-num"># {{ $details['number'] ?? '001' }}</div>
        </td>
    </tr>
</table>

<!-- From address small line -->
@if(!empty(addrPdf($from)))
<div style="font-size:11px;color:#666;margin-bottom:16px;">{{ addrPdf($from) }}</div>
@endif

<!-- Meta bar -->
<div class="meta-bar">
    <table>
        <tr>
            <td class="meta-cell" style="width:35%;">
                <div class="meta-label">{{ $type === 'quote' ? 'Prepared For' : 'Bill To' }}</div>
                <div class="meta-val" style="font-size:13px;">{{ $to['company'] ?? ($to['name'] ?? '—') }}</div>
                @if(!empty($to['company']) && !empty($to['name']))
                <div style="font-size:11.5px;color:#555;">{{ $to['name'] }}</div>
                @endif
                @if(!empty(addrPdf($to)))
                <div style="font-size:11px;color:#666;margin-top:2px;">{{ addrPdf($to) }}</div>
                @endif
                @if(!empty($to['email']))
                <div style="font-size:11px;color:#666;">{{ $to['email'] }}</div>
                @endif
            </td>
            <td class="meta-cell" style="width:22%;text-align:center;">
                <div class="meta-label">Issue Date</div>
                <div class="meta-val">{{ fmtDatePdf($details['date'] ?? '') }}</div>
            </td>
            <td class="meta-cell" style="width:22%;text-align:center;">
                <div class="meta-label">{{ $dueLbl }}</div>
                <div class="meta-val {{ $type==='invoice' ? 'due' : '' }}">
                    {{ fmtDatePdf($details['due_date'] ?? '') }}
                </div>
            </td>
            @if(!empty($details['po_number']))
            <td class="meta-cell" style="text-align:right;">
                <div class="meta-label">PO Number</div>
                <div class="meta-val">{{ $details['po_number'] }}</div>
            </td>
            @endif
        </tr>
    </table>
</div>

<!-- Line Items -->
<table class="items-table">
    <thead>
        <tr>
            <th style="text-align:left;width:50%;">Description</th>
            <th class="right" style="width:12%;">Qty</th>
            <th class="right" style="width:18%;">Rate</th>
            <th class="right" style="width:20%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item['description'] ?? '' }}</td>
            <td class="right">{{ $item['qty'] ?? 1 }}</td>
            <td class="right">{{ fmtPdf(($item['rate'] ?? 0), $currency) }}</td>
            <td class="right bold">{{ fmtPdf(($item['qty']??1)*($item['rate']??0), $currency) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="totals-row">
            <td colspan="3" class="label">Subtotal</td>
            <td class="value">{{ fmtPdf($subtotal, $currency) }}</td>
        </tr>
        @if($discount['enabled'] ?? false)
        <tr class="totals-row">
            <td colspan="3" class="label">{{ ($discount['type']??'percentage')==='percentage' ? 'Discount ('.$discount['value'].'%)' : 'Discount' }}</td>
            <td class="value" style="color:#dc2626;">- {{ fmtPdf($discAmt, $currency) }}</td>
        </tr>
        @endif
        @if($tax['enabled'] ?? false)
        <tr class="totals-row">
            <td colspan="3" class="label">{{ $tax['label'] ?? 'Tax' }} ({{ $tax['rate'] ?? 0 }}%)</td>
            <td class="value">{{ fmtPdf($taxAmt, $currency) }}</td>
        </tr>
        @endif
        @if($shipping['enabled'] ?? false)
        <tr class="totals-row">
            <td colspan="3" class="label">Shipping</td>
            <td class="value">{{ fmtPdf($shipAmt, $currency) }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td colspan="3" class="label">{{ $type === 'receipt' ? 'Total Paid' : 'Total Amount' }}</td>
            <td class="value">{{ fmtPdf($total, $currency) }}</td>
        </tr>
        @if(($paid['enabled'] ?? false) && $type !== 'receipt')
        <tr class="totals-row">
            <td colspan="3" class="label">Amount Paid</td>
            <td class="value" style="color:#16a34a;">{{ fmtPdf($paidAmt, $currency) }}</td>
        </tr>
        <tr class="totals-row">
            <td colspan="3" class="label" style="font-weight:700;color:#1a1a2e;">Balance Due</td>
            <td class="value" style="font-weight:700;color:#dc2626;">{{ fmtPdf($due, $currency) }}</td>
        </tr>
        @endif
    </tfoot>
</table>

<!-- Footer -->
@if($notes || $terms || $payInfo)
<div class="footer-area">
    @if($notes)
    <p class="footer-text"><span class="footer-label">Notes: </span>{{ $notes }}</p>
    @endif
    @if($terms)
    <p class="footer-text"><span class="footer-label">Terms & Conditions: </span>{{ $terms }}</p>
    @endif
    @if($payInfo)
    <p class="footer-text"><span class="footer-label">Payment Info: </span>{{ $payInfo }}</p>
    @endif
</div>
@endif

<div class="brand-line">Generated with FinTrack Free Business Docs &nbsp;·&nbsp; fintrack.co.ke</div>
</div>
</body>
</html>
