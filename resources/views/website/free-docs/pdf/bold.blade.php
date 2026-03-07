<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: {{ $doc['font'] === 'georgia' ? 'Georgia, serif' : ($doc['font'] === 'courier' ? '"Courier New", monospace' : ($doc['font'] === 'verdana' ? 'Verdana, sans-serif' : ($doc['font'] === 'trebuchet' ? '"Trebuchet MS", sans-serif' : 'Helvetica, Arial, sans-serif'))) }};
    color: #1a1a2e; background: #fff; font-size: 13px;
}
.hero { background: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; padding: 36px; }
.page { padding: 0 36px 36px; }
table { border-collapse: collapse; width: 100%; }
.doc-type-badge {
    display: inline-block;
    background: rgba(255,255,255,.15);
    border-radius: 6px;
    padding: 5px 14px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,.85);
    margin-bottom: 8px;
}
.hero-amount { font-size: 34px; font-weight: 900; color: {{ $doc['colors']['accent'] ?? '#22D3EE' }}; letter-spacing: -1px; }
.hero-meta { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 6px; }
.biz-name { font-size: 19px; font-weight: 800; color: #fff; }
.biz-sub { font-size: 11.5px; color: rgba(255,255,255,.7); margin-top: 4px; line-height: 1.5; }
.logo-img { max-height: 58px; max-width: 130px; }
.info-strip { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 36px; }
.section-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 5px; }
.items-table { margin-top: 20px; }
.items-table th {
    background: {{ $doc['colors']['primary'] ?? '#0B2A4A' }};
    color: #fff;
    padding: 10px 12px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    text-align: left;
}
.items-table th.right { text-align: right; }
.items-table td { padding: 9px 12px; border-bottom: 1px solid #f0f0f0; font-size: 12.5px; }
.items-table td.right { text-align: right; }
.items-table td.bold { font-weight: 600; }
.totals-row td { padding: 6px 12px; font-size: 12px; }
.totals-row td.label { text-align: right; color: #555; }
.totals-row td.value { text-align: right; }
.grand-total td { padding: 12px 12px; background: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; }
.grand-total td.label { text-align: right; font-size: 13.5px; font-weight: 700; color: #fff; }
.grand-total td.value { text-align: right; font-size: 15px; font-weight: 900; color: {{ $doc['colors']['accent'] ?? '#22D3EE' }}; }
.footer-area { margin-top: 20px; }
.footer-label { font-weight: 700; font-size: 12px; color: #222; }
.footer-text { font-size: 12px; color: #444; margin-bottom: 8px; line-height: 1.5; }
.brand-line { margin-top: 28px; text-align: center; font-size: 10px; color: #ccc; }
</style>
</head>
<body>
@php
    $primary   = $doc['colors']['primary']   ?? '#0B2A4A';
    $accent    = $doc['colors']['accent']    ?? '#22D3EE';
    $secondary = $doc['colors']['secondary'] ?? $primary;
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
    function fmtPdf4($amt, $cur) {
        $num = number_format((float)$amt, 2);
        return ($cur['pos']??'before')==='after' ? $num.($cur['symbol']??'$') : ($cur['symbol']??'$').$num;
    }
    function fmtDate4($d) {
        if (!$d) return '—';
        try { return (new DateTime($d))->format('d M Y'); } catch(\Exception $e) { return $d; }
    }
    $docLabels = ['invoice'=>'INVOICE','quote'=>'QUOTATION','receipt'=>'RECEIPT'];
    $title = $docLabels[$type ?? 'invoice'] ?? 'DOCUMENT';
    $dueLbl = ($type==='quote') ? 'Valid Until' : (($type==='receipt') ? 'Receipt Date' : 'Due Date');
    function addr4($obj) {
        $parts = array_filter([
            $obj['address'] ?? '',
            trim(($obj['city']??'').' '.($obj['state']??'')),
            $obj['zip'] ?? '',
            $obj['country'] ?? '',
        ]);
        return implode(', ', $parts);
    }
@endphp

<!-- Hero Header -->
<div class="hero">
    <table>
        <tr>
            <td style="vertical-align:middle;">
                @if(!empty($from['logo']))
                <img src="{{ $from['logo'] }}" class="logo-img" alt="Logo">
                <div style="margin-top:8px;"></div>
                @endif
                <div class="biz-name">{{ $from['name'] ?? 'Your Business' }}</div>
                <div class="biz-sub">
                    @if(!empty($from['email'])){{ $from['email'] }}@endif
                    @if(!empty($from['email']) && !empty($from['phone'])) &nbsp;·&nbsp; @endif
                    @if(!empty($from['phone'])){{ $from['phone'] }}@endif
                </div>
            </td>
            <td style="text-align:right;vertical-align:middle;">
                <div class="doc-type-badge">{{ $title }}</div>
                <div class="hero-amount">{{ fmtPdf4($total, $currency) }}</div>
                <div class="hero-meta">
                    # {{ $details['number'] ?? '001' }} &nbsp;·&nbsp; {{ fmtDate4($details['date'] ?? '') }}
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Info strip -->
<div class="info-strip">
    <table>
        <tr>
            <td style="vertical-align:top;width:55%;">
                <div class="section-label">{{ $type === 'quote' ? 'Prepared For' : 'Bill To' }}</div>
                <div style="font-size:13.5px;font-weight:700;">{{ $to['company'] ?? ($to['name'] ?? '—') }}</div>
                @if(!empty($to['company']) && !empty($to['name']))
                <div style="font-size:12px;color:#555;">{{ $to['name'] }}</div>
                @endif
                <div style="font-size:12px;color:#64748b;">{{ addr4($to) }}</div>
                @if(!empty($to['email']))<div style="font-size:12px;color:#64748b;">{{ $to['email'] }}</div>@endif
            </td>
            <td style="vertical-align:top;text-align:right;">
                <table style="margin-left:auto;font-size:12px;">
                    <tr>
                        <td style="padding:2px 10px 2px 0;color:#888;">{{ $dueLbl }}</td>
                        <td style="font-weight:700;{{ $type==='invoice' ? 'color:#dc2626;' : '' }}">{{ fmtDate4($details['due_date'] ?? '') }}</td>
                    </tr>
                    @if(!empty($details['po_number']))
                    <tr>
                        <td style="padding:2px 10px 2px 0;color:#888;">PO#</td>
                        <td>{{ $details['po_number'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($details['reference']))
                    <tr>
                        <td style="padding:2px 10px 2px 0;color:#888;">Ref</td>
                        <td>{{ $details['reference'] }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</div>

<!-- Items -->
<div class="page" style="padding-top:0;">
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
                <td class="right">{{ fmtPdf4(($item['rate'] ?? 0), $currency) }}</td>
                <td class="right bold">{{ fmtPdf4(($item['qty']??1)*($item['rate']??0), $currency) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="3" class="label">Subtotal</td>
                <td class="value">{{ fmtPdf4($subtotal, $currency) }}</td>
            </tr>
            @if($discount['enabled'] ?? false)
            <tr class="totals-row">
                <td colspan="3" class="label">{{ ($discount['type']??'percentage')==='percentage' ? 'Discount ('.$discount['value'].'%)' : 'Discount' }}</td>
                <td class="value" style="color:#dc2626;">- {{ fmtPdf4($discAmt, $currency) }}</td>
            </tr>
            @endif
            @if($tax['enabled'] ?? false)
            <tr class="totals-row">
                <td colspan="3" class="label">{{ $tax['label'] ?? 'Tax' }} ({{ $tax['rate'] ?? 0 }}%)</td>
                <td class="value">{{ fmtPdf4($taxAmt, $currency) }}</td>
            </tr>
            @endif
            @if($shipping['enabled'] ?? false)
            <tr class="totals-row">
                <td colspan="3" class="label">Shipping</td>
                <td class="value">{{ fmtPdf4($shipAmt, $currency) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td colspan="3" class="label">{{ $type === 'receipt' ? 'Total Paid' : 'Total Amount' }}</td>
                <td class="value">{{ fmtPdf4($total, $currency) }}</td>
            </tr>
            @if(($paid['enabled'] ?? false) && $type !== 'receipt')
            <tr class="totals-row">
                <td colspan="3" class="label">Amount Paid</td>
                <td class="value" style="color:#16a34a;">{{ fmtPdf4($paidAmt, $currency) }}</td>
            </tr>
            <tr class="totals-row">
                <td colspan="3" class="label" style="font-weight:700;color:#1a1a2e;">Balance Due</td>
                <td class="value" style="font-weight:700;color:#dc2626;">{{ fmtPdf4($due, $currency) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    @if($notes || $terms || $payInfo)
    <div class="footer-area" style="margin-top:20px;">
        @if($notes)<p class="footer-text"><span class="footer-label">Notes: </span>{{ $notes }}</p>@endif
        @if($terms)<p class="footer-text"><span class="footer-label">Terms & Conditions: </span>{{ $terms }}</p>@endif
        @if($payInfo)<p class="footer-text"><span class="footer-label">Payment Info: </span>{{ $payInfo }}</p>@endif
    </div>
    @endif

    <div class="brand-line">Generated with FinTrack Free Business Docs &nbsp;·&nbsp; fintrack.co.ke</div>
</div>
</body>
</html>
