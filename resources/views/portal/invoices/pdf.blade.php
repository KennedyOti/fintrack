<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1E3A8A;
        }
        
        .company-info {
            max-width: 50%;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1E3A8A;
            margin-bottom: 8px;
        }
        
        .company-details {
            font-size: 11px;
            color: #6B7280;
            line-height: 1.6;
        }
        
        .company-details p {
            margin-bottom: 2px;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #1E3A8A;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .invoice-number strong {
            color: #1E3A8A;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-draft { background-color: #6B7280; color: white; }
        .status-sent { background-color: #3B82F6; color: white; }
        .status-partial { background-color: #F59E0B; color: white; }
        .status-paid { background-color: #10B981; color: white; }
        .status-overdue { background-color: #EF4444; color: white; }
        .status-cancelled { background-color: #1F2937; color: white; }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 40px;
        }
        
        .info-block {
            flex: 1;
            padding: 20px;
            background-color: #F9FAFB;
            border-radius: 8px;
        }
        
        .info-block.bill-to {
            border-left: 4px solid #10B981;
        }
        
        .info-block.invoice-meta {
            border-left: 4px solid #1E3A8A;
        }
        
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #6B7280;
            margin-bottom: 10px;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
        
        .client-name {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 6px;
        }
        
        .date-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12px;
        }
        
        .date-row .label {
            color: #6B7280;
        }
        
        .date-row .value {
            font-weight: 600;
            color: #374151;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background-color: #1E3A8A;
            color: white;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        
        .items-table th:nth-child(2),
        .items-table th:nth-child(3),
        .items-table td:nth-child(2),
        .items-table td:nth-child(3) {
            text-align: center;
        }
        
        .items-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 12px;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .items-table tr:hover {
            background-color: #EEF2FF;
        }
        
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        
        .summary-table {
            width: 280px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
            font-size: 12px;
        }
        
        .summary-row .label {
            color: #6B7280;
        }
        
        .summary-row .amount {
            font-weight: 600;
            color: #374151;
        }
        
        .summary-row.subtotal {
            border-bottom: 1px dashed #E5E7EB;
        }
        
        .summary-row.tax {
            color: #6B7280;
        }
        
        .summary-row.discount {
            color: #10B981;
        }
        
        .summary-row.total {
            border-bottom: none;
            border-top: 2px solid #1E3A8A;
            padding-top: 12px;
            margin-top: 8px;
            font-size: 14px;
        }
        
        .summary-row.total .label {
            font-weight: bold;
            color: #1F2937;
        }
        
        .summary-row.total .amount {
            font-size: 18px;
            font-weight: bold;
            color: #1E3A8A;
        }
        
        .summary-row.paid {
            color: #10B981;
        }
        
        .summary-row.balance {
            border-bottom: none;
            border-top: 2px solid #10B981;
            padding-top: 12px;
            margin-top: 8px;
        }
        
        .summary-row.balance .label {
            font-weight: bold;
            color: #1F2937;
        }
        
        .summary-row.balance .amount {
            font-size: 18px;
            font-weight: bold;
            color: #10B981;
        }
        
        .notes {
            margin-top: 30px;
            padding: 20px;
            background-color: #FEFCE8;
            border-radius: 8px;
            border-left: 4px solid #F59E0B;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #92400E;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notes p {
            color: #92400E;
            font-size: 11px;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #9CA3AF;
            font-size: 10px;
        }
        
        .footer p {
            margin-bottom: 4px;
        }
        
        .footer .company-footer {
            font-weight: 600;
            color: #6B7280;
        }
        
        .tax-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #F9FAFB;
            border-radius: 6px;
            font-size: 10px;
            color: #6B7280;
        }
        
        .tax-info strong {
            color: #374151;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $businessInfo['business_name'] ?? 'Your Business Name' }}</div>
                <div class="company-details">
                    @if($businessInfo['business_address'])
                        <p>{!! nl2br(e($businessInfo['business_address'])) !!}</p>
                    @endif
                    @if($businessInfo['email'])
                        <p>{{ $businessInfo['email'] }}</p>
                    @endif
                    @if($businessInfo['phone'])
                        <p>{{ $businessInfo['phone'] }}</p>
                    @endif
                    @if($businessInfo['tax_number'])
                        <p>Tax ID: {{ $businessInfo['tax_number'] }}</p>
                    @endif
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">Invoice</div>
                <div class="invoice-number">
                    <strong>#{{ $invoice->invoice_number }}</strong>
                </div>
                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="info-block bill-to">
                <div class="section-title">Bill To</div>
                @if($invoice->client)
                    <div class="client-name">{{ $invoice->client->name }}</div>
                    @if($invoice->client->email)
                        <p>{{ $invoice->client->email }}</p>
                    @endif
                    @if($invoice->client->phone)
                        <p>{{ $invoice->client->phone }}</p>
                    @endif
                    @if($invoice->client->address)
                        <p>{{ $invoice->client->address }}</p>
                    @endif
                @else
                    <p>No client</p>
                @endif
            </div>
            <div class="info-block invoice-meta">
                <div class="section-title">Invoice Details</div>
                <div class="date-row">
                    <span class="label">Issue Date:</span>
                    <span class="value">{{ $invoice->issue_date->format('F d, Y') }}</span>
                </div>
                <div class="date-row">
                    <span class="label">Due Date:</span>
                    <span class="value">{{ $invoice->due_date->format('F d, Y') }}</span>
                </div>
                @if($invoice->project)
                <div class="date-row">
                    <span class="label">Project:</span>
                    <span class="value">{{ $invoice->project->title }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No items</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-table">
                <div class="summary-row subtotal">
                    <span class="label">Subtotal</span>
                    <span class="amount">{{ $currencySymbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->tax_amount > 0)
                <div class="summary-row tax">
                    <span class="label">Tax</span>
                    <span class="amount">{{ $currencySymbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @endif
                @if($invoice->discount_amount > 0)
                <div class="summary-row discount">
                    <span class="label">Discount</span>
                    <span class="amount">-{{ $currencySymbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span class="label">Total ({{ $currencyCode }})</span>
                    <span class="amount">{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                @if($invoice->paid_amount > 0)
                <div class="summary-row paid">
                    <span class="label">Paid</span>
                    <span class="amount">{{ $currencySymbol }}{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="summary-row balance">
                    <span class="label">Balance Due</span>
                    <span class="amount">{{ $currencySymbol }}{{ number_format($invoice->outstandingAmount(), 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Tax Info -->
        @if($businessInfo['tax_number'])
        <div class="tax-info">
            <strong>Tax Information:</strong> Tax ID / VAT: {{ $businessInfo['tax_number'] }}
        </div>
        @endif
        
        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <div class="notes-title">Notes</div>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p class="company-footer">{{ $businessInfo['business_name'] ?? 'Your Business Name' }}</p>
            <p>Thank you for your business!</p>
            <p>Generated by FinTrack</p>
        </div>
    </div>
</body>
</html>
