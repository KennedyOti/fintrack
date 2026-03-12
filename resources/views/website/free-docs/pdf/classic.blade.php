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
.header { background: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; color: #fff; padding: 32px 36px; }
.page { padding: 26px 36px 36px; }
table { border-collapse: collapse; width: 100%; }
.doc-title { font-size: 32px; font-weight: 900; color: #fff; letter-spacing: -1px; }
.doc-num { font-size: 12.5px; color: rgba(255,255,255,.75); margin-top: 5px; }
.biz-name { font-size: 20px; font-weight: 800; color: #fff; }
.biz-sub { font-size: 11.5px; color: rgba(255,255,255,.75); margin-top: 4px; line-height: 1.5; }
.logo-img { max-height: 58px; max-width: 130px; }
.section-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: {{ $doc['colors']['primary'] ?? '#0B2A4A' }}; margin-bottom: 6px; }
.items-table th {
    background: #f1f5f9;
    color: #1a1a2e;
    padding: 10px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    text-align: left;
}
.items-table th.right { text-align: right; }
.items-table td { padding: 9px 10px; border-bottom: 1px solid #f0f0f0; font-size: 12.5px; }
.items-table td.right { text-align: right; }
.items-table td.bold { font-weight: 600; }
.totals-row td { padding: 6px 10px; font-size: 12px; }
.totals-row td.label { text-align: right; color: #555; }
.totals-row td.value { text-align: right; }
.grand-total td { padding: 10px 10px; border-top: 2px solid {{ $doc['colors']['accent'] ?? '#22D3EE' }}; }
.grand-total td.label { text-align: right; font-size: 13.5px; font-weight: 700; }
.grand-total td.value { text-align: right; font-size: 13.5px; font-weight: 800; color: {{ $doc['colors']['accent'] ?? '#22D3EE' }}; }
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
    function fmtPdf2($amt, $cur) {
        $num = number_format((float)$amt, 2);
        return ($cur['pos']??'before')==='after' ? $num.($cur['symbol']??'$') : ($cur['symbol']??'$').$num;
    }
    function fmtDate2($d) {
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
        default          => 'Bill To',
    };
    function addr2($obj) {
        $parts = array_filter([
            $obj['address'] ?? '',
            trim(($obj['city']??'').' '.($obj['state']??'')),
            $obj['zip'] ?? '',
            $obj['country'] ?? '',
        ]);
        return implode(', ', $parts);
    }
@endphp

<!-- Coloured Header -->
<div class="header">
    <table>
        <tr>
            <td style="vertical-align:middle;">
                @if(!empty($from['logo']))
                <img src="{{ $from['logo'] }}" class="logo-img" alt="Logo">
                @endif
                <div class="biz-name" style="margin-top:{{ !empty($from['logo']) ? '6px' : '0' }};">{{ $from['name'] ?? 'Your Business' }}</div>
                <div class="biz-sub">
                    @if(!empty($from['email'])){{ $from['email'] }}@endif
                    @if(!empty($from['email']) && !empty($from['phone'])) &nbsp;·&nbsp; @endif
                    @if(!empty($from['phone'])){{ $from['phone'] }}@endif
                    @if(!empty(addr2($from))) <br>{{ addr2($from) }} @endif
                </div>
            </td>
            <td style="text-align:right;vertical-align:middle;">
                <div class="doc-title">{{ $title }}</div>
                <div class="doc-num"># {{ $details['number'] ?? '001' }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="page">
    <!-- From / To / Details -->
    <table style="margin-bottom:24px;">
        <tr>
            <td style="vertical-align:top;width:40%;">
                <div class="section-label">From</div>
                <div style="font-size:12.5px;line-height:1.6;color:#333;">
                    @if(!empty(addr2($from))) {{ addr2($from) }} @else <em style="color:#aaa;">Your address</em> @endif
                </div>
            </td>
            <td style="vertical-align:top;width:{{ $type === 'purchase_order' ? '25%' : '35%' }};padding-left:20px;">
                <div class="section-label">{{ $toLbl }}</div>
                <div style="font-size:13.5px;font-weight:700;">{{ $to['company'] ?? ($to['name'] ?? '—') }}</div>
                @if(!empty($to['company']) && !empty($to['name']))
                <div style="font-size:12.5px;">{{ $to['name'] }}</div>
                @endif
                <div style="font-size:12px;color:#444;line-height:1.6;">{{ addr2($to) }}</div>
                @if(!empty($to['email']))<div style="font-size:12px;color:#555;">{{ $to['email'] }}</div>@endif
            </td>
            @if($type === 'purchase_order' && (!empty($shipTo['company']) || !empty($shipTo['name']) || !empty($shipTo['address'])))
            <td style="vertical-align:top;width:22%;padding-left:20px;">
                <div class="section-label">Ship To</div>
                <div style="font-size:13.5px;font-weight:700;">{{ $shipTo['company'] ?? ($shipTo['name'] ?? '—') }}</div>
                <div style="font-size:12px;color:#444;line-height:1.6;">{{ addr2($shipTo) }}</div>
            </td>
            @endif
            <td style="vertical-align:top;text-align:right;">
                <div class="section-label">Details</div>
                <table style="margin-left:auto;font-size:12px;color:#444;">
                    <tr>
                        <td style="padding:2px 8px 2px 0;color:#888;">Date</td>
                        <td style="font-weight:600;">{{ fmtDate2($details['date'] ?? '') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px 8px 2px 0;color:#888;">{{ $dueLbl }}</td>
                        <td style="font-weight:600;{{ $type==='invoice' ? 'color:#dc2626;' : '' }}">{{ fmtDate2($details['due_date'] ?? '') }}</td>
                    </tr>
                    @if(!empty($details['po_number']))
                    <tr>
                        <td style="padding:2px 8px 2px 0;color:#888;">PO#</td>
                        <td>{{ $details['po_number'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($details['reference']))
                    <tr>
                        <td style="padding:2px 8px 2px 0;color:#888;">Ref</td>
                        <td>{{ $details['reference'] }}</td>
                    </tr>
                    @endif
                </table>
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
    <div style="font-size:11.5px;color:#555;margin-top:10px;"><strong>Tracking Number:</strong> {{ $tracking }}</div>
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
                <td class="right">{{ fmtPdf2(($item['rate'] ?? 0), $currency) }}</td>
                <td class="right bold">{{ fmtPdf2(($item['qty']??1)*($item['rate']??0), $currency) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="3" class="label">Subtotal</td>
                <td class="value">{{ fmtPdf2($subtotal, $currency) }}</td>
            </tr>
            @if($discount['enabled'] ?? false)
            <tr class="totals-row">
                <td colspan="3" class="label">{{ ($discount['type']??'percentage')==='percentage' ? 'Discount ('.$discount['value'].'%)' : 'Discount' }}</td>
                <td class="value" style="color:#dc2626;">- {{ fmtPdf2($discAmt, $currency) }}</td>
            </tr>
            @endif
            @if($tax['enabled'] ?? false)
            <tr class="totals-row">
                <td colspan="3" class="label">{{ $tax['label'] ?? 'Tax' }} ({{ $tax['rate'] ?? 0 }}%)</td>
                <td class="value">{{ fmtPdf2($taxAmt, $currency) }}</td>
            </tr>
            @endif
            @if($shipping['enabled'] ?? false)
            <tr class="totals-row">
                <td colspan="3" class="label">Shipping</td>
                <td class="value">{{ fmtPdf2($shipAmt, $currency) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td colspan="3" class="label">{{ $type === 'receipt' ? 'Total Paid' : 'Total Amount' }}</td>
                <td class="value">{{ fmtPdf2($total, $currency) }}</td>
            </tr>
            @if(($paid['enabled'] ?? false) && $type !== 'receipt')
            <tr class="totals-row">
                <td colspan="3" class="label">Amount Paid</td>
                <td class="value" style="color:#16a34a;">{{ fmtPdf2($paidAmt, $currency) }}</td>
            </tr>
            <tr class="totals-row">
                <td colspan="3" class="label" style="font-weight:700;color:#1a1a2e;">Balance Due</td>
                <td class="value" style="font-weight:700;color:#dc2626;">{{ fmtPdf2($due, $currency) }}</td>
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
        @php $piLabel2 = $type==='delivery_note' ? 'Delivery Instructions' : ($type==='purchase_order' ? 'Payment Terms' : 'Payment Info'); @endphp
        <p class="footer-text"><span class="footer-label">{{ $piLabel2 }}: </span>{{ $payInfo }}</p>
        @endif
    </div>
    @endif

    @if($type === 'purchase_order' && !empty($authorizedBy))
    <div style="text-align:right;margin-top:24px;">
        <div style="display:inline-block;text-align:center;min-width:190px;">
            <div style="border-top:1px solid #334155;padding-top:6px;margin-top:32px;font-size:12.5px;font-weight:600;">{{ $authorizedBy }}</div>
            <div style="font-size:10.5px;color:#888;margin-top:2px;">Authorized Signature</div>
        </div>
    </div>
    @endif

    <div class="brand-line">Generated with FinTrack Free Business Docs &nbsp;·&nbsp; fintrack.co.ke</div>
</div>
</body>
</html>
