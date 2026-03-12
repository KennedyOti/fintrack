<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: {{ $doc['font'] === 'georgia' ? 'Georgia, serif' : ($doc['font'] === 'courier' ? '"Courier New", monospace' : ($doc['font'] === 'verdana' ? 'Verdana, sans-serif' : ($doc['font'] === 'trebuchet' ? '"Trebuchet MS", sans-serif' : 'Helvetica, Arial, sans-serif'))) }};
    color: #0f172a; background: #fff; font-size: 13px;
}
.page { padding: 44px 48px; }
table { border-collapse: collapse; width: 100%; }
.doc-title { font-size: 36px; font-weight: 900; color: {{ $doc['colors']['accent'] ?? '#22D3EE' }}; letter-spacing: -2px; }
.doc-num { font-size: 11.5px; color: #94a3b8; margin-top: 4px; }
.biz-name { font-size: 14px; font-weight: 700; color: #0f172a; }
.biz-sub { font-size: 11.5px; color: #64748b; margin-top: 3px; line-height: 1.5; }
.logo-img { max-height: 52px; max-width: 120px; }
.divider { border: none; border-top: 2px solid {{ $doc['colors']['accent'] ?? '#22D3EE' }}; margin: 20px 0; }
.section-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #94a3b8; margin-bottom: 6px; }
.items-table th {
    background: {{ $doc['colors']['accent'] ?? '#22D3EE' }};
    color: #fff;
    padding: 9px 10px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    text-align: left;
}
.items-table th.right { text-align: right; }
.items-table td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; }
.items-table td.right { text-align: right; }
.items-table td.bold { font-weight: 600; }
.totals-row td { padding: 5px 10px; font-size: 12px; }
.totals-row td.label { text-align: right; color: #64748b; }
.totals-row td.value { text-align: right; }
.grand-total td { padding: 10px 10px; border-top: 2px solid {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; }
.grand-total td.label { text-align: right; font-size: 13.5px; font-weight: 700; color: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; }
.grand-total td.value { text-align: right; font-size: 13.5px; font-weight: 800; color: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; }
.footer-area { margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9; }
.footer-label { font-weight: 700; font-size: 12px; color: #222; }
.footer-text { font-size: 12px; color: #444; margin-bottom: 8px; line-height: 1.5; }
.brand-line { margin-top: 36px; text-align: center; font-size: 9.5px; color: #cbd5e1; letter-spacing: .5px; text-transform: uppercase; }
</style>
</head>
<body>
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
    function fmtPdf3($amt, $cur) {
        $num = number_format((float)$amt, 2);
        return ($cur['pos']??'before')==='after' ? $num.($cur['symbol']??'$') : ($cur['symbol']??'$').$num;
    }
    function fmtDate3($d) {
        if (!$d) return '—';
        try { return (new DateTime($d))->format('d M Y'); } catch(\Exception $e) { return $d; }
    }
    $shipTo       = $doc['ship_to']      ?? [];
    $authorizedBy = $doc['authorized_by'] ?? '';
    $carrier      = $doc['details']['carrier']  ?? '';
    $tracking     = $doc['details']['tracking'] ?? '';

    $docLabels = [
        'invoice'        => 'INVOICE',
        'quote'          => 'QUOTATION',
        'receipt'        => 'RECEIPT',
        'proforma'       => 'PROFORMA INVOICE',
        'purchase_order' => 'PURCHASE ORDER',
        'delivery_note'  => 'DELIVERY NOTE',
    ];
    $title = $docLabels[$type ?? 'invoice'] ?? 'DOCUMENT';
    $dueLbl = match($type ?? 'invoice') {
        'quote', 'proforma' => 'Valid Until',
        'receipt'           => 'Receipt Date',
        'purchase_order'    => 'Expected Delivery',
        'delivery_note'     => 'Delivery Date',
        default             => 'Due Date',
    };
    $toLbl = match($type ?? 'invoice') {
        'quote'          => 'Prepared For',
        'purchase_order' => 'Vendor / Supplier',
        'delivery_note'  => 'Deliver To',
        default          => 'Billed To',
    };
    function addr3($obj) {
        $parts = array_filter([
            $obj['address'] ?? '',
            trim(($obj['city']??'').' '.($obj['state']??'')),
            $obj['zip'] ?? '',
            $obj['country'] ?? '',
        ]);
        return implode(', ', $parts);
    }
@endphp

<div class="page">
<!-- Top row: brand left, title right -->
<table style="margin-bottom:22px;">
    <tr>
        <td style="vertical-align:top;">
            @if(!empty($from['logo']))
            <img src="{{ $from['logo'] }}" class="logo-img" alt="Logo">
            <div style="margin-top:8px;"></div>
            @endif
            <div class="biz-name">{{ $from['name'] ?? 'Your Business' }}</div>
            <div class="biz-sub">
                @if(!empty($from['email'])){{ $from['email'] }}<br>@endif
                @if(!empty($from['phone'])){{ $from['phone'] }}<br>@endif
                @if(!empty(addr3($from))){{ addr3($from) }}@endif
            </div>
        </td>
        <td style="text-align:right;vertical-align:top;">
            <div class="doc-title">{{ $title }}</div>
            <div class="doc-num">{{ $details['number'] ?? '001' }}</div>
        </td>
    </tr>
</table>

<hr class="divider">

<!-- Bill To + Dates -->
<table style="margin-bottom:28px;">
    <tr>
        <td style="vertical-align:top;width:50%;">
            <div class="section-label">{{ $toLbl }}</div>
            <div style="font-size:14px;font-weight:700;">{{ $to['company'] ?? ($to['name'] ?? '—') }}</div>
            @if(!empty($to['company']) && !empty($to['name']))
            <div style="font-size:12.5px;color:#555;">{{ $to['name'] }}</div>
            @endif
            <div style="font-size:12px;color:#64748b;line-height:1.5;">{{ addr3($to) }}</div>
            @if(!empty($to['email']))<div style="font-size:12px;color:#64748b;">{{ $to['email'] }}</div>@endif
            @if($type === 'purchase_order' && (!empty($shipTo['company']) || !empty($shipTo['name']) || !empty($shipTo['address'])))
            <div class="section-label" style="margin-top:12px;">Ship To</div>
            <div style="font-size:13px;font-weight:700;">{{ $shipTo['company'] ?? ($shipTo['name'] ?? '—') }}</div>
            <div style="font-size:12px;color:#64748b;">{{ addr3($shipTo) }}</div>
            @endif
        </td>
        <td style="vertical-align:top;text-align:right;">
            <div class="section-label">Date Issued</div>
            <div style="font-size:13px;font-weight:600;margin-bottom:12px;">{{ fmtDate3($details['date'] ?? '') }}</div>
            <div class="section-label">{{ $dueLbl }}</div>
            <div style="font-size:13px;font-weight:600;">{{ fmtDate3($details['due_date'] ?? '') }}</div>
            @if(!empty($details['po_number']))
            <div class="section-label" style="margin-top:12px;">PO Number</div>
            <div style="font-size:13px;font-weight:600;">{{ $details['po_number'] }}</div>
            @endif
        </td>
    </tr>
</table>

<!-- Items -->
@if($type === 'delivery_note')
<table class="items-table">
    <thead>
        <tr>
            <th style="width:46%;">Description</th>
            <th class="right" style="width:18%;">Qty Ordered</th>
            <th class="right" style="width:18%;">Qty Delivered</th>
            <th style="text-align:center;width:18%;">Unit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item['description'] ?? '' }}</td>
            <td class="right">{{ $item['qty'] ?? 1 }}</td>
            <td class="right">{{ $item['delivered_qty'] ?? ($item['qty'] ?? 1) }}</td>
            <td style="text-align:center;">{{ $item['unit'] ?? 'pcs' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@if(!empty($tracking))
<div style="font-size:11.5px;color:#64748b;margin-top:10px;"><strong>Tracking Number:</strong> {{ $tracking }}</div>
@endif
@else
<table class="items-table">
    <thead>
        <tr>
            <th style="width:50%;">Description</th>
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
            <td class="right">{{ fmtPdf3(($item['rate'] ?? 0), $currency) }}</td>
            <td class="right bold">{{ fmtPdf3(($item['qty']??1)*($item['rate']??0), $currency) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="totals-row">
            <td colspan="3" class="label">Subtotal</td>
            <td class="value">{{ fmtPdf3($subtotal, $currency) }}</td>
        </tr>
        @if($discount['enabled'] ?? false)
        <tr class="totals-row">
            <td colspan="3" class="label">{{ ($discount['type']??'percentage')==='percentage' ? 'Discount ('.$discount['value'].'%)' : 'Discount' }}</td>
            <td class="value" style="color:#dc2626;">- {{ fmtPdf3($discAmt, $currency) }}</td>
        </tr>
        @endif
        @if($tax['enabled'] ?? false)
        <tr class="totals-row">
            <td colspan="3" class="label">{{ $tax['label'] ?? 'Tax' }} ({{ $tax['rate'] ?? 0 }}%)</td>
            <td class="value">{{ fmtPdf3($taxAmt, $currency) }}</td>
        </tr>
        @endif
        @if($shipping['enabled'] ?? false)
        <tr class="totals-row">
            <td colspan="3" class="label">Shipping</td>
            <td class="value">{{ fmtPdf3($shipAmt, $currency) }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td colspan="3" class="label">{{ $type === 'receipt' ? 'Total Paid' : 'Total Amount' }}</td>
            <td class="value">{{ fmtPdf3($total, $currency) }}</td>
        </tr>
        @if(($paid['enabled'] ?? false) && $type !== 'receipt')
        <tr class="totals-row">
            <td colspan="3" class="label">Amount Paid</td>
            <td class="value" style="color:#16a34a;">{{ fmtPdf3($paidAmt, $currency) }}</td>
        </tr>
        <tr class="totals-row">
            <td colspan="3" class="label" style="font-weight:700;color:#0f172a;">Balance Due</td>
            <td class="value" style="font-weight:700;color:#dc2626;">{{ fmtPdf3($due, $currency) }}</td>
        </tr>
        @endif
    </tfoot>
</table>
@endif

@if($notes || $terms || $payInfo)
<div class="footer-area">
    @if($notes)<p class="footer-text"><span class="footer-label">Notes: </span>{{ $notes }}</p>@endif
    @if($terms)<p class="footer-text"><span class="footer-label">Terms & Conditions: </span>{{ $terms }}</p>@endif
    @if($payInfo)
    @php $piLabel3 = $type==='delivery_note' ? 'Delivery Instructions' : ($type==='purchase_order' ? 'Payment Terms' : 'Payment Info'); @endphp
    <p class="footer-text"><span class="footer-label">{{ $piLabel3 }}: </span>{{ $payInfo }}</p>
    @endif
</div>
@endif

@if($type === 'purchase_order' && !empty($authorizedBy))
<div style="text-align:right;margin-top:24px;">
    <div style="display:inline-block;text-align:center;min-width:190px;">
        <div style="border-top:1px solid #0f172a;padding-top:6px;margin-top:32px;font-size:12.5px;font-weight:600;">{{ $authorizedBy }}</div>
        <div style="font-size:10.5px;color:#94a3b8;margin-top:2px;">Authorized Signature</div>
    </div>
</div>
@endif

<div class="brand-line">Generated with FinTrack Free Business Docs · fintrack.co.ke</div>
</div>
</body>
</html>
