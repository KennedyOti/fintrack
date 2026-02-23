<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote {{ $quote->quote_number }}</title>
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
        
        .quote-container {
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
            border-bottom: 3px solid #7C3AED;
        }
        
        .company-info {
            max-width: 50%;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #7C3AED;
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
        
        .quote-info {
            text-align: right;
        }
        
        .quote-title {
            font-size: 32px;
            font-weight: bold;
            color: #7C3AED;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        
        .quote-number {
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .quote-number strong {
            color: #7C3AED;
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
        .status-accepted { background-color: #10B981; color: white; }
        .status-rejected { background-color: #EF4444; color: white; }
        .status-expired { background-color: #F59E0B; color: white; }
        .status-converted { background-color: #7C3AED; color: white; }
        
        .quote-details {
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
        
        .info-block.quote-meta {
            border-left: 4px solid #7C3AED;
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
            background-color: #7C3AED;
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
            background-color: #F3E8FF;
        }
        
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        
        .summary-table {
            width: 280px;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 10px 12px;
            font-size: 12px;
        }
        
        .summary-table .label {
            color: #6B7280;
            text-align: right;
        }
        
        .summary-table .value {
            font-weight: 600;
            text-align: right;
        }
        
        .summary-table tr:last-child {
            background-color: #7C3AED;
            color: white;
        }
        
        .summary-table tr:last-child .label,
        .summary-table tr:last-child .value {
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .summary-table .divider {
            border-bottom: 1px solid #E5E7EB;
        }
        
        .notes {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #F9FAFB;
            border-radius: 8px;
        }
        
        .notes-title {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notes-content {
            font-size: 11px;
            color: #6B7280;
            line-height: 1.6;
        }
        
        .terms {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #FEF3C7;
            border-radius: 8px;
            border-left: 4px solid #F59E0B;
        }
        
        .terms-title {
            font-size: 12px;
            font-weight: bold;
            color: #92400E;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .terms-content {
            font-size: 11px;
            color: #92400E;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 10px;
            color: #9CA3AF;
        }
        
        .footer p {
            margin-bottom: 4px;
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
    <div class="quote-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $businessInfo['business_name'] }}</div>
                <div class="company-details">
                    @if($businessInfo['business_address'])
                        <p>{{ $businessInfo['business_address'] }}</p>
                    @endif
                    @if($businessInfo['email'])
                        <p>Email: {{ $businessInfo['email'] }}</p>
                    @endif
                    @if($businessInfo['phone'])
                        <p>Phone: {{ $businessInfo['phone'] }}</p>
                    @endif
                    @if($businessInfo['tax_number'])
                        <p>Tax ID: {{ $businessInfo['tax_number'] }}</p>
                    @endif
                </div>
            </div>
            <div class="quote-info">
                <div class="quote-title">QUOTE</div>
                <div class="quote-number">
                    <strong>{{ $quote->quote_number }}</strong>
                </div>
                <span class="status-badge status-{{ $quote->status }}">{{ ucfirst($quote->status) }}</span>
            </div>
        </div>
        
        <!-- Quote Details -->
        <div class="quote-details">
            <div class="info-block bill-to">
                <div class="section-title">Quote To</div>
                @if($quote->client)
                    <div class="client-name">{{ $quote->client->name }}</div>
                    @if($quote->client->company_name)
                        <p style="font-size: 12px; color: #6B7280; margin-bottom: 4px;">{{ $quote->client->company_name }}</p>
                    @endif
                    @if($quote->client->email)
                        <p style="font-size: 11px; color: #6B7280;">{{ $quote->client->email }}</p>
                    @endif
                    @if($quote->client->phone)
                        <p style="font-size: 11px; color: #6B7280;">{{ $quote->client->phone }}</p>
                    @endif
                    @if($quote->client->address)
                        <p style="font-size: 11px; color: #6B7280; margin-top: 4px;">{{ $quote->client->address }}</p>
                    @endif
                @else
                    <p style="font-size: 12px; color: #6B7280;">No client specified</p>
                @endif
            </div>
            <div class="info-block quote-meta">
                <div class="section-title">Quote Details</div>
                <div class="date-row">
                    <span class="label">Issue Date:</span>
                    <span class="value">{{ $quote->issue_date->format('M d, Y') }}</span>
                </div>
                <div class="date-row">
                    <span class="label">Valid Until:</span>
                    <span class="value">{{ $quote->valid_until->format('M d, Y') }}</span>
                </div>
                @if($quote->project)
                <div class="date-row">
                    <span class="label">Project:</span>
                    <span class="value">{{ $quote->project->title }}</span>
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
                @foreach($quote->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">{{ $currencySymbol }}{{ number_format($quote->subtotal, 2) }}</td>
                </tr>
                @if($quote->tax_amount > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">{{ $currencySymbol }}{{ number_format($quote->tax_amount, 2) }}</td>
                </tr>
                @endif
                @if($quote->discount_amount > 0)
                <tr>
                    <td class="label">Discount</td>
                    <td class="value">-{{ $currencySymbol }}{{ number_format($quote->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Total</td>
                    <td class="value">{{ $currencySymbol }}{{ number_format($quote->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Notes -->
        @if($quote->notes)
        <div class="notes">
            <div class="notes-title">Notes</div>
            <div class="notes-content">{{ $quote->notes }}</div>
        </div>
        @endif
        
        <!-- Terms -->
        <div class="terms">
            <div class="terms-title">Terms & Conditions</div>
            <div class="terms-content">
                <p>• This quote is valid until {{ $quote->valid_until->format('M d, Y') }}.</p>
                <p>• Prices are subject to change after the validity period.</p>
                <p>• To accept this quote, please contact us or sign and return a copy.</p>
                <p>• Payment terms will be discussed upon quote acceptance.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Generated by FinTrack on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
